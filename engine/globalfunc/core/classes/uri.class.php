<?php

class URI {

    private $arg;
    private $model;
    private $variant;
    private $view;

    private function __construct() {}

    public static function parse() {

		static $uriObject;
		if (! isset($uriObject)) {
			$uriObject = new URI();
			$uri = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
			$arg = $URIelements = array_values(array_filter(explode('/', $uri)));
			$uriObject->arg = $arg;
//			$uriObject->brend   =  (isset($arg[0])) ? $arg[0] : null;
			$uriObject->model   =  isset($_GET['modelid']) ? $_GET['modelid'] : null;
			$uriObject->variant =  isset($_GET['variantid']) ? $_GET['variantid'] : null;
//			$uriObject->view    =  (isset($arg[2])) ? $arg[2] : null;
		}
		return $uriObject;
    }

    public function getModel() {
        return $this->model;
    }

    public function getVariant() {
        return $this->variant;
    }

    public function getView() {
        return $this->view;
    }

}