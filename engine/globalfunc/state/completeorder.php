<?php
function itemData($id, $part) {
    global $sql, $dsn;
    if (Config::val('BASE_DB')) {
        mysql_select_db(Config::val('BASE_DB'));
    }
    $price = 0;
    $name  = '';
    $item = $sql->select('Assort', array('Id' => $id), Sql::ONE);
    if (! empty($item)) {
        $price  = $item['Price'];
        $prices = $sql->select('Prices', array('Id' => $part), Sql::ONE);
        if (! empty($prices['Price'])) {
            $price = $prices['Price'];
        }
        $tree = $sql->select('Tree', array('Id' => $item['Part']), Sql::ONE);
        $name  = sprintf('%s %s %s', $tree['Name'], $item['Name'], $prices['Name']);
    }
    if (Config::val('BASE_DB')) {
        mysql_select_db(substr($dsn->path, 1));
    }
    return array('name' => $name, 'price' => $price);
}

$ctime = date ("YmdHis");
$sessionId = (empty($_COOKIE['sessionid'])) ? 0 : fileEsc($_COOKIE['sessionid']);
$query     = "select * from Cart where SessionId = '$sessionId' and Expire > $ctime";
$result    = mysql_query($query);
if ($result) {
    $numm = mysql_num_rows($result);
}else{
    SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
}
if($numm > 0){
    $f   = new Form($_REQUEST);
    $msg = 	sprintf("<center><font face=\"Verdana\" size=3>Заказ</font><br><br>");
    printf("<center><b>Заказ</b><br><br>");
    $msg .= sprintf("<center><table cellspacing=0 cellpadding=3 border=1><tr><td width=40 align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\"><b>№</b></td><td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\">Наименование</td><td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\" width=80>Цена</td><td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\" width=60>Количество</td><td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\" width=60>Сумма</td></tr>");
    print('<table class="orderData"><tr><th>№</th><th>Наименование</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr>');
    $query = "select * from Cart where SessionId = '".$_COOKIE["sessionid"]."' and Expire > $ctime;";
    $result = mysql_query($query);
    if($result){
        $i=0;
        $summ = 0;
        $countt = 0;
        while($row = mysql_fetch_array($result)){
            $i++;
            $item = itemData($row['ItemId'], $row["Part"]);
            $itemOut  = sprintf("<tr><td>%d</td>", $i);
            $itemOut .= sprintf('<td>%s</td>', $item['name']);
            $itemOut .= sprintf('<td>%.2f руб.</td>
        	        <td>%d шт.</td>
        	        <td>%.2f руб.</td>
        	        </tr>', $item["price"], $row['Count'], $item["price"] * $row['Count']);
            echo $itemOut;
            $msg .= $itemOut;
            $countt += $row["Count"];
            $summ   += $item["price"] * $row["Count"];
        }
    }else{
        SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
    }
    if (Config::val('BASE_DB')) {
        mysql_select_db(Config::val('BASE_DB'));
    }
    $query = "select * from Delivery where Id = $deliv;";
    $result = mysql_query($query);
    if($result){
        $row = mysql_fetch_array($result);
        $addi = '';
        if ($row["PerKm"] != 0) {
            $addi = sprintf(" (+ %d км. за МКАД)", $km);
        }
        $dcost = $row["Cost"] + $row["PerKm"] * abs($km);
        printf("<tr><td align=left style=\"font-family:Arial Cyr, Arial;font-size:8pt\" colspan=4><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s%s</td>",$row["Name"],$addi);
        $msg .= sprintf("<tr><td align=left style=\"font-family:Arial Cyr, Arial;font-size:8pt\" colspan=4><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s%s</td>",$row["Name"],$addi);
        printf("<td align=right><font style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%.2f руб.</td></tr>",$dcost);
        $msg .= sprintf("<td align=right><font style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%.2f руб.</td></tr>",$dcost);
        $summ += $dcost;
    }else{
        SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
    }
    if (Config::val('BASE_DB')) {
        mysql_select_db(substr($dsn->path, 1));
    }
    printf("<tr><td align=left style=\"font-family:Arial Cyr, Arial;font-size:8pt\" colspan=3><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Итого</td>");
    $msg .= sprintf("<tr><td align=left style=\"font-family:Arial Cyr, Arial;font-size:8pt\" colspan=3><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Итого</td>");
    printf("<td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%d шт.</td><td align=right><font style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%.2f руб.</td></tr>", $countt, $summ);
    $msg .= sprintf("<td align=center style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%d шт.</td><td align=right><font style=\"font-family:Arial Cyr, Arial;font-size:8pt\">%.2f руб.</td></tr></table>", $countt, $summ);
    printf("</table><br><br>Успешно принят!<br>В ближайшее время мы свяжемся с Вами по телефону <b>%s</b> для подтверждения заказа.",$f->field('phone'));
    printf("<br>Заказ будет доставлен по адресу: <b>%s</b><br>", $f->field('address'));
    $sql->delete('Cart', array('SessionId' => $_COOKIE["sessionid"]));
    if (substr($HTTP_HOST,0,3) == "www") {
        $HTTP_HOST = substr($HTTP_HOST, 4, strlen($HTTP_HOST) - 4);
    }
    $fromwer = sprintf("From: \"Processing center\" <info@%s>\nContent-Type: text/html;\n\tMIME-Version: 1.0\n\tcharset=\"windows-1251\"\n\tCharset-Transfer-Encoding: quoted-printable\n",$HTTP_HOST);
    $from = sprintf("From: \"%s Shop. User: %s\" <%s>\nContent-Type: text/html;\n\tMIME-Version: 1.0\n\tcharset=\"windows-1251\"\n\tCharset-Transfer-Encoding: quoted-printable\n", ucfirst($HTTP_HOST),$fio, $email);
    $udata = sprintf("<br><br>Клиент %s, e-mail: %s<br>Телефон: %s.",$fio,$email,$phone);
    $udata .= sprintf("<br>Доставка по адресу: <b>%s</b><br>",$address);
    $udata .= sprintf("<br>Пожелания: <b>%s</b><br>", $dop);
    if($f->data('sb')) {
        $udata .= sprintf("<br><b>Необходима сборка!</b><br>");
    }
    $mes = "<html><head><META content=\"text/html; charset=windows-1251\" http-equiv=Content-Type></head><body>\n" . $msg .$udata. "</body></html>";
    $mesc = "<html><head><META content=\"text/html; charset=windows-1251\" http-equiv=Content-Type></head><body>\n" . $msg . "</body></html>";
    $attach = array();
    if (isset($_FILES['extfile'])) {
    	$file = $_FILES['extfile'];
    	if ($file['error'] == 0) {
    		if (empty($file['type'])) {
    			$file['type'] = mime_content_type($file['tmp_name']);
    		}
    		$attach = array('body' => $file['tmp_name'], 'ftype' => $file['type'], 'name' => String::translit($file['name']), 'isFile' => true);
    	}
    }
    if (Config::val('EMAIL')) {
        if (is_array(Config::val('EMAIL'))) {
            $emails = Config::val('EMAIL');
            foreach ($emails as $eml) {
                //mail($eml, 'Order from ' . $HTTP_HOST, $mes, $from);
                qmail::mail($mes, $eml, 'Order from '.$HTTP_HOST, $email, array(0 => $attach));
            }
        } else {
            //mail(Config::val('EMAIL'), 'Order from ' . $HTTP_HOST, $mes, $from);
            qmail::mail($mes, Config::val('EMAIL'), 'Order from '.$HTTP_HOST, $email, array(0 => $attach));
        }
    }
} else {
    printf('<div class="cartTitle">Ваша корзина пуста</div>');
}
?>
