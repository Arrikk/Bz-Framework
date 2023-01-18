<?php
namespace Core\Traits\Model;

use Core\Http\Res;
use Core\Model\Model;

trait Relationship
{
    protected function hasOne(string $model, $matchColumn = null, $table = null) : Model
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;
        return $model::use($table == null ? $this->getCalledClass($model) : $table)::findOne([$classPlulraRmv => $this->id]);
    }

    protected function hasMany(string $model, $matchColumn = null, $table = null) : array
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;
        return $model::use($table == null ? $this->getCalledClass($model) : $table)::find([$classPlulraRmv => $this->id]);
    }

    private function getCalledClass($class = null) : string
    {
        $class = explode('\\', $class ?? get_called_class());
        $class = strtolower(end($class)) . 's';
        return $class;
    }
}