<script type="text/javascript">
function message() {
    if (document.getElementById('clean').checked) {
        if (confirm('Вы действительно хотите очистить базу и удалить все раннее введенные данные')) {
            return true;
        } else {
            return false;
        }
    }
    return true;
}
</script>
<?php
//$homedir = 'z:/home/kosmosshop/www/';
//$homeurl = 'http://kosmosshop/';
//$link = '?test';

define('ID_COLUMN', 1);

$homedir =  '/web/kosmosshop/';
$homeurl = 'http://shop.cosmos-stc.ru/';
$link = sprintf("auth.phtml?session_id=%s&page=%d&actsite=%d&action=%s&actcomp=%s",
    $_GET['session_id'], $_GET['page'], $_GET['actsite'], $_GET['action'], $_GET['actcomp']);

include_once $homedir . 'addons/config/loader.php';

function uploadForm() {
    echo '<form enctype="multipart/form-data" method="post" onsubmit="return message()">
          <label for="clean">Очистить базу</label> <input type="checkbox" name="clean" value="1" id="clean">
          <label for="xlsfile">Выберите файл</label> <input type="file" name="xlsfile" id="xlsfile">
          <input type="submit" value="Загрузить">
          </form>';
}

function saveCategory($row, $id, $parent) {
    $map[2] = "name";
    $sql = new Sql(Config::getInstance()->prop('dsn'));
    $id = $sql->queryOne(sprintf("SELECT id FROM Tree WHERE id = '%d'", $id));
    if (! empty($id)) {
        $row[ID_COLUMN] = $id;
        $query = queryUpdate($row, $map, $parent, 'Tree');
    } else {
        $query = queryInsert($row, $map, $parent, 'Tree');
    }
    if (empty($query)) {
        return false;
    }
//    print_r($query);
//    print_r('<br>');
    $sql->query($query);
    return true;
}

function queryInsert($row, $map, $parent, $table) {
    $sql = new Sql(Config::getInstance()->prop('dsn'));
    $values = '';
    $keys = '';
    foreach ($map as $key => $value) {
        if (isset($row[$key])) {
            $keys .= sprintf("`%s`,", $value);
            $values .= sprintf("'%s',", $sql->esc($row[$key]));
        }
    }
    if (empty($keys) || empty($values)) {
        return false;
    }
    return sprintf("INSERT INTO `%s` (%s,`Parent`) VALUES (%s,'%d')", $table,
                    substr($keys, 0, -1), substr($values, 0, -1), $parent);
}

function queryUpdate($row, $map, $parent, $table) {
    $sql = new Sql(Config::getInstance()->prop('dsn'));
    $values = '';
    foreach ($map as $key => $value) {
        if (isset($row[$key])) {
            $values .= sprintf("`%s` = '%s',", $value, $sql->esc($row[$key]));
        }
    }
    if (empty($values)) {
        return false;
    }
    return sprintf("UPDATE `%s` SET %s,`Parent` = '%d' WHERE id = '%d'", $table,
                    substr($values, 0, -1), $parent, $row[ID_COLUMN]);
}

function saveItem($row, $parent) {
    $map[2] = "Name";
    $map[3] = "Cnt";
    $map[4] = "Cost";
    $map[5] = "Brend";
    //$map[6] = "Size";
    $map[6] = "LongDesc";
    $sql = new Sql(Config::getInstance()->prop('dsn'));
    $id = (isset($row[ID_COLUMN])) ? $row[ID_COLUMN] : null;
    $id = $sql->queryOne(sprintf("SELECT id FROM Items WHERE id = '%d'", $id));
    if (! empty($id)) {
        $query = queryUpdate($row, $map, $parent, 'Items');
    } else {
        $query = queryInsert($row, $map, $parent, 'Items');
    }
    if (empty($query)) {
        return false;
    }
//    print_r($query);
//    print_r('<br>');
    $sql->query($query);
    return true;
    //printf('Категория: %s, id: %s, parent: %s<br>', $row[1], $id, $parent);
}

function parse($file) {
    $data = new XLS();
    $data->setOutputEncoding('CP1251');
    $data->read($file);
    $parent = 0;
    $cCount = 0;
    $iCount = 0;
    $parents  = array();
    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
        // если не пустая строка
        if (isset($data->sheets[0]['cells'][$i])) {
            $row = $data->sheets[0]['cells'][$i];
            // установлен артикул
            if (isset($row[ID_COLUMN])) {
                // если это категория
                if (strpos($row[ID_COLUMN], 'D') === 0) {
                    $id = sprintf('%d', substr($row[ID_COLUMN], 3));
                    $level  = sprintf('%d', substr($row[ID_COLUMN], 1, 1));
                    if ($level == 0) {
                        $parents = array($id);
                    } elseif ($level > count($parents) - 1) {
                        $parents[] = $id;
                    } elseif ($level < count($parents) - 1) {
                        unset($parents[count($parents) - 1]);
                    }
                    $level = ($level == 0) ? 0 : $parents[count($parents) - 2];
                    (saveCategory($row, $id, $level)) ? $cCount++ : 0;
                } else {
                    (saveItem($row, $id)) ? $iCount++ : 0;
                }
            // элементы с неустановленнным артиклем добавляются к текущей категории
            } else {
                (saveItem($row, $id)) ? $iCount++ : 0;
            }
        }
    }
    printf('<div class="message">Обработано категорий: %s<br>Обработано элементов: %s</div>', $cCount, $iCount);
}

function cleanBase() {
    $sql = new Sql(Config::getInstance()->prop('dsn'));
    $sql->query("DELETE FROM Tree");
    $sql->query("DELETE FROM Items");
}

if (isset($_FILES['xlsfile'])) {
    $f = new Form($_POST);
    if ($f->isChecked('clean')) {
        cleanBase();
    }
    $uploadfile = $homedir . 'tmp/items.xls';
    if (move_uploaded_file($_FILES['xlsfile']['tmp_name'], $uploadfile)) {
        parse($uploadfile);
    } else {
        echo '<div class="error">Ошибка загрузки файла</div>';
    }
} else {
    uploadForm();
}