<?php
require_once('Reader.class.php');
require_once('smfbot.php');

$bot = new SMFBot("wolframalpha","hahanicetryyoushouldguessmypasswordisslightlymoresecure","http://www.110mb.com/forum");
$reader = new Reader('http://www.110mb.com/forum/index.php?type=rss;action=.xml', './cache');


# What is the bots name?
$botname = 'wolframalpha';

function at_bot($str)
{
  global $botname;

  $ats = array();

  if(stripos($str, '@'. $botname) !== false)
  {
    $lines = explode("\n", $str);

    foreach($lines as $line)
    {
      $line = trim($line);

      if(strtolower(substr($line, 0, strlen('@'. $botname))) != ('@'. strtolower($botname)))
        continue;

      @list(, $at) = preg_split('~[,:\s]~', $line, 2);

      $at = trim($at);

      if(!empty($at))
        $ats[] = $at;
    }
  }

  return $ats;
}


echo '<pre>';
foreach($reader->return_items() as $item){
  $message = "";
  $ats = at_bot($item['description']);
  print_r($ats);
  
  
  foreach($ats as $query){
    $message .= "[quote=".$item['author']."]@".$botname.", ".$query."[/quote]\n";
    echo "searching...\n";
    foreach(search_wolframalpha($query) as $img){
      echo "img: $img \n";
      $message .= "[img]".$img."[/img]\n";
    }
    
  }
  if($message != ""){
    preg_match_all("/t([0-9]+)\.[0-9]+/", $item['link'], $out);
    $topicid =  $out[1][0];
    echo "posting message... \n";
    $bot->postMessage($topicid, $message, $item['topic']?$item['topic']:"WolframAlpha Bot Reply", 'cheesy');
  }
  
  echo $item['link'] .  "\n";
}
echo '</pre>';



function search_wolframalpha($query){
  $url2 = 'http://www.wolframalpha.com/input/?i='.urlencode($query);
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_POST, 0);
	//curl_setopt($ch, CURLOPT_REFERER, $ref);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
	//curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $url2);

	$r = curl_exec($ch);		

  $doc = new DOMDocument();
  @$doc->loadHTML($r);
  
  $results = $doc->getElementById("results");
  
  $imgs = array();
  
  foreach($results->getElementsByTagName("img") as $img){
    $src = $img->getAttribute("src");
    if($src != "/images/loadingdots.gif"){
      //echo "<img src='".$src."'><br>";
      $imgs[] = $src;
    }
  }
  return $imgs;
}

?>
