<?php
namespace Core\Helpers;
class Paginate
{
    public static function page($extra = null)
    {
        $extra = (object) $extra;
        $currentPage = $extra->page ?? 1;
        $offset = (int) $extra->offset ?? 0;
        $currentPage = (int) $currentPage;
        $orderCol =  $extra->orderCol ?? 'id';
        $limit = $extra->limit ?? LIMIT;
        $order = $extra->order ?? DESC;

        if ($currentPage < 1) $currentPage = 1;
        $startAt = ($currentPage - 1) * $limit;
        return (object) [
            'page' => $currentPage,
            'pageLimit' => $limit,
            'order' => $orderCol.' '.$order,
            'offset' => $offset,
            'limit' => "$startAt, $limit"
        ];
    }
}