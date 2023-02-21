<?php
namespace Core;

/**
 * *************** Error *******************
 * ==============================================
 * ============== Code Hart (Bruiz) =============
 * ==============================================
 */

class Error
{
    /**
     * Error Handler: Convert all error to an exception 
     * By throwing an exception Error
     * 
     * @param int $level Level of the Error
     * @param string $message Error Mssage
     * @param string $file File name the error was raised
     * @param string $line Line number in the file
     * 
     * @return void
     */

    public static function errorHandler($level, $message, $file, $line)
    {
        if(error_reporting() !== 0){
            throw new \ExceptionError($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception Handler
     * 
     * @param exception $exception the exception
     * 
     * @return void
     */
    public static function exceptionHandler($exception){
        $code = $exception->getCode();
        if($code !== 404){
            $code = 500;
        }
        http_response_code($code);
        if(\App\Config::SHOW_ERROR){
            echo '<h1>Fatal Error</h1>';
            echo "<p>Uncaught Exception: '".get_class($exception)."'</p>";
            echo "<p>Message: '".$exception->getMessage()."'</p>";
            echo "<p>Stack Trace: <pre>".$exception->getTraceAsString()."</pre></p>";
            echo "<p>Throw in '".$exception->getFile()."' on line ".$exception->getLine()."</p>";
        }else{
            $log = dirname(__DIR__).'/logs/'.date('Y-m-d').'.txt';
            ini_set('error_log', $log);

            $message = '<h1>Fatal Error</h1>';
            $message .= "<p>Uncaught Exception: '".get_class($exception)."'</p>";
            $message .= "<p>Message: '".$exception->getMessage()."'</p>";
            $message .= "<p>Stack Trace: <pre>".$exception->getTraceAsString()."</pre></p>";
            $message .= "<p>Throw in '".$exception->getFile()."' on line ".$exception->getLine()."</p>";
            error_log($message);

            View::render("$code.html");
        }
        
    }
}