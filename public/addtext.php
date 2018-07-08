<?php
require_once('header.php');
?>

  <div class="container mtb">
    <div class="row">
      <div class="col-lg-12">

        <?php
          if (isset($_GET['id'])) { // modify text
              $id = $_GET['id'];
              $result = $con->query("SELECT textTitle, textAuthor, text, textSourceURI FROM texts WHERE textID='$id'") or die(mysqli_error($con));
              $row = $result->fetch_assoc();
              $art_title = $row['textTitle'];
              $art_author = $row['textAuthor'];
              $art_url = $row['textSourceURI'];
              $art_content = $row['text'];
          } elseif (isset($_POST['art_title'])) { // rss
              $art_title = $_POST['art_title'];
              $art_author = $_POST['art_author'];
              $art_url = $_POST['art_url'];
              $art_content = $_POST['art_content'];
          } elseif (isset($_GET['url'])) { // external call (bookmarklet, addon)
              $art_url = $_GET['url'];
              $external_call = true;
          }
        ?>
          <div id="alert-error-msg" class="hidden"></div>
          <form id="form_addtext" action="" class="add-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php if (isset($id)) {echo $id;}?>" />
            <input type="hidden" name="mode" value="simple" />
            <div class="form-row">
            <div class="form-group col-xs-12">
              <label for="type">Type:</label>
              <select name="type" id="type" class="form-control">
                  <option value="0">Article</option>
                  <option value="1">Conversation</option>
                  <option value="2">Letter</option>
                  <option value="3">Song</option>
                  <option value="4">Other</option>
              </select>
            </div>
              <div class="form-group col-md-6">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" maxlength="200" placeholder="Text title (required)" autofocus
                  required value="<?php if (isset($art_title)) {echo $art_title;}?>">
              </div>
              <div class="form-group col-md-6">
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" class="form-control" maxlength="100" placeholder="Author full name (optional)"
                  value="<?php if (isset($art_author)) {echo $art_author;}?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-9">
                <div class="input-group">
                  <label for="url">Source URL:</label>
                  <input type="url" id="url" name="url" class="form-control" placeholder="Source URL (optional)" value="<?php if (isset($art_url)) {echo $art_url;}?>">
                  <div class="input-group-btn">
                    <button id="btn_fetch" class="btn btn-default" type="button"><i id="btn_fetch_img" class="fa fa-arrow-down"></i> Fetch</button>
                  </div>
                </div>
              </div>
              <div class="form-group col-md-3">
                <label for="audio">Audio:</label>
                <input id="audio_uri" type="file" name="audio" accept="audio/mpeg,audio/ogg">
              </div>
            </div>
            <div class="form-group col-xs-12">
              <label for="text">Text:</label>
              <textarea id="text" name="text" class="form-control" rows="16" cols="80" maxlength="65535" placeholder="Text goes here (required), max. length=65,535 chars"
                required><?php if (isset($art_content)) {echo $art_content;}?></textarea>
              <input id="upload_text" type="file" name="upload_text" accept=".txt, .epub">
            </div>
            <div class="form-group col-xs-12">
              <?php if (isset($external_call)) { echo '<input id="external_call" type="hidden">'; } ?>
              <button type="button" id="btn_cancel" name="cancel" class="btn btn-default" onclick="window.location='/'">Cancel</button>
              <button type="submit" id="btn_add_text" name="submit" class="btn btn-success">Save</button>
            </div>
          </form>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="js/readability/Readability.js"></script>
  <script type="text/javascript" src="js/addtext.js"></script>

  <?php require_once 'footer.php'?>