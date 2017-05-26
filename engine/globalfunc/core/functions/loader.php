<?php
if (Config::val('DEBUG_MODE')) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    set_error_handler('debugErrorHandler', E_ALL);
} else {
    ini_set('display_errors', 0);
    set_error_handler('debugErrorHandler', E_ALL & ~E_NOTICE);
}

set_exception_handler('debugExeptionHandler');

function debugExeptionHandler(Exception $e) {
    $post = '';
    if (isset($_POST)) {
        foreach ($_POST as $key => $value) {
            $post .= sprintf('[%s] = %s, ', $key, $value);
        }
    }
    $post = substr($post, 0, -2);
    $get = '';
    if (isset($_GET)) {
        foreach ($_GET as $key => $value) {
            $get .= sprintf('[%s] = %s, ', $key, $value);
        }
    }
    $get = substr($get, 0, -2);
    @$message = sprintf('errno: %s,<br>errmsg: %s,<br>errfile: %s,<br>errline: %s,<br>trace: %s',
        $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
    @$message .= sprintf('<br>ip: %s,<br>referer: %s,<br>url: %s,<br>host: %s,<br>ua: %s',
        $_SERVER['REMOTE_ADDR'], @$_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], $_SERVER['HTTP_USER_AGENT']);
    @$message .= sprintf('<br>post: %s,<br>get: %s', $post, $get);
    if (Config::val('DEBUG_MODE')) {
        printf('<br>--<br>%s<br>--', $message);
    } else {
        adminEmail($message, 'ERR: ' . SITE_URL, SITE_URL);
    }
    $log = '['.date('d-D-Y H:i:s').'] Caught exception: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()."\n";
    $fd = fopen('/var/log/php/php_error.log', 'a');
    fwrite($fd, $log);
    fclose($fd);
}

function debugErrorHandler($errno, $errmsg, $errfile, $errline, $vars) {
	// через Config::val() имхо лучше ничего не проверять, т.к. при отсутствии
	// какого-то параметра в конфиге обработчик ошибок сам выкинет ошибку

	if (!empty($GLOBALS['config']['DEBUG_MODE'])
		&& !empty($GLOBALS['config']['DEBUG_SKIP_ERRORS'])
		&& ($GLOBALS['config']['DEBUG_SKIP_ERRORS'] | $errno)) {

        return;
    }
    $post = '';
    if (isset($_POST)) {
        foreach ($_POST as $key => $value) {
            $post .= sprintf('[%s] = %s, ', $key, $value);
        }
    }
    $post = substr($post, 0, -2);
    $get = '';
    if (isset($_GET)) {
        foreach ($_GET as $key => $value) {
            $get .= sprintf('[%s] = %s, ', $key, $value);
        }
    }
    $get = substr($get, 0, -2);
    $path = debug_backtrace();
    $trace = '';
	//$trace = debug_print_backtrace();
	foreach ($path as $value) {
		$trace .= sprintf('# %s called at [%s:%d]<br>', $value['function'], @$value['file'], @$value['line']);
	}
    @$message = sprintf('errno: %s,<br>errmsg: %s,<br>errfile: %s,<br>errline: %s,<br>trace: %s', $errno, $errmsg, $errfile, $errline, $trace);
    @$message .= sprintf('<br>ip: %s,<br>referer: %s,<br>url: %s,<br>host: %s,<br>ua: %s',
        $_SERVER['REMOTE_ADDR'], @$_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], $_SERVER['HTTP_USER_AGENT']);
    @$message .= sprintf('<br>post: %s,<br>get: %s', $post, $get);

    if (!empty($GLOBALS['config']['DEBUG_MODE']) && $_SERVER['REMOTE_ADDR'] == '89.175.99.178') {
        printf('<br>--<br>%s<br>--', $message);
    } else {
        adminEmail($message, 'ERR: ' . SITE_URL, SITE_URL);
    }
    ob_flush();
}

/**
 * Переходная функция предназначена для старой системы запросов
 *
 * @param string $query
 * @param string $error
 */
function dbError($query = '', $error = '') {
    throw new Exception("Запрос '$query' вызвал ошибку '$error'");
}

//require_once 'Smarty.class.php';
//$smarty  = new Smarty();
//$smarty->template_dir = 'templates/';
//$smarty->compile_dir  = 'templates_c/';
//$smarty->cache_dir    = 'cache/';
//$smarty->config_dir   = 'configs/';
