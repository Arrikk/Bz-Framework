<?php
namespace Core;

abstract class Component {

    static function __callStatic($name, $arguments)
    {
        $static = (new static);
        return $static->call($name, $arguments);

    }

    private function call($method, $arguments){ 
        $method = '_'.$method;
        if(method_exists($this, $method)):
            return $this->{$method}(...$arguments);
        else:
            echo "You have not created $method method";
        endif;
        
    }

    public function _render()
    {
        return true;
    }
}