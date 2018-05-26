<?php
session_start();

require_once 'db/dbinit.php';

$userid = '1';

// save preferences values in $_SESSION

$result = mysqli_query($con, "SELECT * FROM preferences WHERE prefUserId = '$userid'");
$row = mysqli_fetch_assoc($result);

$_SESSION['fontfamily'] = isset($row['prefFontFamily']) ? $row['prefFontFamily'] : "Helvetica";
$_SESSION['fontsize'] = isset($row['prefFontSize']) ? $row['prefFontSize'] : '12px';
$_SESSION['lineheight'] = isset($row['prefLineHeight']) ? $row['prefLineHeight'] : '1';
$_SESSION['alignment'] = isset($row['prefAlignment']) ? $row['prefAlignment'] : 'left';
$_SESSION['mode'] = isset($row['prefMode']) ? $row['prefMode'] : 'light';
$_SESSION['actlangid'] = isset($row['prefActLangId']) ? $row['prefActLangId'] : 0;
$_SESSION['assistedlearning'] = isset($row['prefAssistedLearning']) ? $row['prefAssistedLearning'] : true;

require_once('header.php');
?>

  <!-- *****************************************************************************************************************
TABS
***************************************************************************************************************** -->

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
        <ol class="breadcrumb">
          <li>
            <a href="/">Home</a>
          </li>
          <li>
            <a class="active">My texts</a>
          </li>
        </ol>
        <div class="row flex">
          <div class="col-xs-12">
            <form class="form-flex-row" action="" method="post">
              <div class="input-group searchbox">
                <input type="text" id="search" name="searchtext" class="form-control" placeholder="Search..." value="<?php echo isset($_POST['submit']) ? $_POST['searchtext'] : '' ?>">
                <div class="input-group-btn">
                  <button type="submit" name="submit" class="btn btn-default">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </div>
              </div>
              <!-- Split button -->
              <div class="btn-group btn-add-text searchbox">
                <a class="btn btn-success" href="addtext.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add text</a>
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                    <a href="addtext.php">Add plain text</a>
                  </li>
                  <li>
                    <a href="addrss.php">Add RSS text</a>
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