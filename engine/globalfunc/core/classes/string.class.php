<?php

class String {

    /**
      * ������� �� ��������� utf8 � win1251
      *
      * @param string $s
      * @return string
      */
    public static function utf8win1251($s) {
        $out   = "";
        $c1    = "";
        $byte2 = false;
        for ($c=0; $c < strlen($s); $c++) {
            $i = ord($s[$c]);
            if ($i <= 127) {
                $out .= $s[$c];
            }
            if ($byte2){
                $new_c2 = ($c1 & 3) * 64 + ($i & 63);
                $new_c1 = ($c1 >> 2) & 5;
                $new_i  = $new_c1 * 256 + $new_c2;
                if ($new_i == 1025) {
                    $out_i = 168;
                } elseif ($new_i == 1105) {
                    $out_i = 184;
                } else {
                    $out_i = $new_i - 848;
                }
                $out  .= chr($out_i);
                $byte2 = false;
            }
            if (($i >> 5) == 6) {
                $c1    = $i;
                $byte2 = true;
            }
        }
        return $out;
    }

    public static function isRuUtf8($str) {
        return ! preg_replace('#[\x00-\x7F]|\xD0[\x81\x90-\xBF]|\xD1[\x91\x80-\x8F]#s', '', $str );
    }

    // ������������ �� ������ ��������� �����������
    public static function utf8win1251a($s) {
        $max = sizeof($s);
        for ($i = 0; $i < $max; $i++) {
            if (is_array($s[$i])) {
                $jmax = sizeof($s[$i]);
                for ($j = 0; $j < $jmax; $j++) {
                    $s[$i][$j] = self::utf8win1251($s[$i][$j]);
                }
            } elseif (is_string($s[$i])) {
                $s[$i] = self::utf8win1251($s[$i]);
            }
        }
        return $s;
    }

    /**
     * ����������� ������� �������� � ��������
     *
     * @param string $st
     * @return string
     */
    public static function translit($st) {
        // ������� �������� "��������������" ������.
        $st=strtr($st,"������������������������_",
        "abvgdeeziyklmnoprstufh'iei");
        $st=strtr($st,"�����Ũ������������������_",
        "ABVGDEEZIYKLMNOPRSTUFH'IEI");
        // ����� - "���������������".
        $st=strtr($st,
        array(
        "�"=>"zh", "�"=>"ts", "�"=>"ch", "�"=>"sh",
        "�"=>"shch","�"=>"", "�"=>"yu", "�"=>"ya",
        "�"=>"ZH", "�"=>"TS", "�"=>"CH", "�"=>"SH",
        "�"=>"SHCH","�"=>"", "�"=>"YU", "�"=>"YA",
        "�"=>"i", "�"=>"Yi", "�"=>"ie", "�"=>"Ye"
        )
        );
        return $st;
    }

    public static function htmla($s, $strong = true) {
        $max = sizeof($s);
        for ($i = 0; $i < $max; $i++) {
            if (is_array($s[$i])) {
                $jmax = sizeof($s[$i]);
                for ($j = 0; $j < $jmax; $j++) {
                    $s[$i][$j] = self::html($s[$i][$j]);
                }
            } elseif (is_string($s[$i])) {
                $s[$i] = self::html($s[$i]);
            }
        }
        return $s;
    }

    /**
     * �������������� �������� � ������ � html �� $_GET, $_POST � �.�.
     *
     * @param string $s
     * @param boolean $strong ���������� ������� ���� ��� ���
     * @return string
     */
    public static function html($s, $strong = true) {
        if (get_magic_quotes_gpc()) {
            $s = stripslashes($s);
        }
        $s = ($strong) ? (htmlspecialchars(strip_tags($s))) : htmlspecialchars($s);
        return $s;
    }

    public static function email($email) {
        return preg_match('/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/', $email);
    }

