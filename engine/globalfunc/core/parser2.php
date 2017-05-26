<?php
if ($GLOBALS['config']['cleanurl']) {

}
$rul = ((isset($rul)) ? $rul : null);
$rul = str_replace(array('.', ',', '!', ')', '(', '#', '*', '$', '"', "'"), '', $rul);
$urlElements = explode('/', $rul);

?>