<?php
define("_bmoddir", dirname(__FILE__)."/");
define("_bpatdir", "../../");

function checkDir2($dir){
  $h1 = opendir(_bmoddir._bpatdir."$dir");
  $results1 = array();
  while(false !== ($file = readdir($h1))){
    if($file != "." && $file != ".."){
      if(is_dir(_bmoddir._bpatdir."$dir/$file")){
        if($file != "mod"  && $file != ".svn"){
          $results1 = array_merge($results1, checkDir2("$dir/$file"));
        }
      }else{
        
          $fullfn = "$dir/$file";
          if(substr($fullfn, 0, 1) == "/"){
            $fullfn = substr($fullfn, 1);
          }
          array_push($results1, array(
            "filename" => $fullfn,
            "mtime" => filemtime(_bmoddir._bpatdir."$dir/$file")
          ));
      }
    }
  }
  return $results1;
}

function indexFilename($files){
  $indexed = array();
  foreach($files as $file){
    $indexed[] = $file["filename"];
  }
  return $indexed;
}

function reIndexByFile($files){
  $indexed = array();
  foreach($files as $file){
    $indexed[$file["filename"]] = $file;
  }
  return $indexed;
}

function renderHeader($file, $fts1, $fts2){
    $out = "";
    $out.= "Index: $file\n";
    $out.= "===================================================================\n";
    $out.= "--- $file	$fts1\n";
    $out.= "+++ $file	$fts2\n";
    return $out;
}


function quickCheck($dir = ""){
  $h = opendir(_bmoddir._bpatdir."$dir");
  $tartime = filemtime(_bmoddir."Data/Cache.tar");
  while(false !== ($file = readdir($h))){
    if($file != "." && $file != ".."){
      if(is_dir(_bpatdir."$dir/$file")){
        if($file != "mod"  && $file != ".svn"){
          if(quickCheck("$dir/$file") == true){
            return true;
          }
        }
      }else{
          if(filemtime(_bmoddir._bpatdir."$dir/$file") > $tartime){
            return true;
          }
      }
    }
  }
}

function createPatch($updatecache = false){


  include_once "Lib/Archive/Tar.php";
  include_once 'Lib/Text/Diff.php';
  include_once 'Lib/Text/Diff/Renderer/unified.php';

  $start = microtime(true);

  $tar_object = new Archive_Tar(_bmoddir."Data/Cache.tar");
  $tar_object->setErrorHandling(PEAR_ERROR_PRINT);

  $tardata = $tar_object->listContent();
  $working = checkDir2("");
  $fmerged = array_merge($tardata, $working);
  $tarf_db = reIndexByFile($tardata);
  $work_db = reIndexByFile($working);
  $workidx = indexFilename($working);
  $tar_idx = indexFilename($tardata);
  $f_names = array_unique(array_merge($workidx, $tar_idx));

  $out = "";
  

  foreach($f_names as $file){
    //speed optimization
    if($tarf_db[$file] && $work_db[$file] &&
       $tarf_db[$file]["mtime"] == $work_db[$file]["mtime"] &&
       $updatecache != true){
      continue;
    }

    if($tarf_db[$file]){
      $fts1 = $tarf_db[$file]["mtime"];
      $fdata = $tar_object->extractInString($file);
      $lines1 = explode("\n",$fdata);
      //$lines1 = file("Data/$file");
      if(substr($fdata, -1, 1) == "\n"){
        //$lines1[] = "";
      }
    }else{
      $fts1 = 0;
      $lines1 = array();
    }
    if($work_db[$file]){
      $fts2 = $work_db[$file]["mtime"];
      //$lines2 = file(_bpatdir."$file");
      
      $filetext = file_get_contents(_bmoddir._bpatdir."$file");
      $lines2 = explode("\n",$filetext);
    }else{
      $fts2 = 0;
      $lines2 = array();
      $filetext = "";
    }

    
    if(array_search($file, $workidx) === false &&
       array_search($file, $tar_idx) !== false){
      //delted file
      $out.=renderHeader($file, $fts1, $fts2);
      $out.= "@@ -0,0 @@\n\n";
      continue;
    }
    if(array_search($file, $workidx) !== false &&
       array_search($file, $tar_idx) === false){
      //added file
    }
      
    if($filetext == $fdata){
      continue;
    }

    $diff = new Text_Diff('auto', array($lines1, $lines2));
    
    $renderer = new Text_Diff_Renderer_unified();
    $render = $renderer->render($diff);
    if($render != ""){
       $out.=renderHeader($file, $fts1, $fts2); //get ts to work!
      $out.= $render."\n";
      if(substr($filetext, -1, 1) != "\n"){
        $out.= "\\ No newline at end of file\n\n";
      }  
    }
    
  }
  if($updatecache == true){
    $tar_object->create(array());
    foreach($f_names as $file){
      $tar_object->addString($file, file_get_contents(_bmoddir._bpatdir."$file"));
    }
  }
  return array(microtime(true)-$start, $out, count($addlist));
}


?>