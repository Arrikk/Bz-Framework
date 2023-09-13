<?php

namespace App\Controllers\Files;

use App\Models\File;
use App\Models\Folder;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Folders extends FolderService
{
    public function _create(Pipes $data)
    {
        try {
            //code...
            $folderPipe = $this->createPipe($data, $this->user->id);
            $folder = $this->createService((array) $folderPipe);
            Res::json($folder);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->throwable($th);
        }
    }

    public function _folder(Pipes $folder)
    {
        try {
            //code...
            $folderPipe = $this->folderPipe($folder);
            $folder = $this->folderService($folderPipe->user_id, $folderPipe->id);
            Res::json($folder);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

    public function _folders()
    {
        try {
            //code...
            $folders = $this->foldersService($this->user);
            Res::json($folders);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }

    public function _delete()
    {
        try {
            $folderID = $this->route_params['id'];
        if ($folderID <= 0) Res::status(400)->error('Invalid Folder ID');
            
        File::findAndDelete(['folder_id' => $folderID]);
        Folder::findAndDelete([
            'id' => $folderID,
            'and.user_id' => $this->user->id,
        ]);

        Res::send(['message' => " Folder deleted successfully With associated Files..."]);
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }
}
