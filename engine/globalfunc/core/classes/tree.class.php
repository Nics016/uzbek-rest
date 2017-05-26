<?php
/**
 * Enter description here...
 *
 * @example
 * $trees = r(new Tree)->nullParent();
 * foreach ($trees as $tree) {
 *  echo $tree->Name;
 *  $items = $tree->items();
 *  foreach ($items as $item) {
 *   echo $item->Name;
 *  }
 * }
 *
 */
class Tree extends TreeStruct {

    private $items;

    public function __construct($id = 0, $autoload = true) {
        parent::__construct('Tree', 'Id', 'Parent', $id);
        if ($autoload) {
            $this->load();
        }
    }

   public function childrens() {
        $result = $this->sql->query("SELECT * FROM {$this->table} WHERE {$this->parentName} = {$this->id}");
        while ($row = $result->fetchRow()) {
            $class = get_class($this);
            $element = new $class($row['Id'], false);
            $element->setData($row);
            $this->childrens[$row['Id']] = $element;

        }
        return $this->childrens;
    }

    public function items() {
        $item = new Item();
        $this->items = $item->getByParams(array('Parent' => $this->id));
        return $this->items;
    }

    public function allItems() {
        $data = array();
        $trees = $this->allChildrens();
        foreach ($trees as $tree) {
            $data += $tree->items();
        }
        return $data;
    }

}