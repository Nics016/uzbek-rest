<?php
class Order extends GenericObject {
    public function __construct($id = 0) {
        $this->id = $id;
        parent::__construct('Orders', 'Id');
        $this->load();
    }
}