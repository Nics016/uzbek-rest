<?php
date_default_timezone_set('Europe/Moscow');
date_default_timezone_set("Asia/Tbilisi");
setlocale(LC_ALL, "ru_RU.CP1251");
//setlocale(LC_ALL, "ru_RU");
$dir = $_SERVER['DOCUMENT_ROOT'];
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/';
define('SITE_DIR', $dir);
define('SITE_URL', $url);
define('FUNC_DIR', SITE_DIR . 'engine/globalfunc/');
// ;длючает обработку ошибок
define('LOADER', FUNC_DIR . 'core/functions/loader.php');
define('CLRF', "\n");
define('CLASS_DIR', FUNC_DIR . 'core/classes/');
if (isset($GLOBALS['config']['BASE_DIR'])) {
    define('LOCAL_CLASS_DIR', $GLOBALS['config']['BASE_DIR'] . 'engine/classes/');
} else {
    define('LOCAL_CLASS_DIR', SITE_DIR . 'engine/classes/');
}
set_include_path(get_include_path() . ':/usr/lib/php');
set_include_path(get_include_path() . ':' . FUNC_DIR);
set_include_path(get_include_path() . ':' . FUNC_DIR . '/pear');
if (!function_exists('__autoload')) {
    function __autoload ($className)
    {
        $className = strtolower(str_replace('_', '', $className));
        if (! class_exists($className)) {
            if (file_exists($Class = LOCAL_CLASS_DIR . $className . '.class.php')) {
                require_once ($Class);
            } elseif (file_exists($Class = CLASS_DIR . $className . '.class.php')) {
                require_once ($Class);
            } else {
                eval('class ' . $className . ' extends Exception {}');
                throw new $className('[__autoload] this file doesn\'t exists: ' . $Class);
            }
        }
    }
}
$GLOBALS['config']['DEBUG_MODE'] = false;
$GLOBALS['config']['ADMIN_EMAIL'] = array('anton@shustov.ru' , 'sshiyanov@newdesign.ru');
define('CSID_IP', '89.175.99.178', true);
