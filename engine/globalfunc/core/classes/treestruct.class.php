<?php
class TreeStruct extends GenericObject {

    protected $parentName;

    protected $childrens = array();

    private $allChildrens = array();

    public function __construct($table, $idName, $parentName = 'Parent', $id = 0) {
        $this->id = intval($id);
        $this->parentName = $parentName;
        parent::__construct($table, $idName);
    }

    public function childrens($parent = false) {
        if ($parent === false) {
            $parent = $this->id;
        }
        $result = $this->sql->query("SELECT * FROM {$this->table} WHERE {$this->parentName} = {$parent}");
        while ($row = $result->fetchRow()) {
            $class = get_class($this);
            $this->childrens[$row['Id']] = new $class($row['Id']);
        }
        return $this->childrens;
    }

    public function allChildrens() {
        $childrens = $this->tree($this->id);
        $childrens[$this->id] = $this;
        return $childrens;
    }

    public function allChildrensIds() {
        $childrens = $this->allChildrens();
        $data = array();
        foreach ($childrens as $key => $children) {
            $data[] = $key;
        }
        return $data;
    }

    private function tree($id) {
        $class = get_class($this);
        $tree = new $class($id);
        $childrens = $tree->childrens();
        foreach ($childrens as $id => $children) {
            $this->allChildrens[$id] = $children;
            $this->tree($id);
        }
        return $this->allChildrens;
    }

    public function nullParent() {
        return $this->get(array('Parent' => 0));
    }
    
    public function parents(& $parents = array()) {
        $class = get_class($this);
        if ($this->data[$this->parentName] != 0) {
            $r = $this->sql->select('Content', array($this->idName => $this->data[$this->parentName]), Sql::ONE);
            if ($r) {
                $parents[$r[$this->idName]] = new $class($r[$this->idName]);
                $parents[$r[$this->idName]]->parents($parents);
            }
        }
        return $parents;
    }
        
}