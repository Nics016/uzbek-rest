<?php 

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

if(isset($_REQUEST['pageId'])){
	$path = GetPath($pageId);
	if($path != false){
		if($path[1] != ""){
			if(file_exists('engine/core/redirurl/'.$path[1].'.php'))
				require_once 'engine/core/redirurl/'.$path[1].'.php';			
		}else{
			$exc = array("pageId");
			$addur = "";
			foreach($_GET as $k => $v){
				if(!in_array($k,$exc)){
					if($addur != "")
						$addur .= "&";
					$addur .=$k."=".$v;
				}
			}
		if($addur == "")
			GenError(301,"http://".$_SERVER['SERVER_NAME'].$path[0]);
		else
			GenError(301,"http://".$_SERVER['SERVER_NAME'].$path[0]."?".$addur);
		}
	}
}

function GetPath($pageId){
	$out = array(0 => "",1 => "");
	$query = "select * from Content where Id = ".$pageId;
	//echo $query;
	//$out = true;
	$result = mysql_query($query);
    if ($result) {
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
			if($row['urlName'] != ""){
				if($row["Parent"] != 0){
            		$out = GetPath($row["Parent"]);
            		if($out != FALSE){
            			$out[0] .= $row["urlName"]. "/";
            		}
				}else
            		$out[0] .= "/".$row["urlName"]. "/";
            	$out[1] = $row["Mech"];
            	//echo $out;
            	return $out;
			}else{
				return false;
			}
        }else{
        	return false;
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
}

function GenURL($pageId,$param=array()){
	//print_r($param);
	$u = GetPath($pageId);
	$url = $u[0];
	if($u[1] != ""){
		if(file_exists('engine/core/makeurl/'.$u[1].'.php')){
			include_once 'engine/core/makeurl/'.$u[1].'.php';
			//if(isset($param['catId']))
			//$url .= MakePathTree($param['catId']);
			
		}
	}
	if($url)
		return $url;
	else
		return "/?pageId=".$pageId;	
}
function GenTreeArray(){
	$out0 = array();
	$out1 = array();
	$query = "select * from Content where Active = 1 and urlName != '' order by Id desc";
	$result = mysql_query($query);
	if ($result) {
		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_array($result)){
				$url = GenURL($row['Id']);
				if(substr($url,0,2) != '/?'){
					$out0[] = "/?pageId=".$row['Id'];
					$out1[]	= $url;
				}
	//			print $url;			
			}
		}
	} else {
		SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
	}
	$o = array($out0,$out1);
//ob_flush();
//	exit();
//	print_r($o);
	return 	$o;
}

?>