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

    public function __construct(prototype $prototype = null)
    {
        $this->inherited = $prototype;
        $this->instance = [];
    }

    public function __set($name, $value)
    {
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
        return call_user_func_array($this->$name->bindTo($this), $arguments);
    }
}