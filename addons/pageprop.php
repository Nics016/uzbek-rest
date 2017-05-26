<tr><td>Изображение для раздела</td><td><?
$pic = sprintf("%simg/page/%d.jpg", $GLOBALS['homedir'], $row["Id"]);
	    if (file_exists($pic)) {
		?>
		<img src="http://<?=$GLOBALS['url']?>/img/page/<?=$row['Id']?>.jpg" style='max-width:200px;max-height:200px'/>
		<br />
    	<br><a href="#" onClick="if(confirm('Удалить?')){self.location.href='write.phtml?writestatus=unlinkfile&param[path]=img/page&param[file]=<?=$row['Id']?>.jpg';}else{alert('Отменено!');}" style='color:red'>удалить файл &gt;&gt;</a>
		<?
	    } else {
			print("<center>нет картинки</center>");
	    }
?></td></tr>
<?php 	printf("<tr><td>Новая картинка</td><td><input type=file size=50 name=param[pageimage]></td></tr>");?>