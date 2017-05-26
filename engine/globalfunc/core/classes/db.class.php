<?php
class DB {

    const DEBUG_ALL   = 16;
    const DEBUG_ERROR = 4;

    private $connect;

    private $host;

    private $user;

    private $pass;

    private $db;

    private $result;

    private $debug = 0;

    public function __construct($user = '', $pass = '', $db = '',  $host = 'localhost') {
        $this->connect($user, $pass, $db,  $host);
    }

    public function setDebug($debug = 4) {
        $this->debug = $debug;
    }

    private function connect($user, $pass, $db,  $host) {
        $this->connect = mysql_connect($host, $user, $pass);
        if (!$this->connect) {
            throw new Exception("Unable to connect {$host}, {$user}, {$pass}");
        }
        if (!mysql_select_db($db, $this->connect)) {
            throw new Exception("Unable to select {$db}");
        }
    }

    public function query($query, $binds = array()) {
        if ($binds) {
            foreach ($binds as &$v) {
                $v = $this->esc($v);
            }
            array_unshift($binds, $query);
            $query = call_user_func_array('sprintf', $binds);
        }
        $this->result = mysql_query($query);
        if ($this->debug >= self::DEBUG_ALL) {

            echo "<br>***<br> " . nl2br($query) . " <br>***<br><br>";
        }
        if ($this->debug >= self::DEBUG_ERROR) {
            if (!$this->result) {
                $error = mysql_error($this->connect);
                throw new Exception("Failed query {$query}, error {$error}");
            }
        }
        return $this;
    }

    public function row() {
        if ($this->result) {
            return mysql_fetch_object($this->result);
        }
        return array();
    }

    public function result() {
        if ($this->result) {
            $data = array();
            while ($row = mysql_fetch_object($this->result)) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function esc($s) {
        return mysql_real_escape_string($s);
    }

    public function select($table, $where = array()) {
        return $this->query(sprintf("
            SELECT *
            FROM `%s`
            WHERE 1%s
        ", $table, $this->parseParams($where)));
    }

    private function parseParams($params, $glue = 'AND') {
        $conditions = '';
        foreach ($params as $k => $v) {
            $symbol = '=';
            if (($spacePos = strpos($k, ' ')) !== false) {
                $symbol = substr($k, $spacePos + 1);
                $k = substr($k, 0, $spacePos);
            }
            $conditions .= sprintf(" {$glue} `%s` {$symbol} '%s' ", $this->esc($k), $this->esc($v));
        }
        return $conditions;
    }

}