<?php

class Application {

    /**
     * URI
     *
     * @var URI
     */
    private $uri;

    private $model;

    /**
     * Smarty
     *
     * @var Smarty
     */
    public $smarty;

    private function __construct() {}

    private function setSmarty($smarty) {
        $this->smarty = $smarty;

    }

    /**
     * Enter description here...
     *
     * @param URI $uri
     * @param Smarty $smarty
     * @return Application
     */
    public function getInstanse($uri, $smarty) {

		static $appObject;
		if (! isset($appObject)) {
			$appObject = new Application();
			$appObject->uri = $uri;
			$appObject->smarty = $smarty;
			//$appObject->selectView();
		}
		return $appObject;
    }

    public function title() {}

    public function variantMenu() {
        $model  = Model::initializeByLink($this->uri->getModel());
        return $model->VariantsArray();
    }

    public function content() {
            $model       = Model::initializeByLink($this->uri->getModel());;
            //$variant     = ModelVariant::initializeByLink($this->uri->getVariant());
            $af          = new AutoFactory();
            //$autos       = $af->autoByModel($model);
            $engines = array();
            $gearboxes = array();
            foreach ($model->getVariants() as $v) {
                $engine = $v->getEngine();
                $engines[$engine['id']] = $engine['name'];
                $gearbox = $v->getGearbox();
                $gearboxes[$gearbox['id']] = $gearbox['name'];
            }
            $this->smarty->assign('colors',         $model->getColors());
            //$this->smarty->assign('variantPath',    $variant->GetField('link'));
            $this->smarty->assign('modelPath',      $model->GetField('link'));
            // опции уже встроенные в комплектацию
            $this->smarty->assign('defaultOptions', AutoOptionFactory::filter($model->options(), $model));
            // опции, которые можно добавлять
            $this->smarty->assign('additOptions',   AutoOptionFactory::filter($model->options('addit'), $model));
//            print_r($model->options('addit'));
            $opts = AutoOptionFactory::additOptionByModel($model);
            $this->smarty->assign('userAdditOptions',
                AutoOptionFactory::filter(AutoOptionFactory::additOptionByModel($model), $model));
            //print_r($opts[0]->additPrice());
            $this->smarty->assign('modelName',      $model->getName());
            //$this->smarty->assign('autoCount',      count($autos));
            $this->smarty->assign('engines',        $engines);
            $this->smarty->assign('gearboxes',      $gearboxes);
            $this->smarty->assign('modelHasChild',  $model->GetField('haschild'));
    }
}