
	<paralax class="hidden-sm">
		<div src="img/page/<?=$menupath[1]?>.jpg" apply="none"></div>
	</paralax>

	<div class="circleBox hidden-sm">
		<div id="circle">
			<div class="center">
				<ul class="jump">
<?php 
if ($result = $sql->query("SELECT * FROM MenuTree where Parent = 0 and Active = 1 order by Sort")) {
	while ($row = $result->fetch_assoc()) {
		printf("\t\t\t\t\t\t<li><a jump=\"%s\" href=\"#\">%s</a></li>",$row['Class'],$row['Name'.$nameadd]);
	}
} 
?>
				</ul>
			</div>
		</div>
	</div>

	<div id="content" class="home mCustomScrollbar _mCS_1 hidden-sm" style="overflow: visible">
		<div class="menu" id="menu" type="paralax">
			<div id="circleContent">
				<ul>
<?php 
if ($result = $sql->query("SELECT * FROM MenuTree where Parent = 0 and Active = 1 order by Sort")) {
	$acad = " active";
	while ($row = $result->fetch_assoc()) {
		$part = $row['Id'];
		echo <<<END
					<li class="mainContent ${row['Class']}$acad" >
						<div class="mainTitle">
							<span>${row['Name'.$nameadd]}</span>
						</div>
						<div class="container">
							<div class="content">
								<div id="tabs-${row['Id']}" type="tabsPanel" class="tabsHead">
									<ul>

END;
		$acad = "";
		if ($result1 = $sql->query("SELECT * FROM MenuTree where Parent = ".$row['Id']." and Active = 1 order by Sort")) {
			$act = " class=\"active\"";
			$menuarr = array();
			while ($row1 = $result1->fetch_assoc()) {
				printf("\t\t\t\t\t\t\t\t\t\t<li%s>%s</li>\n",$act,$row1['Name'.$nameadd]);
				$menuarr[] = array($row1['Id']);
				$act = '';
			}
			//print_r($menuarr);
		}
echo <<<END
									</ul>
								</div>
								<div class="tabsBody" tabsbody="tabs-${row['Id']}">

END;
			$aact = " active";
			foreach($menuarr as $m){
echo <<<END
									<div class="tab${aact}">
										<div type="tabsPanel" id="tabs-${m[0]}" class="leftBox">
											<ul class="tabsHeadVertical">

END;
			if ($result2 = $sql->query("SELECT * FROM MenuTree where Parent = ".$m[0]." and Active = 1 order by Sort")) {
				$act2 = " class=\"active\"";
				$submenuarr = array();
				while ($row2 = $result2->fetch_assoc()) {
					printf("\t\t\t\t\t\t\t\t\t\t\t\t<li%s>%s</li>\n",$act2,$row2['Name'.$nameadd]);
					$submenuarr[] = array($row2['Id'],$row2['EnShow']);
					$act2 = '';
				}
			}
			$dl = $menupath[0] == 14?"Download Menu":"Скачать меню";
echo <<<END
											</ul>
											<div class="download">
												<a href="menu/menu_uzbek${nameadd}.pdf" style='color:#966'>$dl</a>
											</div>
										</div>
										<div tabsbody="tabs-${m[0]}" class="rightBox">

END;
			$sact = " active";
			foreach($submenuarr as $s){
	
echo <<<END
											<div class="tab${sact}">
												<div class="priseTable">

END;
						postCatHeader($sql,$s[0],$s[1]);
						
echo <<<END
												</div>
											</div>

END;
				$sact = "";
			}
echo <<<END
										</div>
									</div>

END;
			$aact = "";
		}
echo <<<END
								</div>
							</div>
						</div>
					</li>		
END;
	}
} 
?>				
				</ul>
			</div>
			<div id="circleBar" class="hidden-sm">
				<ul>
<?php
if ($result = $sql->query("SELECT * FROM MenuTree where Parent = 0 order by Sort")) {
	$pos = array(1=>'left',2=>"right");
	while ($row = $result->fetch_assoc()) {
echo <<<END
					<li type="circleMenu" id="${row['Class']}" pos='${pos[$row['Id']]}'>
						<div class="circle">
							<a hook href="#"></a>
							<div class="titleBox">
								<div class="circleTitle" style="background: url(img/circle/${row['ClassMenu']}${nameadd}.svg);"></div>
								<div class="title">${row['NameLong'.$nameadd]}</div>
							</div>
							<div class="image">
								<img src="img/menu/${row['ClassMenu']}.jpg">
							</div>
						</div>
					</li>
END;
// class="active"
	}
} 
?>			
				</ul>
			</div>
		</div>
<?php include("engine/layout/footer.php"); ?>
	</div>

	<div id="content" class="home visible-sm visible-xs">
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias laudantium amet officia error tempora omnis ipsa corporis maiores non facilis, officiis nobis quasi illum doloribus, sequi voluptatem libero blanditiis et voluptatum ipsam quia. Ut illo, maxime, est iure ullam consequatur nobis. Suscipit dicta impedit eveniet nostrum porro ducimus, quisquam obcaecati assumenda accusamus sint culpa architecto earum molestiae? Corrupti, quis nesciunt in ab. Molestiae ullam eveniet, tempora repellat libero, vero obcaecati animi repellendus. Odio architecto nesciunt ipsum a, mollitia vitae nemo velit eligendi commodi pariatur consequatur consectetur saepe. Ratione aut harum, ipsam nostrum eligendi, adipisci explicabo quos, deserunt soluta quod assumenda.
	</div>


<?php 
function postMenuPos($sql,$parent,$part){
	global $nameadd;
	if ($result = $sql->query("SELECT * FROM MenuItems where Parent = $parent and Active = 1 order by Sort")) {
		while ($row = $result->fetch_assoc()) {
			if($part == 1 && $row['NameEn'] != "" && $nameadd == ""){ 
				$row['Name'] = $row['NameEn']."/".$row['Name'];
				$row['Weight'] .= $row['Weight']==""?"":" ml";
			}elseif($nameadd == "En" && $part == 1){
				$row['Weight'] .= $row['Weight']==""?"":" ml";
			}
			if($row['Name'.$nameadd] == "")
				$name = $row['Name'];
			else 
				$name = $row['Name'.$nameadd];
				
			if($row["Weight"] != "" && $row['Weight'] != " ml")$row["Weight"] = "(".$row["Weight"].")";
echo <<<END
													<div class="row">
														<div class="con">
															<div class="name">${name}
																<span class='weight'>${row['Weight']}</span>
															</div>
															<div class="desc">${row['Descr'.$nameadd]}</div>
														</div>
														<div class="prise">${row['Price']}</div>
													</div>

END;
	if(file_exists('img/menupic/'.$row['Id'].".jpg"))
		echo <<<END
												<div class="image">
													<img src="img/menupic/${row['Id']}.jpg">
												</div>
END;
		}
	}
}
function postCatHeader($sql,$parent,$part){
	global $nameadd;
	if ($result = $sql->query("SELECT * FROM MenuTree where Parent = ".$parent." and Active = 1 order by Sort")) {
		if($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				if($nameadd == ""):
				echo <<<END
								 					<div class="weaning">
														${row['NameEn']}/${row['Name']}
													</div>

END;
				else:
				echo <<<END
								 					<div class="weaning">
														${row['NameEn']}
													</div>

END;
				endif;
				postCatHeader($sql,$row['Id'],$row['EnShow']);
				//postMenuPos($sql,$row['Id'],$row['EnShow']);
			}
		}else{
			postMenuPos($sql,$parent,$part);
		}
	}
}
?>