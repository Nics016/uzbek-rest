<table width=100% height=100% class=main cellspacing=10><tr><td><?
function utf8win1251($s){
        $out="";
        $c1="";
        $byte2=false;
        for ($c=0;$c<strlen($s);$c++){
                $i=ord($s[$c]);
                if ($i<=127) $out.=$s[$c];
                if ($byte2){
                        $new_c2=($c1&3)*64+($i&63);
                        $new_c1=($c1>>2)&5;
                        $new_i=$new_c1*256+$new_c2;
                        if ($new_i==1025) $out_i=168; else
                        if ($new_i==1105) $out_i=184; else $out_i=$new_i-848;
                        $out.=chr($out_i);
                        $byte2=false;
                }
                if (($i>>5)==6) {
                        $c1=$i;
                        $byte2=true;
                }
        }
        return $out;
}
function ShowP($Id){
	$mysqlIds = mysql_connect('localhost',"SiteAdmin","ghTcvi3435GFc58hHk",true) or die("Connect");
	mysql_select_db('backoffice',$mysqlIds);
	$query = "select * from Preview where Id = '$Id';";
//	print $query;
	$result = mysql_query($query);
	if($result){
		$row = mysql_fetch_array($result);
		print utf8win1251($row["Prev"]);
	}else{
		printf("In query to DataBase '%s' detected error: <Font color=red><b>%s</b></font>, please contact <a href=\"mailto:anton@shustow.com?subject='%s at %s'\">system administrator</a>", $query,mysql_error(),mysql_error(),$query);
	}
	mysql_close($mysqlIds);
}
ShowP($Id);
include("engine/dbopen.php");
?></td></tr></table>
