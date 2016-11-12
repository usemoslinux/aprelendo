<?php
  function SanitizeAndEscapeString ($con, $text) {
    $result = filter_var($text, FILTER_SANITIZE_STRING); // sanitize string (remove HTML elements)
    $result = mysqli_real_escape_string($con, $result); // escape string, for security
    // return utf8_encode($result);
    return $result;
  }


 ?>
