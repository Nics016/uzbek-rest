<?php
class Form {
    private $data;
    private $errors;

    public function __construct($data) {
        $this->data = $data;
    }

    public function isChecked($name, $list = null) {
        $search = array();
        if ($list == null) {
            $search = $this->data;
        } else {
            if (key_exists($list, $this->data)) {
                $search = $this->data[$list];
            }
        }
        if (key_exists($name, $search) && ($search[$name] == 1)) {
            return 'checked';
        }
        return null;
    }

    /**
     * ��� ������ ���� �� ��������, ��������, �������������� �����
     *
     * @param unknown_type $name
     * @param boolean $html
     * @param boolean $utf8
     * @return string
     */
    public function field($name, $html = true, $utf8 = false) {
        if (key_exists($name, $this->data)) {
            $value = $this->data[$name];
            if ($utf8) {
                if (String::isRuUtf8($value)) {
                    $value = String::utf8win1251($value);
                }
            }
            return ($html) ? String::html($value) : $value;
        }
        return null;
    }

    public function data($name) {
        return $this->field($name, false);
    }

    public function db($name, $sql) {
        return $sql->esc($this->field($name, false));
    }

    private function email($email) {
        return String::email($email);
    }

    private function alpha($s) {
        return ! preg_match('/[^a-z�-� ]/i', $s);
    }

    private function num($s) {
        if (is_numeric($s)) {
            return true;
        }
        return false;
    }

    private function address($s) {
        return ! preg_match('/[^0-9a-z�-� \.,-]/i', $s);
    }

    private function phone($s) {
        return ! preg_match('/[^0-9-]/', $s);
    }

    private function max_len($s, $len) {
        if (strlen($s) > $len) {
            return false;
        }
        return true;
    }

