<div class="orderContainer">
<form enctype="multipart/form-data" method="post" action="/">
<input type="hidden" name="state" value="completeorder">
<div class="orderTitle">Ваш заказ</div>
<table class="orderData">
<tr><th>№</th><th>Наименование</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr>
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

$cart = $sql->select('Cart', array('SessionId' => ((empty($_COOKIE['sessionid'])) ? 0 : fileEsc($_COOKIE['sessionid'])), 'Expire|>' => date("YmdHis")));
$i = 0;
$itemsCount = 0;
$itemsSum   = 0;
if (! empty($cart)) {
    foreach ($cart as $element) {
		$i++;
		$item = itemData($element['ItemId'], $element["Part"]);
		$itemsCount += $element["Count"];
		$itemsSum   += $item['price'];
		printf("<tr><td>%d</td>", $i);
        printf('<td>%s</td>', $item['name']);
        printf('<td>%.2f руб.</td>
    	        <td>%d шт.</td>
    	        <td>%.2f руб.</td>
    	        </tr>', $item["price"], $element['Count'], $item["price"] * $element['Count']);

    }
}
printf('<tr><td colspan="3" class="">Итого</td>');
printf('<td>%d шт.</td><td>%.2f руб.</td></tr>', $itemsCount, $itemsSum);
print("</table>");
print('<table class="orderData noBorder">');
echo <<<END
	<tr><td>Ваш e-mail:</td><td><input type=text name=email size=30 required></td></tr>
	<tr><td>Адрес доставки:</td><td><input type=text name=address size=30 required></td></tr>
	<tr><td>Тип доставки</td><td><select size=1 name=deliv>
END;
if (Config::val('BASE_DB')) {
    mysql_select_db(Config::val('BASE_DB'));
}
$query = "select * from Delivery;";
$result = mysql_query($query);
if($result){
    while($row = mysql_fetch_array($result)){
        printf("<option value=%d>%s\n",$row["Id"],$row["Name"]);
    }
}else{
    SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
}
if (Config::val('BASE_DB')) {
    mysql_select_db(substr($dsn->path, 1));
}
echo <<<END
	</select></tr></td>
	<tr><td>Если за МКАД, то укажите расстояние (км):</td><td><input type=text name=km size=10 required></td></tr>
	<tr><td>Нужна сборка</td><td><input type=checkbox name=sb value='Да'></td></tr>
	<tr><td>Телефон для связи:</td><td><input type=text name=phone size=30 required></td></tr>
	<tr><td>Контактное лицо:</td><td><input type=text name=fio size=30 required></td></tr>
	<tr><td>Ваши пожелания:</td><td><input type=text name=dop size=30 required></td></tr>
END;
echo '<tr><td>Файл:</td><td><input type="file" name="extfile" size="30" required></td></tr>';
print("</table>");
printf("<input type=submit value='Подтвердить заказ'></form>");
echo '</div>';
?>