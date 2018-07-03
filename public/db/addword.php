<?php

if (isset($_POST['word'])) {
  require_once('dbinit.php'); // connect to database
  require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id

  $user_id = $user->id;
  $learning_lang_id = $user->learning_lang_id;
  
  $word = mysqli_real_escape_string($con, $_POST['word']);
  $status = 2;
  $isphrase = $_POST['isphrase'];

  $result = mysqli_query($con, "INSERT INTO words (wordUserId, wordLgId, word, wordStatus, isPhrase)
             VALUES ('$user_id', '$learning_lang_id', '$word', $status, $isphrase) ON DUPLICATE KEY UPDATE
             wordUserId='$user_id', wordLgId=$learning_lang_id, word='$word', wordStatus=$status, isPhrase=$isphrase, wordModified=now()")
             or die(mysqli_error($con));
}
?>