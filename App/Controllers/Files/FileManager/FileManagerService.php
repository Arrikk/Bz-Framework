<?php

namespace App\Controllers\Files\FileManager;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\Files\Files\FilesService;
use App\Controllers\Files\FolderService;
use App\Helpers\Filters;
use App\Models\File as FileModel;
use App\Models\Folder;
use Core\Http\Res;
use Core\Pipes\Pipes;
use Module\File\File;

class FileManagerService extends Authenticated
{
    public $file;

    /**
     * Upload File Pipeline
     * Create a secure pipeline of File Upload
     * @param Pipes $data, Request Object
     */
    public function uploadPipe(Pipes $data)
    {
        return $data->pipe([
            'name' => $data
                ->name()
                ->serialize()
                ->default('')
                ->name,
            'file' => $data->file()->isrequired()->file,
            'folder_id' => $data->folder_id()
                ->isint()->folder_id,
            'folder_name' => Folder::findOne(['id' => $data->folder_id], 'name')->name ?? ''
        ]);
    }

    /**
     * Retrieve File Pipeline...
     * Prepare File for retrieval
     * @param Pipes $data, Request Object
     * @return mixed
     */
    public function filesPipe(Pipes $data)
    {
        return $data->pipe([
            'page' => $data->page()->default(1)->toint()->page,
        ]);
    }

    /**
     * Upload File Service... 
     * Initiate file upload Module to upload File 
     * set file configuration to meet requirements
     * e.g... filename, filepath, filetype, fileextension... etc
     * @param Pipes $data, Request Object
     * @return FileManagerSercice 
     */
    public function uploadService($piped, $folder = "Document")
    {
        $file = File::upload([
            'name' => $piped->name,
            'file' => $piped->file,
            'path' => "Public/".$folder.'/' . $this->user->id
        ]);
        $file['folder_id'] = $piped->folder_id;
        $this->file = (object) $file;
        return $this;
    }

    /**
     * Method Saves uploaded file data to DB..
     * set all required fields to save record of uploaded file
     * @return FileModel
     */
    public function _uploadDbService()
    {
        return FileModel::dump([
            'user_id' => $this->user->id,
            'storage_size' => $this->file->size['size_in_bytes'],
            'file_data' => json_encode($this->file),
            'file_path' => $this->file->path,
            'folder_id' => $this->file->folder_id
        ]);
    }


    /**
     * Receive and process file data... 
     * Include and remove data...
     * @param FileModel $file
     * @return FileMode|null
     */
    public static function formatFileService($file, $useFilter = true)
    {
        if ($file instanceof FileModel)
            return $file->append([
                'folder_id' => (int) $file->folder_id,
                'storage_size' => (float) $file->storage_size,
                'deleted' => $file->deleted === YES ? true : false,
                'enabled' => $file->status === ENABLED ? true : false,
                'disabled' => $file->status === DISABLED ? true : false,
                'meta' => json_decode($file->file_data),
                '_doc' => self::metaData($file->file_path),
                'partners' => FilesService::sharedWith($file->shared, $useFilter)
            ])->remove('file_data');
    }

    /**
     * Retrieve a file by their id and user... owner. 
     * @param int $fileID id of file to retrieve
     * @param int $userID id of user's file to retrieve
     * @return FileModel
     */
    public function fileService($fileID, $userID)
    {
        $file = FileModel::findOne(['id' => $fileID, 'and.user_id' => $userID]);
        if (!$file) Res::status(404)::json(['message' => 'File not found']);
        return $this->formatFileService($file);
    }

    /**
     * Receive and process file data... 
     * Include and remove data...
     * @param array $file
     * @return array
     */
    public static function formatFilesService($files)
    {
        $filter = Filters::from($files)->append([
            // 'shared' =>
            'storage_size' => fn ($file) => (float) $file,
            'deleted' => fn ($file) => $file === YES ? true : false,
            'enabled' => fn ($file) => $file === ENABLED ? true : false,
            'disabled' => fn ($file) => $file === DISABLED ? true : false,
            'file_data.meta' => fn ($file) => json_decode($file),
            'shared' => fn($shared) => FilesService::sharedWith($shared),
            'file_path._doc' => [(new static([])), 'metaData']
        ])->remove('file_data')->done();

        return $filter;
    }

    public static function metaData($path)
    {
        $metadata = stat($path);
        return [
            'permissions' => decoct(fileperms($path) & 0777),
            'file_size' => $metadata[7] ?? '0' . " bytes",
            'creation_time' => isset($metadata[10]) ? date('Y-m-d H:i:s', $metadata[10]) : null,
            'modified_time' => isset($metadata[9]) ? date('Y-m-d H:i:s', $metadata[9]) : null
        ];
    }
}
