<?php
namespace App\Models;

use Core\Http\Res;
use Core\Model\Model;

class Folder extends Model
{
    public static function myFolders(User $user) : array 
    {
        $templates = DocumentTemplate::find(['$.where' => 'documenttemplates.folder_id = folders._id'], 'count(*)', false)->query;
        $files = File::find(['$.where' => 'files.folder_id = folders._id'], 'count(*)', false)->query;
        // Res::send($templates);
        $folders = self::find([
            "user_id" => $user->_id,
        ], "*,  DATE_FORMAT(created_at, '%b %d %Y') AS created_on, DATE_FORMAT(updated_at, '%b %d %Y') AS last_updated_on, ($templates) as totalTemplates, ($files) as totalFiles");
        return $folders;
    }
}   