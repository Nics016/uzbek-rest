<?
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

if($param[100] == "Удалить"){
    $query = "delete from MenuItems where Id='".$param['Id']."';";
}elseif($param['Id'] == 0){
    $query = "select max(Sort) from MenuItems where Parent = ".$param['Parent'].";";
    $result = mysql_query($query);
    if($result){
        $row = mysql_fetch_array($result);
        $srt = $row[0]+1;
    }
    $query = "insert into MenuItems (Parent,Name,NameEn,Active,Descr,DescrEn,Weight,Price,Sort) values ('".$param['Parent']."','".$param["Name"]."','".$param['NameEn']."','".$param['Active']."','".$param['Descr']."','".$param['DescrEn']."','".$param['Weight']."','".$param['Price']."',$srt);";
	//echo $query;
	//exit();
}else{
    $query = "update MenuItems set Name='".$param["Name"]."',NameEn='".$param['NameEn']."',Active='".$param['Active']."',Descr='".$param['Descr']."',DescrEn='".$param['DescrEn']."',Weight='".$param['Weight']."',Price='".$param['Price']."' where Id=".$param['Id'].";";
}
//print $query;
$result = mysql_query($query);
if($result){
    if($param['Id'] == 0) $param['Id'] = mysql_insert_id();
    if($_FILES['param']['tmp_name']['pageimage'] != ""){
    	$newname = sprintf("%s/img/menupic/%d.jpg",$GLOBALS['homedir'],$param['Id']);
    	//print $newname;
    	copy($_FILES['param']['tmp_name']['pageimage'],$newname);
    }    
    if(isset($_POST['return'])) 
    	$loc = sprintf("Location: /?action=pages&actcomp=editem&param[Id]=%d",$param['Id']);
    else $loc = sprintf("Location: /?action=pages&actcomp=menuitems&param[Parent]=%d",$param['Parent']);
    header($loc);
}else{
    printf("%s<br><Font color=red><b>%s</b></font><BR>",$query, mysql_error());
}

?>
