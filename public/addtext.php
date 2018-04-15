<?php
  session_start();
  require_once('header.php')
  ?>

<div class="container mtb">
  <div class="row">
    <div class="col-lg-12">

      <?php

        if (isset($_POST['submit'])) {
          require_once('db/dbinit.php'); // connect to database

          $actlangid = $_SESSION['actlangid'];
          $title = trim(mysqli_real_escape_string($con, $_POST['title']));
          $author = trim(mysqli_real_escape_string($con, $_POST['author']));
          $source_url = trim(mysqli_real_escape_string($con, $_POST['url']));
          $text = trim(mysqli_real_escape_string($con, $_POST['text']));

          $target_dir = APP_ROOT . '/public/uploads/';
          $target_file_name = basename($_FILES['audio']['name']);
          $target_file_URI = $target_dir . $target_file_name;


          $file_extension = pathinfo($target_file_URI,PATHINFO_EXTENSION);
          $file_size = $_FILES['audio']['size'] / 1024; // size in KBs

          $upload_max_filesize = ini_get('upload_max_filesize'); // max file size
          $allowed_extensions = array('mp3', 'ogg');

          // File validation
          $errormsg = "";
          if ($_FILES['audio']['error'] !=  UPLOAD_ERR_NO_FILE) { // if a file was uploaded
            // Check if file exists
            if (file_exists($target_file_URI)) {
              $errormsg .= "File already exists. Change the file name and try again.\n";
            }

            // Check file size
            if ($_FILES['audio']['error'] == 1) {
              $errormsg .= "File size should be less than $upload_max_filesize\n" .
              "This is a limitation of the hosting server.\n" .
              "If you have access to the php ini file you can fix this by changing the <code>upload_max_filesize</code> setting.\n" .
              "If you can't, please ask your host to increase the size limits.\n";
            }

            // Check file extension
            $allowed_ext = false;
            for ($i=0; $i < sizeof($allowed_extensions); $i++) {
              if (strcasecmp($allowed_extensions[$i], $file_extension) == 0) {
                $allowed_ext = true;
              }
            }

            if (!$allowed_ext) {
              $errormsg .= 'Only the following file types are supported: ' . implode(', ', $allowed_extensions) . "\n";
            }

            // upload file & save info to db
            if ($_FILES['audio']['error'] == UPLOAD_ERR_OK && empty($errormsg)) {
              if (!is_dir($target_dir)) {
                mkdir($target_dir);
              }
              // try to move file to uploads folder. If this fails, show error message
              if (!move_uploaded_file($_FILES["audio"]["tmp_name"], $target_file_URI)) {
                $errormsg .= "Sorry, there was an error uploading your file.\n";
              }
            }
          } elseif ($_FILES['audio']['error'] == UPLOAD_ERR_INI_SIZE) {
            $errormsg .= "File size should be less than $upload_max_filesize.";
          }

          if (empty($errormsg)) {
            // save text in db
            $audio_uri = empty($target_file_name) ? '' : '/uploads/' . $target_file_name;
            $result = mysqli_query($con, "INSERT INTO texts (textLgId, textTitle, textAuthor, text, textAudioURI, textSourceURI)
              VALUES ('$actlangid', '$title', '$author', '$text', '$audio_uri', '$source_url') ")
              or die(mysqli_error($con));

            header('Location: /');
          } else {
            echo '<div class="alert alert-danger">' . $errormsg . '</div>';

          }

        }
        //catch file overload error...
        elseif (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
          $post_max_size = ini_get('post_max_size'); //grab the size limits...
          echo  "<div class='alert alert-danger'>Please note that posts larger than $post_max_size will result in this error!" .
                "<br>This is a limitation of the hosting server." .
                "<br>If you have access to the php ini file you can fix this by changing the <code>post_max_size</code> setting." .
                "<br>If you can't, please ask your host to increase the size limits.</div>";
        }
      ?>

      <form action="addtext.php" class="add-form" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Title:</label>
          <input type="text" id="title" name="title" class="form-control" maxlength="200"
            placeholder="Text title (required)" autofocus required
            value="<?php if(isset($_POST['title'])){echo $_POST['title'];}?>">
        </div>
        <div class="form-group">
          <label for="author">Author:</label>
          <input type="text" id="author" name="author" class="form-control" maxlength="100"
            placeholder="Author full name (optional)"
            value="<?php if(isset($_POST['author'])){echo $_POST['author'];}?>">
        </div>
        <div class="form-group">
          <label for="url">Source URL:</label>
          <input type="url" id="url" name="url" class="form-control"
            placeholder="Source URL (optional)"
            value="<?php if(isset($_POST['url'])){echo $_POST['url'];}?>">
        </div>
        <div class="form-group">
          <label for="text">Text:</label>
          <textarea id="text" name="text" class="form-control" rows="16" cols="80" maxlength="65535"
            placeholder="Text goes here (required), max. length=65,535 chars"
            required><?php if(isset($_POST['text'])){echo $_POST['text'];}?></textarea>
        </div>
        <div class="form-group">
          <label for="audio">Audio:</label>
          <input type="file" name="audio"  accept="audio/mpeg">
          <label for="audio" class="error" id="audio_error"></label>
        </div>
        <button type="button" id="cancelbtn" name="cancel" class="btn btn-danger" onclick="window.location='/'">Cancel</button>
        <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
      </form>

    </div>
  </div>
</div>

<?php require_once('footer.php') ?>
