<?php
class Config {

	private $data;

	private static $instance = null;

	private function __construct($data) {
	    $this->data = (array)$data;
	}

	private function __clone () {}

	public function prop($name) {
        if (key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new Exception('Обращение к несуществующему параметру конфигурации ' . $name . var_export($data, true));
        }
		return null;
	}

	public static function val($name) {
        $config = self::getInstance();
        if (func_num_args() > 1) {
            $config->data[$name] = func_get_arg(1);
        }
        return $config->prop($name);
	}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Config($GLOBALS['config']);
        }
        return self::$instance;
    }

    public function getPropUpdateDate() {
        static $result;
		if (empty($result)) {
            global $sql;
            if (empty($sql)) {
	        	//trigger_error('Config::getPropUpdateDate() called, but Sql object not initialized');
	    	}
		    $result = $sql->queryOne("SELECT `Value` FROM `Prop` WHERE `Name` = 'LastPropUpdate';");
		    /*if (empty($result)) {
		    	$result = date('Y-m-d H:i:s');
		    	$sql->insert('Prop', array('Name' => 'LastPropUpdate', 'Value' => $result));
		    }*/
		}
		return $result;
    }
    
    public static function reset() {
    	self::$instance = null;
    }

}
