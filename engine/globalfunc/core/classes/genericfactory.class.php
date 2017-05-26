<?php

class GenericFactory {

    protected $table;

    protected $objectName;

    private $objects;

    /**
     * Доступ к базе данных
     *
     * @var Sql
     */
    protected $sql;

    public function __construct() {
        global $config;
        $this->sql = new Sql($config['dsn']);
    }

    protected function generateObjects($data) {
        $objects = array();
        $objectName = $this->objectName;
        foreach ($data as $objectId => $object) {
            $newObj = new $objectName($objectId);
            $newObj->SetData($object);
            $objects[$objectId] = $newObj;
        }
        return $objects;
    }

    public function getObjects($params = array()) {
        global $config;
        $sql = new Sql($config['dsn']);
        $where = '';
        foreach ($params as $key => $value) {
            $where .= sprintf("`%s` = '%s' AND", $key, $sql->db->escape($value));
        }
        $where = (! empty($where)) ? ' WHERE' . substr($where, 0, -4) : '';
        $data = $sql->queryAll(sprintf("SELECT * FROM `%s`$where", $this->table), false);
        return $this->generateObjects($data);
    }
}