<?php
namespace Core\Traits\Model;

use Core\Model\Model;

trait Relationship
{
    public function hasOne(string $model, $matchColumn = null) : Model
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;
        return $model::findOne([$classPlulraRmv => $this->id]);
    }

    public function hasMany(string $model, $matchColumn = null) : array
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;
        return $model::find([$classPlulraRmv => $this->id]);
    }

    public function getCalledClass() : string
    {

        $class = explode('\\', get_called_class());
        $class = strtolower(end($class)) . 's';
        return $class;
    }
}