<?php 
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
if($_GET["urien"]){
$out = explode("/",$_GET["urien"]);
foreach($out as $oo)
	$out1[] = urlencode($oo);
$out = $out1;
if(is_array($out))
	$lasto = $out[sizeof($out)-1];
else{ 
	$lasto = $_GET["urien"];
	$out[0] = $_GET["urien"];
}
if($lasto != ""){
	if(strpos($lasto,'.') !== false){
		$fin = explode(".",array_pop($out));
		$param[1] = $fin[0];
		$param[2] = $fin[1];
	}
}else
	array_pop($out);
$param[0] = $out;
//echo $out;
//exit();
if(CheckColumn()){ 
	$parent=0;
	foreach ($param[0] as $k => $v){
		$id = GetIdfromURL($v,$parent);
		$parent = $id[0];
	//	print_r($id);
		array_shift($param[0]);
		if(!$id){
			GenError(404);			
		}elseif($id[1] != ""){
			if(file_exists('engine/core/parseurl/'.$id[1].'.php')){
				require_once 'engine/core/parseurl/'.$id[1].'.php';
			//if($_SERVER['REMOTE_ADDR'] == '89.175.99.178'){ die(); }
	//		} else {
	//			GenError(404);
			}
			break;			
		}
	}
	if($parent == 0 OR isset($param[1])){
		GenError(404);			
	}else{
//echo $id[0];
		$pageId = $id[0];
		$_GET['pageId'] = $id[0];
	}
}
}
function GetIdfromURL($url,$parent = 0){
	$query = "select * from Content where urlName = '$url' and Parent = $parent";
    //echo $query;
	$result = mysql_query($query);
    if ($result) {
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
			$out[0] = $row["Id"];
			$out[1] = $row["Mech"];
			return $out;
        }else{
        	return false;
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
	
}
?>
