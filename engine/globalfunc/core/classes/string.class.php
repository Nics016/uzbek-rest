<?php

class String {

    /**
      * Перевод из кодировки utf8 в win1251
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

    // обрабатывает не больше двумерной вложенности
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
     * Кодирование русских символов в транслит
     *
     * @param string $st
     * @return string
     */
    public static function translit($st) {
        // Сначала заменяем "односимвольные" фонемы.
        $st=strtr($st,"абвгдеёзийклмнопрстуфхъыэ_",
        "abvgdeeziyklmnoprstufh'iei");
        $st=strtr($st,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ_",
        "ABVGDEEZIYKLMNOPRSTUFH'IEI");
        // Затем - "многосимвольные".
        $st=strtr($st,
        array(
        "ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh",
        "щ"=>"shch","ь"=>"", "ю"=>"yu", "я"=>"ya",
        "Ж"=>"ZH", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH",
        "Щ"=>"SHCH","Ь"=>"", "Ю"=>"YU", "Я"=>"YA",
        "ї"=>"i", "Ї"=>"Yi", "є"=>"ie", "Є"=>"Ye"
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
     * Подготавлявает значения к выводу в html из $_GET, $_POST и т.д.
     *
     * @param string $s
     * @param boolean $strong определяет удалять теги или нет
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
        static $table = array("\xA8" => "\xD0\x81", // Ё
        "\xB8" => "\xD1\x91", // ё
        // украинские символы
        "\xA1" => "\xD0\x8E", // Ў (У)
        "\xA2" => "\xD1\x9E", // ў (у)
        "\xAA" => "\xD0\x84", // Є (Э)
        "\xAF" => "\xD0\x87", // Ї (I..)
        "\xB2" => "\xD0\x86", // I (I)
        "\xB3" => "\xD1\x96", // i (i)
        "\xBA" => "\xD1\x94", // є (э)
        "\xBF" => "\xD1\x97", // ї (i..)
        // чувашские символы
        "\x8C" => "\xD3\x90", // &#1232; (A)
        "\x8D" => "\xD3\x96", // &#1238; (E)
        "\x8E" => "\xD2\xAA", // &#1194; (С)
        "\x8F" => "\xD3\xB2", // &#1266; (У)
        "\x9C" => "\xD3\x91", // &#1233; (а)
        "\x9D" => "\xD3\x97", // &#1239; (е)
        "\x9E" => "\xD2\xAB", // &#1195; (с)
        "\x9F" => "\xD3\xB3", // &#1267; (у)
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
     * Формирует список используя массив значений и шаблон вывода
     *
     * @param array $items
     * @param string $pattern
     * @param array $params все конструкции языка передаются в одинарных кавычках,
     * все значения передаются как например sprintf("'%s'", $_SERVER['REQUEST_URI'])
     * @param string $message если массив пустой, то возвращается сообщение
     * @return string
     * @example Пример использования
     *
     *  $class = 'list';
     *  echo itemsList(
     *      array(array('id' => 1,'name' => 'Элемент 1'), array('id' => 2,'name' => 'Элемент 2')),
     *      '<a class="%s" href="%s">%s</a>',
     *      array("'$class'", '$item["id"]', '$item["name"]'),
     *      'нет элементов'
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
                $months = array('Января','Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
                break;
            default:
                $months = array('Январь','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
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

        $x_win = array('а'=>'0.07890365448505', 'б'=>'0.013981173864895', 'в'=>'0.043050941306755', 'г'=>'0.018687707641196', 'д'=>'0.027685492801772', 'е'=>'0.089285714285714', 'ж'=>'0.0094130675526024', 'з'=>'0.01578073089701', 'и'=>'0.071151716500554', 'й'=>'0.013427464008859', 'к'=>'0.038898117386489', 'л'=>'0.044435215946844', 'м'=>'0.032392026578073', 'н'=>'0.072120708748616', 'о'=>'0.11600221483942', 'п'=>'0.024363233665559', 'р'=>'0.040420819490587', 'с'=>'0.054817275747508', 'т'=>'0.063538205980066', 'у'=>'0.024363233665559', 'ф'=>'0.0016611295681063', 'х'=>'0.0080287929125138', 'ц'=>'0.0038759689922481', 'ч'=>'0.017303433001107', 'ш'=>'0.008859357696567', 'щ'=>'0.0024916943521595', 'ъ'=>'0.00027685492801772', 'ы'=>'0.018410852713178', 'ь'=>'0.017995570321152', 'э'=>'0.002906976744186', 'ю'=>'0.0065060908084164', 'я'=>'0.018964562569214');
        $x_koi = array('б'=>'0.07890365448505', 'в'=>'0.013981173864895', 'Ч'=>'0.043050941306755', 'з'=>'0.018687707641196', 'д'=>'0.027685492801772', 'е'=>'0.089285714285714', 'Ц'=>'0.0094130675526024', 'Ъ'=>'0.01578073089701', 'й'=>'0.071151716500554', 'к'=>'0.013427464008859', 'л'=>'0.038898117386489', 'м'=>'0.044435215946844', 'н'=>'0.032392026578073', 'о'=>'0.072120708748616', 'п'=>'0.11600221483942', 'Р'=>'0.024363233665559', 'Т'=>'0.040420819490587', 'У'=>'0.054817275747508', 'Ф'=>'0.063538205980066', 'Х'=>'0.024363233665559', 'ж'=>'0.0016611295681063', 'и'=>'0.0080287929125138', 'г'=>'0.0038759689922481', 'Ю'=>'0.017303433001107', 'Ы'=>'0.008859357696567', 'Э'=>'0.0024916943521595', 'Я'=>'0.00027685492801772', 'Щ'=>'0.018410852713178', 'Ш'=>'0.017995570321152', 'Ь'=>'0.002906976744186', 'а'=>'0.0065060908084164', 'С'=>'0.018964562569214');
        $x_iso = array('Р'=>'0.07890365448505', 'С'=>'0.013981173864895', 'Т'=>'0.043050941306755', 'У'=>'0.018687707641196', 'Ф'=>'0.027685492801772', 'Х'=>'0.089285714285714', 'Ц'=>'0.0094130675526024', 'Ч'=>'0.01578073089701', 'Ш'=>'0.071151716500554', 'Щ'=>'0.013427464008859', 'Ъ'=>'0.038898117386489', 'Ы'=>'0.044435215946844', 'Ь'=>'0.032392026578073', 'Э'=>'0.072120708748616', 'Ю'=>'0.11600221483942', 'Я'=>'0.024363233665559', 'а'=>'0.040420819490587', 'б'=>'0.054817275747508', 'в'=>'0.063538205980066', 'г'=>'0.024363233665559', 'д'=>'0.0016611295681063', 'е'=>'0.0080287929125138', 'ж'=>'0.0038759689922481', 'з'=>'0.017303433001107', 'и'=>'0.008859357696567', 'й'=>'0.0024916943521595', 'к'=>'0.00027685492801772', 'л'=>'0.018410852713178', 'м'=>'0.017995570321152', 'н'=>'0.002906976744186', 'о'=>'0.0065060908084164', 'п'=>'0.018964562569214');
        $x_dos = array(' '=>'0.07890365448505', 'с'=>'0.013981173864895', 'т'=>'0.043050941306755', 'у'=>'0.018687707641196', 'ф'=>'0.027685492801772', 'х'=>'0.089285714285714', 'ц'=>'0.0094130675526024', 'ч'=>'0.01578073089701', 'ш'=>'0.071151716500554', 'щ'=>'0.013427464008859', 'ъ'=>'0.038898117386489', 'ы'=>'0.044435215946844', 'ь'=>'0.032392026578073', '_'=>'0.072120708748616', 'ю'=>'0.11600221483942', 'я'=>'0.024363233665559', 'а'=>'0.040420819490587', 'б'=>'0.054817275747508', 'в'=>'0.063538205980066', 'г'=>'0.024363233665559', 'д'=>'0.0016611295681063', 'е'=>'0.0080287929125138', 'ж'=>'0.0038759689922481', 'з'=>'0.017303433001107', 'и'=>'0.008859357696567', 'й'=>'0.0024916943521595', 'к'=>'0.00027685492801772', 'л'=>'0.018410852713178', 'м'=>'0.017995570321152', 'н'=>'0.002906976744186', 'о'=>'0.0065060908084164', 'п'=>'0.018964562569214');

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