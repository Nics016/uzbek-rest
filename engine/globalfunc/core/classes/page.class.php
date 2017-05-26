<?php
class Page extends TreeStruct {
    
    public function __construct($id = 0) {
        parent::__construct('Content', 'Id', 'Parent', $id);
        $this->load();
    }

    public function all() {
        return $this->get(array('Parent' => 0, 'Active' => 1, 'orderby' => 'Sort'));
    }

    public static function menu($parent = 0, $levels = 1, $level = 0) {
        global $sql;
        static $out = '';
        $r = $sql->query("SELECT * FROM Content WHERE Parent = {$parent} ORDER BY Sort");
        if ($level < $levels) {
            $out .= '<ul>';
            while ($row = $r->fetchRow()) {
                $out .= sprintf('<li><a href="%s">%s</a>', href(array('pageId' => $row['Id'])), $row['MenuName']);
                self::menu($row['Id'], $levels, $level + 1);
                $out .= '</li>';
            }
            $out .= '</ul>';
        }
        return $out;
    }
}
