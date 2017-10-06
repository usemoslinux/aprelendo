<form class="" action="addtext.php" method="post">
  <div class="formgroup">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" class="form-control" autofocus></textarea>
  </div>
  <div class="formgroup">
    <label for="text">Text:</label>
    <textarea id="text" name="text" class="form-control" rows="16" cols="80"></textarea>
  </div>
  <button type="submit" name="submit" class="btn btn-default">save</button>
  <button type="button" name="cancel" class="btn btn-default">cancel</button>
</form>

<?php
  if (isset($_POST['submit'])) {
    require_once('../private/init.php'); // connect to database

    $text = mysqli_real_escape_string($con, $_POST['text']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $result = mysqli_query($con, "INSERT INTO texts (textTitle, text) VALUES ('$title', '$text') ") or die(mysqli_error($con));
  }
 ?>
