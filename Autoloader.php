<?php

class Autoloader{

    /**
     * 自动加载类
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $name = $class;
        if (strpos($name, '\\')) {
            $name = strstr($class, '\\', true);
        }

        $filename = FORAGE_AUTOLOADER_PATH.'/basic/'.$name.'.php';
        if (is_file($filename)) {
            include $filename;
            return;
        }

        $filename = FORAGE_AUTOLOADER_PATH.'/plus/'.$name.'.php';
        if (is_file($filename)) {
            include $filename;
            return;
        }

        $filename = FORAGE_AUTOLOADER_PATH.'/dingtalk/'.$name.'.php';
        if (is_file($filename)) {
            include $filename;
            return;
        }
    }
}

spl_autoload_register('Autoloader::autoload');