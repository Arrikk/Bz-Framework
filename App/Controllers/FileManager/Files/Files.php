<?php

namespace App\Controllers\FileManager\Files;

use App\Helpers\Logs;
use App\Helpers\Paginate;
use App\Models\File as ModelsFile;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Files extends FileService
{
    /**
     * This is a public function called "_upload" which takes an instance of the "Pipes" class as a parameter. 
     * Method to Upload a new file to server
     * Method controller create receives a POST Pipe object service
     * to upload file as received from client
     * @param Pipes $data, Request Object
     */
    public function _upload(Pipes $data)
    {
        // Inside the function, there is a try-catch block. 
        // The code inside the try block is responsible for uploading a file.
        try {
            // First, the function calls the "uploadPipe" method passing the "Pipes" object as an argument. 
            // This method is responsible for processing the uploaded file data.
            $piped = $this->uploadPipe($data);


            // Then, the function calls the "getFolderUploadPath" 
            // method to get the folder path where the file will be uploaded. 
            // This method takes the processed file data and the 
            // user ID as arguments and returns the folder path.  
            $folderName = $this->getFolderUploadPath($piped, $this->user->_id);

            // Next, the function calls the "uploadService" method passing the processed file data and the folder path as arguments. 
            // This method is responsible for uploading the file to the specified folder.  
            $upload = $this
                ->uploadService($piped, $folderName)
                // After that, the function calls the "_uploadDbService" method on the returned object from the "uploadService" method. 
                // This method is responsible for saving the file upload details to the database
                ->_uploadDbService();

            //  Finally, the function returns a JSON response with a success message and the formatted file data. 
            // # Return Response.... with formatted data..  
            Res::json([
                'message' => "File uploaded successfully",
                'data' => $this->formatFileService($upload, false)
            ], true);

            Logs::instance()->createDocument($this->user);
        } catch (\Throwable $th) {
            // If any error occurs during the file upload process, the catch block is executed, and an error response is returned.
            //throw $th;
            Res::status(400)->throwable($th);
        }
    }

    /**
     * Handle the '_file' endpoint.
     *
     * This method is responsible for processing a file based on the provided Pipes data.
     * It retrieves and validates the file ID, then retrieves the file details using the
     * FileService and returns the result in a JSON response.
     *
     * @param Pipes $data The Pipes object containing the file ID.
     *
     * @return void
     */
    public function _file(Pipes $data)
    {
        try {
            // Retrieve the file ID from the data.
            $fileID = $data->file;
    
            // Validate that the 'file' parameter is required.
            $this->required([
                'file' => $fileID
            ]);
    
            // Retrieve file details using the FileService.
            $file = $this->fileService($fileID, $this->user->_id);
    
            // Return a JSON response with the file details.
            Res::json(['message' => 'File', 'data' => $file]);
        } catch (\Throwable $th) {
            // Handle any exceptions and return a 400 Bad Request response with the error message.
            Res::status(400)->error($th->getMessage());
        }
    }
    
    /**
     * 
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
            Res::json(['message' => 'files', 'data' => $formatted]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

    /**
     * This is a function named  _deletePerm  that takes a parameter  $param . 
      It attempts to perform some code operations, including finding and deleting a file with the given ID ( $fileID ) using the  ModelsFile  model. 
      It also deletes the physical file from the file system using the file path stored in the  $doc  variable.  
     * Method to delete a single file from server
     * Method controller file receives a DELETE Pipe object service
     * to retrieve by their ID and user....
     * @param Pipes $data, Request Object
     */
    public function _deletePerm($param)
    {
        try {

            // Get the file ID from the server get variable...
            $fileID = $param->file;
            // Force the acceptance of the file ID to be provided.. 
            $this->required([
                'file' => $fileID
            ]);
            // use the fileService method from FileService class to make some operations on the file to be deleted wch includes verification of owner and permissions and existence of the file.
            $doc = $this->fileService($fileID, $this->user->_id);
            // find and delete a file with the given ID ( $fileID ) using the  ModelsFile  model. 
            ModelsFile::findAndDelete(['_id' => $doc->id]);
            // delete the physical file from the file system using the file path stored in the $doc variable.  
            unlink($doc->file_path);
            Res::json(['message' => 'File Permanently deleted']);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }
}
