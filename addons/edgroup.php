<script type="text/javascript">
$(document).ready(function(){
	$('.chk').change(function(){
		//alert($(this).attr('url'));
		$.startCloser();
		var fld = $(this); 
		$.get( 'http://siteadmin.ru/?actcomp=<?= $actcomp;?>&'+$(this).attr('url')+'&param[cval]='+$(this).val(), function( data ) {
			if(data != ''){
				$(fld).css('border-color',"red");
				$(fld).after("<div class='error'>"+data+"</div>");
				$(fld).focus();
			}else{
				$(fld).css('border-color','initial');
				var cls = $(fld).next().attr('class');
				if(cls = 'error'){
					$(fld).next().detach();
				}
			}
			$.endCloser();
			}, "html" );	
	
		});    
})
</script>
<?
if($param[chk]){
	ob_clean();
	//echo $param[id];
	$q = "select Parent from ${param[tab]} where ${param[chk]} like '${param[cval]}' && Id != '${param[id]}'";
	$rs = $sql->queryOne($q);
	//echo $q;
	if($rs != ''){
		$name = $sql->queryOne("select Name from ${param[tab]} where Id = $rs");
		echo iconv('windows-1251','UTF-8',"Такое название уже есть в базе данных в разделе `$name`! Введите другое название!");
	}
	exit();
}
$homeUrl = 'uzb-rest.ru';
$chk = array(""," checked");
$query = "select * from MenuTree where Id = '".$param['Id']."'";
$result = mysql_query($query);
if($result){
	$row = mysql_fetch_array($result);
	printf("<form action=write.phtml method=post enctype='multipart/form-data'>");
	printf("<input type=hidden name=param[Id] value='%d'>",$param['Id']);
	printf("<input type=hidden name=param[Parent] value='%d'>",$param['Parent']);
	printf("<input type=hidden name=writestatus value='grp'>");
	printf("<table class=fontst>");
	printf("<tr><td>Категория</td><td><input type=text name=param[Name] value='%s' size=120></td></tr>\n",$row["Name"]);
	printf("<tr><td>Категория (en)</td><td><input type=text name=param[NameEn] value='%s' size=120></td></tr>\n",$row["NameEn"]);
	if($param['Id'] == 0)$row["Active"]=1;
    printf("<tr><td>Показывается</td><td><input type=checkbox name=param[Active] value=1%s></td></tr>",$chk[$row['Active']]);
	printf("<tr><td>Показывать оба языка</td><td><input type=checkbox name=param[EnShow] value=1%s></td></tr>",$chk[$row['EnShow']]);
	printf('</table><input type="Submit" name="exit" value="Cохранить и выйти"><input type="Submit" name="return" value="Cохранить/записать">');
	if($param['Id'] != 0){
	printf("<input type=Submit value=Удалить name=param[100]>");
	}
	print("</form>");
}else{
	printf("In query to DataBase '%s' detected error: <Font color=red><b>%s</b></font>, please contact <a href=\"mailto:anton@shustow.com?subject='%s at %s'\">system administrator</a>", $query,mysql_error(),mysql_error(),$query);
}
if($param[0] != 0 && $row['Link'] == 0){
	printf("<br><a href=auth.phtml?session_id=%s&page=%d&actsite=%d&action=pages&actcomp=movetree&param[0]=%d>перенести раздел &gt;&gt;</a>",$session_id,$page,$actsite,$param[0]);
}
?>
