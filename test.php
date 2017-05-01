<?php
  require_once 'functions.php';
  $str = "hola: esta es una, prueba de fuego para saber; si funciona.";
  $exploded = multiexplode(array(" "),$str);

  print_r($exploded);
?>