    public function validate($rules) {
        $errors = array();
        foreach ($rules as $field => $options) {
            $value = trim(request($field));
            // ������������ ���� ��� ������������
            $fName = (empty($options['name'])) ? $field : $options['name'];
            if (isset($options['func'])) {
                $func = explode('|', $options['func']);
                foreach ($func as $funcName) {
                    $param = 0;
                    if (strpos($funcName, '[')) {
                        $explode  = explode('[', $funcName);
                        $funcName = $explode[0];
                        $param    = explode(']', $explode[1]);
                        $param    = $param[0];
                    }
                    if (empty($options['message'])) {
                        switch ($funcName) {
                            case 'alpha' :
                                $options[$funcName]['message'] = '���� %s ����� ��������� ������ ��������� ������� � �������';
                                break;
                            case 'address' :
                                $options[$funcName]['message'] = '���� %s ����� ��������� ������ ������� ���������� ��� ������ (�����, �����, �������, �����, �������, ������)';
                                break;
                            case 'phone' :
                                $options[$funcName]['message'] = '���� %s ����� ��������� ������ �������� ������� � ������';
                                break;
                            case 'max_len' :
                                $options[$funcName]['message'] = "���� %s ��������� ���������� ����� ($param)";
                                break;
                            case 'num' :
                                $options[$funcName]['message'] = "���� %s ������ ��������� �������� ��������";
                                break;
                            case 'email' :
                                $options[$funcName]['message'] = "���� %s ������ ��������� ���������� email";
                                break;
                        }
                    }
                    if ($funcName == 'isset') {
                        if (empty($value)  && $value !== '0') {
                            $message = (isset($options['empty'])) ? $options['empty'] : sprintf('���� %s ������ ���� �����������', $fName);
                            $errors[$field] = $message;
                            continue;
                        }
                    } else {
                        $pattern = (empty($options['message'])) ? ((empty($options[$funcName]['message'])) ? '������ � ���� %s' : $options[$funcName]['message']) : $options['message'];

                        if (is_callable(array('Form', $funcName))) {
                            if (call_user_func(array('Form', $funcName), $value, $param) == false) {
                                $errors[$field] = sprintf($pattern, $fName);
                            }
                        } elseif (is_callable($funcName)) {
                            if (call_user_func($funcName, $value, $param) == false) {
                                $message = (isset($options['message'])) ? sprintf($options['message'], $fName) : sprintf('������ � ���� %s', $fName);
                                $errors[$field] = sprintf($pattern, $fName);
                            }
                        } else {
                            $errors[$field] = sprintf('������� �������� %s �� ���������� (����: %s)', $funcName, $fName);
                        }
                    }
                }
            } else {
                $errors[$field] = sprintf('������� �������� �� ����������� (����: %s)', $fName);
            }
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function getErrors() {
        return $this->errors;
    }

    public static function label($label, $params = array()) {
        $out = '';
        if (! empty($label)) {
            $for = '';
            if (isset($params["id"])) {
                $for = sprintf('for="%s"', $params["id"]);
            }
            $out = sprintf('<label %s>%s</label>%s', $for, $label, ((empty($params['br'])) ? '' : '<br>'));
        }
        return $out;
    }

    public static function checkbox($label ='',  $params = array(), $checked = false) {
        $out = self::label($label, $params);
        $checked = ($checked) ? 'checked="checked"' : '';
        return  $out . sprintf('<input type="hidden" value="0" %1$s><input type="checkbox" %2$s value="1" %1$s>', DBList::parseParams($params), $checked);
    }

    public static function text($label ='',  $params = array()) {
        if (is_string($params)) {
            $params = array('name' => $params);
        }
        if (empty($params['class'])) {
            $params['class'] = 'text';
        }
        $out = self::label($label, $params);
        return  $out . sprintf('<input type="text" %s>', DBList::parseParams($params));
    }

    public static function textarea($label ='', $params = array(), $value = '') {
        $out = self::label($label, $params);
        return  $out . sprintf('<textarea %s>%s</textarea>', DBList::parseParams($params), $value);
    }

    public static function select($options, $label ='',  $params = array(), $selected = '') {
        $pre = sprintf('<select %s>', DBList::parseParams($params));
        $post = '</select>';
        $pre = self::label($label, $params) . $pre;
        $out = '';
        foreach ($options as $key => $option) {
            $out .= sprintf('<option value="%s" %s>%s</option>', $key, ($key == $selected) ? 'selected="selected"' : '', $option);
        }
        return (empty($out)) ? '' : $pre . $out . $post;
    }

    public static function input($label ='',  $params = array()) {
        $out = self::label($label, $params);
        return  $out . sprintf('<input %s>', DBList::parseParams($params));
    }

    public static function hidden($params = array(), $value = '') {
        $add = (is_array($params)) ? $params : array('name' => $params);
        if ($value) {
            $add += array('value' => $value);
        }
        return self::input('', array('type' => 'hidden') + $add);
    }

    public static function open($params = array(), $multipart = false) {
        if ($multipart) {
            $params = array_merge(array('method' => 'post', 'enctype' => 'multipart/form-data'), $params);
        }
        return  sprintf('<form %s>', DBList::parseParams($params));
    }

    public static function close($label ='���������') {
        return  Form::submit($label) . '</form>';
    }

    public static function submit($label ='���������') {
        return  sprintf('<input type="submit" value="%s" class="submit">', $label);
    }

    public function insert($table, $sql) {
        if ($this->data["enablemessagedate"] == 0) {
            unset($this->data["mdate"]);
        } else {
            $tmp = explode('.', $this->data["mdate"]);
            $this->data["mdate"] = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0] . ' ' . $this->data["mh"];
        }
        if ($this->data["enablecalldate"] == 0) {
            unset($this->data["cdate"]);
        } else {
            $tmp = explode('.', $this->data["cdate"]);
            $this->data["cdate"] = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0] . ' ' . $this->data["ch"];
        }
        unset($this->data["mh"]);
        unset($this->data["enablemessagedate"]);
        unset($this->data["ch"]);
        unset($this->data["enablecalldate"]);
        unset($this->data["orderSave"]);
        $rawKeys = array_keys($this->data);
        $rawValues = array_values($this->data);
        $keys = join(',', $rawKeys);
        //$keys = substr($keys, 0, -1);
        $values = join("','", $rawValues);
        //        print_r($rawKeys);
        //        print_r($rawValues);
        $query = sprintf("INSERT INTO %s (%s, added) VALUES ('%s', NOW())", $table, $keys, $values);
        //echo $query;
        $sql->query($query);

    }

    public function save($table, $sql, $addKey = '', $addValue = '', $new = true) {
        if ($new) {
            $rawKeys = array_keys($this->data);
            $rawValues = array_values($this->data);
            $keys = join(',', $rawKeys);
            $values = '';
            foreach ($rawValues as $rawValue) {
                $values .= sprintf("'%s',", $sql->esc($rawValue));
            }
            $values = substr($values, 0, -1);
            $query = sprintf("INSERT INTO `%s` (%s%s) VALUES (%s%s)", $table, $keys, $addKey, $values, $addValue);
            $sql->query($query);
            //echo $query;
        } else {
            $values = '';
            foreach ($this->data as $key => $value) {
                $values .= sprintf("`%s`='%s',", $sql->esc($key), $sql->esc($value));
            }
            $values = substr($values, 0, -1);
            if (isset($this->data['id'])) {
                $query = sprintf("UPDATE %s SET %s WHERE id = '%d'", $table, $values, $this->data['id']);
                //print_r($query);
                $sql->query($query);
            }
        }
    }

    public function setField($name, $value) {
        $this->data[$name] = $value;
    }

    public function deleteField($name) {
        if (key_exists($name, $this->data)) {
            unset($this->data[$name]);
        }
    }

}