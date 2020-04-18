<?php

/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2020/4/18
 * Time: 14:04
 */
class Util
{
    public static function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //https 请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
    }

    public static function param($param_name, $default = NULL) {
        $str = file_get_contents("php://input");
        $params = [];
        if (!empty($str) && empty($_REQUEST)) $params = json_decode($str, true);
        else if (!empty($_REQUEST)) $params = $_REQUEST;
        if (isset($params[$param_name])) {
            return $params[$param_name];
        }
        if (!($default === NULL)) {
            return $default;
        }
        return NULL;
    }
}