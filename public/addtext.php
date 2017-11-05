<?php require_once('header.php') ?>

<div class="container mtb">
  <div class="row">
    <div class="col-lg-12">

      <form action="../private/addtext.php" method="post">
        <div class="form-group">
          <label for="title">Title:</label>
          <input type="text" id="title" name="title" class="form-control" autofocus></textarea>
        </div>
        <div class="form-group">
          <label for="text">Text:</label>
          <textarea id="text" name="text" class="form-control" rows="16" cols="80"></textarea>
        </div>
        <button type="button" name="cancel" class="btn btn-danger" onclick="window.location='/'">Cancel</button>
        <button type="submit" name="submit" class="btn btn-success">Save</button>
      </form>

    </div>
  </div>
</div>

<?php require_once('footer.php') ?>
