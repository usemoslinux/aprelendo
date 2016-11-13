<div>
  <form data-remote="true" method="post">
    <div class="formgroup">
      <label for="word">Word:</label>
      <input id="word" name="word" class="form-control" type="text" maxlength="250" autofocus></textarea>
    </div>
    <div class="formgroup">
      <label for="translation">Translation:</label>
      <textarea id="translation" name="translation" class="form-control" rows="3" cols="35"></textarea>
    </div>
    <div class="formgroup">
      <label for="image">Image URI:</label>
      <input id="image" name="image" class="form-control" type="text"></textarea>
    </div>
    <div class="formgroup">
      <label for="tags">Tags:</label>
      <input id="tags" name="tags" class="form-control" type="text"></textarea>
    </div>

    <button type="button" id="submitbtn" name="submit" data-dismiss="modal" class="btn btn-default">save</button>
    <button type="reset" value="Reset" data-dismiss="modal" name="cancel" class="btn btn-default">cancel</button>
  </form>
  <hr>
  <iframe id="theFrame" src="http://www.wordreference.com/fres/maison" style="width:100%;height:200px;" frameborder="0"></iframe>
</div>

<?php
if (isset($_POST['word'])) {
    include 'connect.php'; // connect to database
  include 'functions.php';

    $word = SanitizeAndEscapeString($con, $_POST['word']);
    $translation = SanitizeAndEscapeString($con, $_POST['translation']);
  //$image = sanitizeString($con, $_POST['image']);
  $tags = SanitizeAndEscapeString($con, $_POST['tags']);

    $result = mysqli_query($con, "INSERT INTO words (word, wordTranslation, tags) VALUES ('$word', '$translation', '$tags') ") or die(mysqli_error($con));
}
?>

<script>

// empty form inputs before showing modal window
$('#myModal').on('show.bs.modal', function() {
  //$('#word').val('');
  $('#translation').val('');
  $('#image').val('');
  $('#tags').val('');
})

// give focus to first input after modal window loads
$('#myModal').on('shown.bs.modal', function() {
  $('#translation').focus();
})

$('#submitbtn').click(function()
{
  var word = $('#word').val();
  var translation = $('#translation').val();
  var image = $('#image').val();
  var tags = $('#tags').val();

  $.ajax({
    method: "POST",
    url: "addword.php",
    data: { word: word, translation: translation, image: image, tags: tags },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert("Oops! There was an error processing your request. Please try again later.");
    }
  });
});aaaaaaa

</script>
