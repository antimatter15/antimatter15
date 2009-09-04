<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_REQUEST["url"]);
curl_exec($ch);
curl_close($ch);
?>