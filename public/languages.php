<?php
  session_start();
  require_once('header.php')
  ?>


<div class="container mtb">
  <div class="row">
    <div class="col-lg-12">

      <!-- first, check if we need to save information filled out in form -->
      <?php
      if (isset($_POST['submit'])) {
          require_once('db/dbinit.php'); // connect to database

          $lgName = mysqli_real_escape_string($con, $_POST['language']);
          $lgDictionaryURI = mysqli_real_escape_string($con, $_POST['dictionaryURI']);
          $lgTranslatorURI = mysqli_real_escape_string($con, $_POST['translatorURI']);

          if (isset($_POST['id'])) {
            $lgID = mysqli_real_escape_string($con, $_POST['id']);
            mysqli_query($con, "UPDATE languages SET LgName='$lgName', LgDict1URI='$lgDictionaryURI',
            LgTranslatorURI='$lgTranslatorURI' WHERE LgID='$lgID'")
            or die(mysqli_error($con));
          } else {
            mysqli_query($con, "INSERT INTO languages (LgName, LgDict1URI, LgTranslatorURI)
              VALUES ('$lgName', '$lgDictionaryURI', '$lgTranslatorURI')") or die(mysqli_error($con));
          }
      }
      ?>

      <!-- option 1: create new language ('new' parameter was passed) -->
      <?php if (isset($_GET['new'])) {
          ?>

        <form class="" action="languages.php" method="post">
          <div class="form-group">
            <label for="language">Language:</label>
            <input type="text" name="language" class="form-control" value="">
          </div>
          <div class="form-group">
            <label for="dictionaryURI">Dictionary URI:</label>
            <input type="url" name="dictionaryURI" class="form-control" value="">
          </div>
          <div class="form-group">
            <label for="translatorURI">Translator URI:</label>
            <input type="url" name="translatorURI" class="form-control" value="">
          </div>
          <button type="button" id="cancelbtn" name="cancel" class="btn btn-danger" onclick="window.location='languages.php'">Cancel</button>
          <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
        </form>

        <!-- option 2: change existing language settings ('chg' parameter was passed) -->
        <?php

      } elseif (isset($_GET['chg'])) {
          require_once('db/dbinit.php'); // connect to database
          $id = mysqli_real_escape_string($con, $_GET['chg']);
          $result = mysqli_query($con, "SELECT * FROM languages WHERE LgID='$id'") or die(mysqli_error($con));
          $row = mysqli_fetch_assoc($result);

          $lgname = $row['LgName'];
          $lgdictionaryURI = $row['LgDict1URI'];
          $lgtranslatorURI= $row['LgTranslatorURI']; ?>

        <form class="" action="languages.php" method="post">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <div class="form-group">
            <label for="language">Language:</label>
            <input type="text" name="language" class="form-control" value="<?php echo $lgname; ?>">
          </div>
          <div class="form-group">
            <label for="dictionaryURI">Dictionary URI:</label>
            <input type="url" name="dictionaryURI" class="form-control" value="<?php echo $lgdictionaryURI; ?>">
          </div>
          <div class="form-group">
            <label for="translatorURI">Translator URI:</label>
            <input type="url" name="translatorURI" class="form-control" value="<?php echo $lgtranslatorURI; ?>">
          </div>
          <button type="button" id="cancelbtn" name="cancel" class="btn btn-danger" onclick="window.location='languages.php'">Cancel</button>
          <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
        </form>

        <!-- option 3: delete existing language ('del' parameter was passed) -->
        <?php

      } elseif (isset($_GET['del'])) {
          require_once('db/dbinit.php'); // connect to database
          $id = mysqli_real_escape_string($con, $_GET['del']);
          $result = mysqli_query($con, "DELETE FROM languages WHERE LgID='$id'") or die(mysqli_error($con));
          header('Location: languages.php'); ?>

        <!-- option 4: show list of available languages or set active language -->
        <?php

      } else {
          require_once('db/dbinit.php'); // connect to database
          if (isset($_GET['act'])) { // set active language if $_GET['act']
              $actlangid = $_GET['act'];
              mysqli_query($con, "UPDATE preferences SET prefActLangId = '$actlangid'") or die(mysqli_error($con));
              $_SESSION['actlangid'] = $actlangid;
          } else { // else, check in db for active language
              $actlangid = $_SESSION['actlangid'];
          }

         ?>

        <div class="row">
          <div class="col-lg-12">
            <p><a href="languages.php?new=1"><span class='glyphicon glyphicon-plus-sign'></span> Add New Language...</a></p>
            <table id="textstable" class="table table-bordered">
              <colgroup>
                <col width="*">
                <col width="10">
                <col width="10">
              </colgroup>
              <thead>
                <tr>
                  <th class="col-lgname">Language</th>
                  <th class="col-lgname">Activate</th>
                  <th class="col-lgname">Delete</th>
                </tr>
              </thead>
              <tbody>

                <?php
                // then, show list of available languages
                $result = mysqli_query($con, "SELECT LgID, LgName FROM languages") or die(mysqli_error($con));

                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['LgID'];
                    $lgname = $row['LgName'];

                    echo "<tr><td><a href='languages.php?chg=$id'>$lgname</a></td>";

                    if ($actlangid == $id) {
                        echo "<td class='text-center'>
                    <span class='glyphicon glyphicon-arrow-left' title='Active language'></span></td>
                    <td></td></tr>";
                    } else {
                        echo "<td class='text-center'><a href='languages.php?act=$id'>
                    <span if='actbtn' class='glyphicon glyphicon-ok-sign' title='Set as active language'></span></a></td>
                    <td class='text-center'><a href='languages.php?del=$id'>
                    <span id='delbtn' class='glyphicon glyphicon-remove-sign' title='Delete'></span></a></td></tr>";
                    }
                } ?>

              </tbody>
            </table>

          </div>
        </div>

        <?php

      } ?>

    </div>
  </div>
</div>

<?php require_once('footer.php') ?>

<script type="text/javascript" src="js/languages.js"></script>
