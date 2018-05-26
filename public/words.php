<?php
 	session_start();
	require_once('header.php')
	?>

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
      <ol class="breadcrumb">
          <li>
            <a href="/">Home</a>
          </li>
          <li>
            <a class="active">Word list</a>
          </li>
        </ol>
        <div class="row flex">
          <div class="col-xs-12">
            <form class="" action="" method="post">
              <div class="input-group searchbox">
                <input type="text" id="search" name="searchtext" class="form-control" placeholder = "Search..."
                  value= "<?php echo isset($_POST['submit']) ? $_POST['searchtext'] : '' ?>" >
                <div class="input-group-btn">
                  <button type="submit" name="submit" class="btn btn-default">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <?php require_once('listwords.php'); ?>
      </div>
    </div>
  </div>

  <?php require_once('footer.php') ?>