<?php

namespace App\Controllers\FileManager\Files;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Files\FileManager\FileManagerService;
use App\Controllers\Files\FolderService;
use App\Models\Employee;
use App\Models\File;
use App\Models\Folder;
use App\Models\Manager;
use App\Models\User;
use Core\Env;
use Core\Http\Res;
use Core\Pipes\Pipes;

class FilesSrvice  extends Authenticated
{

    public function sharePipe(Pipes $share)
    {
        return $share->pipe([
            'type' => $share->share_type()
                ->isenum('file', 'folder')
                ->tostudly()->share_type,
            'share_with' => $share->share_with()
                ->isrequired()->share_with,
            'share_id' => $share->share_id()
                ->isrequired()->isint()
                ->toint()->share_id
        ]);
    }
    public function unsharePipe(Pipes $share)
    {
        return $share->pipe([
            'type' => $share->share_type()
                ->isenum('file', 'folder')
                ->tostudly()->share_type,
            'with' => $share->share_with()
                ->isrequired()->share_with,
            'id' => $share->share_id()
                ->isrequired()->isint()
                ->toint()->share_id
        ]);
    }

    public function accessPipe(Pipes $access)
    {
        return $access->pipe([
            'role' => $access->role,
            'share_link' => $access->share_link()
                ->isrequired()
                ->share_link,
            'access_id' => $access->access_id
        ]);
    }

    public function shareService($data)
    {
        $share = ($data->type === 'Folder' ? Folder::class : File::class)::findAndUpdate(['id' => $data->share_id,], [
            'shared' => is_array($data->share_with) ? implode(',', $data->share_with) : NULL,
            'visibility' => is_string($data->share_with) || is_null($data->share_with) ? PUBLIC_F : PRIVATE_F,
            'share_link' => GenerateKey()
        ]);

        if ($share) return $share->append([
            'share_id' => $share->share_link,
            'share_link' => ($share->share_link !== null || $share->share_link !== '') ? Env::BASE_URI().'share-access?'.$share->share_link.'&access_id=': '',
            'shared' => self::sharedWith($share->shared ?? '')
        ])->only('share_link', 'share_id', 'visibility', 'shared');

        // return File::findAndUpdate();
    }
    public function unshareService($data)
    {
        $fd = ($data->type === 'Folder' ? Folder::class : File::class)::findOne(['id' => $data->id,], 'shared, share_link') ?? (object) ['share_link' => NULL, 'shared' => NULL];

        if($data->with === "public"){
            $share = ($data->type === 'Folder' ? Folder::class : File::class)::findAndUpdate(['id' => $data->id,], [
                'visibility' => PRIVATE_F,
            ]);
        }else{
            $unshareWith = str_replace(explode(' ', implode(', ', $data->with)), '', $fd->shared);
            $share = ($data->type === 'Folder' ? Folder::class : File::class)::findAndUpdate(['id' => $data->id,], [
                'shared' => $unshareWith,
                'visibility' => PRIVATE_F,
                'share_link' => empty($unshareWith) ? NULL : $fd->share_link
            ]);
    
            
        }
        if ($share) return $share->append([
            'share_id' => $share->share_link,
            'shared' => self::sharedWith($share->shared ?? ''),
            'share_link' => ($share->share_link !== null || $share->share_link !== '') ? Env::BASE_URI().'share-access?'.$share->share_link.'&access_id=': ''
        ])->only('share_link', 'share_id', 'visibility', 'shared');

        // return File::findAndUpdate();
    }

    public function sharedLinkService($link, $currentUserRole, $accessID = null)
    {
        $roleWithAccess = $accessID.":$currentUserRole";

        $isFile = FileManagerService::formatFileService(
            File::findOne([
                'share_link' => $link, 
                '$.and' => File::inset($roleWithAccess, 'shared'),
                'or.share_link' => $link,
                'and.visibility' => PUBLIC_F
            ])
        );
        $isFolder = FolderService::folderFormat(
            Folder::findOne([
                'share_link' => $link, 
                '$.and' => Folder::inset($roleWithAccess, 'shared'),
                'or.share_link' => $link,
                'and.visibility' => PUBLIC_F
            ])
        );

        $file = $isFile ? $isFile : $isFolder;
        if(!$file) Res::status(400)->error("Access denied");
        return $file;
    }

    function sharedFolders($user_id) {
        return FolderService::foldersFormat(Folder::find([
            'user_id' => $user_id,
             "$.and" => "shared != ''",
             "$.or" => "share_link != ''",
            ]), $user_id);
    }

    public static function sharedWith($sharedWithID, $useFilter = true)
    {
        if ($sharedWithID == "") return [];

        // $exp = str_replace(' ', '', $sharedWithID);
        $exp = explode(',', $sharedWithID);

        if ($useFilter) :
            $user =(array) self::filters($exp);
            return $user;
        else:
            return $exp;
        endif;
    }

    public static function filters($data)
    {
        $iDs = implode(',', $data);

        if (!empty($iDs)) :
                return User::find([
                    '$.where' => Folder::in('id', $iDs)
                ], 'id, fullname, avatar, account_type as role');
        endif;
    }
}
