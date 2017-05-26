<?php
class Base {

    protected $sql;

    public function __construct($dsn = '') {
        $dsn = (empty($dsn)) ? Config::val('dsn') : $dsn;
    	$this->sql  = new Sql($dsn);
    }
}