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
								<div class="circleTitle" style="background: url(../../img/circle/${row['ClassMenu']}${nameadd}.svg);"></div>
								<div class="title">${row['NameLong'.$nameadd]}</div>
							</div>
							<div class="image">
								<img src="../../img/menu/${row['ClassMenu']}.jpg">
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
	<!-- END OF DESKTOP-CONTENT -->

<!-- MOBILE-CONTENT -->
	<div id="content" class="home visible-sm visible-xs">
		<?php 
			// Главная категория
			if ($result = $sql->query("SELECT * FROM MenuTree where Parent = 0 and Active = 1 order by Sort")):
				// Get data
				$categories = array();
					while ($row = $result->fetch_assoc()):
						$curCategory = array();
						$curCategory["Name"] = $row["Name"];
						$curCategory["Id"] = $row["Id"];
						$curCategory["subcats"] = array();
						// Подкатегория
						$curCatId = $row["Id"];
					 	if ($resultScat = $sql->query("SELECT * FROM MenuTree where Parent = $curCatId and Active = 1 order by Sort")):
								$subcats = array();
							while ($rowScat = $resultScat->fetch_assoc()):
								$subcat = array();
								$subcat["Name"] = $rowScat["Name"];
								$subcat["Id"] = $rowScat["Id"];
								// Сорты подкатегорий
								$subcat["dishes"] = getSubcatChilds($sql, $subcat, $curCatId);
								
								$subcats[] = $subcat;
								$curCategory["subcats"] = $subcats;
							endwhile; // Подкатегории
						endif; // Подкатегория

						$categories[] = $curCategory;
					endwhile; // Категория
				endif; // Категория

				/**
				 * Рекурсивно получает все подкатегории данной категории и возвращет их.
				 * Также находит блюда и присваивает их в $subcat["dishes"].
				 */
				function getSubcatChilds($sql, &$subcat, $curCatId){
					$curId = $subcat["Id"];
					$childs = false;

					if ($result = $sql->query("SELECT * FROM MenuTree where Parent = $curId and Active = 1 order by Sort")):
						$childs = array();
						while ($row = $result->fetch_assoc()):
							$child = array();
							$child["Id"] = $row["Id"];
							$child["Name"] = $row["Name"];
							$child["childs"] = getSubcatChilds($sql, $child, $curCatId);
							$childs[] = $child;
						endwhile; // childs
					endif; // childs

					$subcat["items"] = getCatItems($sql, $subcat["Id"], $curCatId);

					return $childs;
				}

				function getCatItems($sql, $curId, $curCatId){
					// Блюда
					$items = false;
					if ($resultItem = $sql->query("SELECT * FROM MenuItems where Parent = $curId and Active = 1 order by Sort")):
						$items = array();
						while ($rowItem = $resultItem->fetch_assoc()):
							$item = array();
							$item["Price"] = $rowItem["Price"];
							$item["Descr"] = $rowItem["Descr"];
							$item["Weight"] = $rowItem["Weight"];
							// Kitchen
							if ($curCatId == 1){
								$item["Name"] = $rowItem['Name'];
							}
							else{
							// Bar 
								if ($rowItem["NameEn"])
									$item["Name"] = $rowItem['NameEn']." / ".$rowItem['Name'];
								else
									$item["Name"] = $rowItem["Name"];
								$item['Weight'] .= $rowItem['Weight']==""?"":" ml";
							}

							$items[] = $item;
						endwhile; // Menu Item
						$dish["items"] = $items;
					endif; // Menu Item
					return $items;
				}

				function outputDishes($dishes, $children = false){
					foreach($dishes as $dish):
						if (!$children){ // dishes-tab_item
							echo '<div class="dishes-tab_item">';
						} else {
							echo '<strong>' . $dish['Name'] . '</strong>';
						}
						if (!$dish["childs"]):		    				
		    					foreach($dish["items"] as $item):
		    						echo '<div class="item-box clearfix">';
		        						echo '<div class="left">';
			        						echo '<span class="item-name">' . $item["Name"] . '</span>';
			        						echo '<span class="item-weight">' . $item["Weight"] . '</span>';
			        						echo '<span class="item-descr">' . $item["Descr"] . '</span>';
		        						echo '</div>';
		        						echo '<div class="right">';
		        							echo '<span class="item-price">' . $item["Price"] . '</span>';
		        						echo '</div>';
		    						echo '</div>';
		    					endforeach;		    				
		    			else:
		    				outputDishes($dish["childs"], true);
		    			endif; // has childs
		    			if (!$children){ // end of dishes-tab_item
							echo '</div>';
						}
			        endforeach;
			    }
			 ?>
			
			<?php // Output data ?>
			<div class="categories-wrapper">
			<div id="bar"></div>
			<div id="kitchen"></div>
			    <div class="cat-tabs">
			    	<?php foreach($categories as $cat): ?>
			        	<span class="cat-tab"><?= $cat["Name"] ?></span>  
			        <?php endforeach; ?>     
			    </div>
			    <div class="cat-tab_content">
			    	<?php foreach($categories as $cat): ?>
				        <div class="cat-tab_item">
				        	<div class="subcategories-wrapper">
							    <div class="subcat-tabs">
							    	<?php foreach($cat["subcats"] as $subcat): ?>
							        	<span class="subcat-tab"><?= $subcat["Name"] ?></span>  
							        <?php endforeach; ?>     
							    </div>
							    <div class="subcat-tab_content">
							    	<?php foreach($cat["subcats"] as $subcat): ?>
								        <div class="subcat-tab_item">
								        	<div class="dishes-wrapper clearfix">
								        		<div class="dishes-tabs">
								        			<?php foreach($subcat["dishes"] as $dish): ?>
								        				<div class="dishes-tab"><?= $dish["Name"] ?></div>
										        	<?php endforeach; ?>
								        		</div>
								        		<div class="dishes-tab_content">	        		
									        		<?php outputDishes($subcat["dishes"]) ?>
										        </div>
								        		<!-- /.dishes-tab_content -->
								        	</div>
								        	<!-- /.dishes-wrapper -->
								        </div>
								        <!-- subcategories /.tab_item -->
							        <?php endforeach; ?>  
							    </div>
							    <!-- subcategories /.tab_content -->
							</div>
				        </div>
				        <!-- categories /.tab_item -->
			        <?php endforeach; ?>  
			    </div> 
			    <!-- categories /.tab_content -->
			</div>
			
			<script>
				// SCRIPT FOR TABS
				// generate count arrays
				var subcats_count = [];
				var dishes_count = [];
				var cur_cat = 0;
				var cur_subcat = 0;

				<?php foreach($categories as $cat): ?>
					subcats_count.push(<?= count($cat["subcats"]) ?>);
					var cur_dishes_count = [];
					var cur_count = 0;
					<?php foreach($cat["subcats"] as $subcat): ?>
						cur_count += <?= count($subcat['dishes']) ?>;
						var push_count = cur_count;
						cur_dishes_count.push(push_count);
					<?php endforeach; // subcats ?>
					dishes_count.push(cur_dishes_count);
				<?php endforeach; // cats ?>

				// categories
				$(".categories-wrapper .cat-tab_item").not(":first").hide();
				$(".categories-wrapper .cat-tab").click(function() {
					$(".categories-wrapper .cat-tab").removeClass("active").eq($(this).index()).addClass("active");
					$(".categories-wrapper .cat-tab_item").hide().eq($(this).index()).fadeIn(1000);
					// click on first subcat to init
					cur_cat = $(this).index();
					$(".subcat-tab").eq(subcats_count[cur_cat]).click().addClass("active");
				}).eq(0).click().addClass("active");

				// subcategories
				$(".subcat-tab_item").not(":first").hide();
				$(".subcat-tab").click(function() {
					$(".subcat-tab").removeClass("active").eq($(this).index()).addClass("active");
					$(this).addClass("active");
					$(".subcat-tab_item").hide().eq($(this).index()).fadeIn(300);
					$(".subcat-tab_item").eq($(this).index()+4).fadeIn(300);
					cur_subcat = $(this).index();			
					if (cur_cat == 0 && cur_subcat == 0)		
						$(".dishes-tab").eq(0).click().addClass("active");
					else{
						// Kitchen
						if (cur_cat == 0){
							$(".dishes-tab").eq(dishes_count[cur_cat][cur_subcat-1]).click().addClass("active");
						} else{ // Bar
							if (cur_subcat === 0)
								$(".dishes-tab").eq(16).click().addClass("active");
							else if (cur_subcat === 1)
								$(".dishes-tab").eq(22).click().addClass("active");
							else if (cur_subcat === 2)
								$(".dishes-tab").eq(38).click().addClass("active");
							else if (cur_subcat === 3)
								$(".dishes-tab").eq(41).click().addClass("active");
						}
					}
				}).eq(0).click().addClass("active");

				// dishes
				$(".dishes-tab_item").not(":first").hide();
				$(".dishes-tab").click(function() {
					$(".dishes-tab").removeClass("active").eq($(this).index());
					$(this).addClass("active");
					$(".dishes-tab_item").hide().eq($(this).index()).fadeIn(100);
					var plusIndex = dishes_count[cur_cat][cur_subcat-1];
					// for Bar
					if (cur_cat == 1){
						plusIndex += 16;
						// for Bar / Wine
						if (cur_subcat == 0)
							plusIndex = 16;
					}
					// $("#debugging").html(plusIndex);
					$(".dishes-tab_item").eq($(this).index()+plusIndex).fadeIn(500);
				});
			</script>

		 </div>

		</div>
		<!-- END OF CATEGORY-WRAPPER -->

		  <br> <br>
	</div>
	<!-- END OF MOBILE-CONTENT -->