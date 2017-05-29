<?
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

if($param[100] == "Удалить"){
    $query = "delete from MenuTree where Id='".$param['Id']."';";
}elseif($param['Id'] == 0){
    $query = "select max(Sort) from MenuTree where Parent = ".$param['Parent'].";";
    $result = mysql_query($query);
    if($result){
        $row = mysql_fetch_array($result);
        $srt = $row[0]+1;
    }
    $query = "insert into MenuTree (Parent,Name,NameEn,Active,Sort,EnShow) values ('".$param['Parent']."','".$param["Name"]."','".$param['NameEn']."','".$param['Active']."',$srt,'".$param['EnShow']."');";
	//echo $query;
	//exit();
}else{
    $query = "update MenuTree set Name='".$param["Name"]."',NameEn='".$param['NameEn']."',Active='".$param['Active']."',EnShow='".$param['EnShow']."' where Id=".$param['Id'].";";
}
//print $query;
$result = mysql_query($query);
if($result){
    if($param['Id'] == 0) $param['Id'] = mysql_insert_id();
    if(isset($_POST['return'])) 
    	$loc = sprintf("Location: /?action=pages&actcomp=edgroup&param[Id]=%d",$param['Id']);
    else $loc = sprintf("Location: /?action=pages&actcomp=menu_inc");
    header($loc);
}else{
    printf("%s<br><Font color=red><b>%s</b></font><BR>",$query, mysql_error());
}

?>
