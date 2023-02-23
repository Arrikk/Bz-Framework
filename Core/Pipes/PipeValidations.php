<?php

namespace Core\Pipes;

use AllowDynamicProperties;

#[AllowDynamicProperties]
abstract class PipeValidations
{

    public function isint(string $message = null): PipeValidations
    {
        if (!is_int($this->pipe_property_value))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be an integer");
        return $this;
    }

    public function isstring(int $message = null): PipeValidations
    {
        if (!is_string($this->pipe_property_value))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be a string");
        return $this;
    }

    public function max(int $max = 10, string $message = null): PipeValidations
    {
        if (strlen($this->pipe_property_value) > $max)
            return $this->setError($this->pipe_property_name, $message ?? "Value must be greater than $max");
        return $this;
    }

    public function min(int $min = 4, string $message = null): PipeValidations
    {
        if (strlen($this->pipe_property_value) < $min)
            return $this->setError($this->pipe_property_name, $message ?? "Value cannot be lesser than $min");
        return $this;
    }

    public function isemail(string $message = null): PipeValidations
    {
        if (!filter_var($this->pipe_property_value, FILTER_VALIDATE_EMAIL))
            return $this->setError($this->pipe_property_name, $message ?? "Value requires a valid email address");
        return $this;
    }

    public function is_strong_password(string $message = null): PipeValidations
    {
       return $this;
    }

    public function isrequired(string $message = null): PipeValidations
    {
        if(empty($this->pipe_property_value))
        return $this->setError($this->pipe_property_name, $message ?? "Value cannot be empty");
        return $this;
    }

    public function isurl(string $message = null): PipeValidations
    {
        if(!filter_var($this->pipe_property_value, FILTER_VALIDATE_URL))
        return $this->setError($this->pipe_property_name, $message ?? "Value Requires a valid URL");
        return $this;
    }

    public function setError(string $pipe, string $error): PipeValidations
    {
        if (isset($this->pipe_validation_error[$pipe])) {
            $pipeError = $this->pipe_validation_error[$pipe];
            if (is_string($pipeError)) {
                $this->pipe_validation_error[$pipe] = [$pipeError, $error];
            } else $this->pipe_validation_error[$pipe][] = $error;
        } else $this->pipe_validation_error[$pipe] = $error;
        return $this;
    }
}
