<table cellspacing=5 width=100% height=100%><tr><td valign=middle>
<?php

$searchword = str_replace("'","",trim(request('searchword')));
if ($searchword) {
    if (strlen($searchword) < 3) {
        print("<center>Искомое слово не может быть короче 3-х символов</center>");
    } else {
        $searchword = strtolower($searchword);
        $query = sprintf("SELECT Comment, Content, Id FROM Content WHERE Content LIKE '%%%s%%'", $sql->esc($searchword, true));
        $result = mysql_query($query);
        if ($result) {
            $num = mysql_num_rows($result);
            if ($num > 0) {
                printf("<center><font size=+1>По вашему запросу - '%s' найдено совпадений - <b>%d</b>:</font><br></center>", $searchword, $num);
                $cc = 0;
                while($row=mysql_fetch_array($result)){
                    if($cc == 1){
                        print("<hr width=100% style=\"color:#000000\" size=1>");
                    }
                    $cc = 1;

                    //$tstr = strip_tags(str_replace("[","<",str_replace("]",">",$row["Content"])));
                    $tstr = $row["Content"];
                    $pos = strpos(Strtolower($tstr), $searchword);
                    if($pos === false) {
                    }else{
                        printf("<a href=\"/?pageId=%d\">%s</a><br>\n",$row["Id"],$row["Comment"]);
                        if($pos < 100){
                            $str1 = substr($tstr,0,$pos);
                        }else{
                            $str1 = substr($tstr,$pos-100,100);
                        }
                        if($pos+100 > strlen($tstr)){
                            $str3 = substr($tstr,$pos+strlen($searchword),strlen($tstr)-$pos);
                        }else{
                            $str3 = substr($tstr, $pos + strlen($searchword), 100);
                        }
                        printf("...%s<b>%s</b>%s...<br><br>",$str1,substr($tstr,$pos,strlen($searchword)),$str3);
                        $found=1;
                    }
                }
            }else{
                printf("<center><font size=+1>Извините, но по вашему запросу ничего не найдено</font>");
            }
        }else{
            SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
        }
    }
} else {
    echo '<form>';
    echo '<input type="hidden" name="state" value="search">';
    echo '<label>Введите слово для поиска</label><br>';
    echo '<input type="text" name="searchword"><br>';
    echo '<input type="submit" value="искать"><br>';
}
?>
</td></tr></table>
