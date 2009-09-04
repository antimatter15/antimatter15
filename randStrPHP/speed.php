<?php
function randFast2($length = 6){
  $chars = array('a','b','c','d','e','f','g','h','i','j','k',
                 'l','m','n','o','p','q','r','s','t','u','v',
                 'w','x','y','z','!','@','*','~','_','&','-',
                 'A','B','C','D','E','F','G','H','I','J','K',
                 'L','M','N','O','P','Q','R','S','T','U','V',
                 'W','X','Y','Z', 1, 2, 3, 4, 5, 6, 7, 8, 9);
  $randStr = '';
  $chrlen = count($chars) - 1;
  for($i = 0; $i < $length; $i++){
    $randStr .= $chars[mt_rand(0, $chrlen)];
  }
  return $randStr;
}

function randFast($length = 6){
  $chars = array('a','b','c','d','e','f','g','h','i','j','k',
                 'l','m','n','o','p','q','r','s','t','u','v',
                 'w','x','y','z','!','@','*','~','_','&','-',
                 'A','B','C','D','E','F','G','H','I','J','K',
                 'L','M','N','O','P','Q','R','S','T','U','V',
                 'W','X','Y','Z', 1, 2, 3, 4, 5, 6, 7, 8, 9);
  shuffle($chars);
  return substr(implode("", $chars), 0, $length);
}

function randFast3($length = 6){
  $chars = array('a','b','c','d','e','f','g','h','i','j','k',
                 'l','m','n','o','p','q','r','s','t','u','v',
                 'w','x','y','z','!','@','*','~','_','&','-',
                 'A','B','C','D','E','F','G','H','I','J','K',
                 'L','M','N','O','P','Q','R','S','T','U','V',
                 'W','X','Y','Z', 1, 2, 3, 4, 5, 6, 7, 8, 9);
  $randStr = '';
  foreach(array_rand($chars, $length) as $key){
    $randStr .= $chars[$key];
  }
  return $randStr;
}

for($q = 0; $q < 9999; $q++){
  randFast2(10);
}
echo microtime(true)-$start;
echo "<br>";
for($q = 0; $q < 9; $q++){
  echo randString3(100)."<br>";
}
?>
