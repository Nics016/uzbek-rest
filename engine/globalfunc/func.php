<?php
/**
 * ЏроверЯет наличие колонки $col в таблице $table
 * по указаному $id
 *
 * @param string $table
 * @param string $col
 * @return string | void
 */ 
function CheckColumn($table = "Content", $col = "urlName") {
	$parseutl=0;
    $query = sprintf("SHOW COLUMNS FROM %s", $table);
    $result = mysql_query($query);
    if ($result) {
        if (mysql_num_rows($result) > 0) {
             while($row = mysql_fetch_array($result)){
             		if($row["Field"] == $col){
						$parseurl = 1;
					}             	
             }
        }
    } else {
    	//echo mysql_error();
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
	if($parseurl == 1)
	    return true;
	else
	    return false;
}
/**
 * Получает значение столбца $col в таблице $table
 * по указаному $id
 *
 * @param integer $id
 * @param string $table
 * @param string $col
 * @return string | void
 */
function GetVar($id, $table = "Vars", $col = "Name") {
    $query = sprintf("select `%s` from `%s` where Id = '%d'", $col, $table, $id);
    $result = mysql_query($query);
    if ($result) {
        if (mysql_num_rows($result) > 0) {
            return mysql_result($result, 0, 0);
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
    return 0;
}

/**
 * Рекурсивное построение меню содержимого (таблица Content) начиная с $Parent
 *
 * @param integer $Parent
 */
function BuildSubmExpm($parent) {
    $query = sprintf("select * from Content where Parent = '%d' AND Active = 1 order by Sort asc", $parent);
    $result0 = mysql_query($query);
    if ($result0) {
        while ($row = mysql_fetch_array($result0)) {
            $query = sprintf("select * from Content where Parent = '%d' AND Active = 1", $row["Id"]);
            $result1  = mysql_query($query);
            $menuName = str_replace('"','',$row["MenuName"]);
            if ($result1) {
                print('<li>');
                if ($row["Active"] != 1) {
                    printf("%s\n", $menuName);
                } elseif ($row["Url"] != '') {
                    printf("<a href=%s>%s</a>\n",$row["Url"], $menuName);
                } elseif ($row["File"] != '') {
                    printf("<a href=/img/content/%s>%s</a>\n", $row["File"], $menuName);
                } else {
                    printf("<a href=/?pageId=%d>%s</a>\n", $row["Id"], $menuName);
                }
                if ((mysql_num_rows($result1) > 0) AND ($row["Mech"] == '')) {
                    print("<ul>\n");
                    BuildSubmExpm($row["Id"]);
                    print("</ul>\n");
                }
                print('</li>');
            }else{
                SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
            }
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
}

function SqlErrorRep($error, $query, $line, $file, $fatal = true) {
    $fp = fopen("/var/log/php/mysql_errors.log","a");
    $referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    $message = date("[Y-m-d H:i:s] [").$_SERVER['REMOTE_ADDR']."] `".$query ."` in ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ." (".$file." on line ".$line.") ref: ".$referer." Err: ". $error." \n\r";
    fwrite($fp, $message);
    fclose($fp);
    if ($GLOBALS['config']['DEBUG_MODE']) {
        throw new Exception("Database error, $error, $query");
    }
    if ($fatal) {
        ob_clean();
        print("<html><body><table width=100% height=100%><tr><td valign=middle align=center style='font-family:Tahoma,Arial;font-size:13px;'>Произошла ошибка выполнения скрипта. Администратор сайта оповещен об ошибке.<br>Приносим извинения за неудобства...<br><br><hr width=50%><br><br>Script produced an error. System administrator is notifyed.<br>Sorry for the inconvenience...</td></tr></table></body></html>");
        adminEmail($message, $query);
        throw new Exception("Database error, $error, $query");
    }
}
function ErrorHandling($error, $e_string, $file, $line, $cont) {
	//	$fp = fopen("/var/log/php/mysql_errors.log","a");
		$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
		echo $e_string."<br>";
	if($error != E_NOTICE && $error != E_STRICT && $error != E_DEPRECATED){
		echo "<b>".$e_string."<br>";
		$message = date("[Y-m-d H:i:s] [").$_SERVER['REMOTE_ADDR']."] ".$e_string." in ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ." (".$file." on line ".$line.") ref: ".$referer." Err: ". $error." \n\r";
		$message .= $cont;
	//	fwrite($fp, $message);
	//	fclose($fp);
	//	if ($GLOBALS['config']['DEBUG_MODE']) {
	//		throw new Exception("Database error, $error, $query");
	//	}
	//	if ($fatal) {
	//ob_clean();
	print("<html><body><table width=100% height=100%><tr><td valign=middle align=center style='font-family:Tahoma,Arial;font-size:13px;'>Произошла ошибка выполнения скрипта. Администратор сайта оповещен об ошибке.<br>Приносим извинения за неудобства...<br><br><hr width=50%><br><br>Script produced an error. System administrator is notifyed.<br>Sorry for the inconvenience...</td></tr></table></body></html>");
		adminEmail($message, $e_string);
	// throw new Exception("Database error, $error, $query");
	//	}
	//	return false;
	}else{
		return true;
	}
}

function ending($digit,$end01,$end0204,$enddef){
    switch(substr($digit,strlen($digit)-2,2)){
        case '11':
            $text = $enddef;
            break;
        case '12':
            $text = $enddef;
            break;
        case '13':
            $text = $enddef;
            break;
        case '14':
            $text = $enddef;
            break;
        default:
            switch(substr($digit,strlen($digit)-1,1)){
                case '1':
                    $text = $end01;
                    break;
                case '2':
                    $text = $end0204;
                    break;
                case '3':
                    $text = $end0204;
                    break;
                case '4':
                    $text = $end0204;
                    break;
                default:
                    $text = $enddef;
            }
    }
    return $text;
}

/**
 * Поиск родителя страницы с идентификатором $id
 *
 * @param integer $id
 * @param integer $FindId
 * @return integer
 */
function FindParent($id, $stopId) {
    $parent = $id;
    if (! empty($id)) {
        while (($id != $stopId) && ($id != 0)) {
            $query = sprintf("select Parent from Content where Id = '%d'", $id);
            $result = mysql_query($query);
            if ($result) {
                if (mysql_num_rows($result) > 0) {
                    $id = mysql_result($result, 0, 0);
                    if ($id != $stopId) {
                        $parent = $id;
                    }
                } else {
                    $id = 0;
                }
            }else{
                SqlErrorRep(mysql_error(), $query, __LINE__, __FILE__);
            }
        }
    }
    return $parent;
}

/**
 * Выводит содержимое страницы с идентификатором $id
 *
 * @param integer $id
 */
function PagePost($id, $buffer = false, $connectId = false) {
    if ($buffer) {
        ob_start();
    }
    if (function_exists('lang') && function_exists('ContLangTrans')) {
        if (lang()) {
            $content = a(ContLangTrans(lang(), 'Content', array('Content' => ''), $id), 'Content');

            //print str_replace("]",">", str_replace("[","<", $content));
            print $content;
            return;
        }
    }
//    echo mysql_result(mysql_query('select DATABASE()'), 0);
    $query = "select `Content` from `Content` where `Id` = '" . $id . "'";
    //echo $query;
    if (false === $connectId) {
        $result = mysql_query($query);
    } else {
        $result = mysql_query($query, $connectId);
    }
    if ($result) {
        $row = mysql_fetch_array($result);
        $content = $row['Content'];
    }else{
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
    //print str_replace(array("]","[","<br />"),array(">","<","<br>"),$content);
    print $content;
    if ($buffer) {
        return ob_get_clean();
    }
}

function printHeader($pageId, $standartMode = false) {
    $query  = 'SELECT * FROM Prop';
    $result = mysql_query($query);
    if ($result) {
        while ($row = mysql_fetch_array($result)) {
            $nm  = $row["Name"];
            $val = str_replace("&#039;","'",$row["Value"]);
            eval("\$$nm = \$val;\n");
        }
    } else {
        SqlErrorRep(mysql_error(), $query , __LINE__, __FILE__);
    }
    $query = sprintf("select * from Content where Id = '%d'", $pageId);
    $result = mysql_query($query);
    if ($result) {
        $row = mysql_fetch_array($result);
        $ptitle = $row["Title"];
        $pname  = $row["Comment"];
        $pcounters = str_replace("&#039;","'",$row["Counters"]);
        $pmetadesc = $row["MetaDescr"];
        $pmetakey  = $row["MetaKey"];
        if ($row["Url"] != '') {
            header("Location: ".$row["Url"]);
        }
        if ($row["File"] != '') {
            header("Location: /img/catalog/".$row["File"]);
        }
    } else {
        SqlErrorRep(mysql_error(), $query , __LINE__, __FILE__);
    }
    $state = empty($_REQUEST['state']) ? '' : $_REQUEST['state'];
    if ($state == "sp") {
        $pname = "Специальное предложение";
    }
    if ($ptitle != '') {
        $pageTitle = $ptitle;
    } elseif ($Title != '') {
        $pageTitle = $Title;
    } elseif ($pname != '') {
        $pageTitle = $pname;
    } elseif ($Name != '') {
        $pageTitle = $Name;
    }
    $docType = ($standartMode) ? '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' : '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
    echo $docType;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1251">
<meta name="Author" content="CSID">
<meta name="allow-search" content="yes">
<meta name="robots" content="index,follow">
<meta name="rating" content="General">
<meta name="revisit-after" content="1 day">
<meta name="Description" content="<?php echo ($pmetadesc != '') ? $pmetadesc : $MetaDesc ?>">
<meta name="Keywords" content="<?php echo ($pmetakey != '') ? $pmetakey : $MetaKey ?>">
<link rel='stylesheet' type='text/css' href='../css/style.css'>
<title><?php echo $pageTitle ?></title>
<script type="text/javascript">
<!--
function pano(id) {
    var MyURL = "/pano/"+id+".html";
    var Opt = "fullscreen=no,type=fullWindow,location=no,menubar=no,titlebar=no,width=400,height=300,scrollbars=no";
    newww4 = window.open (MyURL, "pano", Opt);
    newww4.focus();
}
function ViewImg(width,height,id) {
    var MyURL = "image.phtml?Image="+id;
    var Opt = "fullscreen=no,type=fullWindow,location=no,menubar=no,titlebar=no,width="+width+",height="+height+",scrollbars=no";
    window.open (MyURL, "image", Opt);
}
function OpenPage(width, height, id, sb) {
    if (!sb) {
        sb='no';
    }
    var MyURL = "page.phtml?pageId="+id;
    var Opt = "fullscreen=no,type=fullWindow,location=no,menubar=no,titlebar=no,width="+width+",height="+height+",scrollbars="+sb;
    window.open (MyURL, "page", Opt);
}
//-->
</script>
<?php
return $pname;
}

function utf8win1251($s) {
    $out = "";
    $c1  = "";
    $byte2 = false;
    for ($c=0; $c < strlen($s); $c++) {
        $i = ord($s[$c]);
        if ($i <= 127) {
            $out .= $s[$c];
        }
        if ($byte2) {
            $new_c2 = ($c1&3)*64+($i&63);
            $new_c1 = ($c1>>2)&5;
            $new_i  = $new_c1*256+$new_c2;
            if ($new_i == 1025) {
                $out_i = 168;
            } elseif ($new_i == 1105) {
                $out_i = 184;
            } else {
                $out_i = $new_i - 848;
            }
            $out .= chr($out_i);
            $byte2 = false;
        }
        if (($i>>5) == 6) {
            $c1    = $i;
            $byte2 = true;
        }
    }
    return $out;
}

function siteMenu($pageId) {
    $parentPageId = $pageId;
    $mirror = 0;
    $page = loadPage($pageId);
    if (! empty($page)) {
        // если страница не корневая, то пробуем найти родителя
        if ($page['Parent'] != 0) {
            $parentPageId = FindParent($page['Id'], 0);
        }
        $mirror = $page['Mirror'];
    }
    $out = '';
    $result0 = mysql_query(sprintf("select * from Content where Parent = '%d' AND Active = '1' order by Sort asc", $parentPageId));
    if ($result0) {
        while ($row = mysql_fetch_array($result0)) {
            $menuName = htmlspecialchars($row["MenuName"]);
            if ($row['Id'] == $pageId || $row['Id'] == $mirror || $row['Id'] == $page['Parent']) {
                $out .= sprintf("<li class=\"active\">%s\n", $menuName);
            } else {
                $out .= sprintf('<li><a href="%s">%s</a>', href(array('pageId' => $row["Id"])), $menuName);
            }
            $result1 = mysql_query(sprintf("select * from Content where Parent = '%d' AND Active = '1' order by Sort asc", $row['Id']));
            if (mysql_num_rows($result1) > 0) {
                $out .= '<ul>';
                while ($row1 = mysql_fetch_array($result1)) {
                    if ($row1['Parent'] == $pageId || $row1['Id'] == $pageId || $row1['Parent'] == $page['Parent']) {
                        $menuName = htmlspecialchars($row1["MenuName"]);
                        if ($row1['Id'] == $pageId || $row1['Id'] == $mirror) {
                            $out .= sprintf("<li class=\"active\">%s\n", $menuName);
                        } else {
                            $out .= sprintf('<li><a href="%s">%s</a>', href(array('pageId' => $row1["Id"])), $menuName);
                        }
                    }
                }
                $out .= '</ul>';
            }
            $out .= '</li>';
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
    return (empty($out)) ? '' : sprintf('<ul class="siteMenu">%s</ul>', $out);
}

function loadPage($pageId) {
    $page = array();
    $query = sprintf("select * from Content where Id = '%d'", $pageId);
    $result0 = mysql_query($query);
    if ($result0) {
        if (mysql_num_rows($result0) > 0) {
            $page = mysql_fetch_array($result0);
        }
    } else {
        SqlErrorRep(mysql_error(),$query,__LINE__,__FILE__);
    }
    return $page;
}

/**
 * Масштабирование изображения
 *
 * @param string $fileName полный путь к файлу
 * @param array $params параметры масштабирования
 * - width integer
 * - height integer
 * - bgcolor array('r' => 255, 'g' => 255, 'b' => 255) цвет подложки
 * - border integer толщина рамки
 * - brdcolor array('r' => 255, 'g' => 255, 'b' => 255) цвет рамки
 * @return string изображение
 */
function scaleImage($fileName, $params, $link = false) {
    ob_start();
    if (! file_exists($fileName)) {
		//echo $fileName;
    	return;
    }

    $padding = 0;
    if (isset($params['padding'])) {
        //FIXME не правильно работает паддинг, растягивает картинки
        //    $padding = $params['padding'];
    }
    $image = imagecreatefromjpeg($fileName);
    if (! $image) {
        $image = imagecreatefromgif($fileName);
        if (! $image) {
            $image = imagecreatefrompng($fileName);
            if (! $image) {
                throw new Exception('Не могу открыть изображение ' . $fileName);
            }
        }
    }
    $imgInfo = getimagesize($fileName);
    if (!isset($params['height']) || 0 == $params['height']) {
        $params['height'] = $imgInfo[1];
    }
    if (!isset($params['width']) || 0 == $params['width']) {
        $params['width'] = $imgInfo[0];
    }

    $scaledImage = imagecreatetruecolor($params['width'], $params['height']);
    if (! $scaledImage) {
        throw new Exception('Не могу создать изображение');
    }
    $imageW = imagesx($image);
    $imageH  = imagesy($image);
    $bgcolor['r'] = 255;
    $bgcolor['g'] = 255;
    $bgcolor['b'] = 255;
    if (isset($params['bgcolor'])) {
        $bgcolor = $params['bgcolor'];
    }
    imagefilledrectangle($scaledImage, 0, 0, $params['width'], $params['height'], imagecolorallocate($scaledImage, $bgcolor['r'], $bgcolor['g'], $bgcolor['b']));
    $scaledImageW = $imageW;
    $scaledImageH = $imageH;
    // определяем новые размеры изображения
    if ($imageW > $params['width']) {
        $scaleX = $params['width'] / $imageW;
        $scaledImageW = ceil($imageW * $scaleX);
        $scaledImageH = ceil($imageH * $scaleX);
    }
    if ($scaledImageH > $params['height']) {
        $scaleY = $params['height'] / $scaledImageH;
        $scaledImageW = ceil($scaledImageW * $scaleY);
        $scaledImageH = ceil($scaledImageH * $scaleY);
    }
    $scaledImageX = ceil(($params['width'] / 2) - ($scaledImageW / 2));
    $scaledImageY = ceil(($params['height'] / 2) - ($scaledImageH / 2));
    imagecopyresampled($scaledImage, $image, $scaledImageX + $padding, $scaledImageY + $padding, 0, 0, $scaledImageW - 2 * $padding, $scaledImageH - 2 * $padding, $imageW, $imageH);
    // добавление рамки
    if (! empty($params['border'])) {
        $brdcolor['r'] = 100;
        $brdcolor['g'] = 100;
        $brdcolor['b'] = 100;
        if (isset($params['brdcolor'])) {
            $brdcolor = $params['brdcolor'];
        }
        $borderWidth = $params['border'];
        $borderColor = imagecolorallocate($scaledImage, $brdcolor['r'], $brdcolor['g'], $brdcolor['b']);
        imagelinethick($scaledImage, 0, 0, 0, ($params['height'] - 1), $borderColor, $borderWidth);
        imagelinethick($scaledImage, 0, 0, ($params['width'] - 1), 0, $borderColor, $borderWidth);
        imagelinethick($scaledImage, ($params['width'] - 1), ($params['height'] - 1), ($params['width'] - 1), 0, $borderColor, $borderWidth);
        imagelinethick($scaledImage, ($params['width'] - 1), ($params['height'] - 1), 0, ($params['height'] - 1), $borderColor, $borderWidth);
    }
    if ($link) {
        return $scaledImage;
    }
    imagejpeg($scaledImage);
    $img = ob_get_contents();
    ob_end_clean();
    return $img;
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* this way it works well only for orthogonal lines
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
    round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
    round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
    round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
    round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

/**
 * Поиск элемента массива
 *
 * @param	array	$array где		искать
 * @param	mixed	$searchFor		что искать. Формат: array('index1', 'index2', ...) или 'index1|index2|...'
 * (соответствует $array['index1']['index2'][...])
 * @param	mixed	$defaultValue	значение по умолчанию (если элемент не найден)
 * @return	mixed	найденный элемент либо значение по умолчанию
 */
function findInArray($array, $searchFor, $defaultValue = '') {
	if (is_string($searchFor)) {
		if (!preg_match('#^[a-z0-9_]+$#i', $searchFor)) {
			$searchFor = preg_split('#[^a-z0-9_]+#i', $searchFor, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$searchFor = array($searchFor);
		}
	}
	$tmp = &$array;
	foreach ($searchFor as $key) {
		if (!isset($tmp[$key])) {
			return $defaultValue;
		}
		$tmp = &$tmp[$key];
	}
	return !isset($tmp) ? $defaultValue : $tmp;
}

function request($name, $default = false, $type = null, $reinit = false) {
    $value = findInArray($_REQUEST, $name, $default);
    switch ($type) {
        case 'int':
            $result = sprintf('%d', $value);
            if (empty($result)) {
                $result = '';
            }
            break;
        case 'string':
            $result = strval($value);
            break;
        case 'file':
            $result = fileEsc($value);
            break;
        default:
            $result = $value;
            break;
    }
    if ($reinit) {
        $_REQUEST[$name] = $result;
		foreach (array('_POST', '_GET', '_COOKIE', 'HTTP_POST_VARS', 'HTTP_GET_VARS', 'HTTP_COOKIE_VARS') as $array) {
			if (!empty($$array[$name])) {
				$$array[$name] = $result;
			}
		}
    }
    return $result;
}

function cookie($name, $default = '') {
	return findInArray($_COOKIE, $name, $default);
}

function server($name, $default = '') {
	return findInArray($_SERVER, $name, $default);
}

function fileEsc($s, $except = array()) {
    return preg_replace('/[^a-zA-Z_0-9' . join('', $except) .  ']/', '', $s);
}

function href($params, $path = '/') {
    $link = '';
    if (is_string($params)) {
        $params = array('pageId' => $params);
    }
    foreach ($params as $key => $value) {
        $link .= sprintf('%s=%s&', $key, $value);
    }
    $link = $path . ((empty($link)) ? '' : '?' . substr($link, 0, -1));
    return htmlentities($link);
}

function _href($name, $value) {
    return href(array($name => $value));
}

function breadcrumbs($pageId, $delimiter = ' :: ', & $out = '') {
    $page = loadPage($pageId);
    if (! empty($page)) {
        if ($out == '') {
            $out = sprintf('<strong>%s</strong>', $page['MenuName']);
        } else {
            $out = sprintf('<a href="%s">%s</a>', href(array('pageId' => $pageId)), $page['MenuName']) . $delimiter . $out;
        }
        breadcrumbs($page['Parent'], $delimiter, $out);
    }
    return $out;
}

function month($index) {
    $months = array('Январь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
    return $months[$index];
}

function css($cssList) {
    $out = '';
    foreach ($cssList as $value) {
        $out .= '<link rel="stylesheet" type="text/css" href="' . $value .'">' . "\n";
    }
    return $out;
}

function script($scriptList) {
    $out = '';
    foreach ($scriptList as $value) {
        $out .= '<script src="' . $value . '" type="text/javascript"></script>' . "\n";
    }
    return $out;
}

function uploadFile($src, $dest) {
    if (move_uploaded_file($src, $dest)) {
        echo '<div class="message">Файл загружен</div>';
        return true;
    } else {
        echo '<div class="message">Произошла ошибка при загрузке</div>';
        if (ini_get('display_errors') == '1') {
            print_r($_FILES);
        }
        return false;
    }
}

function pageBreadcrumb($pageId, $delimiter) {

}

function childrens($sql, $id, $table = 'Tree', $params = array()) {
    static $list = array();
    if (empty($list)) {
        $list[] = $id;
    }
    $parentName = 'Parent';
    if (isset($params['parent'])) {
        $parentName = $params['parent'];
    }
    $idName = 'Id';
    if (isset($params['id'])) {
        $idName = $params['id'];
    }
    $query = $sql->select($table, array($parentName => $id));
    if (! empty($query)) {
        foreach ($query as $tree) {
            $list[] = $tree[$idName];
            childrens($sql, $tree[$idName], $table, $params);
        }
    }
    return $list;
}

function adminEmail($message, $subject, $from = '') {
    //if(Config::val('ADMIN_EMAIL'))$emails=Config::val('ADMIN_EMAIL');
    if(!$emails)$emails=array('as@celicom.ru');
    foreach ($emails as $email) {
        qMail::mail($message, $email, $subject, $from);
    }
}

function dumpServerVars() {
    ob_start();
    print_r($_SERVER);
    print_r($_REQUEST);
    $out = ob_get_clean();
    $out = str_replace("\n", '<br>', $out);
    $out = str_replace("\t", '    ', $out);
    return $out;
}

function img($filePath, $imgPath = false, $params = array()) {
    $src = ($imgPath) ? $imgPath : $filePath;
    $alt = (isset($params['alt'])) ? $params['alt'] : '';
    unset($params['alt']);
    if (file_exists($filePath)) {
        $imgInfo = getimagesize($filePath);
        if (false !== $imgInfo) {
            $width   = (isset($params['width'])) ? $params['width'] : $imgInfo[0];
            $height  = (isset($params['height'])) ? $params['height'] : $imgInfo[1];
            unset($params['height']);
            unset($params['width']);
            $params = DBList::parseParams($params);
            $params = empty($params) ? '' : ' ' . $params;
            return "<img src=\"$src\" alt=\"$alt\" height=\"$height\" width=\"$width\"$params>";
        } else {
            // файл не изображение или изображение с ошибкой
        }
    }
    return false;
}

function createGDImage($fileName) {
    $image = imagecreatefromjpeg($fileName);
    if (! $image) {
        $image = imagecreatefromgif($fileName);
        if (! $image) {
            $image = imagecreatefrompng($fileName);
            if (! $image) {
                return false;
            }
        }
    }
    return $image;
}

function calendar($date, $format = '%d.%m.%Y', $label = '', $field = 'date', $button = 'trigger') {
    static $script;
    $field  = ($field)  ? $field : 'date';
    $button = ($button) ? $button : 'trigger';
    $format = ($format) ? $format : '%d.%m.%Y';
    $date   = ($date)   ? $date : date('d.m.Y');
    $out = '';
    if (empty($script)) {
        $out .= '<script type="text/javascript" src="http://newdesign.ru/js/calendar/calendar.js"></script>';
        $out .= '<script type="text/javascript" src="http://newdesign.ru/js/calendar/calendar-ru.js"></script>';
        $out .= '<script type="text/javascript" src="http://newdesign.ru/js/calendar/calendar-setup.js"></script>';
    }
    if ($label) {
        $out .= "<label for=\"{$button}\">{$label}</label><br>";
    }
    $out .= "<input type=\"text\" name=\"{$field}\" id=\"$field\" value=\"$date\">";
    $out .= "<input name=\"$button\" style=\"width: 20px\" id=\"{$button}\" type=\"button\">";
    $out .= sprintf('<script type="text/javascript">
            Calendar.setup({
                inputField  : "%s",
            	ifFormat    : "%s",
            	button      : "%s"
            });
            </script>', $field, $format, $button);
    return $out;
}

function &r($v) { return $v; }

function &a(&$a, $i) { return $a[$i]; }

function getProp($name) {
	global $sql;
	static $data = array();
	if (empty($data[$name])) {
		$value = '';
		$query = "SELECT `Value` FROM `Prop` WHERE `Name` = '".$name."';";
		if ($sql) {
			$value = $sql->queryOne($query);
		} else {
			$r = mysql_query($query);
			list($value) = mysql_fetch_row($r);
		}
		$data[$name] = $value;
	}
	//return str_replace(array("]","[","<br />"),array(">","<","<br>"),$data[$name]);
	return $data["name"];
}

function loadView($template, $data) {
    foreach ($data as $key => $value) {
        $$key = $value;
    }
    ob_start();
    include(SITE_DIR . '/engine/views/' . $template . '.php');
    return ob_get_clean();
}
function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',  'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    	' ' => '_'
    );
    return strtr($string, $converter);
}
function str2url($str) {
    // переводим в транслит
    $str = rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_\.]+~u', '', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}
function GenError($errcode,$moveurl = ""){
	if($moveurl == '' && $GLOBALS['config']['url404']){
		$moveurl = $GLOBALS['config']['url404'];
	}
	ob_clean();
	//echo $moveurl;
	switch($errcode){
		case "404":			
			//header('Location: /error404');
			//die();
			//header("HTTP/1.1 404 Not Found");
			header("HTTP/1.0 404 Not Found");
			header('Status: 404 Not Found');
			header('HTTP/1.0 404 Not Found');
			if($moveurl != ''){
				//echo $moveurl;
				echo file_get_contents ($moveurl);
			}else{
			echo <<<END
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>
END;
}
			die();
		case "301":
			Header( "HTTP/1.1 301 Moved Permanently" );
			Header( "Location: ".$moveurl );
			die();
			
	}
}

function checkEmail($email){
	if (!eregi("^[\._a-zA-Z0-9-]+@[\.a-zA-Z0-9-]+\.[a-z]{2,6}$", $email))
		return false;
	else{
		list($username, $domain) = split("@",$email);
		if (@getmxrr($domain, $mxhost))
			return true;
		else{
			$f=@fsockopen($domain, 25, $errno, $errstr, 30);
			if($f) {
				fclose($f);
				return true;
			}else{
				return false;
			}
		}
	}
/*	
	if(eregi("^[a-zA-Z0-9_]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$]", $email)){
		return false;
	}else{
		list ($Username, $Domain) = split ("@",$email);
		if (getmxrr ($Domain, $MXHost)){
			return TRUE;
		}else{
			if (fsockopen ($Domain, 25, $errno, $errstr, 30)){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
	*/
}
//*/
function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
   
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
   
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}
function GetGeoInfo($ip){
	$xmlStr = file_get_contents("http://ipgeobase.ru:7020/geo?ip=".$ip);
	$xmlObj = simplexml_load_string($xmlStr);
	//if($_SERVER['REMOTE_ADDR'] == '89.175.99.178')
	//print_r($xmlObj);
	//$arrXml = objectsIntoArray($xmlObj);
return $xmlObj;
}

?>
