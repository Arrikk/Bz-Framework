<?php
namespace App\Models;

use Core\Model\Model;

class Notification extends Model
{
    public static function notifications($page, $userID) {
        return self::paginate($page, 5, 'notifications.id')::find([
            '$.left' => 'users',
            '$.on' => 'users._id = notifications.from_id',
            'where.to_id' => $userID
        ], '
        users.avatar, 
        users.first_name as firstName, 
        users.last_name as lastName,
        notifications.description,
        DATE_FORMAT(notifications.created_at, "%a, %b %d at %h %p") as date_fmt,
        notifications.created_at as date_raw
        ');
    }
}