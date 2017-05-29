<?
SortJQHdr('GalleryImg','Sort','Id');
$Id = $param['Parent'];
	$cou=0;
	$query = "select * from GalleryImg where Parent = ".$param['Parent']." order by Sort asc;";
	$result = mysql_query($query);
	if($result){
		echo "<ul class='sortable'>";
		$num = mysql_num_rows($result);
		while($row = mysql_fetch_array($result)){
			$id = $row["Id"];
			$name = $row['Name'];
            $end = "";
            $cou++;
            //if($cou == $num)$end = " class='end'";
            printf("<li id='srt-%d'%s>", $row['Id'],$end);
			print("<span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>");
			printf("<a href=auth.phtml?actcomp=edgallery&param[Id]=%d&param[Parent]=%d style='vertical-align:top'>",$row["Id"],$row["Parent"]);
			if($row["Active"] == 0)echo "<font color=#555555>";
			if($row["Name"] == "")
				$row["Name"] = "без имени";
			printf("%s",$name);
			if($row["Main"] == 1)echo "<font color=#e00>&lt;&lt;</font>";
			printf("</a>");
			print("</li>");
		}
		echo "</ul>";
		printf("<a href=auth.phtml?actcomp=edgallery&param[Id]=0&param[Parent]=%d id='sortablep'>",$param['Parent']);
		printf("<img src=\"../../imgsrc/lev+.gif\" alt=\"Добавить позицию\" border=0 style='padding-left:8px'></a><br>");
	}else{
		printf("%s<br><Font color=red><b>%s</b></font><BR>", $query,mysql_error());
	}
?>
