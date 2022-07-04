<?php

namespace Module;

use Bulletproof\Image as BulletproofImage;
use Core\Http\Res;

class Image
{

    protected $files;

    public function __construct($files = [])
    {
        $this->files = new BulletproofImage($files);
    }

    public static function upload($image, $key='image', $path = '')
    {
        $image = new Image($image);
        if ($image->files[$key]) :
            $image->files->setName(bin2hex(random_bytes(6)));
            $image->files->setMime(array('jpeg', 'gif', 'png'));
            $image->files->setLocation(FILE_PATH.$path);
            $image->files->upload();
            return json_decode($image->files->getjson());
        endif;
        return ['Nothing Here'];
    }

    public static function multiple($files, $key = 'image', $path = '')
    {
        $paths = [];
        for($i = 0; $i < count($files[$key]['name']); $i++) {
  
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
