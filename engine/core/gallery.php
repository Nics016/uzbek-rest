			<div class="blockBox">
				<div class="leftSide">
					<div class="textContainer">
						<div class="textBox">
							<div class="tabsHeadGallery" id="totalGallery">
								<div class="title"><?=$name?></div>
								<ul>
<?php 
$galarr = array();
if ($result = $sql->query("SELECT * FROM GalleryTree where GroupId = 1 order by Sort")) {
	while ($row = $result->fetch_assoc()) {
		printf("\t\t\t\t\t\t\t\t\t<li><a attitude=\"gallery-%d\" href=\"#\">%s</a></li>\n",$row['Id'],$row['Name'.$nameadd]);
		if ($res = $sql->query("SELECT * FROM GalleryImg where Parent = ".$row['Id']." AND Active = 1 order by Sort")) {
			$imgarr = array();
			while ($rw = $res->fetch_assoc()) {
				if($rw['Main'] == 1) 
					$imgmain = $rw['Id'];
				else
					$imgarr[] = array($rw['Id'],$rw['Name'.$nameadd],$rw['Descr']);
			}
		}
		$tmparray = array($row['Id'],$row['Name'.$nameadd],$imgarr,$imgmain);
		$galarr[] = $tmparray;
	}
}
?>
								</ul>
							</div>
							<div class="tour">
								<div class="title"><?=$menupath[0] == 14?"Restaurant 3D-Tour":"3D-тур по ресторану"; ?></div>
								<div id="tour">
									<iframe width="340" height="240" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ru/maps?q=%D1%80%D0%B5%D1%81%D1%82%D0%BE%D1%80%D0%B0%D0%BD+%D1%83%D0%B7%D0%B1%D0%B5%D0%BA%D0%B8%D1%81%D1%82%D0%B0%D0%BD&amp;layer=c&amp;sll=55.765674,37.620061&amp;cid=5870756882787766553&amp;panoid=mDWscxnhG-gAAAQJOHFU2Q&amp;cbp=13,294.43,,0,0&amp;ie=UTF8&amp;hq=&amp;hnear=&amp;ll=55.765674,37.620061&amp;spn=0.006295,0.006295&amp;t=m&amp;cbll=55.765905,37.62016&amp;source=embed&amp;output=svembed"></iframe>
									<a href="#" onclick="return hs.htmlExpand(this, {width: 800})"><?=$menupath[0] == 14?"zoom":"увеличить"; ?></a>
									<div class="highslide-maincontent">
										<iframe width="799" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ru/maps?q=%D1%80%D0%B5%D1%81%D1%82%D0%BE%D1%80%D0%B0%D0%BD+%D1%83%D0%B7%D0%B1%D0%B5%D0%BA%D0%B8%D1%81%D1%82%D0%B0%D0%BD&amp;layer=c&amp;sll=55.765674,37.620061&amp;cid=5870756882787766553&amp;panoid=mDWscxnhG-gAAAQJOHFU2Q&amp;cbp=13,294.43,,0,0&amp;ie=UTF8&amp;hq=&amp;hnear=&amp;ll=55.765674,37.620061&amp;spn=0.006295,0.006295&amp;t=m&amp;cbll=55.765905,37.62016&amp;source=embed&amp;output=svembed"></iframe>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="rightSide" >
					<div class="tabsBodyGallery" type="tabs" hook="tabs-1">
						<div class="tab active">
							<div class="gallery">
<?php 
foreach($galarr as $g){
echo <<<END
								<div class="gImage">
									<div class="highslide-gallery">
										<a id="gallery-${g[0]}" href="/img/gallery/${g[3]}.jpg" attitude="gallery-${g[0]}" class="highslide" onclick="return hs.expand(this, config_${g[0]})">
											<img src="/img/gallery/${g[3]}.jpg" alt="Highslide JS" title="Click to enlarge" />
											<span class="description"><span>${g[1]}</span></span>
										</a>
										<div class="hidden-container">
END;
	foreach($g[2] as $im){
echo <<<END
											<a href="/img/gallery/${im[0]}.jpg" class="highslide" onclick="return hs.expand(this, { thumbnailId: 'gallery-${g[0]}', slideshowGroup: ${g[0]} })">
												<img src="/img/gallery/${im[0]}.jpg" alt="Highslide JS" title="Click to enlarge" />
											</a>
END;
	}
echo <<<END
										</div>
									</div>
								</div>

END;
}
?>
							</div>
						</div>
					</div>
				</div>
			</div>