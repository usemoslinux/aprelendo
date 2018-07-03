<?php
  require_once('dbinit.php'); // connect to database

if (isset($_POST['textIDs'])) {
  require_once('dbinit.php'); // connect to database

  $ids = json_decode($_POST['textIDs']);
  foreach ($ids as $id) {
    $id = mysqli_real_escape_string($con, $id);
  }
  $textIDs = implode(',', $ids);

  // decide wether we are deleting an archived text or not
  $referer = basename($_SERVER['HTTP_REFERER']);
  if (strpos($referer, 'archivedtexts.php') !== false) {
    $selectsql = "SELECT atextAudioURI FROM archivedtexts WHERE atextID IN ($textIDs)";
    $deletesql = "DELETE FROM archivedtexts WHERE atextID IN ($textIDs)";
    $audiouri = 'atextAudioURI';
  } else {
    $selectsql = "SELECT textAudioURI FROM texts WHERE textID IN ($textIDs)";
    $deletesql = "DELETE FROM texts WHERE textID IN ($textIDs)";
    $audiouri = 'textAudioURI';
  }

  // create an array of the audio files to delete
  $result = mysqli_query($con, $selectsql) or die(mysqli_error($con));
  $audiouris = mysqli_fetch_all($result);

  // delete entries from db
  $deletedfromdb = mysqli_query($con, $deletesql) or die(mysqli_error($con));

  // delete audio files
  if ($deletedfromdb) {
    // check if there is an audio file associated to this text and store its URI
    foreach ($audiouris as $key => $value) {
        $filename = PRIVATE_PATH . 'uploads/' . $audiouris[$key][0];
        if (is_file($filename) && file_exists($filename)) {
          unlink($filename);
        }
    }
  }
}