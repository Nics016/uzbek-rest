<?php
class Cart extends Base {

    private $sessionId;

    private $items;

    public function __construct($sessionId) {
        parent::__construct();
        $this->sessionId = $sessionId;
        $cartItem = new CartItem();
        $this->items = $cartItem->getByParams(array('SessionId' => $this->sessionId));
    }

    public function items() {
        return $this->items;
    }

    public function item($id) {
        return $this->items[$id];
    }

    public function count() {
        return sizeof($this->items);
    }

    public function totalCount() {
        $count = 0;
        foreach ($this->items as $key => $item) {
            $count += $item->Count;
        }
        return $count;
    }

    public function totalCost() {
        $cost = 0;
        foreach ($this->items as $key => $item) {
            $cost += $item->Count * $item->data('Cost');
        }
        return $cost;
    }
}