<?
$Id = $param['Parent'];
do{
	$query = "select Name,Parent from MenuTree where Id = $Id";
	$result = mysql_query($query);
	if($result){
		$row = mysql_fetch_array($result);
		if($Id != $param['Parent'])
			$hdr = $row['Name']." &gt;&gt; ".$hdr;
		else
			$hdr = sprintf("<a href=auth.phtml?action=pages&actcomp=menuitems&param[Parent]=%d>%s</a>",$param['Parent'],$row['Name']);				
		$Id = $row['Parent'];
	}else{
		printf("%s<br><Font color=red><b>%s</b></font><BR>", $query,mysql_error());
		$Id = 0;
	}
}while ($Id != 0);
echo $hdr;
$chk = array(""," checked");
$query = "select * from MenuItems where Id = '".$param['Id']."'";
$result = mysql_query($query);
if($result){
	$row = mysql_fetch_array($result);
	echo " &gt;&gt; ".$row['Name']. "<br>";
	printf("<form action=write.phtml method=post enctype='multipart/form-data'>");
	printf("<input type=hidden name=param[Id] value='%d'>",$param['Id']);
	printf("<input type=hidden name=param[Parent] value='%d'>",$param['Parent']);
	printf("<input type=hidden name=writestatus value='menuitem'>");
	printf("<table class=fontst>");
	printf("<tr><td>Название</td><td><input type=text name=param[Name] value='%s' size=120 ></td></tr>\n",$row["Name"]);
	printf("<tr><td>Название (en)</td><td><input type=text name=param[NameEn] value='%s' size=120></td></tr>\n",$row["NameEn"]);
	if($param['Id'] == 0)$row["Active"]=1;
    printf("<tr><td>Показывается</td><td><input type=checkbox name=param[Active] value=1%s></td></tr>",$chk[$row['Active']]);
	printf("<tr><td>Описание</td><td><input type=text name=param[Descr] value='%s' size=120 ></td></tr>\n",$row["Descr"]);
	printf("<tr><td>Описание (en)</td><td><input type=text name=param[DescrEn] value='%s' size=120></td></tr>\n",$row["DescrEn"]);
	printf("<tr><td>Вес</td><td><input type=text name=param[Weight] value='%s' size=20></td></tr>\n",$row["Weight"]);
	printf("<tr><td>Цена (руб)</td><td><input type=text name=param[Price] value='%s' size=20></td></tr>\n",$row["Price"]);
	print("<tr><td>Изображение для раздела</td><td>");
	$pic = sprintf("%simg/menupic/%d.jpg", $GLOBALS['homedir'], $row["Id"]);
	    if (file_exists($pic)) {
		?>
		<img src="http://<?=$GLOBALS['url']?>/img/menupic/<?=$row['Id']?>.jpg" style='max-width:200px;max-height:200px'/>
		<br />
    	<br><a href="#" onClick="if(confirm('Удалить?')){self.location.href='write.phtml?writestatus=unlinkfile&param[path]=img/menupic&param[file]=<?=$row['Id']?>.jpg';}else{alert('Отменено!');}" style='color:red'>удалить файл &gt;&gt;</a>
		<?
	    } else {
			print("<center>нет картинки</center>");
	    }
	print("</td></tr>");
	printf("<tr><td>Новая картинка</td><td><input type=file size=50 name=param[pageimage]></td></tr>");
	printf('</table><input type="Submit" name="exit" value="Cохранить и выйти"><input type="Submit" name="return" value="Cохранить/записать">');
	if($param['Id'] != 0){
	printf("<input type=Submit value=Удалить name=param[100]>");
	}
	print("</form>");
}else{
	printf("In query to DataBase '%s' detected error: <Font color=red><b>%s</b></font>, please contact <a href=\"mailto:anton@shustow.com?subject='%s at %s'\">system administrator</a>", $query,mysql_error(),mysql_error(),$query);
}
?>
