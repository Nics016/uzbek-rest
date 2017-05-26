<?php

class PageTree
{
	static $tree=array();
	
	static function getTree($pid)
	{
		self::$tree[]=$pid;
		do
		{
			$result=mysql_query("SELECT * FROM Content WHERE Id=".end(self::$tree)." AND Id!=1");
			$res=mysql_fetch_assoc($result);
			self::$tree[]=$res['Parent'];
		}while(end(self::$tree)>0);
	}

	static function isParent($pid,$par)
	{
		foreach(self::$tree as $t)
			if($t==$pid)
				return true;
		return false;
	}

	static function getParent($pid)
	{
		$result=mysql_query("SELECT * FROM Content WHERE Parent=0 AND Id!=1");
		while($res=mysql_fetch_assoc($result)){
		    if(self::isParent($pid,$res['Id']))
			return $res['Id'];
		}
		return false;
	}

	static function isLast($pid)
	{
		return self::$tree[0]==$pid;
	}	
	
	static function root()
	{
		if(count(self::$tree)==0)
			return false;
		return self::$tree[count(self::$tree)-1];
	}

	static function getId($level)
	{
		if(count(self::$tree)<$level)
			return false;
		return self::$tree[count(self::$tree)-$level];
	}

	static function prePreRoot()
	{
		if(count(self::$tree)<3)
			return false;
		return self::$tree[count(self::$tree)-3];
	}

	static function preRoot()
	{
		if(count(self::$tree)<2)
			return false;
		return self::$tree[count(self::$tree)-2];
	}
}

function addFancybox(&$pcontent){
    $offset = 0;
    //$step = 0;
    while($img_begin = strpos($pcontent, '<img', $offset)){
	$src_begin = strpos($pcontent, 'src="', $img_begin) + 5;
	$src_end = strpos($pcontent, '"', $src_begin) + 1;
	$src = substr($pcontent,$src_begin,$src_end-$src_begin-1);
	$img_end = strpos($pcontent, '/>', $src_end) + 2;
	$img_tag = substr($pcontent,$img_begin,$img_end-$img_begin);
	$first_part = substr($pcontent,0,$img_begin);
	$second_part = substr($pcontent,$img_end);
	$pcontent = $first_part.'<a class="fancy" rel="fb" href="'.$src.'">'.$img_tag.'</a>'.$second_part; 
	$offset = $img_end+4;
    }
}

function getMenus($pId,$path)
{
	global $pageId;
	if($pId == 100) return '';
	$out='<ul>';
	//echo "SELECT * FROM Content WHERE Parent=".$pId." AND Id!=1 AND Active=1";
	$result=mysql_query("SELECT * FROM Content WHERE Parent=".$pId." AND Id!=1 AND Active=1 order by Sort");
	$first=true;
	while($res=mysql_fetch_assoc($result))
	{
		if($res['Url'] =='' && $res['Mirror'] != 0)
		{
		    $subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
		    $subres = mysql_fetch_assoc($subres);
		    if($subres['Url'] == '' && $subres['Mirror'] != 0){
			$subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
			$subres = mysql_fetch_assoc($subres);
			$res['Url'] = $subres['Url'];
		    } else {
		    $res['Url'] = $subres['Url'];
		    }
		}
		$class="";
		if($first)
		{
			$class=' class="noborder"';
			$first=false;
		}
		$active='';
		$tag='a';
		if($pageId==$res['Id']){// || PageTree::isParent($res['Id'],$pageId)){
			$active='  style="font-weight:700;color: #4a2824;"';
			$tag='span ';

		}
		//$out.='<li'.$class.'><'.$tag.$active.' href="'.$res['Url'].'">'.$res['MenuName'].'</'.$tag.'>'.getMenus($res['Id']).'</li>';
		$out.='<li'.$class.'><'.$tag.$active.' href="'.GenURL($res['Id']).'">'.$res['MenuName'].'</'.$tag.'>';
		if(in_array($res['Id'],$path)){
		    $out .= getMenus($res['Id'],$path);
		}
	}
		$out .='</li>';
	if($first)
		return '';
	return $out.'</ul>';
}

