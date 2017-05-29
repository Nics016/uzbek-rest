<?
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
if($param['Main'] == 1){
	$query = "update GalleryImg set Main = 0 where Parent = ".$param['Parent'].";";
	$result = mysql_query($query);
}
if($param[100] == "Удалить"){
    $query = "delete from GalleryImg where Id='".$param['Id']."';";
}elseif($param['Id'] == 0){
    $query = "select max(Sort) from GalleryImg where Parent = ".$param['Parent'].";";
    $result = mysql_query($query);
    if($result){
        $row = mysql_fetch_array($result);
        $srt = $row[0]+1;
    }
    $query = "insert into GalleryImg (Parent,Name,NameEn,Active,Descr,DescrEn,Main,Sort) values ('".$param['Parent']."','".$param["Name"]."','".$param['NameEn']."','".$param['Active']."','".$param['Descr']."','".$param['DescrEn']."','".$param['Main']."',$srt);";
	//echo $query;
	//exit();
}else{
    $query = "update GalleryImg set Name='".$param["Name"]."',NameEn='".$param['NameEn']."',Active='".$param['Active']."',Descr='".$param['Descr']."',DescrEn='".$param['DescrEn']."',Main='".$param['Main']."' where Id=".$param['Id'].";";
}
//print $query;
$result = mysql_query($query);
if($result){
    if($param['Id'] == 0) $param['Id'] = mysql_insert_id();
    if($_FILES['param']['tmp_name']['pageimage'] != ""){
    	$newname = sprintf("%s/img/gallery/%d.jpg",$GLOBALS['homedir'],$param['Id']);
    	//print $newname;
    	copy($_FILES['param']['tmp_name']['pageimage'],$newname);
    }    
    if(isset($_POST['return'])) 
    	$loc = sprintf("Location: /?action=pages&actcomp=edgallery&param[Id]=%d",$param['Id']);
    else $loc = sprintf("Location: /?action=pages&actcomp=galleryitems&param[Parent]=%d",$param['Parent']);
    header($loc);
}else{
    printf("%s<br><Font color=red><b>%s</b></font><BR>",$query, mysql_error());
}

?>
