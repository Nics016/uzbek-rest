<?php 
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//print_r($_FILES);
if($_FILES['param']['tmp_name']['pageimage'] != ""){
		$newname = sprintf("%s/img/page/%d.jpg",$GLOBALS['homedir'],$param[0]);
		//print $newname;
		copy($_FILES['param']['tmp_name']['pageimage'],$newname);
	}
?>