<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add text</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container-fluid">
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
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

<?php
  if(isset($_POST['submit'])){
    include 'connect.php'; // connect to database
    include 'functions.php';

    $txt = SanitizeAndEscapeString($con, $_POST['text']);
    $title = SanitizeAndEscapeString($con, $_POST['title']);

    $result = mysqli_query($con, "INSERT INTO texts (textTitle, text) VALUES ('$title', '$txt') ") or die(mysqli_error($con));
  }
 ?>
