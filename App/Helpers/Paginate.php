<?php
namespace App\Helpers;
class Paginate
{
    public static function page($extra = null)
    {
        $extra = (object) $extra;
        $currentPage = $extra->page ?? 1;
        $currentPage = (int) $currentPage;
        $limit = $extra->limit ?? LIMIT;
        $order = $extra->order ?? DESC;

        if ($currentPage < 1) $currentPage = 1;
        $startAt = ($currentPage - 1) * $limit;
        return (object) [
            'order' => $order,
            'page' => "$startAt, $limit"
        ];
    }
}