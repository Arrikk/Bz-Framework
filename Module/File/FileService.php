<?php

namespace Module\File;

use Core\Env;
use Core\Http\Res;

class FileService
{
    protected $file;
    protected $fileSize;
    protected $fileExtension;
    protected $maxSize = 1024;
    protected $fileExtensions = [
        "pdf",
        "docx",
        "xlsx",
        "png",
        "jpeg",
        "jpg",
        "gif",
        "svg",
        "doc",
        "txt",
        "html",
        "htm",
        "ods",
        "rtf",
        "odp",
        "ppt",
        "pptx",
        "odt"
    ];
    public $fileTypes = [
        "application/rtf",
        "text/plain",
        "application/vnd.oasis.opendocument.text",
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "application/vnd.oasis.opendocument.presentation",
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "image/png",
        "image/jpeg",
        'audio/mpeg',
        'audio/wav',
        'audio/aac',
        'audio/ogg',
        'audio/flac',
        'audio/x-m4a'
    ];
    protected $fileError;
    protected $filePath = FILE_PATH;
    protected $requiredParams = ['file'];
    protected $fileName;
    protected $fileMime;

    public function __construct($params)
    {
        foreach ($this->requiredParams as $key) :
            if (!array_key_exists($key, (array) $params) || empty((array) $params[$key]))
                $this->fileError["params"] = [
                    'name' => '@param string, optional (e.g Bruiz File)',
                    'extensions' => "@param array, optional (e.g['php', 'png', 'jpg'])",
                    'types' => "@param array, optional, (e.g ['application/png'])",
                    'file' => '@param FILEOBJECT, required (e.g $_FILE[image])',
                    'path' => "storage path .... optional (e.g /public/images)"
                ];
        endforeach;

        $this->file = $params['file'];

        if (isset($params['extensions']) && !empty($params['extensions'])) $this->fileExtensions = $params['extensions'];

        if (isset($params['types']) && !empty($params['types'])) $this->fileTypes = $params['types'];

        if (isset($params['name']) && !empty($params['name'])) $this->fileName = $params['name'];

        if (isset($params['path']) && !empty($params['path'])) $this->filePath = $params['path'];
    }

    protected function validFileData(): FileService
    {
        if (!isset($this->file['name']) || !is_object((object) $this->file))  $this->fileError['file'] = "File Not Found... File Error";
        if (isset($this->file['error']) && $this->file['error'] > 0):
            switch($this->file['error']):
                case UPLOAD_ERR_INI_SIZE:
                    $this->fileError['error'] = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->fileError['error'] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->fileError['error'] = "The uploaded file was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->fileError['error'] = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->fileError['error'] = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->fileError['error'] = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->fileError['error'] = "A PHP extension stopped the file upload.";
                    break;
                default:
                    $this->fileError['error'] = "An unknown error occurred.";
                    break;
            endswitch;
        endif;
        // if (empty($this->file['name'] || $this->file['error'] > 0)) $this->fileError['file'] = "File Not Found... File Error";
        return $this;
    }

    protected function validateFileExtension(): FileService
    {
        $fileExtension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $this->fileExtensions))
            $this->fileError['extension'] = "Invalid File Extension .$fileExtension";

        $this->fileExtension = $fileExtension;
        return $this;
    }

    protected function validateFileType(): FileService
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $this->file['tmp_name']);
        if (!in_array($fileType, $this->fileTypes)) $this->fileError['file_type'] = "Invalid File Type $fileType";

        $this->fileMime = $fileType;
        return $this;
    }

    public function makeDir($dir)
    {
        // $dir = FILE_PATH . '/photos/' . date('Y');
        if (!file_exists($dir)) :
            mkdir($dir, 0777, true);
        // $this->dir = $dir;
        endif;
    }

    public function fileSize($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $i = 0;
        $original = $size;
        while ($size >= 1024 && $i < 4) {
            $size /= 1024;
            $i++;
        }
        return [
            'size_in_bytes' => $original,
            'size' => $size,
            'size_round' => round($size, 2),
            'units' => $units[$i],
            'size_full' => round($size, 2) . ' ' . $units[$i]
        ];
    }

    protected function fileError()
    {
        if (!empty($this->fileError))
            Res::json($this->fileError);
        return $this;
    }

    protected function save()
    {
        $fileName = !empty($this->fileName) ? $this->fileName : $this->file['name'];

        $fileName = explode('.', $fileName);
        $fileName = $fileName[0] . '.' . $this->fileExtension;

        $filePath = trim(rtrim($this->filePath, '/'));
        $this->makeDir($filePath);

        $fileFullName = str_replace(' ', '_', $filePath . '/' . $fileName);

        move_uploaded_file($this->file['tmp_name'], $fileFullName);

        return [
            'type' => $this->fileExtension,
            'mimeType' => $this->fileMime,
            'name' => $fileName,
            'path' => $fileFullName,
            'abs_path' => Env::BASE_URI().$fileFullName,
            'size' => $this->fileSize($this->file['size'])
        ];
    }
}
