<?php

function orderItem($id, $num, $count, $part) {
    global $sql, $dsn;
    if (Config::val('BASE_DB')) {
        mysql_select_db(Config::val('BASE_DB'));
    }
	$item = $sql->select('Assort', array('Id' => $id), Sql::ONE);
    if (! empty($item)) {
        $price = $sql->select('Prices', array('Id' => $part), Sql::ONE);
    	if (! empty($price['Price'])) {
    	    $item['Price'] = $price['Price'];
    	}
    	$tree = $sql->select('Tree', array('Id' => $item['Part']), Sql::ONE);
    	$name = sprintf('%s %s %s', $tree['Name'], $item['Name'], $price['Name']);
  	    printf("<tr><td>%d</td>", $num);
    	printf('<td>%s</td>', $name);
    	printf('<td>%.2f руб.</td>
    	        <td>
    	        <input type=text size=5 value="%d" name=count[%d]>
    	        <input type=hidden size=5 value=%d name=pId[%d]></td>
    	        <td>%.2f руб.</td>
    	        <td><a href="/?haction=delcart&Id=%d&pId=%d">Удалить</a></td></tr>', $item["Price"], $count, $id, $part, $id, $item["Price"] * $count, $id, $part);
    }
    if (Config::val('BASE_DB')) {
        mysql_select_db(substr($dsn->path, 1));
    }
	return $item["Price"] * $count;
}

$ctime = date("YmdHis");
$sessionId = (empty($_COOKIE['sessionid'])) ? 0 : fileEsc($_COOKIE['sessionid']);
$query = "select * from Cart where SessionId = '$sessionId' and Expire > $ctime";
$i = 0;
$result = mysql_query($query);
if ($result) {
	if(mysql_num_rows($result) > 0){
	    print '<div class="cartContainer">';
		print('<form action="/"><input type="hidden" name="haction" value="recalc">');
		printf('<div class="cartTitle">Ваша корзина</div>');
		print('<table class="cartData"><tr><th>№</th><th>Наименование</th><th>Цена</th><th>Количество</th><th>Сумма</th><th>Удалить</th></tr>');
		$itemsCount = 0;
		$itemsSum   = 0;
		while ($row = mysql_fetch_array($result)) {
			$i++;
			$itemsCount += $row["Count"];
			$itemsSum   += orderItem($row["ItemId"], $i, $row["Count"], $row["Part"]);
		}
		printf('<tr><td colspan="3" class="">Итого</td>');
		printf('<td>%d шт.</td><td>%.2f руб.</td><td>&nbsp;</td></tr>', $itemsCount, $itemsSum);
		print("</table>");
		print('<input type="submit" name=Order value="Оформить заказ"><input type="submit" name=Calc value="Пересчитать"></form>');
		print '</div>';
	} else {
		printf('<div class="cartTitle">Ваша корзина пуста</div>');
	}
} else {
	SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
}
?>
