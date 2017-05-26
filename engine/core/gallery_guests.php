						<div class="leftSide">
							<div class="textContainer">
								<div class="textBox">
									<div class="title"><?=$name?></div>
									<div id="change" class="change">
										<ul>
<?php 
if ($res = $sql->query("SELECT * FROM GalleryImg where Parent = 5 AND Active = 1 order by Sort")) {
	$imgarr = array();
	$act = " class=\"active\"";
	while ($row = $res->fetch_assoc()) {
		if(!$defid)$defid=$row['Id'];
echo <<<END
											<li>
												<a href="/img/gallery/${row['Id']}.jpg"${act}>
												<div class="name">${row['Name'.$nameadd]}</div>
												<div class="desc">${row['Descr'.$nameadd]}</div>
												</a>
											</li>

END;
		$act = "";
	}
}
?>

										</ul>
									</div>
									<a href="#" class="early"><?=$menupath[0] == 14?"back":"а начало"; ?></a>
								</div>
							</div>
						</div>
						<div class="rightSide">
							<div id="changeScene" class="changeScene">
								<div class="background"></div>
								<div class="image">
									<img src="/img/gallery/<?=$defid ?>.jpg">
								</div>
							</div>
						</div>