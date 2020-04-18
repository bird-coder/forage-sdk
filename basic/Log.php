<?php

/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 2020/4/18
 * Time: 16:54
 */
class Log
{
    public static function write($msg, $file = 'error') {
        if (!file_exists(FORAGE_LOG_PATH)) {
            @mkdir(FORAGE_LOG_PATH);
        }
        chmod(FORAGE_LOG_PATH, 777);
        if (!is_writable(FORAGE_LOG_PATH)) exit('FORAGE_LOG_PATH is not writable !');
        $s_now_time = date('[Y-m-d H:i:s]');
        $s_now_day  = date('Y_m_d');
        $s_target = FORAGE_LOG_PATH.$file.'_'.$s_now_day.'.log';
        if (file_exists($s_target)) {
            @chmod($s_target, 0666);
        }
        clearstatcache();
        // 写日志, 返回成功与否
        return error_log("$s_now_time $msg\n", 3, $s_target);
    }
}