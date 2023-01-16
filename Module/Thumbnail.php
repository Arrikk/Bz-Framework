<?php
namespace Module;
class Thumbnail {
	public $img_file;
	public $img_format;
	public $img_source;
	public $img_width;
	public $img_height;
	public $thumb_width;
	public $thumb_height;
	public $thumb_resize;
	public $thumb_quality;
    public function __construct($file) {
        $this->img_file      = $file;
        $this->thumb_resize  = true;
        $this->thumb_quality = 70;
		list($width, $height, $type, $attr) = getimagesize($this->img_file, $info);
		if (is_array($info)) {
	        $this->img_width    = $width;
	        $this->img_height   = $height;
	        $this->thumb_width  = $width;
	        $this->thumb_height = $height;
	
			switch ($type) {
	            case 1:
	                $this->img_format = 'gif';
	            	$this->img_source = imagecreatefromgif($this->img_file);
	                break;
	            case 2:
                	$this->img_format = 'jpeg';
	            	$this->img_source = imagecreatefromjpeg($this->img_file);
	                break;
	            case 3:
            		$this->img_format = 'png';
	            	$this->img_source = imagecreatefrompng($this->img_file);
	                break;
	            default:
	                $this->img_format = null;
	            	$this->img_source = null;
	                break;
	        } 
		}		
    } 
    public function setResize($resize) {
		$this->thumb_resize = $resize;
    } 
    public function setSize($width, $height) {
		$this->thumb_width  = $width;
		$this->thumb_height = $height;
    } 
    public function sizeWidth($width) { 
		$this->thumb_width  = $width;
		$this->thumb_height = @($width / $this->img_width) * $this->img_height;
    } 
    public function sizeHeight($height) {
		$this->thumb_width  = @($height / $this->img_height) * $this->img_width;
		$this->thumb_height = $height;
    } 
    public function save($file, $blur = false) {
		$return = false;
		
		if ($this->img_format) {
			$src   = $this->img_source;
			$src_x = 0;
			$src_y = 0;
			if ($this->thumb_resize) {
				$resized_width  = @($this->thumb_height / $this->img_height) * $this->img_width;
				$resized_height = @($this->thumb_width / $this->img_width) * $this->img_height;
				
				if ($this->thumb_width <= $resized_width) {
					$width  = $resized_width;
					$height = $this->thumb_height;
					$src_x  = intval(($resized_width - $this->thumb_width) / 2);
				} else {
					$width  = $this->thumb_width;
					$height = $resized_height;
					$src_y  = intval(($resized_height - $this->thumb_height) / 2);
				}
				$src = imagecreatetruecolor($width, $height);
				if ($this->img_format == 'png') {
					imagecolortransparent($src, imagecolorallocate($src, 0, 0, 0));
					imagealphablending($src, false);
					imagesavealpha($src, true);
				}
				if ($this->img_format == 'gif') {
					imagealphablending($src, false);
					$transindex = imagecolortransparent($this->img_source) <= imagecolorstotal($src) ? imagecolortransparent($this->img_source) : imagecolorstotal($src);
					if($transindex >= 0) {
						$transcol = imagecolorsforindex($this->img_source, $transindex);
						$transindex = imagecolorallocatealpha($src, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
						imagefill($src, 0, 0, $transindex);
					}
				}
				if (function_exists('imagecopyresampled')) {
					@imagecopyresampled($src, $this->img_source, 0, 0, 0, 0, $width, $height, $this->img_width, $this->img_height);
				} else {
					@imagecopyresized($src, $this->img_source, 0, 0, 0, 0, $width, $height, $this->img_width, $this->img_height);
				}
				if ($this->img_format == 'gif') {
					if ($transindex >= 0) {
						imagecolortransparent($src, $transindex);
						for ($y=0; $y < imagesy($src); ++$y) {
							for ($x=0; $x < imagesx($src); ++$x) {
								if (((imagecolorat($src, $x, $y)>>24) & 0x7F) >= 100) {
									imagesetpixel($src, $x, $y, $transindex);				
								}
							}
						}
					}
				}
			}
			$thumbnail = imagecreatetruecolor($this->thumb_width, $this->thumb_height);

			if ($this->img_format == 'png') {
				imagecolortransparent($thumbnail, imagecolorallocate($src, 0, 0, 0));
				imagealphablending($thumbnail, false);
				imagesavealpha($thumbnail, true);
			}
			if ($this->img_format == 'gif') {
				imagealphablending($thumbnail, false);
				$transindex = imagecolortransparent($src);
				if($transindex >= 0) {
					$transcol = imagecolorsforindex($src, $transindex);
					$transindex = imagecolorallocatealpha($thumbnail, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
					imagefill($thumbnail, 0, 0, $transindex);
				}
			}



			@imagecopy($thumbnail, $src, 0, 0, $src_x, $src_y, $this->thumb_width, $this->thumb_height);
			if ($this->img_format == 'gif') {
				if($transindex >= 0) {
					imagecolortransparent($thumbnail, $transindex);
					for($y=0; $y < imagesy($thumbnail); ++$y) {
						for($x=0; $x < imagesx($thumbnail); ++$x) {
							if(((imagecolorat($thumbnail, $x, $y)>>24) & 0x7F) >= 100) {
								imagesetpixel($thumbnail, $x, $y, $transindex);				
							}
						}
					}
				}
			}

            if( $blur == true ) {
                for ($x = 1; $x <= 50; $x++) {
                    imagefilter($thumbnail, IMG_FILTER_GAUSSIAN_BLUR);
                }
            }



			switch ($this->img_format) {
	            case 'gif':
	            	$return = imagegif($thumbnail, $file);
	                break;
	            case 'jpeg':
	       			$return = imagejpeg($thumbnail, $file, $this->thumb_quality);
	                break;
	            case 'png':
	    			$return = imagepng($thumbnail, $file);
					break;
	        }



			imagedestroy($thumbnail);	 
			imagedestroy($src);
		}
		return $return;
    } 
    public static function check() {
		$gd_functions = array(
			'getimagesize',
			'imagecreatefromgif',
			'imagecreatefromjpeg',
			'imagecreatefrompng',
			'imagecreatetruecolor',
			'imagecopyresized',
			'imagecopy',
			'imagegif',
			'imagejpeg',
			'imagepng'
			);
		
		foreach ($gd_functions as $name) {
			if (!function_exists($name)) return false;
		}
		
		return true;
    } 
}