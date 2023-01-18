<?php
namespace App\Models;

use Core\Model\Model;
use Core\Traits\User\Extended;
use Core\Traits\User\Help;

class User extends Model
{
    use Help, Extended;
}