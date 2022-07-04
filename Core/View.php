<?php
namespace Core;
/**
 * View Base Controller
 * 
 * PHP version 7.4.8
 */
class View 
{
    /**
     * Render a view file
     * 
     * @param string $view the view file
     * 
     * @return void 
     */
    public static function draw($view = null, $args = [], $autoload = false){
        if($autoload){
            self::autoload();
        }
        extract($args, EXTR_SKIP);
        $__page = '';
        $views = "{ $view }";
        if(preg_match('/\{(?P<name>[^\}]+)\}/i', "$views", $matches)){
            $view = 'index.php';
            $__page = str_replace(' ', '', $matches['name']);
        }
        $file = 'App/Views/'.$view;

        if(is_readable($file)){
            require $file;
        }else{
            echo "$file Not Found";
        }
    }
    public static function component($view = null, $args = []){

        extract($args, EXTR_SKIP);
        $__page = '';
        $views = "{ $view }";
        if(preg_match('/\{(?P<name>[^\}]+)\}/i', "$views", $matches)){
            $__page = str_replace(' ', '', $matches['name']);
        }
        $file = "App/Views/components/$__page.php";

        if(is_readable($file)){
            require $file;
        }else{
            echo "$file Not Found";
        }
    }



    /**
     * Autoload component files 
     * @param string $path the component folder
     * Loads on two levels 
     * @return void
     */
    public static function autoload($path = 'components'){
        $path = "App/Views/$path/";

        if(!file_exists($path)) mkdir($path);
        $dir = scandir($path);
        // \extract($GLOBALS['settings']);     
        foreach($dir as $dir){
            if($dir == '..' || $dir == '...' || $dir == '.'){
                continue;
            }else{
                if(is_dir($path.$dir) === false){
                    require $path.$dir;
                }elseif (is_dir($path.$dir)) {
                    $inDir = \scandir($path.$dir);
                    foreach ($inDir as $in_dir) {
                        if($in_dir == '..' || $in_dir == '...' || $in_dir == '.'){
                            continue;
                        }else{
                            if(is_dir($path.$dir.'/'.$in_dir) === false){
                                require $path.$dir.'/'.$in_dir;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Render a Twig template File
     */
    public static function render($view, $args = [])
    {
        static $twig = null;
        if($twig === null){
            $loader = new \Twig_Loader_Filesystem('App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('user', \App\Auth::getUser());
            $twig->addGlobal('message', \App\Flash::getMessage());
            $twig->addGlobal('URL', \App\Config::BASE_URL);
            
        }
        echo $twig->render($view, $args);
    }

    /**
     * Render twig view for emails
     */
    public static function template($view, $args = [])
    {
        static $twig = null;
        if($twig === null){
            $loader = new \Twig_Loader_Filesystem('App/Views');
            $twig = new \Twig_Environment($loader);
        }
        return $twig->render($view, $args);
    }
}
