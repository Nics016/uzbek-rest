<?php
class Item extends GenericObject {
    public function __construct($id = 0) {
        $this->id = $id;
        parent::__construct('Items', 'Id');
        $this->load();
    }

}