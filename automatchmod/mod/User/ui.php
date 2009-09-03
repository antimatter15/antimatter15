<?php
  $superstart = microtime(true);
  include "functions.php";
  echo "Status: ";
  $act = $_REQUEST['act'];
  if($act == "patch"){
    $res = installPatch($_REQUEST['package']);
    if($res == 1){
      echo $_REQUEST['package']." sucessfully installed!";
    }else{
      echo "The plugin may have conflicts or may be already installed".$res;
    }
  }elseif($act == "unpatch"){
    $res = uninstallPatch($_REQUEST['package']);
    if($res == 1){
      echo $_REQUEST['package']." sucessfully removed!";
    }else{
      echo "The plugin may have conflicts or may be already installed".$res;
    }
  }elseif($act == "upload"){
    $data = file_get_contents($_FILES['uploadedfile']['tmp_name']);
    if(strlen($data) == 0){
      echo "Nothing Uploaded!";
    }else{
      echo "Mod uploaded successfully!";
      echo addPatchText($data);
    }
  }else{
    echo "Nothing...";
  }
  //else{
    echo "<h2>Install/Uninstall</h2>";
    $dba = getDBInfo();
    if(count($dba) == 0){
      echo "It looks like you have no mods in the database! Go add some!";
    }
    foreach($dba as $key=>$value){
      echo $value['name']." (<i>$key</i>) - ";
      if($value['installed']){
        echo "<a href='?act=unpatch&package=$key'>Uninstall</a> ";
      }else{
        echo "<a href='?act=patch&package=$key'>Install</a> ";
      }
      echo "<a href='Data/$key.patch'>Download</a> ";
      echo "<br>";
    }
    
    echo "<h2>Upload</h2>";
    echo '<form enctype="multipart/form-data" 
            action="?act=upload" method="POST">
          <input name="uploadedfile" type="file" />
          <input type="submit" value="Upload Patch" />
          </form>';
  //}

echo "\n\n<br>\nCreated Page In: ".(microtime(true)-$superstart);
?>