function getLeftMenus($pId)
{
	global $pageId;
	$path = array();
	getPath1($pId,$path);
	array_pop($path);

	//foreach($path as $node) echo $node.'-';
	//$parent = end($path);
	$parent = 0; 
	$result = mysql_query("select * from Content where Id=".$parent." and Active=1 order by Sort");
	$result = mysql_fetch_assoc($result);
	echo '<h2>'.$result['MenuName'].'</h2>';
	//print_r($path);
	$out='<ul>';
	$result = mysql_query("select * from Content where Parent=".$parent." and Active=1 order by Sort");
	//echo "select * from Content where Parent=".end($path);
	while($res = mysql_fetch_assoc($result)){
	    //var_dump($res);
		if($res['Url'] =='' && $res['Mirror'] != 0)
		{
		    $subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
		    $subres = mysql_fetch_assoc($subres);
		    if($subres['Url'] == '' && $subres['Mirror'] != 0){
			$subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
			$subres = mysql_fetch_assoc($subres);
			$res['Url'] = $subres['Url'];
		    } else {
		    $res['Url'] = $subres['Url'];
		    }
		}
		$sub = '';
		$tag='a';
		$active = '';
		if($pageId==$res['Id']){ //|| PageTree::isParent($res['Id'],$pageId)){
			$active=' style="font-weight:700;color: #4a2824;"'; 
			$tag = 'span';
		}
		if(in_array($res['Id'],$path) && $pageId != 28){
			$sub = getMenus($res['Id'],$path);
		}

		$out.='<li><'.$tag.$active.' href="'.GenURL($res['Id']).'">'.$res['MenuName'].'</'.$tag.'>'.$sub.'</li>';
	}
	return $out.'</ul>';
	//return getMenus($parent,$path);
}


function getPath1($pId, &$path)
{
	$path[]=$pId;
	do
	{
		$result=mysql_query("SELECT * FROM Content WHERE Id=".end($path)." AND Id!=1");
		$res=mysql_fetch_assoc($result);
		$path[]=$res['Parent'];
	}while(end($path)>0);
	return $path;
}

function getPathWay($pId, $path = array())
{
	array_unshift($path, $pId);
	//echo $pId." ";
	$result=mysql_query("SELECT * FROM Content WHERE Id=".$pId);
	while($res=mysql_fetch_assoc($result))
		if($res['Parent'] != 0)
			$path = getPathWay($res['Parent'],$path);
	return $path;
}
function genMenuItem($item) {
	return '<li><a jump="'.$item[1].'" href="'.$item[0].'">'.$item[2].'</a></li>';
}

function getFirstLevelMenus($pId,$f=false) {
	global $pageId;
	if($pId == 100) return '';
	$out = $f ? '<ul id="nav">' : '<ul>';
	$result = mysql_query("SELECT * FROM Content WHERE Parent=".$pId." AND Id!=1 AND Active=1 order by Sort");
	$first = true;
	while($res = mysql_fetch_assoc($result)) {		
		if($res['Id'] == '180') break;		
		if($res['Url'] =='' && $res['Mirror'] != 0){
		    $subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
		    $subres = mysql_fetch_assoc($subres);
		    if($subres['Url'] == '' && $subres['Mirror'] != 0) {
				$subres = mysql_query("SELECT * FROM Content WHERE Id=".$res['Mirror']."  AND Active=1");
				$subres = mysql_fetch_assoc($subres);
				$res['Url'] = $subres['Url'];
			} else {
				$res['Url'] = $subres['Url'];
		    }
		}		
		//echo $res['Url'];
		$class = "";		
		if($first) {
			$class=' class="noborder"';
			$first=false;
		}
		$active='';
		$wrap_start = '';
		$wrap_end = '';
		$tag='a';
		if($pageId==$res['Id'] || PageTree::isParent($res['Id'],$pageId)){
			$active=' class="active"';
			$wrap_start = '<span class="nav_but_l"><span class="nav_but_r"><span class="nav_but_c">';
			$wrap_end = '</span></span></span>';
		}
		$out.='<li><'.$tag.$active.' href="/'.$res['Url'].'">'.$wrap_start.$res['MenuName'].$wrap_end.'</'.$tag.'></li>';
	}
	if($first)
		return '';
	return $out.'</ul>';
}

function getMonth($i)
{
	switch($i)
	{
		case 1: return 'Января';
		break;
		case 2: return 'Февраля';
		break;
		case 3: return 'Марта';
		break;
		case 4: return 'Апреля';
		break;
		case 5: return 'Мая';
		break;
		case 6: return 'Июня';
		break;
		case 7: return 'Июля';
		break;
		case 8: return 'Августа';
		break;
		case 9: return 'Сентября';
		break;
		case 10: return 'Октября';
		break;
		case 11: return 'Ноября';
		break;
		case 12: return 'Декабря';
		break;
	}
}

function formDate($sqlDateTime)
{
	return intval(substr($sqlDateTime,8,2)).' '.mb_strtolower(getMonth(intval(substr($sqlDateTime,5,2)))).' '.substr($sqlDateTime,0,4);;
}
?>
