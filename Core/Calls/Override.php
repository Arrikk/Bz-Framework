<?php

namespace Core\Calls;

abstract class Override
{
    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        // call_user_func_array([new static(), '_'.$method], $args);
        return (new static())->call($method, $args);
    }

    private function call($method, $args)   
    {
        $method = '_' . $method;
        if (method_exists($this, $method)) {
            return $this->{$method}(...$args);
        }
        echo "Method " . $method . " does not exist";
    }
}
