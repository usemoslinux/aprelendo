<?php
require_once('dbinit.php'); // connect to database
require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

$word = $_GET['word'];
$result = mysqli_query($con, "SELECT * FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word = '$word'") or die(mysqli_error($con));
$response = mysqli_num_rows($result) > 0;

echo json_encode(array(
  'wordfound' => $response
));

?>
