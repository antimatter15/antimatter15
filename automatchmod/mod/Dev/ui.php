<?php
  function diffHighlight($str){
    echo "<style>pre{margin:0}</style>";
    foreach(explode("\n",$str) as $line){
      if(substr($line, 0, 4) == "====" ||
         substr($line, 0, 3) == "---" ||
         substr($line, 0, 3) == "+++"){
        echo "<pre style='background-color: yellow'>$line</pre>";
      }elseif(substr($line, 0, 1) == "+"){
        echo "<pre style='background-color: #00E421'>$line</pre>";
      }elseif(substr($line, 0, 1) == "-"){
        echo "<pre style='background-color: #F36F6F'>$line</pre>";
      }elseif(substr($line, 0, 2) == "@@"){
        echo "<pre style='color: red'>$line</pre>";
      }elseif(substr($line, 0, 1) != " "){
        echo "<pre style='color: green'>$line</pre>";
      }else{
        echo "<pre>$line</pre>";
      }
    }
  }
  
  $superstart = microtime(true);
  if(file_exists("Data/database.txt")){
    $status = file_get_contents("Data/database.txt");
  }else{
    $status = "";
  }
  include "functions.php";

  $act = $_REQUEST['act'];
  
  if($act == "start"){

    include_once "Lib/Archive/Tar.php";
    $tar_object = new Archive_Tar("Data/Cache.tar");
    $tar_object->create(array());
    createPatch(true);
    file_put_contents("Data/database.txt", "develop");
    header("location: ".basename(__file__));
  }elseif($act == "stop"){
    $dat = createPatch();
    $name = $_REQUEST['pname'];
    $ver = $_REQUEST['pver'];
    $sum = $_REQUEST['psum'];
    if($name && $ver && $sum){
      file_put_contents("Data/database.txt", "");
      $fn = "mod_".$_REQUEST['pname']."_v".$_REQUEST['pver'];
      file_put_contents("Data/$fn.patch", "fn=$fn\nname=$name\nversion=$ver\nsummary=$sum\n\n".$dat[1]);    
      echo "Done! Your package can be found in your Data/ directory as <a href='Data/$fn.patch'>$fn.patch</a>";
    }else{
      echo "Error! Form not complete! Please go <a href='?act=finish'>back</a> and repeat the form";
      exit;
    }

  }elseif($act == "cancel"){
    file_put_contents("Data/database.txt", "");
    echo "Done! You fail at life, the universe, and everything.";
  }elseif($act == "finish"){
    if($status == ""){
      die("Error! You haven't even started coding yet, What kind of twisted world do you live in?");
    }
    echo "<h2>Finalize Mod</h2>
          <form method='post' action='?act=stop' onsubmit='if(!confirm(\"Are you sure?\"))return false;'>
            <table>
              <tr>
                <td>
                  <label for='pname'>Name</label>
                </td>
                <td>
                  <input name='pname' type='text' value='NewApp".rand(10000,99999)."'>
                </td>
              </tr>
              <tr>
                <td>
                  <label for='pver'>Version</label>
                </td>
                <td>
                  <input name='pver' type='text' value='1'>
                </td>
              </tr>
              <tr>
                <td>
                  <label for='psum'>Summary</label>
                </td>
                <td>
                  <input name='psum' type='text' value='An Insanely Great App'>
                </td>
              </tr>
            </table>
            <input type='submit' value='Finish'>
            </form>";
  }//else{
  //  echo "Status: Nothing...";
  //}

  
  if(file_exists("Data/database.txt")){
    $status = file_get_contents("Data/database.txt");
  }else{
    $status = "";
  }

  //echo " (<i>$key</i>) - ";
  if($act != "finish"){
    echo "<h2>Development</h2>";
    if($status == "develop"){
      echo "<a href='?act=finish'>Finalize Mod</a><br>";
      echo "<a href='?act=cancel' onclick='if(!confirm(\"Are you sure?\"))return false;'>Cancel Development</a><br>";
      echo "<a href='javascript:window.location.reload()'>Reload Page</a> ";
      $data = createPatch();
      echo "<h2>Patch</h2>";
      if($data[1] == "") $data[1] = "It's empty! oh noes!";
      diffHighlight($data[1]);
    }else{
      echo "<a href='?act=start'>Begin Development</a> ";
    }
  }else{
      $data = createPatch();
      if($data[1] == "") $data[1] = "It's empty! oh noes!";
      echo "<h2>Patch</h2>";
      diffHighlight($data[1]);
  }
  echo "<br>";


echo "\n\n<br>\nCreated Page In: ".(microtime(true)-$superstart);
?>