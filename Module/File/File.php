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

    public static function uploadMultiple(array $options)
    {
        $fileOBJ = [];

        $files = $options['file'];
        $arr_file  = [];

        for ($i = 0; $i < count($files['name']); $i++) {

            $arr_file[] = array(
                "name" => $files['name'][$i],
                "type" => $files['type'][$i],
                "tmp_name" => $files['tmp_name'][$i],
                "error" => $files['error'][$i],
                "size" => $files['size'][$i],
            );
        }

        foreach ($arr_file as $file) :
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

            $fileOBJ[] = $file;
        endforeach;

        for ($i=0; $i < count($fileOBJ); $i++) {
            $fileOBJ[$i] = $fileOBJ[$i]->save();
        }

        return $fileOBJ;
    }
}
