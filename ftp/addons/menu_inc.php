<?
if(!$parent)$parent = 0;
$sql = $GLOBALS['sql'];
SortJQHdr('MenuTree','Sort','Id',1,0,'/?action=pages&actcomp=menu_inc');
function PrintTree($Parent,$Sm,$hide='-',$jk = 0){
	global $UserId,$gsql,$TreeClose;
	$sql = $GLOBALS['sql'];
	$query = "select * from MenuTree where Parent = $Parent order by Sort asc;";
	//echo $query;
	$cou = 0;
	$result = mysql_query($query);
	if($result){
		$num = mysql_num_rows($result);
		if($num>0 && $jk == 0){
			echo "<ul class='sortable'";
			if($hide == '+')
				echo " style='display:none'";
			echo ">";
		}
		if($hide == '-'){
		while($row = mysql_fetch_array($result)){
			$Idd = $row["Id"];
            $end = "";
            $cou++;
            if($cou == $num)$end = " class='end'";
            printf("<li id='srt-%d'%s>", $row['Id'],$end);
			if($sql->queryOne("select count(Id) from MenuTree where Parent = ".$row['Id'])>0){
				$cn = $GLOBALS['gsql']->queryOne("select Val from UserVars where UserId = ".$_SESSION['UserId']." and SiteId = ".$_SESSION['actsite']." and VarName = 'treeoc_MenuTree' and SubVar = ".$row['Id']);
				if($cn == "1")
					$cntn = "+";
				else
					$cntn = "-"; 
				printf("<span class='openclose' style='margin-left:-2em'>%s</span>",$cntn);
			}
			printf("<span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>");
			if($sql->queryOne("select count(Id) from MenuTree where Parent = ".$row['Id'])==0){
				printf("<a href=/?action=pages&actcomp=menuitems&param[Parent]=");
				printf("%d>",$row["Id"]);
			}
			if($row["Active"] == 0)echo "<font color=#555555>";
			if($row["Name"] == ""){
				$row["Name"] = "без имени";
			}
			printf("%s",$row["Name"]);
			if($row["Active"] == 0)echo "</font>";
			printf("</a>");
			printf("<span class='action'>");
			printf("<a href=/?action=pages&actcomp=edgroup&param[Id]=%d title=\"€зменить раздел %s\"><img src=../imgsrc/lev-sm.phtml?let=i border=0></a>",$row["Id"],$row["Name"]);
			if($sql->queryOne("select count(Id) from MenuItems where Parent = ".$row['Id'])==0)
				printf("<a href=/?actcomp=edgroup&param[Id]=0&param[Parent]=%d title='Добавить субкатегорию'><img src=\"../imgsrc/lev-sm.phtml?let=plus\" border=0 alt=\"Добавить субкатегориию\"></a>",$row['Id']);
			print("</span>");
			PrintTree($row["Id"],$Sm+1,$cntn);
			print("</li>"); 
			//if($sql->queryOne("select count(Id) from MenuItems where Parent = ".$row['Id'])==0)
				//printf("<li class='add'><a href=/?actcomp=edgroup&param[Id]=0&param[Parent]=%d title='Добавить субкатегорию'><img src=\"/imgsrc/lev+.gif\" alt=\"Добавить позицию\" border=0 style='padding-left:8px'></a></li>",$row['Id']);
		}
		}
		if($num>0 && $jk == 0)
			echo "</ul>";
	}else{
		printf("%s<br><Font color=red><b>%s</b></font><BR>", $query,mysql_error());
	}
}
if($param[jk] != 0){
	ob_clean();
	ob_start();
	PrintTree($param[jk],1,'-',1);
	echo iconv('WINDOWS-1251','UTF-8', ob_get_clean());
//	exit();
}else{
	PrintTree($parent,1);
}

if(!$param[jk])
	printf("<a href=/?action=pages&actcomp=edgroup&param[Id]=0&param[Parent]=%d title='Добавить субкатегории'><img src=\"/imgsrc/lev+.gif\" alt=\"Добавить позицию\" border=0 style='padding-left:8px'></a><div id='itemload' atyle='display:none'></div>",$parent);
else
	exit();
?>
