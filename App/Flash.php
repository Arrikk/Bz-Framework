<?php
namespace App;

/**
 * Flash Messages
 */

class Flash
{
    /**
     * Add success alert message
     * 
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * Add info alert message
     * 
     * @var string
     */
    const INFO = 'info';

    /**
     * Add warning alert message
     * 
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Add danger alert message
     * 
     * @var string
     */
    const DANGER = 'danger';
    /**
     * Add default alert message
     * 
     * @var string
     */
    const DEFAULT = 'default';



    /**
     * Add a message
     *  
     * @param string $message The message content
     * 
     * @return void
     */
    public static function addMessage($message, $type = 'default')
    {
        // Create array in message if it dosent already exists
        if(! isset($_SESSION['flash_notification'])){
            $_SESSION['flash_notification'] = [];
        }

        // Append to the flash message array 
        $_SESSION['flash_notification'][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Get all messages
     * 
     * @return mixed an array with all messages or null if empty
     */
    public static function getMessage()
    {
        if(\App\Config::FLASH):

            if(isset($_SESSION['flash_notification'])){
                $message = $_SESSION['flash_notification'];
                unset($_SESSION['flash_notification']);
                return $message;
            }

        endif;

        return false;
    }
}