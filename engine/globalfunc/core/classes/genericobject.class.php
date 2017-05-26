<?php
class GenericObject extends Base {

    protected $id;

    protected $idName;

    protected $data;

    protected $table;

    public function __construct($table, $idName = 'id', $id = 0) {
        parent::__construct();
        if (! empty($id)) {
            $this->id = intval($id);
        }
        $this->table = $table;
        $this->idName = $idName;
    }

    protected function load() {
	$this->data = $this->sql->select($this->table, array($this->idName => intval($this->id)), Sql::ONE);
        if (empty($this->data)) {
            $this->data = array();
            return false;
        }
        return true;
    }

    public function __get($name) {
        if (key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return false;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function data() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = array();
        if (! empty($data[$this->idName])) {
            $this->id  = $data[$this->idName];
        }
        $columns = $this->sql->columns($this->table);
        foreach ($columns as $column) {
            if (key_exists($column, $data)) {
                $this->data[$column] = $data[$column];
            }
        }
    }

    public function save() {
        if (empty($this->id)) {
            $this->sql->insert($this->table, $this->data);
            $this->id = mysql_insert_id();
        } else {
            $this->sql->update($this->table, $this->data, array($this->idName => intval($this->id)));
        }
        return $this->id;
    }

    public function getByParams($params, $glue = 'AND', $count = 0, $offset = 0) {
        $where[] = ' 1 = 1 ';
        $orderby = '';
        if (isset($params['orderby'])) {
            $orderby = ' ORDER BY ' . $params['orderby'];
            unset($params['orderby']);
        }
        foreach ($params as $key => $value) {
        	if ($key == 'SQL clause') {
        		$where[] = ' '.$value.' ';
        	} else {
            	$where[] = sprintf(" `%s` = '%s' ", $key, $value);
        	}
        }
        $limit = '';
        if ($count) {
            $limit = " LIMIT {$offset}, {$count}";
        }
        $data = $this->getByQuery("SELECT {$this->idName} FROM {$this->table} WHERE " . join($glue, $where) . $limit . $orderby);
        return $data;
    }
	
    public function getByQuery($query) {
        $result = $this->sql->query($query);
        $data = array();
        $class = get_class($this);
        while ($row = $result->fetchRow()) {
            $data[$row[$this->idName]] = new $class($row[$this->idName]);
        }
        return $data;
    }
    
    public function create($ids) {
        $class = get_class($this);
        foreach ($ids as $id) {
            $data[$id] = new $class($id);
        }
        return $data;
    }

    public function get($params = array(), $glue = 'AND', $count = 0, $offset = 0) {
        return $this->getByParams($params, $glue);
    }

    public function notEmpty() {
        return ! empty($this->data);
    }
}