    public static function win1251utf8($str) {
        static $table = array("\xA8" => "\xD0\x81", // �
        "\xB8" => "\xD1\x91", // �
        // ���������� �������
        "\xA1" => "\xD0\x8E", // � (�)
        "\xA2" => "\xD1\x9E", // � (�)
        "\xAA" => "\xD0\x84", // � (�)
        "\xAF" => "\xD0\x87", // � (I..)
        "\xB2" => "\xD0\x86", // I (I)
        "\xB3" => "\xD1\x96", // i (i)
        "\xBA" => "\xD1\x94", // � (�)
        "\xBF" => "\xD1\x97", // � (i..)
        // ��������� �������
        "\x8C" => "\xD3\x90", // &#1232; (A)
        "\x8D" => "\xD3\x96", // &#1238; (E)
        "\x8E" => "\xD2\xAA", // &#1194; (�)
        "\x8F" => "\xD3\xB2", // &#1266; (�)
        "\x9C" => "\xD3\x91", // &#1233; (�)
        "\x9D" => "\xD3\x97", // &#1239; (�)
        "\x9E" => "\xD2\xAB", // &#1195; (�)
        "\x9F" => "\xD3\xB3", // &#1267; (�)
        );
        return preg_replace('#[\x80-\xFF]#se',
        ' "$0" >= "\xF0" ? "\xD1".chr(ord("$0")-0x70) :
            ("$0" >= "\xC0" ? "\xD0".chr(ord("$0")-0x30) :
            (isset($table["$0"]) ? $table["$0"] : "")
            )',
        $str
        );
    }

    /**
     * ��������� ������ ��������� ������ �������� � ������ ������
     *
     * @param array $items
     * @param string $pattern
     * @param array $params ��� ����������� ����� ���������� � ��������� ��������,
     * ��� �������� ���������� ��� �������� sprintf("'%s'", $_SERVER['REQUEST_URI'])
     * @param string $message ���� ������ ������, �� ������������ ���������
     * @return string
     * @example ������ �������������
     *
     *  $class = 'list';
     *  echo itemsList(
     *      array(array('id' => 1,'name' => '������� 1'), array('id' => 2,'name' => '������� 2')),
     *      '<a class="%s" href="%s">%s</a>',
     *      array("'$class'", '$item["id"]', '$item["name"]'),
     *      '��� ���������'
     *      );
     */
    public static function itemsList($items, $pattern, $params, $message = '', $pre = '', $post = '') {
        $function = sprintf("printf('%s', %s);", str_replace("'", '\\\'', $pattern), join(', ', $params));
        $out = '';
        ob_start();
        foreach ($items as $key => $item) {
            eval($function);
        }
        $out = ob_get_contents();
        ob_end_clean();
        $out = (empty($out)) ? $message : $pre . $out . $post;
        return $out;
    }

    public function dbDate($format, $date, $mktime = false) {
        $y   = sprintf('%d', substr($date, 0, 4));
        $m   = sprintf('%d', substr($date, 5, 2));
        $d   = sprintf('%d', substr($date, 8, 2));
        $h   = sprintf('%d', substr($date, 11, 2));
        $min = sprintf('%d', substr($date, 14, 2));
        $s   = sprintf('%d', substr($date, 17, 2));
        $tm  = mktime($h, $min, $s, $m, $d, $y);
        return ($mktime) ? $tm : date($format, $tm);
    }

    public function month($index, $case = 0) {
        switch ($case) {
            case 1:
                $months = array('������','������','�������','�����','������','���','����','����','�������','��������','�������','������','�������');
                break;
            default:
                $months = array('������','������','�������','����','������','���','����','����','������','��������','�������','������','�������');
        }
        if (function_exists('lang')) {
            if (lang()) {
                $months = array('');
                for ($i = 0; $i < 12; $i++) {
                    $months[] = date('F', mktime(0, 0, 0, $i + 1, 1, 1970));
                }
            }
        }
        return $months[$index];
    }

