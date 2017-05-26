<?php
require_once 'MDB2.php';
require_once 'dbexception.class.php';

class Sql {

    const ALL = 0;

    const ONE = 1;

    /**
     * —оединение с базой
     *
     * @var MDB2_Driver_Common
     */
    public $db;


    /**
     * –езультат запроса
     *
     * @var MDB2_Result
     */
    public $result;

    private $queries;

    /**
     *  онструктор класса дл€ работы с базой данных
     *
     * @param mixed $dsn
     * @return MDB2_Driver_Common
     */
    public function __construct($dsn, $forceNew = false) {
        if ($forceNew) {
            $this->db = MDB2::factory($dsn);
        } else {
            $this->db = MDB2::singleton($dsn);
        }
        if (PEAR::isError($this->db)) {
            throw new DbException($this->db->getMessage());
        }
        $this->db->setFetchMode(MDB2_FETCHMODE_ASSOC);
        // при установки MDB2_PORTABILITY_FIX_CASE преобразует имена таблиц, столбцов в lowercase
        $this->db->setOption('portability', MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE);
        $this->result = $this->db->query("SET NAMES cp1251");
        if (PEAR::isError($this->result)) {
            throw new DbException($this->result->getMessage());
        }
    }

    /**
     * Enter description here...
     *
     * @example
     * $result = $sql->query($query);
     * if ($result->numRows()) {
     *   while ($row = $result->fetchRow()) {
     *     ...
     *   }
     * }
     * @param unknown_type $query
     * @param unknown_type $exec
     * @return unknown
     */
    public function query($query, $exec = 0) {
        $this->statistic($query, __METHOD__);
        $this->result = ($exec) ? $this->db->exec($query) : $this->db->query($query);
        if (PEAR::isError($this->result)) {
            self::error(mysql_error(), $query);
            return false;
        }
        return $this->result;
    }

    private static function error($error, $query) {
        // {{{ выкинул, ибо оставл€ет много мусора в логах
        // $fd = fopen('/var/log/php/php_error.log', 'a');
	// $callStack = serialize(debug_backtrace());
	// fwrite($fd, $callStack."\n");
	// fclose($fd);
	// }}}
        throw new DbException($error .', ' .  $query);
    }

    /**
     * ¬ыбирает одиночное значение из базы
     *
     * @param string $query
     * @param array() $where пока не используетс€
     * @example
     * $id = $sql->queryOne('SELECT `Id` FROM `Gallery`;');
     * @return string || null
     */
    public function queryOne($query, $where = array()) {
        $this->statistic($query, __METHOD__);
        $this->result = $this->db->queryOne($query);
        if (PEAR::isError($this->result)) {
            self::error(mysql_error(), $query);
        }
        return $this->result;
    }

    public function queryRow($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT) {
        $this->statistic($query, __METHOD__);
        $this->result = $this->db->queryRow($query, $types, $fetchmode);
        if (PEAR::isError($this->result)) {
            self::error(mysql_error(), $query);
        }
        return $this->result;
    }

    /**
     * ¬озвращает стоки таблицы в виде массива
     *
     * @param string $query
     * @param boolean $rekey если установлен в true, то возвращает хеш,
     * ключем которого €вл€етс€ первый стобец из запроса, иначе обычный массив
     * @param array $where хеш поле => значение (услови€ выборки)
     * @return array
     */
    public function queryAll($query, $rekey = true, $where = array()) {
        $this->statistic($query, __METHOD__);
        $this->result = $this->db->queryAll($query, null, MDB2_FETCHMODE_DEFAULT, $rekey);
        if (PEAR::isError($this->result)) {
            self::error(mysql_error(), $query);
        }
        return $this->result;
    }

    private function statistic($query, $method) {
//        Debug::message('-----------------------');
//        Debug::message($query . ', метод: ' . $method);
//        Debug::message('-----------------------');
        $this->queries[] = $query;
    }

    public function getQueries() {
        return $this->queries;
    }

