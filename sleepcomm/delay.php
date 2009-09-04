<?php
$data = array("h","e","l","l","o");
$char = $data[intval($_REQUEST['part'])];

$code = strpos("abcdefghijklmnopqrstuvxyz0123456789ABCDEFGHIJKLMNOPQRSTUVXYZ", $char);

//$code = ord($data[intval($_REQUEST['part'])]);

usleep(100000 * $code);

echo file_get_contents("blank.bmp");

?>