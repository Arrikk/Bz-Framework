<?php

namespace App\Controllers\Files;

use App\Models\File;
use App\Models\Folder;
use Core\Http\Res;
use Core\Pipes\Pipes;

/**
 * This is a PHP class named "Folders" that belongs to the namespace "App\Controllers\Files". 
 * It imports the "File" and "Folder" models from the respective namespaces. 
 * It also imports the "Res" class from the "Core\Http" namespace and the "Pipes" class from the "Core\Pipes" namespace.
 * 
 * The class extends the "FolderService" class and contains several methods. 
 * Here is a brief description of each method: 
 */
class Folders extends FolderService
{

    /**
     *  This method is responsible for creating a new folder. It uses the "createPipe" method from the parent class to create a pipe object and then calls the "createService" method to create the folder. 
     * The resulting folder object is returned as a JSON response.
     * 
     * @param Pipes $data... Pipe And Validations
     * @return void|object|array
     */
    public function _create(Pipes $data)
    {
        try {
            $folderPipe = $this->createPipe($data, $this->user->_id);
            $folder = $this->createService((array) $folderPipe);
            Res::json($folder);
        } catch (\Throwable $th) {
            Res::status(400)->throwable($th);
        }
    }

    /**
     * This method is used to retrieve information about a specific folder. 
     * It calls the "folderPipe" method from the parent class to create a pipe object and then calls the "folderService" method to get the folder information. 
     * The folder object is returned as a JSON response
     * @param Pipes $data... Pipe And Validations
     * @return void|object|array
     */
    public function _folder(Pipes $folder)
    {
        try {
            // - The method "folderPipe" is called with the variable "$folder" as its parameter. The result is assigned to the variable "$folderPipe". 
            $folderPipe = $this->folderPipe($folder);

            //The method "folderService" is called with the properties "user_id" and "id" of "$folderPipe" as its parameters. The result is assigned to the variable "$folder". 
            $folder = $this->folderService($folderPipe->user_id, $folderPipe->id);

            // The "Res::json" method is called with "$folder" as its parameter. This method is responsible for returning the JSON representation of the "$folder" object. 
            Res::json($folder);
        } catch (\Throwable $th) {
            // If any exception or error occurs within the try block, the catch block is executed. It catches any throwable object (including exceptions and errors) and assigns it to the variable "$th". 

            Res::status(400)
            //Then, the "Res::status(400)->throwable($th)" method is called. This method sets the HTTP response status code to 400 (Bad Request) and throws the throwable object, providing a response with the error details. 
            ->throwable($th);
        }
    }

    /**
     * This method retrieves all the folders associated with the user. 
     * It calls the "foldersService" method from the parent class to get the folders and returns them as a JSON response
     * @param Pipes $data... Pipe And Validations
     * @return void|object|array
     */
    public function _folders()
    {
        // The try block is used to enclose the code that may throw an exception. 
        try {
            // Inside the try block, there is a line of code that calls the "foldersService" method from an object named "$this" and passes in the "user" property as an argument. The result of this method call is assigned to the variable "$folders". 
            $folders = $this->foldersService($this->user);

            // After that, there is a line of code that uses the "Res::json" method to convert the "$folders" variable to a JSON response and send it.
            Res::json($folders);
        } catch (\Throwable $th) {
            // Inside the catch block, there is a line of code that uses the "Res::status" method to set the HTTP status code to 400 (Bad Request) and then uses the "throwable" method to send an error message generated from the exception's Throwable class method
            Res::status(400)->throwable($th);
        }
    }

    public function _updateFolder($pipe) {

        try {
            $params = $this->folderPipe($pipe);
            $folderExists = $this->folderService($params->user_id, $params->id, false);

            if(!$folderExists) Res::status(401)::error("Folder not found or you do not have permission to view folder");

            // Res::send($folderExists);

            $update = (array) $this->updateFolderPipe($pipe, $folderExists);

            $updated = $folderExists->modify($update);
            Res::send($updated);

        } catch (\Throwable $th) {
           Res::status(400)->throwable($th);
        }
    }

    /**
     * This method is used to delete a folder and its associated files. 
     * It first checks if the folder ID is valid. If it is, it deletes all the files associated with the folder using the "findAndDelete" method from the "File" model. 
     * Then, it deletes the folder itself using the "findAndDelete" method from the "Folder" model. 
     * Finally, it sends a success message as a response
     * @param Pipes $data... Pipe And Validations
     * @return void|object|array
     */
    public function _delete()
    {
        try {
            # The code first retrieves the folder ID from the route parameters and checks if it is a valid ID. 
            
            $folderID = $this->route_params['id'];

            # If the ID is not valid (less than or equal to 0), it returns a response with a status code of 400 and an error message stating "Invalid Folder ID". 
        if ($folderID <= 0) Res::status(400)->error('Invalid Folder ID');
            
        // If the folder ID is valid, it proceeds to delete the associated files by calling the  findAndDelete  method on the  File  model with the condition  ['folder_id' => $folderID] .
        File::findAndDelete(['folder_id' => $folderID]);

        // Then, it deletes the folder itself by calling the  findAndDelete  method on the  Folder  model with the conditions  ['id' => $folderID, 'and.user_id' => $this->user->id] .  
        Folder::findAndDelete([
            'id' => $folderID,
            'and.user_id' => $this->user->id,
        ]);

        // After successful deletion, it sends a response with a message stating "Folder deleted successfully With associated Files...". 
        Res::send(['message' => " Folder deleted successfully With associated Files..."]);
        } catch (\Throwable $th) {
           // If an error occurs during the deletion process, the code catches the exception, sets the response status code to 400, and returns an error message with the exception's message
            Res::status(400)->throwable($th);
        }
    }
}
