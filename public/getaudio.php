<?php
require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
require_once(PUBLIC_PATH . '/classes/files.php'); // loads File, AudioFile & EbookFile classes

$audio_file = new AudioFile($user->premium_until !== NULL);
echo $audio_file->get($_GET['file']);

?>