    public function range($start, $stop, $fill = false) {
        $out = array();
        for ($i = $start; $i <= $stop; $i++) {
            if ($fill !== false) {
                $out[$i] = $fill;
            } else {
                $out[$i] = $i;
            }
        }
        return $out;
    }

    public function detecteEncoding($text, $short = 1) {
        /*
        returns:
        none - encoding not detected
        w  - windows-1251
        k  - KOI8-R
        i  - ISO
        a  - DOS 866
        */

        setlocale(LC_CTYPE, 'ru_RU');

        $x_win = array('�'=>'0.07890365448505', '�'=>'0.013981173864895', '�'=>'0.043050941306755', '�'=>'0.018687707641196', '�'=>'0.027685492801772', '�'=>'0.089285714285714', '�'=>'0.0094130675526024', '�'=>'0.01578073089701', '�'=>'0.071151716500554', '�'=>'0.013427464008859', '�'=>'0.038898117386489', '�'=>'0.044435215946844', '�'=>'0.032392026578073', '�'=>'0.072120708748616', '�'=>'0.11600221483942', '�'=>'0.024363233665559', '�'=>'0.040420819490587', '�'=>'0.054817275747508', '�'=>'0.063538205980066', '�'=>'0.024363233665559', '�'=>'0.0016611295681063', '�'=>'0.0080287929125138', '�'=>'0.0038759689922481', '�'=>'0.017303433001107', '�'=>'0.008859357696567', '�'=>'0.0024916943521595', '�'=>'0.00027685492801772', '�'=>'0.018410852713178', '�'=>'0.017995570321152', '�'=>'0.002906976744186', '�'=>'0.0065060908084164', '�'=>'0.018964562569214');
        $x_koi = array('�'=>'0.07890365448505', '�'=>'0.013981173864895', '�'=>'0.043050941306755', '�'=>'0.018687707641196', '�'=>'0.027685492801772', '�'=>'0.089285714285714', '�'=>'0.0094130675526024', '�'=>'0.01578073089701', '�'=>'0.071151716500554', '�'=>'0.013427464008859', '�'=>'0.038898117386489', '�'=>'0.044435215946844', '�'=>'0.032392026578073', '�'=>'0.072120708748616', '�'=>'0.11600221483942', '�'=>'0.024363233665559', '�'=>'0.040420819490587', '�'=>'0.054817275747508', '�'=>'0.063538205980066', '�'=>'0.024363233665559', '�'=>'0.0016611295681063', '�'=>'0.0080287929125138', '�'=>'0.0038759689922481', '�'=>'0.017303433001107', '�'=>'0.008859357696567', '�'=>'0.0024916943521595', '�'=>'0.00027685492801772', '�'=>'0.018410852713178', '�'=>'0.017995570321152', '�'=>'0.002906976744186', '�'=>'0.0065060908084164', '�'=>'0.018964562569214');
        $x_iso = array('�'=>'0.07890365448505', '�'=>'0.013981173864895', '�'=>'0.043050941306755', '�'=>'0.018687707641196', '�'=>'0.027685492801772', '�'=>'0.089285714285714', '�'=>'0.0094130675526024', '�'=>'0.01578073089701', '�'=>'0.071151716500554', '�'=>'0.013427464008859', '�'=>'0.038898117386489', '�'=>'0.044435215946844', '�'=>'0.032392026578073', '�'=>'0.072120708748616', '�'=>'0.11600221483942', '�'=>'0.024363233665559', '�'=>'0.040420819490587', '�'=>'0.054817275747508', '�'=>'0.063538205980066', '�'=>'0.024363233665559', '�'=>'0.0016611295681063', '�'=>'0.0080287929125138', '�'=>'0.0038759689922481', '�'=>'0.017303433001107', '�'=>'0.008859357696567', '�'=>'0.0024916943521595', '�'=>'0.00027685492801772', '�'=>'0.018410852713178', '�'=>'0.017995570321152', '�'=>'0.002906976744186', '�'=>'0.0065060908084164', '�'=>'0.018964562569214');
        $x_dos = array(' '=>'0.07890365448505', '�'=>'0.013981173864895', '�'=>'0.043050941306755', '�'=>'0.018687707641196', '�'=>'0.027685492801772', '�'=>'0.089285714285714', '�'=>'0.0094130675526024', '�'=>'0.01578073089701', '�'=>'0.071151716500554', '�'=>'0.013427464008859', '�'=>'0.038898117386489', '�'=>'0.044435215946844', '�'=>'0.032392026578073', '_'=>'0.072120708748616', '�'=>'0.11600221483942', '�'=>'0.024363233665559', '�'=>'0.040420819490587', '�'=>'0.054817275747508', '�'=>'0.063538205980066', '�'=>'0.024363233665559', '�'=>'0.0016611295681063', '�'=>'0.0080287929125138', '�'=>'0.0038759689922481', '�'=>'0.017303433001107', '�'=>'0.008859357696567', '�'=>'0.0024916943521595', '�'=>'0.00027685492801772', '�'=>'0.018410852713178', '�'=>'0.017995570321152', '�'=>'0.002906976744186', '�'=>'0.0065060908084164', '�'=>'0.018964562569214');

        if ($short) $text = substr($text, 0, 200);

        $len = strlen($text);
        for ($i = 0;$i < $len;$i++) {
            $let = strtolower($text[$i]);
            $t[$let]++;
        }

        if (is_array($t))
        foreach($t as $k => $v) {
            $t_win += $v * $x_win[$k];
            $t_koi += $v * $x_koi[$k];
            $t_iso += $v * $x_iso[$k];
            $t_dos += $v * $x_dos[$k];
        }

        $r = 'none';
        $tmp = max($t_win, $t_koi, $t_iso, $t_dos);
        if ($t_win == $tmp) $r = 'w';
        if ($t_koi == $tmp) $r = 'k';
        if ($t_iso == $tmp) $r = 'i';
        if ($t_dos == $tmp) $r = 'a';

        return $r;
    }

