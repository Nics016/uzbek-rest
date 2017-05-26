$query = 'set character_set_client=cp1251;';
$result = mysql_query($query);
if(!$result){
	printf("<Font color=red><b>Detected ERROR %s</b></font> in query '%s'.", mysql_error(),$query);
}
$query = 'set character_set_connection=cp1251;';
$result = mysql_query($query);
if(!$result){
	printf("<Font color=red><b>Detected ERROR %s</b></font> in query '%s'.", mysql_error(),$query);
}
$query = 'set character_set_results=cp1251;';
$result = mysql_query($query);
if(!$result){
	printf("<Font color=red><b>Detected ERROR %s</b></font> in query '%s'.", mysql_error(),$query);
}
