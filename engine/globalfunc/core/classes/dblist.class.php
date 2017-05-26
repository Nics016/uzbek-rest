<?php
class DBList {
    private $table;
    private $sql;
    private $tableParams = array();
    private $actions     = array();
    private $hiddenCols  = array();
    private $paramCols   = array();
    private $trimCols    = array();
    private $query       = null;
    private $parseLinkParamVarsMode;
    
    public function __construct($table, $sql, $options = array()) {
        $this->sql = $sql;
        $this->table = $table;
        $this->tableParams = $options;
        $this->parseLinkParamVarsMode = false;
    }

    public function setTableParams($params) {
        $this->tableParams = (array)$params;
    }

    public function setHiddenCols($hiddenCols) {
        $this->hiddenCols = $hiddenCols;
    }

    public function setParamCols($paramCols) {
        $this->paramCols = $paramCols;
    }

    public function setTrimCols($trimCols) {
        $this->trimCols = $trimCols;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    private function getTableData() {
        if (null !== $this->query) {
            return $this->sql->queryAll($this->query, false);
        } else {
            return $this->sql->queryAll(sprintf("SELECT * FROM `%s` ORDER BY id", $this->table), false);
        }
    }

    public function display($noItem = '<h1>Нет данных</h1>') {
        $this->data = $this->getTableData();
        $out = '';
        foreach ($this->data as $element) {
            $out .= $this->line($element);
        }
        $params = self::parseParams($this->tableParams);
        $headers = $this->printHeaders();
        echo (empty($out)) ? $noItem : sprintf('<table %s>%s%s</table>',$params, $headers, $out);
    }

    private function printHeaders() {
        $out = '';
        //$out .= '<th>' . Form::checkbox() . '</th>';
        $headers = $this->getHeaders();
        foreach ($headers as $key => $value) {
            if (! in_array($value, $this->hiddenCols)) {
                $paramsOut = '';
                if (isset($this->paramCols[$value])) {
                    $paramsOut = $this->parseParams($this->paramCols[$value]);
                }
                $out .= sprintf('<th %s>%s</th>', $paramsOut, $value);
            }
        }
        foreach ($this->actions as $key => $value) {
            if ((! isset($value['type'])) || $value['type'] == 'indiv') {
                $out .= sprintf('<th>%s</th>', $value['name']);
            }
        }
        return empty($out) ? '' : sprintf('<tr>%s</tr>', $out);
    }

    private function line($element) {
        //FIXME упростить функцию
        $out = '';
        foreach ($element as $key => $column) {
            if (! in_array($key, $this->hiddenCols)) {
                $out .= '<td>';
                $actionOut = '';
                if (isset($this->trimCols[$key])) {
                    $column  = substr($column, 0, $this->trimCols[$key]);
                }
                foreach ($this->actions as $id => $action) {
                    if ((isset($action['type'])) && ($action['type'] == $key)) {
                        if (isset($action['link'])) {
                            $title 		=	isset($action['title'])
                            				? self::parseVars($action['title'], $element)
                            				: '';
                            $href  		=	self::parseVars($action['link'], $element);
                            $linkParams =	(isset($action['params']))
                            				? ' ' . self::parseParams($action['params'])
                            				: '';
                            $actionOut .= sprintf('<a href="%s" title="%s"%s>%s</a>', $href , $title, $linkParams, $column);
                        } else {
                            $actionOut .= $column;
                        }
                    }
                }
                
                $out .= (empty($actionOut)) ? $column : $actionOut;
                $out .= '</td>';
            }

        }
        foreach ($this->actions as $key => $value) {
            if ((! isset($value['type'])) || $value['type'] == 'indiv') {
                $out .= '<td>';
                $content = self::parseVars($value['content'], $element);
                if (isset($value['link'])) {
                    $title		=	isset($value['title'])
                    				? self::parseVars($value['title'])
                    				: '';
                    $linkParams =	isset($action['params'])
                    				? ' ' . self::parseParams($value['params'], $this->parseLinkParamVarsMode, $element)
                    				: '';
                    $out .= '<a href="'.self::parseVars($value['link'], $element).'" title="'.$title.'"'.$linkParams.'>'.$content.'</a>';
                } else {
                    $out .= $content;
                }
                $out .= '</td>';
            }
        }
        return empty($out) ? '' : sprintf('<tr>%s</tr>', $out);
    }

    private static function parseVars($str, $values) {
        $vars = explode('{', $str);
        foreach ($vars as & $var) {
            $delimPos = strpos($var, '}');
            if ($delimPos !== false) {
                $index = substr($var, 0, $delimPos);
                $var = $values[$index] . substr($var, $delimPos + 1);
            }
        }
        return join($vars);
    }

    public static function parseParams($params, $parseVars = false, $element = null) {
        $out = '';
        unset($params['br']);
        foreach ($params as $key => $value) {
        	if ($parseVars) {
        		$value = self::parseVars($value, $element);
        	}
            $out .= sprintf('%s="%s" ', $key, $value);
        }
        return $out;
    }

    public function getHeaders() {
        $this->sql->db->loadModule('Manager');
        return $this->sql->db->manager->listTableFields($this->table);
    }

    /**
     * Добавление действия над строкой таблицы
     *
     * @example
     * $fromTable = new DBList('linkfrom', $sql);
     * $fromTable->setTableParams(array('class' => 'etbl'));
     * $deleteOnclick = array('onclick' => 'if (confirm(\'Вы действительно хотите удалить элемент?\')) { return true; } else { return false; }');
     * $fromTable->addAction(array('name' => 'Ссылка', 'content' => '&from={id}'));
     * $fromTable->addAction(array('type' => 'name', 'link' => $link . '&param[1]={id}', 'title' => 'Редактировать {name}'));
     * $fromTable->addAction(array('name' => 'X', 'link' => $link . '&param[0]={id}', 'content' => 'X', 'params' => $deleteOnclick));
     * $fromTable->display();
     *
     * @param array $action
     * name    - заголовок столбца
     * content - содержимое строк добавляемого столбца
     * link    - ссылка каждой строки столбца (может включать названия столбцов, например &param[0]={id})
     * type    - если в типе название столбца, то сслыка делается именно с этого столбца
     * params  - array атрибуты ссылки
     */
    public function addAction($action) {
        $this->actions[] = $action;
    }

    public function addDeleteAction($url = '', $idName = 'Id', $paramIndex = '10') {
        if (!$url) {
            $url = addonLink();
        }
        $deleteOnclick = array('onclick' => 'return confirm(\'Вы действительно хотите удалить элемент?\');');
        $this->addAction(array(	'name'		=> 'X',
        						'link'		=> $url . '&param[' .$paramIndex. ']={' . $idName . '}',
        						'content'	=> 'X',
        						'params'	=> $deleteOnclick));
    }
    
    /**
     * Обрабатывать ли переменные в параметрах ссылок
     *
     * @param	bool $mode
     * @return	void
     */
    public function setLinkParamVarsParseMode($mode) {
    	$this->parseLinkParamVarsMode = $mode;
    }
}