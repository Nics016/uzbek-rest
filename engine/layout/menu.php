
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

	<!-- DESKTOP-CONTENT -->
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
	<!-- END OF DESKTOP-CONTENT -->

	<!-- MOBILE-CONTENT -->
	<div id="content" class="home visible-sm visible-xs">
		<?php 
			// Главная категория
			if ($result = $sql->query("SELECT * FROM MenuTree where Parent = 0 and Active = 1 order by Sort")):
				// Get data
				$categories = [];
					while ($row = $result->fetch_assoc()):
						$curCategory = [];
						$curCategory["Name"] = $row["Name"];
						$curCategory["Id"] = $row["Id"];
						$curCategory["subcats"] = [];
						// Подкатегория
						$curCatId = $row["Id"];
					 	if ($resultScat = $sql->query("SELECT * FROM MenuTree where Parent = $curCatId and Active = 1 order by Sort")):
								$subcats = [];
							while ($rowScat = $resultScat->fetch_assoc()):
								$subcat = [];
								$subcat["Name"] = $rowScat["Name"];
								$subcat["Id"] = $rowScat["Id"];
								$subcat["dishes"] = [];
								// Блюда
								$curId = $rowScat["Id"];
								if ($resultBl = $sql->query("SELECT * FROM MenuTree where Parent = $curId and Active = 1 order by Sort")):
										$dishes = [];
									while ($rowBl = $resultBl->fetch_assoc()):
										$dish = [];
										$dish["Name"] = $rowBl["Name"];
										$dish["Id"] = $rowBl["Id"];
										$dishes[] = $dish;
									endwhile; // Блюда 
										$subcat["dishes"] = $dishes;
								endif; // Блюда
								$subcats[] = $subcat;
								$curCategory["subcats"] = $subcats;
							endwhile; // Подкатегории
						endif; // Подкатегория

						$categories[] = $curCategory;
					endwhile; // Категория
				endif; // Категория
			 ?>

			<?php // Output data ?>
			<div class="categories-wrapper">
			    <div class="tabs">
			    	<?php foreach($categories as $cat): ?>
			        	<span class="tab"><?= $cat["Name"] ?></span>  
			        <?php endforeach; ?>     
			    </div>
			    <div class="tab_content">
			    	<?php foreach($categories as $cat): ?>
				        <div class="tab_item">
				        	<?php foreach($cat["subcats"] as $subcat): ?>
								<?= $subcat["Name"] ?>
				        	<?php endforeach; ?>
				        </div>
			        <?php endforeach; ?>  
			    </div>
			</div>
			<script>
				$(".categories-wrapper .tab_item").not(":first").hide();
				$(".categories-wrapper .tab").click(function() {
					$(".categories-wrapper .tab").removeClass("active").eq($(this).index()).addClass("active");
					$(".categories-wrapper .tab_item").hide().eq($(this).index()).fadeIn()
				}).eq(0).addClass("active");
			</script>
			
		 </div>

		</div>
		<!-- END OF CATEGORY-WRAPPER -->

		  <br> <br>
	</div>
	<!-- END OF MOBILE-CONTENT -->


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