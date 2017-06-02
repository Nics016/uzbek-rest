			<div class="slider">
				<div class="sliderBox">
<?php 
if(!$slider)$slider = 0;
$slider++;

$result = $sql->query("SELECT * FROM Content where Parent = $parent and Active = 1 order by Sort");
$numrows = $result->num_rows;
if($numrows > 4){
?>
				
					<div class="item">
						<div class="blockBox">
				
<?php 
$sliderarr = array();
$menucnt = "";
	while ($row = $result->fetch_assoc()) {
		$menucnt .= sprintf("\t\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"#\">%s</a></li>\n",$row['MenuName']);
	}
			$text = <<<END
								<div class="textContainer">
									<div class="textBox">
										<div class="title">${name}</div>
										<div class="contentsList">
											<ul>
												${menucnt}
											</ul>
										</div>
									</div>
								</div>
END;
			$img = <<<END
								<div class="image">
									<img src="/img/page/${parent}.jpg">
								</div>
END;
		if($slider/2 == floor($slider/2)){
			$block1 = $text;
			$block2 = $img;
		}else{
			$block2 = $text;
			$block1 = $img;
		}
?>
							<div class="leftSide">
								<?=$block1?>
							</div>
							<div class="rightSide">
								<?=$block2?>
							</div>
						</div>
					</div>


<?php 
}
if ($result = $sql->query("SELECT * FROM Content where Parent = $parent and Active = 1 order by Sort")) {
	while ($row = $result->fetch_assoc()) {
echo <<<END
					<div class="item">
						<div class="blockBox">

END;
		if($row['Mech'] == ""){
		$text = <<<END
								<div class="textContainer">
									<div class="textBox">
										<div class="title">${row['MenuName']}</div>
										<div class="text">${row['Content']}</div>
END;
		if($numrows > 2 && $menupath[0] != 14)$text .= "										<a href=\"#\" class=\"early\">в начало</a>";
		elseif($numrows > 2 && $menupath[0] == 14)$text .= "										<a href=\"#\" class=\"early\">back</a>";
		$text .= <<<END
									</div>
								</div>		
END;
		$img = <<<END
								<div class="image">
									<img src="/img/page/${row['Id']}.jpg">
								</div>
END;
		if($slider/2 == floor($slider/2)){
			$block1 = $text;
			$block2 = $img;
		}else{
			$block2 = $text;
			$block1 = $img;				
		}
echo <<<END
							<div class="leftSide">
${block1}
							</div>
							<div class="rightSide">
${block2}
							</div>

END;
		}else{
			include("engine/core/".$row['Mech'].".php");
		}
echo <<<END
						</div>
					</div>

END;
	}
}
?>
				</div>
				<div class="dots"></div>
				<div class="backward">
					<a href="#"></a>
				</div>
				<div class="forward">
					<a href="#"></a>
				</div>
			</div>
			