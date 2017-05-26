<?
$con = mysql_connect('localhost','COMMON',"COmmON") or die('Connect');
mysql_select_db("COMMON");
$date = sprintf("%s",date ("Y-m-d"));
$query = "select * from Currency where Date <= '$date' order by Date desc limit 1;";
$result = mysql_query($query);
if($result){
	$row = mysql_fetch_array($result);
	$commondata["USD"] = $row["Usd"];
	$commondata["EUR"] = $row["Eur"];
}else{
	printf("In query to DataBase '%s' detected error: <Font color=red><b>%s</b></font>, please contact <a href=\"mailto:anton@shustow.com?subject='%s at %s'\">system administrator</a>", $query,mysql_error(),mysql_error(),$query);
}
$query = "select * from Weather where Date <= '$date' order by Date desc limit 1;";
$result = mysql_query($query);
if($result){
	$row = mysql_fetch_array($result);
	$commondata["NightTemp"] = $row["NightTemp"];
	$commondata["DayTemp"] = $row["DayTemp"];
	$commondata["Sky"] = $row["Sky"];
}else{
	printf("In query to DataBase '%s' detected error: <Font color=red><b>%s</b></font>, please contact <a href=\"mailto:anton@shustow.com?subject='%s at %s'\">system administrator</a>", $query,mysql_error(),mysql_error(),$query);
}
mysql_close($con);
?>