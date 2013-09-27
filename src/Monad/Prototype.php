<?php
/**
 * Created by PhpStorm.
 * User: tjerk
 * Date: 9/27/13
 * Time: 5:55 PM
 */

namespace Monad;

class prototype
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
        if (!isset($this->instance[$name]) && isset($this->inherited) && ($fromProto = $this->inherited->$name) instanceof \Closure) {
            $target = $fromProto->bindTo($this);
        } else if (isset($this->instance[$name]) && $this->instance[$name] instanceof \Closure) {
            $target = $this->instance[$name];
        } else {
            return;
        }

        return call_user_func_array($target, $arguments);
    }
}