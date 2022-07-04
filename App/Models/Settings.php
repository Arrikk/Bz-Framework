<?php

namespace App\Models;

use Core\Http\Res;
use Core\Traits\Model;

class Settings extends \Core\Model
{

    use Model; # Use trait only if using the find methods

    /**
     * Each model class requires a unique table base on field
     * @return string $table ..... the table name e.g 
     * (users, posts, products etc based on your Model)
     */
    public static $table = "site_settings"; # declare table only if using traitModel


    public static function findHere($name)
    {
        $found = static::findOne(['name' => $name]);
        if (!$found) {
            $found =  Settings::dump(['name' => $name]);
            return json_decode($found->value);
        };
        // Res::json(json_decode($found->value));
        return json_decode($found->value);
    }

    public static function UpdateHere(string $name, array $setting)
    {
        $update =  static::findAndUpdate([
            'name' => $name
        ], ['value' => json_encode($setting)]);
        return json_decode($update->value);
    }
    // ***********************************************
    // ***************** EMAIL SETTING ***************
    // ***********************************************

    /**
     * Get Email Settings
     * 
     * @return object
     */
    public static function emailSetting()
    {
        return Settings::findHere('email');
    }

    /**
     * Set email details / data
     *
     * @return bool
     **/
    public static function updateEmailSetting($field)
    {
        extract($field);
        
        $setting = array(
            'id' => 0,
            'smtp_host' => static::serialize($host),
            'smtp_port' => (int) $port,
            'smtp_auth' => true,
            'smtp_secure' => static::serialize($secure),
            'smtp_username' => static::serialize($username),
            'smtp_password' => static::serialize($password),
            'mail_from' => static::serialize($from)
        );
        static::emailSetting();
        return Settings::UpdateHere('email', $setting);
    }
    // ***********************************************
    // ***************** SOCIAL SETTING ***************
    // ***********************************************

    /**
     * Get Social Settings
     * 
     * @return object
     */
    public static function socialSetting()
    {
        return Settings::findHere('social');
    }

    /**
     * Set social details / data
     *
     * @return bool
     **/
    public static function updateSocialSetting($field)
    {
        extract($field);

        $setting = array(
            'id' => 0,
            'instagram' => static::serialize($instagram),
            'facebook' => static::serialize($facebook),
            'twitter' => static::serialize($twitter),
            'whatsapp' => static::serialize($whatsapp)
        );
            static::socialSetting();
            return Settings::UpdateHere('social', $setting);
    }
    // ***********************************************
    // ***************** SEO SETTING ***************
    // ***********************************************

    /**
     * Get SEO Settings
     * 
     * @return object
     */
    public static function seoSetting()
    {
        return Settings::findHere('seo');
    }

    /**
     * Set SEO details / data
     *
     * @return bool
     **/
    public static function updateSeoSetting($field)
    {
        extract($field);
        $setting = array(
            'id' => 0,
            'meta_description' => static::serialize($meta_description),
            'meta_keyword' => static::serialize($meta_keyword),
            'google_analytics' => static::serialize($google_analytics)
        );
        static::seoSetting();
        static::UpdateHere('seo', $setting);
    }
    
    // ***********************************************
    // ***************** Site SETTING ***************
    // ***********************************************

    /**
     * Get SITE Settings
     * 
     * @return object
     */
    public static function siteSetting()
    {
        return static::findHere('site');
    }

    /**
     * Set SITE details / data
     *
     * @return bool
     **/
    public static function updateSiteSetting($field, $file = [])
    {
        extract($field);

        if (!empty($file['icon']['name'])) {
            if (isset(self::siteSetting()->favicon) && self::siteSetting()->favicon !== '') {
                if (file_exists('.' . self::siteSetting()->favicon ?? '')) {
                    unlink('.' . self::siteSetting()->favicon ?? '');
                }
            }
            $_icon = Upload::upload($file);
        }
        if (!empty($file['logo']['name'])) {
            if (isset(self::siteSetting()->logo) && self::siteSetting()->logo !== '') {
                if (file_exists('.' . self::siteSetting()->logo ?? '')) {
                    unlink('.' . self::siteSetting()->logo ?? '');
                }
            }
            $_logo = Upload::upload($file);
        }
        // echo json_encode(['logo' => $_logo, 'icon' => $_icon]);

        $setting = array(
            'id' => 0,
            'site_url' => 0,
            'slug' => '',
            'name' => static::serialize($name),
            'logo' => static::serialize(strip_tags($_logo)),
            'favicon' => static::serialize(strip_tags($_icon)),
            'email' => static::serialize($email),
            'address' => static::serialize($address)
        );
        static::siteSetting();
        static::UpdateHere('site', $setting);
    }
}
