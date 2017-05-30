
	<paralax>
<?php 
$menuarr = array();
if ($result = $sql->query("SELECT * FROM Content where Parent = $menupath[1] order by Sort")) {
	$pth = getPath($menupath[1]);
	while ($row = $result->fetch_assoc()) {
		$tmparray = array($pth[0],$row['urlName'],$row['MenuName'],$row['Mech'],$row['Content'],$row['Id']);
		$menuarr[] = $tmparray;
		if(file_exists("img/page/".$row['Id'].".jpg")){
			printf("\t\t<div src=\"/img/page/%d.jpg\" apply=\"%s\"></div>",$row['Id'],$row['urlName']);
		}
	}
} 
?>
	</paralax>

	<div id="content">
		<div class="circleBox">
			<div id="circle">
				<div class="center">
					<ul class="jump">
<?php 
foreach($menuarr as $val){
	echo genMenuItem($val); 
}
?>
					</ul>
				</div>
			</div>
		</div>
		
<?php 
$twink=0;
foreach($menuarr as $val){
	if($twink == 1){
		printf("\t\t<div type=\"twink\" class=\"big\">\n\t\t\t<div class=\"text\">%s</div>\n\t\t</div>",$val[2]);
	}
	printf("\t\t<div class=\"%s\" id=\"%s\" type=\"paralax\">\n",$val[1],$val[1]);
	if($val[3] != ""){
		$parent = $val[5];
		$name = $val[2];
		include("engine/core/".$val[3].".php");
	}else
		echo $val[4];
	print("\t\t</div>"); 
	$twink=1;
}
?>
<?php include("engine/layout/footer.php"); ?>
	</div>
	