<?php
if ($itemId) {
    $data = $sql->select($table, array('Id' => $itemId), Sql::ONE);
}