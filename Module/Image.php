<?php

namespace Module;

use App\Models\User;
use Bulletproof\Image as BulletproofImage;
use Core\Http\Res;

class Image
{

    protected $files;
    public $dir2;
    public $dir;

    public function __construct($files = [])
    {
        $this->files = $files;
        $this->makeDir();       
        return $this;
    }

    public function makeDir()
    {
        $dir = FILE_PATH . '/photos/' . date('Y');
        if (!file_exists($dir)) :
            mkdir($dir, 0777, true);
            $this->dir = $dir;
        endif;

        $dir2 = FILE_PATH . '/photos/' . date('Y') . '/' . date('m');
        if (!file_exists($dir2)) :
            mkdir($dir2, 0777, true);
        endif;
        $this->dir2 = $dir2;
    }

    public function fileExt($ext)
    {
        $allowed = ['png', 'jpg', 'jpeg', 'gif'];
        if (in_array(strtolower($ext), $allowed)) return $ext;
        Res::status(400)::json([
            'message' => "Invalid File",
            'file-ext' => $ext
        ]);
        return $ext;
    }

    public function upload($path = '')
    {
        if (isset($this->files) && !empty($this->files)) :
            $file = $this->files;
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $key = GenerateKey();

            $this->fileExt($ext);

            $fileName = $this->dir2 . '/' . $key . '.' . $ext;

            if (move_uploaded_file($file['tmp_name'], $fileName)) :
                // correctImageOrientation($fileName);

                $thumbFile = $this->dir2 . '/' . $key . '_avater' . '.' . $ext;
                $orgFile = $this->dir2 . '/' . $key . '_full' . '.' . $ext;

                $original = new Thumbnail($fileName);
                $original->setResize(false);
                $original->save($orgFile);
                @unlink($fileName);

                $media = [
                    'file' => $orgFile,
                ];
                return $media;
            else :
                Res::send("Noth happened");
            endif;
        endif;
        Res::status(400)::error([
            'message' => "Please provide a name",
            'data' => [
                'file' => $this->files
            ]
        ]);
        return ['Nothing Here'];
    }

    public function withAws(User $user)
    {
        // $this->awsSettings = $user;
        // return $this;
    }

    public function b64($data = '', $path = '')
    {
        $dataExp = explode(';base64,', $data);
        if ($dataExp && count($dataExp) > 1) :
            $fileExt = (explode('/', $dataExp[0]))[1];

            $dataEnd = end($dataExp);
            if ($dataEnd) :
                $this->fileExt($fileExt);
                $fileData = base64_decode(str_replace(' ', '+', $dataEnd));
                $key = GenerateKey();
                $fileName = $this->dir2 . '/' . $key . '.jpg';
                file_put_contents($fileName, $fileData);
                $media = [
                    'file' => $fileName,
                ];

                return $media;
            endif;
        endif;

        Res::status(400)::json([
            'message' => "Invalid B64 type",
            'data' => [
                'str' => $data,
                'fdata' => $dataExp
            ]
        ]);
    }

    public static function multiple($files, $key = 'image', $path = '')
    {
        $paths = [];
        for ($i = 0; $i < count($files[$key]['name']); $i++) {

            $arr_file[$key] = array(
                "name" => $_FILES[$key]['name'][$i],
                "type" => $_FILES[$key]['type'][$i],
                "tmp_name" => $_FILES[$key]['tmp_name'][$i],
                "error" => $_FILES[$key]['error'][$i],
                "size" => $_FILES[$key]['size'][$i],
            );

            $paths[] = self::upload($arr_file, $key, $path = '');
        }
        return $paths;
    }
}
