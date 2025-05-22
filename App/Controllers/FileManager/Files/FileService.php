<?php
namespace App\Controllers\FileManager\Files;

use App\Controllers\Authenticated\Authenticated;
use App\Controllers\FileManager\Folders\FolderService;
use App\Controllers\FileManager\Manager\FileManagerService;
use App\Helpers\Filters;
use App\Models\File;
use Core\Http\Res;
use Core\Pipes\Pipes;
use Module\File\File as FileFile;

class FileService extends Authenticated
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
            'folder_id' => $data->folder_id()->folder_id
        ]);
    }

    /**
     * This function is used to retrieve the folder upload path for a given object and user ID. 
     * The function takes in two parameters: an object of type "object" and a string representing the user ID.  
     * @param object $piped, Piped or validated object
     * @param string $userID, the id of user... uploading file..
     * @return string
     */
   public function getFolderUploadPath(object $piped, string $userID) :string 
    {
        //  initializes a variable called $folderName as an empty string.  
        $folderName = "";
        // It then checks if the object has a non-null folder ID. 
        if($piped->folder_id && $piped->folder_id !== null):
            // If it does, it calls the FolderService::folderService() method, passing in the user ID and folder ID as arguments. 
            $folder = FolderService::folderService($userID, $piped->folder_id, false);
            // If a folder is found, the name of the folder is assigned to the $folderName variable
            if($folder) $folderName = $folder->name;
        endif;
       // checks if the $folderName is still an empty string or if it evaluates to false. 
       // If it is, the $folderName is set to the default value of 'Files'.  

        $folderName = $folderName == "" || !$folderName ? 'Files' : $folderName;
        return $folderName;  # Finally, the function returns the $folderName.     
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
     * @return FileService 
     */
    public function uploadService($piped, $folder = "Document"):FileService
    {
        $file = FileFile::uploadSecurely([
            'name' => $piped->name,
            'file' => $piped->file,
            'path' => "Public/".$folder.'/' . $this->user->_id
        ]);
        $file['folder_id'] = $piped->folder_id;
        $this->file = (object) $file;
        return $this;
    }

    /**
     * Method Saves uploaded file data to DB..
     * set all required fields to save record of uploaded file
     * @return File
     */  
    public function _uploadDbService()
    {
        return File::dump([
            '_id' => GenerateKey(30, 50),
            'user_id' => $this->user->_id,
            'storage_size' => $this->file->size['size_in_bytes'],
            'file_data' => json_encode($this->file),
            'file_path' => $this->file->path,
            'folder_id' => $this->file->folder_id
        ]);
    }


    /**
     * Receive and process file data... 
     * Include and remove data...
     * @param File $file
     * @return File|null
     */
    public static function formatFileService($file, $useFilter = true): File|null
    {
        if ($file instanceof File)
            return $file->append([
                'id' => $file->_id,
                'folder_id' => (int) $file->folder_id,
                'storage_size' => (float) $file->storage_size,
                'deleted' => $file->deleted === YES ? true : false,
                'enabled' => $file->status === ENABLED ? true : false,
                'disabled' => $file->status === DISABLED ? true : false,
                'meta' => json_decode($file->file_data),
                '_doc' => self::metaData($file->file_path),
                'partners' => FileManagerService::sharedWith($file->shared, $useFilter)
            ])->remove('file_data', '_id');
    }

    /**
     * Retrieve a file by their id and user... owner. 
     * @param int $fileID id of file to retrieve
     * @param int $userID id of user's file to retrieve
     * @return File
     */
    public function fileService($fileID, $userID)
    {
        $file = File::findOne(['_id' => $fileID, 'and.user_id' => $userID]); 
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
            '_id.id' => fn($id) => $id,
            'storage_size' => fn ($file) => (float) $file,
            'deleted' => fn ($file) => $file === YES ? true : false,
            'enabled' => fn ($file) => $file === ENABLED ? true : false,
            'disabled' => fn ($file) => $file === DISABLED ? true : false,
            'file_data.meta' => fn ($file) => json_decode($file),
            'shared' => fn($shared) => FileManagerService::sharedWith($shared),
            'file_path._doc' => [(new static([])), 'metaData']
        ])->remove('file_data', '_id')->done();

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