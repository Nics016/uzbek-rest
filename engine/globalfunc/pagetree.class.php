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
		$ok=false;
		foreach(self::$tree as $t)
		{
			if($t==$pid)
				$ok=true;
			if($ok && $t==$par)
				return true;
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
?>