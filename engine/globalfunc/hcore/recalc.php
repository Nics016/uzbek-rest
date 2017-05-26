<?php
$count = request('count');
$parts = request('pId');
$order = request('Order', 0);
while (list ($key, $val) = each ($count)) {
	$part = $parts[$key];
	$data = array(
        'Count' => ($val > 0) ? $val : 1,
	);
	$sql->update('Cart', $data, array('SessionId' => $_COOKIE["sessionid"], 'ItemId' => $key, 'Part' => $parts[$key],));
}
if ($order) {
	header("Location: /?state=order");
} else {
	header("Location: " . $HTTP_REFERER);
}
?>