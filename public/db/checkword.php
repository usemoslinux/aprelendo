<?php
require_once('dbinit.php'); // connect to database
$word = $_GET['word'];
$result = mysqli_query($con, "SELECT * FROM words WHERE word = '$word'") or die(mysqli_error($con));
$response = mysqli_num_rows($result) > 0;

echo json_encode(array(
  'wordfound' => $response
));

?>
