<?php
function CheckIp($ip){
    $con = mysql_connect('localhost','all',"") or die('Connect');
    mysql_select_db("Common") or die("Select");
    $lev2 = explode(".",$ip);
    $dig = $lev2[0]*16777216+$lev2[1]*65536+$lev2[2]*256+$lev2[3];
    //	print $dig . "-".$ip;
    $que = "select * from RussianNets where Start <= $dig AND End >= $dig;";
    $res = mysql_query($que);
    mysql_close($con);
    if($res){
        if(mysql_num_rows($res) > 0) {
            return true;
        } else {
            return false;
        }
    }else {
        SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
    }
}
?>