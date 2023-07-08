<?php

namespace Core\Traits;

use Core\Http\Res;

trait Filter
{
    private $objectFilter;

    public function object($object)
    {
        $this->objectFilter = $object;
        return $object;
    }

    public function remove()
    {
        $args = func_get_args();
        $object = $this->objectFilter;
        if (empty((array) $object)) return $this;

        foreach ($args as $toRemove) :
            unset($object->$toRemove);
        endforeach;

        $this->objectFilter = $object;
        return $this;
    }

    public function append()
    {
        $args = func_get_arg(0);
        $object = $this->objectFilter;
        if (empty((array) $object)) return $this;

        foreach ($args as $toAppend => $value) :
            if (is_callable($value)) $object->$toAppend = call_user_func_array($value, []);
            else
                $object->$toAppend = $value;
        endforeach;

        $this->objectFilter = $object;
        return $this;
    }

    public function only()
    {
        $args = func_get_args();
        $object = $this->objectFilter;
        if (empty((array) $object)) return $this;

        foreach ($object as $key => $value) :
            if (!in_array($key, $args)) unset($object->$key);
            continue;
        endforeach;

        $this->objectFilter = $object;
        return $this;
    }
}
