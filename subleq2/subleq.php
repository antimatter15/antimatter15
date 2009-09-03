<?php
header("content-type: text/plain");
$pc = 0;

$mem = array(41, 38, 33, 36, 38, 6, 37, 37, 9, 39, 41, 12, 41, 37, 15, 41, 41, 18, 38, 41, 21, 41, 37, 41, 41, 41, 27, 37, -1, 30, 41, 41, 0, 41, 41, -1, 1, 0, 26, 65, 0, 0, 43);
/*
while($pc >= 0) {
  echo "\n>>".$pc."\n";
  
  if($mem[$pc + 1] == -1){
    print("OUT##".$mem[$pc]."\n");
    $pc += 3;
    continue;
  }
  $mem[$mem[$pc + 1]] -= $mem[$mem[$pc]];
  $pc = $mem[$pc + 1] > 0 ? $pc + 3 : $mem[$pc + 2] ;

}
*/
while ($pc >= 0){
  if($mem[$pc + 1] == -1) print(chr($mem[$mem[$pc]]));
   else $mem[$mem[$pc + 1]] -= $mem[$mem[$pc]];
   $pc = $mem[$mem[$pc + 1]] > 0 ? $pc + 3 : $mem[$pc + 2];
}

?>