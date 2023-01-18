<?php

namespace Core\Traits\Model;

use App\Helpers\Paginate;
use Core\Http\Res;
use Core\Model\Model;

trait Relationship
{
    private $paginate = false;
    private $paginateOptions = null;

    protected function hasOne(string $model, $matchColumn = null, $table = null): Model
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;
        return $model::use($table == null ? $this->getCalledClass($model) : $table)::findOne([$classPlulraRmv => $this->id]);
    }

    protected function hasMany(string $model, $matchColumn = null, $table = null): array
    {
        $currClass = $this->getCalledClass();
        $classPlulraRmv = $matchColumn == null ? substr($currClass, 0, strlen($currClass) - 1) . '_id' : $matchColumn;

        if (!$this->paginate)
            return $model::use($table == null ? $this->getCalledClass($model) : $table)::find([$classPlulraRmv => $this->id]);

        $page = Paginate::page($this->paginateOptions);
        return $model::use($table == null ? $this->getCalledClass($model) : $table)::find([
            $classPlulraRmv => $this->id,
            '$.limit' => $page->page
        ]);
    }

    private function getCalledClass($class = null): string
    {
        $class = explode('\\', $class ?? get_called_class());
        $class = strtolower(end($class)) . 's';
        return $class;
    }

    public function paginate($page = 1, $limit = LIMIT, $order = ASC, $offset = 0)
    {
        $this->paginate = true;
        $this->paginateOptions = [
            'limit' => $limit,
            'page' => $page,
            'order' => $order,
            'offset' => $offset
        ];
        return $this;
    }
}
