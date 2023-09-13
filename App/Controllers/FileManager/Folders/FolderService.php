<?php

namespace App\Controllers\FileManager\Folders;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\FileManager\Files\FileService;
use App\Controllers\FileManager\Manager\FileManagerService;
use App\Helpers\Filters;
use App\Models\Employee;
use App\Models\File;
use App\Models\Folder;
use App\Models\Manager;
use App\Models\User;
use Core\Env;
use Core\Http\Res;
use Core\Pipes\Pipes;

/**
 * This is a PHP class called "FolderService" 
 * which is a part of the "Files" namespace under the "App\Controllers" directory.
 * It extends the "Authenticated" class. Which provides services to the Files class..
 * or any other related folder or files classes...
 * 
 * The class contains several methods for creating, 
 * formatting, and retrieving folder data. 
 * Here is a brief explanation of each method: 
 */
class FolderService extends Authenticated
{
    /**
     * createPipe(): 
     * This method takes a "Pipes" object and a user ID as parameters 
     * and returns a formatted array for creating a new folder. 
     * It applies various validation rules to the folder name and visibility. 
     * @param Pipes $pipes
     * @return int|string $userID
     * @return array|object
     */
    public function createPipe(Pipes $folder, $userID): object
    {
        return $folder->pipe([
            '_id' => GenerateKey(30, 50),
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

    /**
     * folderPipe(): This method takes a "Pipes" object and returns a formatted array for retrieving a folder. 
     * It applies validation rules to the folder ID and user ID.
     * 
     * @param Pipes $pipes, Pipes object ready for filtering and validations.. 
     * @return object|array|null
     */
    public function folderPipe(Pipes $folder): object
    {
        return $folder->pipe([
            'id' => $folder->folder()->isrequired()->folder,
            'user_id' => $folder->access_id()->isrequired()->access_id,
        ]);
    }

    /**
     * 
     */
    public function updateFolderPipe(Pipes $pipe, Folder $folder): object|null
    {
        return $pipe->pipe([
            'name' => $pipe->name()->default($folder->name)
                ->isrequired()->min(1)->max(20)
                ->match('/^[\da-z]+$/i')
                ->tostudly()->name,
            'visibility' => $pipe->visibility()->default($folder->visibility)
                ->isenum('private', 'public')
                ->visibility
        ]);
    }

    /**
     * createService(): This method takes data as a parameter and returns a 
     * formatted folder object by using the "Folder::dump()" method
     */
    public function createService($data)
    {
        return $this->folderFormat(Folder::dump($data));
    }

    /**
     * folderService(): This method takes a user ID, folder ID, and an optional folder object as parameters. 
     * It retrieves a folder based on the provided IDs and checks for access permissions. 
     * It returns a formatted folder object. 
     * @param string|int $user_id, required parameter to get the userID
     * @param string $folder_id, Folder ID parameter. 
     * @param bool $formatted, Choose to return a formatted folder or not. Default
     * @return Folder|bool|null
     */
    public static function folderService($user_id, $folder_id, $formatted = true): Folder|bool|null
    {
        $folder = Folder::findOne([
            '_id' => $folder_id,
            'and.user_id' => $user_id,
            'or._id' => $folder_id,
            '$.and0' => Folder::inset($user_id, 'collaborators'),
            'or1._id' => $folder_id,
            '$.and1' => Folder::inset($user_id, 'shared')
        ]);

        if ($formatted)
            return self::folderFormat($folder, $user_id);
        return $folder;
    }

    /**
     * folderFormat(): This is a static method that takes a folder object, an access ID, and a boolean flag for filtering as parameters. 
     * It formats the folder object by appending additional properties like share ID, share link, owner status, shared status, visibility, shared with collaborators, creation date, last update date, and associated files. 
     * It removes unnecessary properties from the folder object.
     * 
     * @param Folder $folder, Folder object
     * @param string|integer|null $accessID , id of user accessing the folder
     * @return Folder|void 
     */
    public static function folderFormat($folder, $accessID = null, $withFilter = true): Folder|null
    {
        if ($folder instanceof Folder) return $folder->append([
            'id' => $folder->_id,
            'share_id' => $folder->share_link,
            'share_link' => ($folder->share_link !== null || $folder->share_link !== '') ? Env::BASE_URI() . 'share-access?' . $folder->share_link . '&access_id=' : '',
            'owner' => $folder->user_id === $accessID,
            'shared' => !empty($folder->shared),
            'public' => $folder->visibility === PUBLIC_F,
            'private' => $folder->visibility === PRIVATE_F,
            // 'collaborators' => static::sharedWith($folder->collaborators ?? ''),
            'shared_with' => FileManagerService::sharedWith($folder->shared ?? '', $withFilter),
            'created_on' => date('D M-d-Y', strtotime($folder->created_at)),
            'last_updated_on' => date('D M-d-Y', strtotime($folder->updated_at)),
            'files' =>
            FileService::formatFilesService(
                File::find(
                    ['folder_id' => $folder->id]
                )
            ),
        ])->remove('_id', 'created_at', 'updated_at');
    }

    /**
     * foldersService(): This is a static method that takes a User object as a parameter. 
     * It retrieves all folders associated with the user and returns a formatted array of folders.
     * 
     * @param User $user, user object
     * @return array array of folders
     */
    public static function foldersService(User $user): array
    {
        return self::foldersFormat(Folder::myFolders($user), $user->_id);
    }

    /**
     * This is a static function called "foldersFormat". It takes in three parameters: $folders (an array of folders), $accessID (an optional access ID), and $withFilter (a boolean value indicating whether or not to include a filter for shared folders). 
     * 
     * The function uses the Filters class to apply filters to the folders array. It appends several filters to the array, including filters for owner user ID, shared status, and visibility (public or private). It also includes a filter for shared folders, using the FilesService::sharedWith method.
     * 
     * The function removes several keys from the array, including visibility, shared, company ID, and user ID.
     * 
     * Finally, the function includes a key for the number of files in each folder, using the File::findOne method to count the number of files with a matching folder ID.
     * 
     * Overall, this function is designed to format a given array of folders with various filters and additional information. 
     * 
     * @param array $folders
     * @param int|null|bool|string $accessID, id of user accessing the folder
     * @return array
     */
    public static function foldersFormat($folders, $accessID = null, $withFilter = true)
    {
        return Filters::from($folders)->append([
            '_id.id' => fn ($id) => $id,
            'user_id.owner' =>  fn ($user_id) => $user_id === $accessID,
            'shared.is_shared' => fn ($shared) => !empty($shared),
            'visibility.public' => fn ($visibility) => $visibility === PUBLIC_F,
            'visibility.private' => fn ($visibility) => $visibility === PRIVATE_F,
            // 'collaborators' => fn ($collaborators) => self::employees($collaborators ?? ''),
            'shared.shared_with' => fn ($shared) =>
            FileManagerService::sharedWith($shared, $withFilter),
            'id.files' => fn ($id) => (int)
            File::findOne(
                ['folder_id' => $id],
                'count(*) as totalFiles'
            )->totalFiles ?? 0,
        ])
            ->remove('visibility', 'shared', '_id', 'company_id', 'user_id')
            ->done();
    }

    // Overall, this class provides methods for creating, formatting, and retrieving folder data in a structured manner.
}
