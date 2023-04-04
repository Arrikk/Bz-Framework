<?php

namespace App\Helpers;

use Core\Calls\Override;

class Filters extends Override
{
    private $objectFilter;
    private $arrayFilter;

    public function object($object)
    {
        $this->objectFilter = $object;
        return $object;
    }

    public function _from($data)
    {
        $this->objectFilter = $data;
        return $this;
    }

    public function done()
    {
        return $this->objectFilter;
    }

    public function remove()
    {
        $args = func_get_args();
        $object = $this->objectFilter;

        if (empty((array) $object)) return $this;

        if (is_array($object)) :
            foreach ($object as $key => $data) :
                foreach ($args as $toRemove) :
                    unset($object[$key]->$toRemove);
                endforeach;
            endforeach;
        else :
            foreach ($args as $toRemove) :
                unset($object->$toRemove);
            endforeach;
        endif;


        $this->objectFilter = $object;
        return $this;
    }

    public function append()
    {
        $args = func_get_arg(0);
        $object = $this->objectFilter;
        if (empty((array) $object)) return $this;

        if (is_array($object)) :
            foreach ($object as $key => $data) :
                foreach ($args as $toAppend => $value) :
                    $oldVal = $object[$key]->$toAppend;
                    if(is_callable($value)) $object[$key]->$toAppend = call_user_func_array($value, [$oldVal]);
                    else $object[$key]->$toAppend = $value;
                endforeach;
            endforeach;
        else :
            foreach ($args as $toAppend => $value) :
                $object->$toAppend = $value;
            endforeach;
        endif;

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
