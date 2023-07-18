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
                    /**
                     * Seperate the incoming value with .
                     * if the value is slitted to an array by dot and the length of 
                     * the result is greater than the 1... this means the key intends to create 
                     * another key from dot suffixed. e.g see.watch (create watch from see) using a value from see,`
                     */
                    $exp = explode('.', $toAppend);
                    if (count($exp) > 1) {
                        # get the old value wch is from the prefixed.. e.g (see.watch) returns the value of watch
                        # because see already existed but wants to give its value to watch for use
                        $prefixedValue = $object[$key]->{$exp[0]} ?? null;
                        # get the suffixed key e.g (see.watch) gets the key watch
                        $toAppend = $exp[1];
                        # check if this key is callable and send the previous value of prefix
                        if (is_callable($value)) $object[$key]->$toAppend = call_user_func_array($value, [$prefixedValue]);
                        else $object[$key]->$toAppend = $value;
                        continue;
                    }
                    $oldVal = $object[$key]->$toAppend ?? null;
                    if (is_callable($value)) $object[$key]->$toAppend = call_user_func_array($value, [$oldVal]);
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

        if (is_array($object)) :
            foreach ($object as $key => $data) :
                foreach($data as $dataKey => $values){
                    // foreach ($args as $only) :
                        if (!in_array($dataKey, $args)) unset($object[$key]->$dataKey);
                    // endforeach;
                }
            endforeach;
        else :
            foreach ($object as $key => $value) :
                if (!in_array($key, $args)) unset($object->$key);
                continue;
            endforeach;
        endif;

        // foreach ($object as $key => $value) :
        //     if (!in_array($key, $args)) unset($object->$key);
        //     continue;
        // endforeach;

        $this->objectFilter = $object;
        return $this;
    }
}