<?
	if(!$group)$group=1;
	$cou=0;
	$query = "select * from GalleryTree where GroupId = $group order by Sort asc;";
	$result = mysql_query($query);
	if($result){
		echo "<ul>";
		$num = mysql_num_rows($result);
		while($row = mysql_fetch_array($result)){
			$id = $row["Id"];
			$name = $row['Name'];
            $end = "";
            $cou++;
            //if($cou == $num)$end = " class='end'";
            printf("<li id='srt-%d'%s>", $row['Id'],$end);
			printf("<a href=auth.phtml?actcomp=galleryitems&param[Parent]=%d style='vertical-align:top'>",$row["Id"]);
			if($row["Name"] == "")
				$row["Name"] = "без имени";
			printf("%s",$name);
			printf("</a>");
			print("</li>");
		}
		echo "</ul>";
	}else{
		printf("%s<br><Font color=red><b>%s</b></font><BR>", $query,mysql_error());
	}
?>
