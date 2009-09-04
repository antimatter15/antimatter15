<?php
  $fh = fopen("db.txt", 'a');
  fwrite($fh, "\n".$_REQUEST['result'].">>".$_REQUEST['count']);
  fclose($fh);

?>