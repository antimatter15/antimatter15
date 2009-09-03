<?php
define("_amoddir", dirname(__FILE__)."/");
define("_apatdir", "../../");


function applyPatch($text, $name, $rev = false){ 
  include_once "Lib/class.pmkpatcher.php";
  
  file_put_contents(_amoddir."Data/$name.patch", $text);
  if($text == "") return 0;
  
  if($rev == true){
    $x = pmkpatcher::unpatch($text, _amoddir._apatdir);
  }else{
    $x = pmkpatcher::patch($text, _amoddir._apatdir);
  }
  if(!is_string($x)){
    foreach($x['data'] as $file){
      file_put_contents(
        _amoddir._apatdir.$file['sourcefile'], 
        $file['patcheddata']);
    }
    return 1;
  }else{
    return $x;
  }
}


function parsePackage($text){
  $lines = explode("\n", $text);
  $data = array();
  foreach($lines as $line){
    if(substr($line, 0, 5) == "Index") return $data;
    $eq = explode("=", $line);
    if(count($eq) == 2){
      $data[trim($eq[0])] = trim($eq[1]);
    }else{
      //error!
    }
  }
  return $data;
}


function getDBInfo(){
  if(!file_exists(_amoddir."Data/database.txt")){
    file_put_contents(_amoddir."Data/database.txt", "");
  }
  $data = unserialize(file_get_contents(_amoddir."Data/database.txt"));
  if(!is_array($data)){
    return array();
  }else{
    return $data;
  }
}

function setDBInfo($data){
  file_put_contents(_amoddir."Data/database.txt", serialize($data));
}

function addPatchText($text){
  $dba = getDBInfo();
  $data = parsePackage($text);
  file_put_contents(_amoddir."Data/".$data['fn'].".patch", $text);
  if(!is_array($dba[$data["fn"]])){
    $dba[$data["fn"]] = array(
        'firstinstall' => -1,
        'lastinstall' => -1,
        'lastuninstall' => -1,
        'firstuninstall' => -1,
        'uninstalltimes' => 0,
        'installtimes' => 0
    );
  }
  $dba[$data["fn"]]['fn'] = $data["fn"];
  $dba[$data["fn"]]['name'] = $data["name"];
  $dba[$data["fn"]]['version'] = $data["version"];
  $dba[$data["fn"]]['summary'] = $data["summary"];
  $dba[$data["fn"]]['installed'] = false;
  
  setDBInfo($dba);
}

function uninstallPatch($name){
  return uninstallPatchText(
    file_get_contents(_amoddir."Data/$name.patch"));
}

function installPatch($name){
  return installPatchText(
    file_get_contents(_amoddir."Data/$name.patch"));
}

function installPatchText($text){
  $dba = getDBInfo();
  $data = parsePackage($text);
  
  if(!is_array($dba[$data["fn"]])){
    $dba[$data["fn"]] = array(
        'firstinstall' => microtime(true),
        'lastinstall' => -1,
        'lastuninstall' => -1,
        'firstuninstall' => -1,
        'uninstalltimes' => -1,
        'installtimes' => -1
    );
  }
  $dba[$data["fn"]]['installtimes']++;
  $dba[$data["fn"]]['fn'] = $data["fn"];
  $dba[$data["fn"]]['name'] = $data["name"];
  $dba[$data["fn"]]['version'] = $data["version"];
  $dba[$data["fn"]]['summary'] = $data["summary"];
  $dba[$data["fn"]]['lastinstall'] = microtime(true);
  $dba[$data["fn"]]['installed'] = true;
  
  setDBInfo($dba);
  
  return applyPatch($text, $data["fn"]);  
}
function uninstallPatchText($text){
  $dba = getDBInfo();
  $data = parsePackage($text);
  
  if(!is_array($dba[$data["fn"]])){
    $dba[$data["fn"]] = array(
        'firstinstall' => -1,
        'lastinstall' => -1,
        'lastuninstall' => -1,
        'firstuninstall' => microtime(true),
        'uninstalltimes' => -1,
        'installtimes' => -1
    );
  }
  
  $dba[$data["fn"]]['uninstalltimes']++;
  $dba[$data["fn"]]['fn'] = $data["fn"];
  $dba[$data["fn"]]['name'] = $data["name"];
  $dba[$data["fn"]]['version'] = $data["version"];
  $dba[$data["fn"]]['summary'] = $data["summary"];
  $dba[$data["fn"]]['lastuninstall'] = microtime(true);
  $dba[$data["fn"]]['installed'] = false; 
  
  setDBInfo($dba);
  
  return applyPatch($text, $data["fn"], true);
}
?>