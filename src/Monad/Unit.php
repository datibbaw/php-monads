<?php
/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 9/27/13
 * Time: 5:55 PM
 */

namespace Monad;

class Unit
{
    private $prototype;

    private $modifier;

    protected function __construct(callable $modifier = null)
    {
        $this->modifier = $modifier;

        $this->prototype = new Prototype();
    }

    protected function instantiate($value)
    {
        $monad = new Monad($this->prototype);

        $monad->bind = function($func, $arguments = array()) use ($value) {
            array_unshift($arguments, $value);
            return call_user_func_array($func, $arguments);
        };

        if ($this->modifier) {
            $modifier = $this->modifier;
            $value = $modifier($monad, $value);
        }

        return $monad;
    }

    /**
     * @param $value
     * @return Monad
     */
    public function __invoke($value)
    {
        return $this->instantiate($value);
    }

    /**
     * @param $name
     * @param callable $func
     * @return Unit
     */
    public function lift($name, callable $func)
    {
        $unit = $this;

        $this->prototype->$name = function() use ($func, $unit) {
            $result = $this->bind($func, func_get_args());

            return $result instanceof monad ? $result : $unit->instantiate($result);
        };

        return $this;
    }

    public function lift_value($name, $func)
    {
        $this->prototype->$name = function() use ($func) {
            return $this->bind($func, func_get_args());
        };

        return $this;
    }

    /**
     * @param callable $modifier
     * @return Unit
     */
    public static function create(callable $modifier = null)
    {
        return new static($modifier);
    }
}