    public static function wordForm($numeric, $many, $one, $two) {
		$numeric = (int) abs($numeric);
		$out = $many;
		if (($numeric % 100 == 1 || ($numeric % 100 > 20) && ( $numeric % 10 == 1 ))) {
		    $out = $one;
		}
		if ($numeric % 100 == 2 || ($numeric % 100 > 20) && ( $numeric % 10 == 2)) {
		    $out = $two;
		}
		if ($numeric % 100 == 3 || ($numeric % 100 > 20) && ( $numeric % 10 == 3 )) {
		    $out = $two;
		}
		if ($numeric % 100 == 4 || ($numeric % 100 > 20) && ( $numeric % 10 == 4)) {
		    $out = $two;
		}
		return $numeric . ' ' . $out;
	}

	public static function ext($file) {
	    //basename()
	}

	public static function password($length = 10) {
	    $letters  = 'abcdefghijklmnopqrstuvwxwz';
	    $letters .= 'ABCDEFGHIJKLMNOPQRSTUVWXWZ';
	    $letters .= '012345678';
	    $len      = strlen($letters);
	    $pass = '';
	    for ($i = 0; $i < $length; $i++) {
	        $index = rand(0, $len - 1);
	        $pass .= $letters[$index];
	    }
	    return $pass;
	}

	public static function normfn($s) {
	    return strtolower(preg_replace('#[^0-9a-z_.]#i', '_', String::translit($s)));
	}



	public static function clear($s) {
	    return String::compressSpace(preg_replace('#[^0-9a-z_.]#i', '_', $s));
	}

	public static function parseParam($str, $values) {
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

    public function compressSpace($s) {
    	return preg_replace('/ {1,}/', ' ', $s);
    }
}