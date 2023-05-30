<?php

namespace Core\Pipes;

use AllowDynamicProperties;
use Core\Http\Res;
use Core\Interfaces\PipeValidationInterface;

#[AllowDynamicProperties]
abstract class PipeValidations implements PipeValidationInterface
{

    public array $pipe_validation_error = [];

    public function isint(string $message = null): PipeValidations
    {
        // die(var_dump((int) $this->{$this->pipe_property_name}));
        if (!is_int((int) $this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be an integer");
        return $this;
    }

    public function isnumeric(string $message = null): PipeValidations
    {
        if (!is_numeric($this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be Float");
        return $this;
    }
    public function isfloat(string $message = null): PipeValidations
    {
        if (!is_float((float) $this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be Float");
        return $this;
    }

    public function isstring(int $message = null): PipeValidations
    {
        if (!is_string($this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Value must be a string");
        return $this;
    }

    public function max(int $max = 10, string $message = null): PipeValidations
    {
        if (strlen($this->{$this->pipe_property_name}) > $max)
            return $this->setError($this->pipe_property_name, $message ?? "Value must be greater than $max");
        return $this;
    }

    public function min(int $min = 4, string $message = null): PipeValidations
    {
        if (strlen($this->{$this->pipe_property_name}) < $min)
            return $this->setError($this->pipe_property_name, $message ?? "Value cannot be lesser than $min");
        return $this;
    }

    public function gte(int $num, string $message = null): PipeValidations
    {
<<<<<<< HEAD
        if ((float) ($this->{$this->pipe_property_name}) > $num)
=======
        if ((float) ($this->pipe_property_value) > $num)
>>>>>>> f8c2e1ef5b8ef8944286753cc803c33055b64e0b
            return $this->setError($this->pipe_property_name, $message ?? "Value must be greater than $num");
        return $this;
    }

    public function lte(int $num, string $message = null): PipeValidations
    {
<<<<<<< HEAD
        if ((float) ($this->{$this->pipe_property_name}) < $num)
=======
        if ((float) ($this->pipe_property_value) < $num)
>>>>>>> f8c2e1ef5b8ef8944286753cc803c33055b64e0b
            return $this->setError($this->pipe_property_name, $message ?? "Value must be Lesser than $num");
        return $this;
    }

    public function isemail(string $message = null): PipeValidations
    {
        if (!filter_var($this->{$this->pipe_property_name}, FILTER_VALIDATE_EMAIL))
            return $this->setError($this->pipe_property_name, $message ?? "Value requires a valid email address");
        return $this;
    }

    public function is_strong_password(string $message = null): PipeValidations
    {
        return $this;
    }

    public function isrequired(string $message = null): PipeValidations
    {

        // Res::json(empty($this->{$this->pipe_property_name}));
        if (empty($this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Value cannot be empty");
        return $this;
    }
    
    public function isjson(string $message = null) : PipeValidations
    {
        if (!is_object($this->pipe_property_value))
            return $this->setError($this->pipe_property_name, $message ?? "Data is mot a valid json object");
        return $this;
        
    }

    public function isjson(string $message = null): PipeValidations
    {
        if (!is_object($this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Data is mot a valid json object");
        return $this;
    }

    public function isurl(string $message = null): PipeValidations
    {
        if (!filter_var($this->{$this->pipe_property_name}, FILTER_VALIDATE_URL))
            return $this->setError($this->pipe_property_name, $message ?? "Value Requires a valid URL");
        return $this;
    }

    public function isequal($comparison, string $message = null): PipeValidations
    {
        if ($this->{$this->pipe_property_name} !== $comparison)
            return $this->setError($this->pipe_property_name, $message ?? "Equality Error");
        return $this;
    }
    public function isbool($message = null): PipeValidations
    {
        return 
        ($this->{$this->pipe_property_name} === "1" || $this->{$this->pipe_property_name} !== "0" || $this->{$this->pipe_property_name} !== '' 
         ? $this : $this->setError($this->pipe_property_name, $message ?? "Value must be a boolean"));
        // if ($this->{$this->pipe_property_name} !== "1"  || $this->{$this->pipe_property_name} !== "0" || ($this->{$this->pipe_property_name} !== ''))
        //     return $this->setError($this->pipe_property_name, $message ?? "Value must be a boolean");
        // return $this;
    }

    public function isenum(): PipeValidations
    {
        if (!in_array($this->{$this->pipe_property_name}, func_get_args()))
            return $this->setError($this->pipe_property_name, $message ?? "Choose between these options (".implode(', ', func_get_args()).")" );
        return $this;
    }
    public function tolower(): PipeValidations
    {
        $this->{$this->pipe_property_name} = strtolower($this->{$this->pipe_property_name});
        return $this;
    }
    public function toupper(): PipeValidations
    {
        $this->{$this->pipe_property_name} = strtoupper($this->{$this->pipe_property_name});
        return $this;
    }
    public function toint(): PipeValidations
    {
        $this->{$this->pipe_property_name} = (int) ($this->{$this->pipe_property_name});
        return $this;
    }
    public function tofloat(): PipeValidations
    {
        $this->{$this->pipe_property_name} = (float) ($this->{$this->pipe_property_name});
        return $this;
    }
    public function tostring(): PipeValidations
    {
        $this->{$this->pipe_property_name} = (string) ($this->{$this->pipe_property_name});
        return $this;
    }
    public function tocapitalized(): PipeValidations
    {
        $this->{$this->pipe_property_name} = ucwords($this->{$this->pipe_property_name});
        return $this;
    }
    public function tocamel(): PipeValidations
    {
        $this->{$this->pipe_property_name} = $this->camel($this->{$this->pipe_property_name});
        return $this;
    }
    public function tostudly(): PipeValidations
    {
        $this->{$this->pipe_property_name} = $this->studly($this->{$this->pipe_property_name});
        return $this;
    }
    public function match($regex): PipeValidations
    {
        if (!preg_match($regex, $this->{$this->pipe_property_name}))
            return $this->setError($this->pipe_property_name, $message ?? "Option Error.. Check Value..");
        return $this;
    }
    public function replace($regex, $value = ""): PipeValidations
    {
        $this->{$this->pipe_property_name} = preg_replace('/^' . $regex . '$/i', $this->{$this->pipe_property_name}, $value);
        return $this;
    }
    public function default($default): PipeValidations
    {
        if ($this->{$this->pipe_property_name} === null || empty($this->{$this->pipe_property_name})) $this->{$this->pipe_property_name} = $default;
        // echo $this->{$this->pipe_property_name};
        return $this;
    }
    public function serialize()
    {
        $value = trim($this->{$this->pipe_property_name});
        $value = htmlspecialchars($value);
        $value = stripslashes($value);
        $this->{$this->pipe_property_name} = $value;
        return $this;
    }
    public function totext()
    {
        $value = nl2br($this->{$this->pipe_property_name});
        $this->{$this->pipe_property_name} = $value;
        return $this;
    }
    public function studly($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    public function camel($string)
    {
        return lcfirst($this->studly($string));
    }
    public function contains($value): PipeValidations
    {
        // COMING SOON
        return $this;
    }
    public function includes($value): PipeValidations
    {
        // COMING SOON
        return $this;
    }
    public function has($value): PipeValidations
    {
        // COMING SOON
        return $this;
    }

    public function nullable()
    {
        return $this;
    }

    public function object($validation)
    {
        $pipe = new Pipes($this->{$this->pipe_property_name});
        $piped = [];
        $originalVal = $this->{$this->pipe_property_name};
        foreach ($validation as $key => $value) :
            $setValue = isset($originalVal->$key) ? $originalVal->$key : '';
            $options = explode('|', $value);
            // $piped[] = $originalVal;
            // Res::json($pipe->{$key});
            foreach ($options as $option) :
                $pipe->{$key}()->{$option}()->{$key};
            endforeach;
        endforeach;

        if (!empty($pipe->pipe_validation_error))
            $this->setError($this->pipe_property_name, $pipe->pipe_validation_error);
        return $this;
    }

    public function setError(string $pipe, $error): PipeValidations
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
