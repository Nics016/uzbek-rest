<?php
class CartItem extends GenericObject {

    private $item;

    public function __construct($id = 0) {
        $this->id = $id;
        parent::__construct('Cart', 'Id');
        if ($this->load()) {
            $this->item = new Item($this->ItemId);
        }
    }

    public function data($name) {
        return $this->item->$name;
    }

    /**
     * Сначала обращается к свойству элемента корзины,
     * если свойства нет, то обращается к свойствe товара, соответствующего
     * элементу в корзине
     *
     * @param string $name
     * @return string
     */
    public function __get($name) {
        if (key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $this->data($name);
    }
}