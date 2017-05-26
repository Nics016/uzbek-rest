<?php
$cnt = 0;
$glpr = 0.0;

if (isset($_COOKIE["sessionid"])) {
	$ctime = sprintf("%s",date ("YmdHis"));
	$query = "select * from Cart where SessionId = '{$_COOKIE["sessionid"]}' and Expire > '$ctime';";
	$result = mysql_query($query);
	if($result){
		while($row = mysql_fetch_array($result)){
			$cnt  += $row["Count"];
			//$glpr += GetPrice($row["ItemId"], @$cur) * $row["Count"];
		}
	}else{
		SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
	}
}
?>
Товаров в <a href="/?state=cart">корзине</a>: <?php echo $cnt ?>