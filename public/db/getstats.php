<?php
  require_once('dbinit.php'); // connect to database
  $actlangid = $_COOKIE['actlangid'];
  
  // get how many words were created in each of the last 7 days
  for ($i=6; $i >= 0; $i--) { 
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE wordCreated < CURDATE() - INTERVAL $i-1 DAY AND wordCreated > CURDATE() - INTERVAL $i DAY") 
      or die(mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $array['created'][] = $row[0];
  }

  // get how many words' status were modified in each of the last 7 days
  for ($i=6; $i >= 0; $i--) { 
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE wordStatus>0 AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
      or die(mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $array['modified'][] = $row[0];
  }

  // get how many words were learned in each of the last 7 days
  for ($i=6; $i >= 0; $i--) { 
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE wordStatus=0 AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
      or die(mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $array['learned'][] = $row[0];
  }

  // get how many learned words were forgotten in each of the last 7 days
  for ($i=6; $i >= 0; $i--) { 
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE wordStatus=2 AND wordModified>wordCreated AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
      or die(mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $array['forgotten'][] = $row[0];
  }

  echo json_encode($array);
