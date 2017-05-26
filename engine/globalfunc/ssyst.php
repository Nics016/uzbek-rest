<?php
$botuserag = array(
    "Yandex/1",
    "StackRambler/",
    "msnbot/",
    "Mozilla/5.0 (compatible; Yahoo!",
    "Mozilla/4.0 compatible ZyBorg/1.",
    "Gigabot/2.0",
    "Googlebot",
    "Aport",
	"eStyleSearch",
	"Mediapartners-Google",
	"NetStat.Ru Agent",
	"ichiro/1.0",
	"TurnitinBot/2.0",
	"Twiceler www.cuill.com",
	"psbot/0.1",
	"ConveraCrawler/",
	"DomainsDB.net",
	"eStyleSearch",
	"e-SocietyRobot",
	"Turtle",
	"MJ12bot",
	"Z-Add Link",
	"WebCopier",
	"Web Download",
	"Twiceler",
	"Teleport",
	"Mozilla/5.0 (compatible; Googlebot",
	"Goon",
	"Gokubot",
	"Adre",
	"imrt_5",
	"ia_arc",
	"WebAlta",
	"NutchCVS",
	"Mozilla/5.0 (Windows;) NimbleCrawler",
	"findlinks",
	"Accoona-AI-Agent",
	"Syntryx ANT",
	"voyager/1.0",
	"Goku/",
	"ESC V1.0alfa",
	"Exabot",
	"LinkWalker",
	"StackSearch Crawler"
);
$searchbot = 0;
$ua        = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
for($i=0, $max = sizeof($botuserag); $i < $max; $i++) {
    if (substr(strtolower($ua), 0, strlen($botuserag[$i])) == strtolower($botuserag[$i])) {
        $searchbot = 1;
    }

}
$botuseragm = array("Yandex/1.01.001 (compatible; Win16; M)");
for($i=0; $i<sizeof($botuseragm); $i++){
	if (substr($_SERVER['HTTP_USER_AGENT'],0,strlen($botuseragm[$i])) == $botuseragm[$i]) {
		$searchbot = 0;
	}
}

function chlet($in){
	global $searchbot;
	$from = array("À","Â","Ê","Å","Í","Õ","Ð","Î","Ñ","Ì","Ò","å","õ","à","ð","î","ñ");
	$frto = array("A","B","K","E","H","X","P","O","C","M","T","e","x","a","p","o","c");
	if($searchbot == 1){
		return str_replace($from, $frto, $in);
	}else{
		return $in;
	}
}
?>