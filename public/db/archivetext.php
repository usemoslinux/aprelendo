<?php
  require_once 'dbinit.php'; // connect to database
  require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id

  $user_id = $user->id;
  $learning_lang_id = $user->learning_lang_id;
  
  // if text is archived using green button at the end, update learning status of words too
  if (isset($_POST['words'])) {
    $wordslearnt = $_POST['words'];
    foreach($wordslearnt as $k => $v) { // escape strings to protect against SQL injection
      $wordslearnt[$k] = mysqli_real_escape_string($con, $v);
    }
    $cvswords = join("','", $wordslearnt); // convert array to comma separated string
    // delete words with wordStatus = 1
    //mysqli_query($con, "DELETE FROM words WHERE word IN ('$cvswords') AND wordStatus=1") or die(mysqli_error($con));
    // -1 to wordStatus if it's not a new word added to the db
    mysqli_query($con, "UPDATE words SET wordStatus=wordStatus-1, wordModified=now() WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word IN ('$cvswords') ") or die(mysqli_error($con));
  }  

  if (isset($_POST['textIDs'])) {
    // change text status (archive or unarchive text)
    $ids = json_decode($_POST['textIDs']);
    
    foreach ($ids as $id) {
      $id = mysqli_real_escape_string($con, $id);
    }
    
    $textIDs = implode(',', $ids);
    
    if ($_POST['archivetext'] === 'true') { //archive text
        $insertsql = "INSERT INTO archivedtexts (atextUserId, atextLgID, atextTitle, atextAuthor, atext, atextAudioURI, atextSourceURI)
          SELECT textUserId, textLgID, textTitle, textAuthor, text, textAudioURI, textSourceURI
          FROM texts WHERE textID IN ($textIDs)";
        $deletesql = "DELETE FROM texts WHERE textID IN ($textIDs)";
    } else { // unarchive text
      $insertsql = "INSERT INTO texts (textUserId, textLgID, textTitle, textAuthor, text, textAudioURI, textSourceURI)
        SELECT atextUserId, atextLgID, atextTitle, atextAuthor, atext, atextAudioURI, atextSourceURI
        FROM archivedtexts WHERE atextID IN ($textIDs)";
      $deletesql = "DELETE FROM archivedtexts WHERE atextID IN ($textIDs)";
    }
    
    // execute sql queries to archive/unarchive text(s)
    mysqli_query($con, $insertsql) or die(mysqli_error($con));
    mysqli_query($con, $deletesql) or die(mysqli_error($con));
  }
?>