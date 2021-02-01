<?php
/**
 * Ideated by Sergio Casizzone
 * User: jambtc
 * Date: 24/01/2020
 */

namespace jambtc\oauthtelegram;

class telegram extends \yii\base\Widget
{
    public $bot_username;
    public $token;

    function __construct($bot_username,$token){
        $this->bot_username=$bot_username;
        $this->token=$token;
    }

    public function loginButton($auth_url,$data_size='large'){
        $bot_username=$this->bot_username;
        return '<script async src="https://telegram.org/js/telegram-widget.js?2" data-telegram-login="'.$bot_username.'" data-size="'.$data_size.'" data-auth-url="'.$auth_url.'"></script>';
    }

    public function checkTelegramAuthorization($auth_data) {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        // fix yii 1.1
        unset($auth_data['r']);
        unset($auth_data['SettingsWebapp_page']);
        // endfix
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            echo 'Data is NOT from Telegram!';
            die();
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            echo 'Data is outdated!';
            die();
        }

        return $auth_data;
    }
}