    public function esca($s) {
        foreach ($s as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    $s[$key][$key1] = $this->esc($value1);
                }
            } elseif (is_string($value)) {
                $s[$key] = $this->esc($value);
            }
        }
        return $s;
    }

    public function esc($s, $wildchars = false) {
        // удал€ем лишние слеши, если нужно
        if (get_magic_quotes_gpc()) {
            $s = stripslashes($s);
        }
        return $this->db->escape($s, $wildchars);
    }

    /**
     * ¬ыполн€ет вставку данных $params в таблицу $tables
     *
     * @param string $table
     * @param array $params хеш им€ столбца таблицы : значение
     * @param boolean $debug если true, то тело запроса выводитс€ на экран,
     * сам запрос не выполн€етс€
     */
    public function insert($table, $params, $debug = false, $update = array()) {
        $rawKeys = array_keys($params);
        $rawValues = array_values($params);
        $keys = '';
        foreach ($rawKeys as $value) {
        	$keys .= sprintf('`%s`,', $this->esc($value));
        }
        $keys = substr($keys, 0, -1);
        $values = '';
        foreach ($rawValues as $value) {
        	$values .= sprintf("'%s',", $this->esc($value));
        }
        $values = substr($values, 0, -1);
        $query = sprintf("INSERT INTO `%s` (%s) VALUES (%s)", $this->esc($table), $keys, $values);
        if (!empty($update)) {
            $query .= ' ON DUPLICATE KEY UPDATE ';
            foreach ($update as $field => $value) {
                $query .= sprintf(" `%s` = '%s' ", $this->esc($field), $this->esc($value));
            }
        }
        if ($debug) {
            $this->debug($query, 'insert');
        } else {
            return $this->query($query,1);
        }
        return false;
    }

    private function debug($query, $method = 'generic') {
        echo "<br>--<br>SQL->$method: $query <br>--<br>";
    }

    public function update($table, $params, $where, $debug = false) {
        $update = '';
        foreach ($params as $key => $value) {
            if (substr($value, 0, 6) == 'unesc|') {
                $update .= sprintf("`%s` = %s,", $this->esc($key), $this->esc(substr($value, 6)));
            } else {
        	   $update .= sprintf("`%s` = '%s',", $this->esc($key), $this->esc($value));
            }
        }
        $update = substr($update, 0, -1);
        $conditions = '';
        foreach ($where as $key => $value) {
        	$conditions .= sprintf(" AND `%s` = '%s' ", $this->esc($key), $this->esc($value));
        }
        $conditions = substr($conditions, 0, -1);
        $query = sprintf("UPDATE `%s` SET %s WHERE 1=1 %s", $this->esc($table), $update, $conditions);
        if ($debug) {
            $this->debug($query, 'insert', 'update');
        } else {
            $this->query($query);
        }
    }

    public function delete($table, $params, $debug = false) {
        $conditions = '';
        foreach ($params as $key => $value) {
        	$conditions .= sprintf(" AND `%s` = '%s' ", $this->esc($key), $this->esc($value));
        }
        $conditions = substr($conditions, 0, -1);
        $query = sprintf("DELETE FROM `%s` WHERE 1=1 %s", $this->esc($table), $conditions);
        if ($debug) {
            $this->debug($query, 'insert', 'delete');
        } else {
            $this->query($query);
        }
    }

    public function save($table, $params, $debug = false) {
        if (isset($params['id'])) {
            $id = $params['id'];
            unset($params['id']);
            $this->update($table, $params, array('Id' => $id), $debug);
        } else {
            $this->insert($table, $params, $debug);
        }
    }

    public function select($table, $params = array(), $type = self::ALL, $rekey = false) {

        $conditions = '';
        $groupby = '';
        if (isset($params['groupby'])) {
            $groupby = ' GROUP BY ' . $params['groupby'];
            unset($params['groupby']);
        }
        $orderby    = '';
        if (isset($params['orderby'])) {
            $orderby = ' ORDER BY ' . $params['orderby'];
            unset($params['orderby']);
        }
        $limit = '';
        if (isset($params['limit'])) {
            $limit = ' LIMIT ' . $params['limit'];
            unset($params['limit']);
        }
        foreach ($params as $key => $value) {
            $delimiter = strpos($key, '|');
            if ($delimiter !== false) {
                $symbol = substr($key, $delimiter + 1);
                $key    = substr($key, 0, $delimiter);
            } else {
                $symbol = '=';
            }

        	$conditions .= sprintf(" AND `%s` %s '%s' ", $this->esc($key), $symbol, $this->esc($value));
        }
        $conditions = substr($conditions, 0, -1);
        $query = sprintf("SELECT * FROM `%s` WHERE 1=1 %s%s%s%s", $this->esc($table), $conditions, $groupby, $orderby, $limit);
        if (is_string($type)) {
            return a($this->queryRow($query), $type);
        }
        return ($type == self::ONE) ? $this->queryRow($query) : $this->queryAll($query, $rekey);
    }

    public function columns($table) {
        $this->db->loadModule('Manager');
        return $this->db->manager->listTableFields($table);
    }

    public function lastId() {
        return $this->db->lastInsertID();
    }

    public function selectDb($db) {
    	$db = preg_match('#^`[^`]+`$#', $db) ? $db : '`'.$db.'`';
    	$this->query('USE '.$db.';');
    }

    function threadId() {
    	// TODO: разобратьс€, где в MDB2 хранитс€ link identifier
    	return mysql_thread_id();
    }

    public function connection() {
        return $this->db->connection;
    }
}
?>
