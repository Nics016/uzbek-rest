<?php 
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
$ctime = date('YmdHis');
require("dbopen.php");
$sql->query("SET character_set_results = 'cp1251', character_set_client = 'cp1251', character_set_connection = 'cp1251', character_set_database = 'cp1251', character_set_server = 'cp1251'");
require('pdf/fpdf.php');
define('FPDF_FONTPATH','pdf/font/');
class PDF extends FPDF
{
	// Load data
	function Footer()
	{
    	// Go to 1.5 cm from bottom
    	$this->SetY(-15);
	    // Select Arial italic 8
	    $this->SetFont('MW','I',8);
    	// Print centered page number
    	if(isset($_GET['en']))
	    	$this->Cell(0,10,date("H:i:s").'                                                                                                   Page '.$this->PageNo(). "                                                                                               ".date("d.m.Y"),0,0,'C');
    	else
	    	$this->Cell(0,10,date("H:i:s").'                                                                                                 Ñòðàíèöà '.$this->PageNo(). "                                                                                              ".date("d.m.Y"),0,0,'C');
	}	
/*	function Header()
	{
    	// Go to 1.5 cm from bottom
    	$this->SetY(+15);
	    // Select Arial italic 8
	    $this->SetFont('MW','I',8);
    	// Print centered page number
	    $this->Cell(0,5,"Ìåíþ ðåñòîðàíà \"Óçáåêñèòàí\"",0,1,'C');
	}*/	
	function LoadData($file)
	{
		// Read file lines
		$lines = file($file);
		$data = array();
		foreach($lines as $line)
			$data[] = explode(';',trim($line));
		return $data;
	}

	// Simple table
	function BasicTable($header, $data)
	{
		// Header
		foreach($header as $col)
			$this->Cell(40,7,$col,1);
		$this->Ln();
		// Data
		foreach($data as $row)
		{
			foreach($row as $col)
				$this->Cell(40,6,$col,1);
			$this->Ln();
		}
	}

