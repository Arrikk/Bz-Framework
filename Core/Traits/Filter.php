<?php

namespace Core\Traits;

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
