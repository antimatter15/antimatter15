<?php
  include "aes.php";
  
  $aeskey = file_get_contents("store/".$_REQUEST["modulus"].".txt");
  
  $data = AESDecryptCtr($_REQUEST["data"], $aeskey, intval($_REQUEST["size"]));
  
  unlink("store/".$_REQUEST["modulus"].".txt");
  echo "DATA:". $data . "KEY:". $aeskey;
?>