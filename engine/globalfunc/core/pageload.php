<?php
$path0 = SITE_DIR . "engine/page.php";
$path1 = FUNC_DIR . "state/page.php";
if (file_exists($path0)) {
   include_once($path0);
} else {
    if (file_exists($path1)) {
        include_once($path1);
    }
}