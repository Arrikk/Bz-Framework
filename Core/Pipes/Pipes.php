<?php

namespace Core\Pipes;

use AllowDynamicProperties;
use Core\Http\Req;
use Core\Http\Res;

class Pipes extends PipeValidations
{
    public function __construct($data)
    {

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }


    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __call($name, $arguments)
    {
        $call = $this->__($name);
        return $call;
    }

    public function __($name)
    {
        $this->pipe_property_name = $name;
        $this->pipe_property_value = $this->$name;
        return $this;
    }

    public function pipe($pipes = [])
    {
        if(isset($this->pipe_validation_error) && !empty($this->pipe_validation_error)) return (object) $this->pipe_validation_error;
        return $pipes;
    }
}
