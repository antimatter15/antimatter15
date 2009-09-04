<?php
include "rsa_aes.php";
if($_REQUEST["act"] == "getkey"){
  $len = 16;
  $base='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789~!@#$%^&*()_+-=[]{}|;:<>,./';
  $max=strlen($base)-1;
  $aeskey='';
  mt_srand((double)microtime()*1000000);
  while (strlen($aeskey)<$len+1)
    $aeskey.=$base{mt_rand(0,$max)};

  echo bin2hex(rsa_encrypt($aeskey, intval($_REQUEST["public"]), $_REQUEST["modulus"], $_REQUEST["size"]));
  file_put_contents("store/".$_REQUEST["modulus"].".txt", $aeskey); //this here, destroys all security
}else{
  $aeskey = file_get_contents("store/".$_REQUEST["modulus"].".txt");
  $data = AESDecryptCtr($_REQUEST["data"], $aeskey, intval($_REQUEST["size"]));
  unlink("store/".$_REQUEST["modulus"].".txt");
  echo "DATA:". $data . "KEY:". $aeskey;
}
?>