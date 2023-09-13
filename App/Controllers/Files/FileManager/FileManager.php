<?php

namespace App\Controllers\Files\FileManager;

use App\Helpers\Paginate;
use App\Models\File as ModelsFile;
use Core\Http\Res;
use Core\Pipes\Pipes;

class FileManager extends FileManagerService
{
    /**
     * Method to Upload a new file to server
     * Method controller create receives a POST Pipe object service
     * to upload file as received from client
     * @param Pipes $data, Request Object
     */
    public function _upload(Pipes $data)
    {
        try {
            //code...
            $piped = $this->uploadPipe($data);
            $folderName = $piped->folder_name == "" || !$piped->folder_name ? 'Documents' : $piped->folder_name;
            $upload = $this
                ->uploadService($piped, $folderName)
                ->_uploadDbService();
    
            # Return Response.... with formatted data..  
            Res::json([
                'message' => "File uploaded successfully",
                'data' => $this->formatFileService($upload, false)
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

    /**
     * Method to retrieve a single file from server
     * Method controller file receives a GET Pipe object service
     * to retrieve by their ID and user....
     * @param Pipes $data, Request Object
     */
    public function _file(Pipes $data)
    {
        try {
            //code...
            $fileID = $this->route_params['id'];
            $file = $this->fileService($fileID, $this->user->id);
            Res::json([ 'message' => 'File', 'data' => $file]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

    /**
     * Method to retrieve all file from server
     * Method controller file receives a GET Pipe object service
     * to retrieve by user....
     * @param Pipes $data, Request Object
     */
    public function _files($pipe)
    {
        try {
            //code...
            $paginated = Paginate::page($this->filesPipe($pipe));
            $files = $this->user->paginate($paginated->page)->files();
            $formatted = $this->formatFilesService($files);
            Res::json([ 'message' => 'files', 'data' => $formatted]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

     /**
     * Method to delete a single file from server
     * Method controller file receives a DELETE Pipe object service
     * to retrieve by their ID and user....
     * @param Pipes $data, Request Object
     */
    public function _deletePerm()
    {
        try {
            //code...
            $fileID = $this->route_params['id'];
            $doc = $this->fileService($fileID, $this->user->id);
            ModelsFile::findAndDelete(['id' => $doc->id]);
            unlink($doc->file_path);
            Res::json(['message' => 'File Permanently deleted']);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }
}
