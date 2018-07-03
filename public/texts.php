<?php require_once('header.php'); ?>

  <!-- *****************************************************************************************************************
TABS
***************************************************************************************************************** -->

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
        <ol class="breadcrumb">
          <li>
            <a href="texts.php">Home</a>
          </li>
          <li>
            <a class="active">My texts</a>
          </li>
        </ol>
        <div class="row flex">
          <div class="col-xs-12">
            <form class="form-flex-row" action="" method="post">
              <div class="input-group searchbox">
              <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li class="active"><a href="#">All</a></li>
                  <li><a href="#">Articles</a></li>
                  <li><a href="#">Conversations</a></li>
                  <li><a href="#">Letters</a></li>
                  <li><a href="#">Songs</a></li>
                  <li><a href="#">Others</a></li>
                  <li role="separator" class="divider"></li>
                  <li><a href="#">Archived</a></li>
                </ul>
              </div><!-- /btn-group -->
                <input type="text" id="search" name="searchtext" class="form-control" placeholder="Search..." value="<?php echo isset($_POST['submit']) ? $_POST['searchtext'] : '' ?>">
                <div class="input-group-btn">
                  <button type="submit" name="submit" class="btn btn-default">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </div>
              </div>
              <!-- Split button -->
              <div class="btn-group btn-add-text searchbox">
                <a class="btn btn-success" href="addtext.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</a>
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a href="addtext.php">Plain text</a>
                  </li>
                  <li>
                    <a href="addrss.php">RSS text</a>
                  </li>
                </ul>
              </div>
            </form>
          </div>
        </div>
        <?php $showarchivedtexts = false; require_once('listtexts.php') ?>
      </div>
    </div>
  </div>

  <?php require_once('footer.php') ?>