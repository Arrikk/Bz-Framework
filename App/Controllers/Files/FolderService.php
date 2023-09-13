<?php

namespace App\Controllers\Files;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Files\FileManager\FileManagerService;
use App\Controllers\Files\Files\FilesService;
use App\Helpers\Filters;
use App\Models\Employee;
use App\Models\File;
use App\Models\Folder;
use App\Models\Manager;
use App\Models\User;
use Core\Env;
use Core\Http\Res;
use Core\Pipes\Pipes;

class FolderService extends Authenticated
{
    public function createPipe(Pipes $folder, $userID)
    {
        return $folder->pipe([
            'user_id' => $userID,
            'name' => $folder->name()
                ->isrequired()->min(1)->max(20)
                ->match('/^[\da-z]+$/i')
                ->tostudly()->name,
            'visibility' => $folder->visibility()
                ->isenum('private', 'public')
                ->visibility
        ]);
    }

    public function folderPipe(Pipes $folder)
    {
        return $folder->pipe([
            'id' => $folder->id()->isrequired()->isint()->id,
            'user_id' => $folder->user_id()->isrequired()->isint()->user_id,
        ]);
    }

    public function createService($data)
    {
        return $this->folderFormat(Folder::dump($data));
    }

    public function folderService($user_id, $folder_id, $folder = null)
    {
        return $this->folderFormat(Folder::findOne([
            'id' => $folder_id,
            'and.user_id' => $user_id,
            'or.id' => $folder_id,
            '$.and' => Folder::inset($user_id, 'collaborators'),
            'or.id' => $folder_id,
            '$.and' => Folder::inset($user_id, 'shared')
        ]), $user_id);
    }

    public static function folderFormat($folder, $accessID = null, $withFilter = true)
    {
        if ($folder instanceof Folder) return $folder->append([
            'share_id' => $folder->share_link,
            'share_link' => ($folder->share_link !== null || $folder->share_link !== '') ? Env::BASE_URI() . 'share-access?' . $folder->share_link . '&access_id=' : '',
            'owner' => $folder->user_id === $accessID,
            'shared' => !empty($folder->shared),
            'public' => $folder->visibility === PUBLIC_F,
            'private' => $folder->visibility === PRIVATE_F,
            // 'collaborators' => static::sharedWith($folder->collaborators ?? ''),
            'shared_with' => FilesService::sharedWith($folder->shared ?? '', $withFilter),
            'created_on' => date('D M-d-Y', strtotime($folder->created_at)),
            'last_updated_on' => date('D M-d-Y', strtotime($folder->updated_at)),
            'files' =>
            FileManagerService::formatFilesService(
                File::find(
                    ['folder_id' => $folder->id]
                )
            ),
        ])->remove('visibility', 'created_at', 'updated_at');
    }

    public static function foldersService($user_id)
    {
        return self::foldersFormat(Folder::find(['user_id' => $user_id]), $user_id);
    }

    public static function foldersFormat($folders, $accessID = null, $withFilter = true)
    {
        return Filters::from($folders)->append([
            'user_id.owner' =>  fn ($user_id) => $user_id === $accessID,
            'shared.is_shared' => fn ($shared) => !empty($shared),
            'visibility.public' => fn ($visibility) => $visibility === PUBLIC_F,
            'visibility.private' => fn ($visibility) => $visibility === PRIVATE_F,
            // 'collaborators' => fn ($collaborators) => self::employees($collaborators ?? ''),
            'shared.shared_with' => fn ($shared) => FilesService::sharedWith($shared, $withFilter),
            'created_at.created_on' => fn ($date) => date('D M-d-Y', strtotime($date)),
            'updated_at.last_updated_on' => fn ($date) => date('D M-d-Y', strtotime($date)),
            'id.files' => fn ($id) => (int)
            File::findOne(
                ['folder_id' => $id],
                'count(*) as totalFiles'
            )->totalFiles ?? 0,
        ])
            ->remove('visibility', 'shared', 'created_at', 'updated_at')
            ->done();
    }
}
