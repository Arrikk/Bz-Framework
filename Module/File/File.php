<?php

namespace Module\File;

use Core\Http\Res;

class File extends FileService
{
    /**
     * 
     */
    public static function upload($options)
    {
        $fileService = new FileService($options);

        $file = $fileService
            ->validFileData()
            ->fileError()
            ->validateFileExtension()
            ->fileError()
            ->validateFileType()
            ->fileError()
            ->save();

        return $file;
    }
    public static function uploadSecurely($options)
    {
        $fileService = new FileService($options);

        $file = $fileService
            ->validFileData()
            ->fileError()
            ->validateFileExtension()
            ->fileError()
            ->validateFileType()
            ->fileError()
            ->encryptFile();

        return $file;
    }

    public static function retriveSecurely($filePath): string
    {
        return self::decryptFile($filePath);
    }





    public static function uploadMultiple(array $options)
    {
        $fileOBJ = [];

        $files = $options['file'];
        $arr_file  = [];


        foreach($files as $key => $file):
            foreach ($file as $fileKey => $value) {
                $arr_file[$fileKey][$key] = $value;
            }
        endforeach;
        // Res::send($arr_file);

        // for ($i = 0; $i < count($files['name']); $i++) {

        //     $arr_file[] = array(
        //         "name" => $files['name'][$i],
        //         "type" => $files['type'][$i],
        //         "tmp_name" => $files['tmp_name'][$i],
        //         "error" => $files['error'][$i],
        //         "size" => $files['size'][$i],
        //     );
        // }

        foreach ($arr_file as $key => $file) :
            $opt = $options;
            $opt['file'] = $file;
            $fileService = new FileService($opt);

            $file = $fileService
                ->validFileData()
                ->fileError()
                ->validateFileExtension()
                ->fileError()
                ->validateFileType()
                ->fileError();

            $fileOBJ[$key] = $file;
        endforeach;

        foreach($fileOBJ as $key => $file):
            $fileOBJ[$key] = $fileOBJ[$key]->save();
        endforeach;

        return $fileOBJ;
    }
}
