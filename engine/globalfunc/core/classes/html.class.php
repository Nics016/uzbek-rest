<?php
class Html {
    public static function pager($pageCount, $url, $page, $prev = '&lt;&lt;', $next = '&gt;&gt;') {
        $pager = '';
        if ($pageCount > 1) {
            for ($i = 1; $i <= $pageCount; $i++) {
                if ($i == $page) {
                    $pager .= sprintf('<strong>[%s]</strong> ', $i);
                } else {
                    $pager .= sprintf('<a href="%s">%s</a> ', String::parseParam($url, array('page' => $i)), $i);
                }
            }
            if ($page > 1) {
                $pager = sprintf('<a href="%s">%s</a> ', String::parseParam($url, array('page' => $page - 1)), $prev) . $pager;
            }
            if ($page < $pageCount) {
                $pager .= sprintf('<a href="%s">%s</a> ', String::parseParam($url, array('page' => $page + 1)), $next);
            }
        }
        return $pager;
    }

    public static function menu($items, $url) {
        $menu = '';
        foreach ($items as $value) {
            $menu .= sprintf('<li><a href="%s">%s</a></li>', $url .  $value['Id'], $value['Name']);
        }
        if ($menu) {
            $menu = sprintf('<ul %s>', DBList::parseParams(array('class' => 'slmMenu'))) . $menu . '</ul>';
        }
        return $menu;

    }

    public static function link($name, $href = array(), $param = array()) {
        $url = (is_array($href)) ? href($href) : $href;
        return sprintf('<a href="%s" %s>%s</a>', $url, DBList::parseParams($param), $name);

    }

//    public static function table($data, $param = array()) {
//        //return sprintf('<a href="%s" %s>%s</a>', href($href), DBList::parseParams($param), $name);
//    }

    public static function h($s, $ind = 1, $style = '') {
    	$style = empty($style) ? '' : ' style="'.$style.'"';
        return "<h{$ind}{$style}>{$s}</h{$ind}>";
    }

}
