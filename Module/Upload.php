<?php

namespace Module;

use Core\Model;
use Core\Http\Res;

/**
 * Upload Model
 * file uploader
 * **************************************
 * ************ PHP V7.4.8 **************
 * **************************************
 */

final class Upload extends Model
{

    protected $dir = false;
    protected $image;
    protected $file_size = false;
    protected $file;
    protected $ext = false;
    public $err = [];

    /**
     * Uploader Constructor
     * @return void
     */
    public function __construct(array $var)
    {
        foreach ($var as $key => $value) :
            $this->$key = $value;
        endforeach;
    }

    /**
     * Make a new Post upload folder
     * @param str $name folder name
     * @return self string or bool otherwise
     */
    private function folder()
    {
        $folder_path = FILE_PATH.'/'.date('Y');
        if (is_readable($folder_path) || is_dir($folder_path)) :
            $this->dir = $folder_path;
        else :
            if (mkdir($folder_path)) $this->dir = $folder_path;
        endif;

        return $this;
    }

    /**
     * Valid image extension
     * allowed extensions [jpg, png, jpeg]
     * @param string $ext extension to check
     * @return self
     */
    private function ext()
    {
        $valid_ext = ['png', 'jpeg', 'jpg'];
        $extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        if (in_array(strtolower($extension), $valid_ext))
            $this->ext = true;

        return $this;
    }

    /**
     * Validate File Size
     * @param int $size file size
     * @return self
     */
    private function fileSize()
    {
        $allowed_size = 5500000 / 1048576;
        $allowed_size = number_format($allowed_size, 2) . 'mb';

        $image_size = $this->size / 1048576;
        $image_size = number_format($image_size, 2) . 'mb';

        if ($this->size > 5500000)
            $this->err[] = 'Image size of' . $image_size . ' is greater than ' . $allowed_size;

        $this->file_size = true;
        return $this;
    }

    /**
     * upload
     */
    private function save()
    {
        $image_name = basename($this->name);
        $name_exp = explode('.', $image_name);
        // sleep(1);
        $new_name = $this->dir.'/'.bin2hex(random_bytes(13)).'.'.end($name_exp);
        if(move_uploaded_file($this->tmp_name, $new_name)):
            $this->dir = $new_name;
        else:
            $this->err[] = 'Failed to upload Image';
        endif;

        return $this;
    }

    public static function upload($upload, $name = 'image')
    {
        $class = new Upload($upload[$name]);
        $save = $class->fileSize()->ext()->folder();

        if(!$class->ext){
            $class->err[] = 'Invalid Image type';
        }
        if(empty($class->err)):
            if ($save->save()):
                return $class->dir;
            else:
                return Res::status(400)->json($class->err);
            endif;
        endif;

        return Res::status(400)->json($class->err);
    }



    /**
     * Upload File
     */
}
