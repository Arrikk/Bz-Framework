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
}
