<?php

namespace App\Controllers\Files\Files;

use App\Controllers\Files\FileManager\FileManagerService;
use App\Models\File;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Files extends FilesService
{
    public function _share(Pipes $pipes)
    {
        $piped = $this->sharePipe($pipes);
        Res::json($this->shareService($piped));
    }

    public function _unshare(Pipes $pipes)
    {
        $piped = $this->unsharePipe($pipes);
        Res::json($this->unshareService($piped));
    }

    public function access(Pipes $pipes)
    {
        $piped = $this->accessPipe($pipes); 
        $access = $this->sharedLinkService($piped->share_link, $piped->access_id, $piped->role);
        Res::json($access);
    }

    public function _shared()
    {
        try {
            //code...
            Res::send($this->sharedFolders($this->user->id));
        } catch (\Throwable $th) {
            Res::status(400)->error($th->getMessage());
        }        
    }

    public function _collaborate()
    {
        
    }

    public function _collaborated()
    {
        
    }

    public function _search($option)
    {
        $search = $option->q ? $option->q : 'N/A';
        $company  = $this->companyID;
        $user = $this->user->id ?? "";
        $account = $this->user->account_type ?? "";
        $role = $user.":$account";

        // Res::send([$user, $company, $role]);

           $search = File::find([
            '$.join' => 'folders',
            '$.on' => 'files.folder_id = folders.id',
            '$.where' => "folders.company_id = '$company'",
            '$.and1' => "files.user_id = '$user'",
            '$.and2' => File::like('files.file_path', $search),
            '$.or' => "folders.company_id = '$company'",
            '$.and3' => File::inset($role, 'files.shared'),
            '$.and4' => File::like('files.file_path', $search)
           ], 'files.*, folders.name as folder_name');

           $formatted = FileManagerService::formatFilesService($search);

           Res::send($formatted);
    }
}
