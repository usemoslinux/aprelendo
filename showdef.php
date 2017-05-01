<?php
//ini_set("allow_url_fopen", 1);
// get definition from glosbe dictionary

require_once 'connect.php';
require_once 'functions.php';

if (isset($_POST['word'])) {
  $word = strtolower(trim($_POST['word']));
  $url ='https://glosbe.com/gapi/translate?from=fr&dest=en&format=json&phrase=' . htmlentities($word);
  echo $url;
  //$url = utf8_encode('https://glosbe.com/gapi/translate?from=fr&dest=en&format=json&phrase=' . $word . '&pretty=true');
  $json = file_get_contents($url);
  $obj = json_decode($json, TRUE); // convert json to array

  // parse array & get definitions

  foreach ($obj['tuc'] as $key => $value) {

    if (isset($obj['tuc'][$key]['phrase']['text'])) {
      echo $obj['tuc'][$key]['phrase']['text'];
      print "</br>";
    }
  }

  if(!isset($obj['tuc'][0]['phrase']['text'])) {
    foreach ($obj['tuc'][0]['meanings'] as $key => $value) {

      if (isset($obj['tuc'][0]['meanings'][$key]['text'])) {
        echo $obj['tuc'][0]['meanings'][$key]['text'];
        print "</br>";
      }
    }
    //echo $obj['tuc'][$key]['meanings']['text'];
  }
}

?>
