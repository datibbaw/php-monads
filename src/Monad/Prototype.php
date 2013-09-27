<?php
/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 9/27/13
 * Time: 5:55 PM
 */

namespace Monad;

class Prototype
{
    private $inherited;

    private $instance;

    public function __construct(Prototype $prototype = null)
    {
        $this->inherited = $prototype;
        $this->instance = [];
    }

    public function __set($name, $value)
    {
        if ($value instanceof \Closure) {
            $value = $value->bindTo($this);
        }

        $this->instance[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->instance)) {
            return $this->instance[$name];
        } else {
            return $this->inherited->$name;
        }
    }

    public function __call($name, $arguments)
    {
        if (!isset($this->instance[$name])) {
            if (!isset($this->inherited) || !($fromPrototype = $this->inherited->$name) instanceof \Closure) {
                return; // or better, blow up
            }

            $this->instance[$name] = $fromPrototype->bindTo($this);
        }

        if (!($this->instance[$name] instanceof \Closure)) {
            return; // or better, blow up
        }

        return call_user_func_array($this->instance[$name], $arguments);
    }
}