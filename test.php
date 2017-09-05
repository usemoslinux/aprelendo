

<?php


?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

  <body>
    <a href="">hola</a> <a href="">mundo</a> <a href="">mundo</a> <a href="">este</a> <a href="">es</a> <a href="">un</a> <a href="">orangutan</a>
    <script type="text/javascript">
    $("a")
      .on("dragover", function(e) {
          e.preventDefault()
          $(this).css({background-color:"green"}); //removeClass("alert-success").text(e.dataTransfer.getData("Text"));

      })
      .on("drop", function(e) {
          e.preventDefault()
      });
    </script>
  </body>
</html>
