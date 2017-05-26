<?php
$back = request('back', 0);
$Id   = request('Id', 0, 'int');
$pId  = request('pId', 0, 'int');
$data = array(
    'SessionId' => $_COOKIE["sessionid"],
    'ItemId' => $Id,
    'Part' => $pId,
);
$sql->delete('Cart', $data);
if ($back) {
	header("Location: ".$back);
}else{
	header("Location: ".$HTTP_REFERER);
}
?>