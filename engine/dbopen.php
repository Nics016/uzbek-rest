<?
extract($_REQUEST, EXTR_SKIP);
mysql_connect('localhost','root','') or die('Connect');
mysql_select_db('uzbekistan') or die('Select');
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'cp1251', character_set_server = 'utf8'");
$query = 'select * from Prop;';
$result = mysql_query($query);
if($result){
	while($row = mysql_fetch_array($result)){
		$nm = $row["Name"];
		$val = str_replace("&#039;","'",$row["Value"]);
		$$nm = $val;
	}
}else{
	printf("<Font color=red><b>Detected ERROR %s</b></font> in query '%s'.", mysql_error(),$query);
}
$sql = new mysqli("localhost", "uzbekistan", "2038urjfeir3", "uzbekistan");
if ($sql->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}
$sql->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'cp1251', character_set_server = 'utf8'");

?>