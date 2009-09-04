<?php
include "rsa.php";

$len = 16;
$base='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789~!@#$%^&*()_+-=[]{}|;:<>,./';
$max=strlen($base)-1;
$aeskey='';
mt_srand((double)microtime()*1000000);
while (strlen($aeskey)<$len+1)
  $aeskey.=$base{mt_rand(0,$max)};


echo bin2hex(rsa_encrypt($aeskey, intval($_REQUEST["public"]), $_REQUEST["modulus"], $_REQUEST["size"]));

file_put_contents("store/".$_REQUEST["modulus"].".txt", $aeskey); //this here, destroys all security
?>