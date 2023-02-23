<?php
namespace Core\Model;

use Core\Model\Base;
use Core\Traits\Model\Model as TraitsModel;
use Core\Traits\Model\Relationship;

class Model extends Base
{
    use TraitsModel, Relationship;


    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }
}