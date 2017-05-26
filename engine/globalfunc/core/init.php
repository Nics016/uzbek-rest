<?php
/**
 * Id из таблицы контент
 */
$pageId = request('pageId', null, 'int');
$subm = request('subm', null, 'int');
$catId = request('catId', null, 'int');
$page = request('page', null, 'int');
$itemId = request('itemId', null, 'int');
$subId = request('subId', null, 'int');
$newsId = request('newsId', null, 'int');
$state = request('state', null, 'file', true);
$rul = request('rul', null);
$count = request('count');
/**
 * Обработка действий, например добавление в корзину и т.д.
 */
$haction = request('haction', null, 'file', true);

// !empty($localHactionProcessing) == обработка значения $haction и инклуды идут в скриптах на сайте
if ($haction && empty($localHactionProcessing)) {
    if (strpos("http", $haction) !== false) {
        exit();
    }
    $path0 = SITE_DIR . "engine/hcore/$haction.php";
    $path1 = FUNC_DIR . "hcore/$haction.php";
    if (file_exists($path0)) {
        include_once ($path0);
    } else {
        if (file_exists($path1)) {
            include_once ($path1);
        } else {
            header("HTTP/1.1 404 Not Found");
			header('Location: http://www.westcom.ru/error404');
            $message = "ERR: Обращение к несуществующему файлу (HACTION) $haction " . SITE_URL;
            adminEmail(dumpServerVars(), $message, SITE_URL);
        }
    }
}

$query = 'SELECT * FROM Prop';
$result = mysql_query($query);
if ($result) {
    while ($row = mysql_fetch_array($result)) {
        $nm = $row["Name"];
        $val = str_replace("&#039;", "'", $row["Value"]);
        eval("\$$nm = \$val;\n");
    }
} else {
    SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
}
$mainId = FindParent($pageId, 0);

$isFront = false;
if (!$pageId || (empty($_GET) && empty($_POST))) {
    $isFront = true;
    $pageId = $dpage;
    //echo $dpage;
}
$currentPage = $row = loadPage($pageId);
/*
if($_SERVER['REMOTE_ADDR']=='89.175.99.178') {
	echo '__';
	print_r($pageId);
	print_r($currentPage);
	exit();
}
*/
if (request('pageId') && empty($currentPage)) {
	GenError(404);
    //    $message = "ERR: Обращение к несуществующей странице $pageId " . SITE_URL;
//    adminEmail(dumpServerVars(), $message, SITE_URL);
}elseif($row["Url"] != ""){
	ob_clean();
    header("Location: ".$row["Url"]);
    ob_flush();
    die();
}elseif($row["File"] != ""){
	ob_clean();
    header("Location: /uploads/files/".$row["File"]);
    ob_flush();
    die();
    
}

$ptitle = (! empty($ptitle)) ? $ptitle : ((! empty($row["Title"])) ? $row["Title"] : null);

$pname = (! empty($pname)) ? $pname : ((! empty($row["Comment"])) ? $row["Comment"] : null);
$pcounters = (! empty($row["Counters"])) ? str_replace("&#039;", "'", $row["Counters"]) : null;
$pmetadesc = (! empty($row["MetaDescr"])) ? $row["MetaDescr"] : null;
$pmetakey = (! empty($row["MetaKey"])) ? $row["MetaKey"] : null;
if (! empty($state)) {
    if (! request('pageId')) {}
    switch ($state) {
        case ("sp"):
            $pname = "Специальное предложение";
            break;
        case ("map"):
            $pname = "Карта сайта";
            break;
        case ("search"):
            $pname = "Поиск";
            break;
        case ("cart"):
            $pname = "Корзина";
            break;
        case ("order"):
            $pname = "Заказ";
            break;
        case ("completeorder"):
            $pname = "Заказ отправлен";
            break;
        case ("calendar"):
            $pname = "Календарь событий";
            break;
        default:
            $pname = '';
    }
    if (strpos("http", $state) !== false) {
        exit();
    }
}
$counter = $Counters . $pcounters . '<!--testcounter-->';
$isFront = false;
if (empty($_GET) && empty($_POST)) {
    $isFront = true;
}
//echo $pname; 

