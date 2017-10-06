<?php

  require_once('../private/init.php'); // connect to database

  $result = mysqli_query($con, "SELECT textID, textTitle FROM texts") or die(mysqli_error($con));

  echo "<ul>";

  while ($row = mysqli_fetch_array($result)) {
    echo '<li><a href ="showtext.php?id=' . $row['textID'] . '">' . $row['textTitle']  . '</a></li>';
  }

  echo "</ul>";

?>
