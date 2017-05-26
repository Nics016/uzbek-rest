<?php
$ctime = sprintf("%s", date("YmdHis"));
$etime = sprintf("%s", date("YmdHis", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 3, date("Y"))));
$sql->query("delete from Cart where Expire < $ctime");

$count = request('count', 0, 'int');
$pId   = request('pId', 0, 'int');
$Id    = request('Id', 0, 'int');

$data = array(
    'Count' => ($count > 0) ? $count : 1,
    'ItemId' => $Id,
    'Part' => $pId,
);

if (! isset($_COOKIE["sessionid"])) {
    $session_id = md5(mt_rand(11111111111111, 99999999999999999));
    setcookie("sessionid", $session_id);
    $data['SessionId'] = $session_id;
    $data['Expire']    = $etime;
    $sql->insert('Cart', $data);
} else {
    $data['SessionId'] = $_COOKIE["sessionid"];
    unset($data['Count']);
    $cart = $sql->select('Cart', $data, Sql::ONE);
    if ($cart['Id']) {
        $data['id'] = $cart['Id'];
        $data['Count'] = $cart['Count'] + $count;
    } else {
        $data['Expire'] = $etime;
        $data['Count']  = $count;
    }
    $sql->save('Cart', $data);

}
print("<center>Добавлено в корзину</center><script type=\"text/javascript\">setTimeout(\"history.go(-1);\",1000);</script>");
exit();
?>