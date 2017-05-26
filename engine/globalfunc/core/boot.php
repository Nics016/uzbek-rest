<?php
//include_once FUNC_DIR . 'core/functions/loader.php';
include_once FUNC_DIR . 'func.php';
include_once FUNC_DIR . 'core/dbopen.php';
if (file_exists(SITE_DIR . 'engine/func.php')) {
    include_once SITE_DIR . 'engine/func.php';
}