	// Better table
	function ImprovedTable($header, $data)
	{
		// Column widths
		$w = array(40, 35, 40, 45);
		// Header
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C');
		$this->Ln();
		// Data
		foreach($data as $row)
			{
			$this->Cell($w[0],6,$row[0],'LR');
			$this->Cell($w[1],6,$row[1],'LR');
			$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
			$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
			$this->Ln();
	}
	// Closing line
	$this->Cell(array_sum($w),0,'','T');
}

// Colored table
function MenuRow($data,$fsize){
	$w = array(160, 15, 15);
	$this->SetLineWidth(0);
	$this->SetFont('Arial',"",$fsize);
	$this->Cell(160,7,$data[0],1,0,'L',false);	
	$this->SetFont('Arial',"",6);
	$this->Cell(15,7,$data[1],1,0,'L',false);	
	$this->SetFont('Arial',"B",7);
	$this->Cell(15,7,$data[2],1,0,'R',false);	
}
function FancyTable($header, $data)
	{
	// Colors, line width and bold font
	$this->SetFont('Arial',"","7");
	$this->SetFillColor(9,123,131);
	$this->SetTextColor(255);
	$this->SetDrawColor(0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	// Header
	$w = array(7,140, 13, 15, 15);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(194,238,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = false;
		$i=1;
		foreach($data as $row)
		{
			$c=0;
			$namea=array();
			$cutlen = 90;
			if(strlen($row['name'])>$cutlen){
				for($c=1;$c<=strlen($row['name']/$cutlen)+1;$c++){
					$namea[] = substr($row['name'],$c*$cutlen-$cutlen,$cutlen);
				}
			}
			if(sizeof($namea)>0){
				$ch = sizeof($namea)*4;
			}else{
				$ch=6;
			}
		$this->Cell($w[0],$ch,$i++,'LR',0,'C',$fill);
		if(sizeof($namea)>0){
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Cell($w[1],$ch," ",'LR',0,'L',$fill);
				for($c=0;$c<=strlen($row['name']/$cutlen);$c++){
					$this->text($x+1,$y+3+($c*3),$namea[$c]);			
				}	
		}else{
			$this->Cell($w[1],$ch,$row['name'],'LR',0,'L',$fill);
		}
		$this->Cell($w[2],$ch,$row['kolvo'],'LR',0,'C',$fill);
		$kolvo +=$row['kolvo'];
		$this->Cell($w[3],$ch,number_format($row['price'],2,"."," "),'LR',0,'R',$fill);
		$this->Cell($w[4],$ch,number_format($row['price']*$row['kolvo'],2,"."," "),'LR',0,'R',$fill);
		$summ += $row['price']*$row['kolvo'];
		$this->Ln();
}
// Closing line
	}
}
/*
$query = "select * from Cart where SessionId = '".(!empty($_COOKIE["sessionid"]) ? $_COOKIE["sessionid"] : "")."' and Expire > $ctime;";
$i=0;
$cartData = $sql->queryAll($query, false);
if(!empty($cartData)){
	foreach($cartData as $row){
				$price = GetPrice1($row['ItemId'], 0, $row['Part']);
				$query = 'select * from Assort where Id = '.(int)$row['ItemId'];
				$itemsarr[] = $row['ItemId'];
				$itemscnt[$row['ItemId']] = $row['Count'];
				$item = $sql->queryRow($query);
				$query = 'select * from Prices where Id='.$row['Part'];
				$options = $sql->queryRow($query);
				if($options['Name'] == ''){
				    $option = '';
				    $query = 'SELECT * 
				    FROM PricesParamValues as ppv
				    JOIN PricesParam as pp on ppv.Parent = pp.Id
				    WHERE PriceId='.$row['Part'];
				    $result = $sql->queryAll($query);
				    foreach($result as $option_row){
					$option .= iconv('UTF-8', 'windows-1251',$option_row['Name']).': '.iconv('UTF-8', 'windows-1251',$option_row['Value']).', ';
				    }
				}else{
				    $delimiter_pos = strpos($options['Name'],'|');
				    if($delimiter_pos == 0)$delimiter_pos = -1;
				    $color_name = trim(mb_substr($options['Name'],$delimiter_pos+1));
				    //$option = 'Ð Â¦Ð Ð†Ð ÂµÐ¡â€š: '.$options['Name'];
				    $option = 'Ð Â¦Ð Ð†Ð ÂµÐ¡â€š: '.iconv('UTF-8', 'windows-1251',$color_name);
				}
				//$specItems = getSpecItem($row["ItemId"]);
			$k = 1;
			$itogo = 0;
				$price = GetPrice1($row['ItemId'], 0, $row['Part']);
				$query = 'select * from Assort where Id = '.(int)$row['ItemId'];
				$itemsarr[] = $row['ItemId'];
				$itemscnt[$row['ItemId']] = $row['Count'];
				$item = $sql->queryRow($query);
				$tspecitem[$item['Id']."_".$item['Part']]['name'] = iconv('UTF-8', 'windows-1251',$item['Name']).($option!=""?" (".$option.")":"");
				$tspecitem[$item['Id']."_".$item['Part']]['kolvo'] += $row['Count'];
				$tspecitem[$item['Id']."_".$item['Part']]['price'] = $price;
	}
		
}
*/
$pdf = new PDF();
$pdf->AddFont('Arial','','Arial.php');
$pdf->AddFont('Arial','B','Arial Bold.php');
$pdf->AddFont('Tenor','','TenorSansRegular.php');
$pdf->AddFont('MW','','MerriweatherRegular.php');
$pdf->AddFont('MW','I','MerriweatherItalic.php');
$pdf->AddPage();
$pdf->SetLineWidth(0);
$nameadd = "";
if(isset($_GET['en'])){
	$nameadd = "En";
	//Seo strings EN lang
	$pdf->SetSubject('The menu of the restaurant "Uzbekistan" in Moscow: Uzbek cuisine, Arab cuisine, pan-Asian cuisine, Azerbaijani cuisine, drinks and desserts – list of food and cost.', true);
	$pdf->SetTitle('The menu of the restaurant "Uzbekistan" - Moscow', true);
	$pdf->SetAuthor('Restaurant Uzbekistan', true);
	$pdf->SetKeywords('Restaurant menu Uzbekistan, Uzbek cuisine, Arab cuisine, pan-Asian cuisine, the cuisine of Azerbaijan.', true);
}else{
	//Seo strings RU lang
	$pdf->SetSubject('Меню ресторана «Узбекистан» в Москве: узбекская кухня, арабская кухня, паназиатская кухня, азербайджанская кухня, напитки и десерты – список блюд и стоимость.', true);
	$pdf->SetTitle('Меню ресторана «Узбекистан» - Москва', true);
	$pdf->SetAuthor('Ресторан Узбекистан', true);
	$pdf->SetKeywords('Меню ресторана узбекистан, узбекская кухня, арабская кухня, паназиатская кухня, азербайджанская кухня.', true);
}

//$pdf->Image('themes/frontend/img/logo2.png',10,12,30,0,'','http://www.tob.ru');
//$pdf->SetLeftMargin(45);
menuTree(0,$pdf,0);
if(isset($_GET['bsp']))
	//$pdf->Output('D','menu-bsp_'.$nameadd.date("d_m").'.pdf'); // Плохо для SEO
	$pdf->Output('D','menu-bsp.pdf');
else
	//$pdf->Output('D','menu-uzbek_'.$nameadd.date("d_m").'.pdf'); // Плохо для SEO
	$pdf->Output('D','menu-uzbek.pdf');
function menuTree($parent,$pdf,$lev){
	global $sql,$nameadd;
	$lev++;
	$num = 0;
	if ($result = $sql->query("SELECT * FROM MenuTree where Parent = $parent and Active = 1 order by Sort")) {
		while ($row = $result->fetch_assoc()) {
			if($num != 0 && (($lev < 3 && $row['EnShow'] == 0) || ($lev < 4 && $row['EnShow'] == 1)))
				$pdf->AddPage();
			$num++;
			$pdf->SetFillColor(170+$lev*14,170+$lev*14,170+$lev*14);
			$pdf->SetFont('Tenor',"",19-$lev);
			$pdf->MultiCell(0, 9, trim($row["Name".$nameadd]) , 0 , 'C',true);
			menuTree($row['Id'],$pdf,$lev);
			menuPos($row['Id'],$pdf,$row['EnShow']);
		}
	}
}
function menuPos($parent,$pdf,$part){
	global $sql,$nameadd;
	if ($result = $sql->query("SELECT * FROM MenuItems where Parent = $parent and Active = 1 order by Sort")) {
		while ($row = $result->fetch_assoc()) {
			if($part == 1 && $row['NameEn'] != "" && $nameadd == ""){ 
				if($row["Descr".$nameadd] != "") $row['NameEn'] .= " (".$row['Descr'.$nameadd].")";
				if($nameadd == "")
					$row['Descr'] = $row['Name'];
				$row['Name'] = $row['NameEn'];
				$row['Weight'] .= $row['Weight']==""?"":" ml";
				
			}elseif($nameadd == "En" && $part == 1){
				if($row["Descr"] != "") $row['NameEn'] .= " (".$row['Descr'].")";
				$row['Weight'] .= $row['Weight']==""?"":" ml";
			}
			if($row['Name'.$nameadd] == "")
				$name = $row['Name'];
			else 
				$name = $row['Name'.$nameadd];
				
			if($row["Weight"] != "" && $row['Weight'] != " ml")$row["Weight"] = "(".$row["Weight"].")";
			$pdf->SetFont('MW',"",12);
			$lines = multiline($pdf,trim($name),55,10);			
			$pdf->SetFont('MW',"",6);
			$pdf->Cell(17,7*$lines,trim($row['Weight']),0,0,'L',false);	
			$pdf->SetFont('MW',"I",10);
			$pdf->Cell(18,7*$lines,$row['Price'],0,1,'R',false);
			if($row['Descr'.$nameadd] != ""){	
				$pdf->SetFont('MW',"I",8);
				$lines = multiline($pdf,trim($row['Descr'.$nameadd]),90,7);			
				$pdf->Cell(18,4*$lines," ",0,1,'R',false);
			}
			$pdf->SetDrawColor(210, 210, 210);
			$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
		}
	}
}
function multiline($pdf,$name,$cutlen,$height){
	$namea=array();
	$lines = 1;
	if(strlen($name)>$cutlen){
		while(strlen($name) > 0){
			$tmpname = substr($name,0,$cutlen);
				if(substr($name,$cutlen,1) != " " && $tmpname != $name){
					$tmpname = substr($tmpname,0,strrpos($tmpname," "));
				}
			$namea[] = $tmpname;
			$name = trim(substr($name,strlen($tmpname),100000));
		}
	}
	if(sizeof($namea)>0){
		$lines = sizeof($namea);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(150,sizeof($namea)*$height," ",'',0,'L');
		for($c=0;$c<=sizeof($namea)-1;$c++){
			$pdf->text($x+1,$y+($height/2)+($c*($height/2)),trim($namea[$c]));
		}
	}else{
		$pdf->Cell(150,$height-3,$name,0,0,'L',false);	
	}
	return $lines;
}
?>