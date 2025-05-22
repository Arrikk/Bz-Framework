<?php

namespace Core\Pipes;

use AllowDynamicProperties;
use Core\Http\Res;

#[AllowDynamicProperties]
class Pipes extends PipeValidations
{

    public function __construct($data)
    {

        if ($data)
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        return $this;
    }


    public function __set($name, $value)
    {
        // die("First Call");
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        // return $this->{$name} ?? null;
    }

    public function __call($name, $arguments)
    {
        $this->pipe_property_name = Secure($name);
        $this->pipe_property_value = Secure(isset($this->{$name}) ? $this->{$name} : '', true);
        return $this;
    }

    public function pipe(array $pipes = [])
    {
        if (isset($this->pipe_validation_error) && !empty($this->pipe_validation_error)) Res::status(400)::error($this->pipe_validation_error);
        return (object) $pipes;
    }
}
