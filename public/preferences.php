<?php
    require_once('header.php');
?>

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
        <ol class="breadcrumb">
          <li>
            <a href="/">Home</a>
          </li>
          <li>
            <a class="active">Preferences</a>
          </li>
        </ol>
        <div id="msgbox"></div>
        <form id="prefs-form" class="" action="" method="post">
          <div class="form-group">
            <label for="fontfamily">Font Family:</label>
            <select name="fontfamily" id="fontfamily">
              <option value="Helvetica" <?php echo $_COOKIE['fontfamily']=='Helvetica' ? 'selected' : ''; ?>>Helvetica</option>
              <option value="Open Sans" <?php echo $_COOKIE['fontfamily']=='Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
              <option value="Times New Roman" <?php echo $_COOKIE['fontfamily']=='Times New Roman' ? 'selected' : ''; ?>>Times New Roman</option>
              <option value="Georgia" <?php echo $_COOKIE['fontfamily']=='Georgia' ? 'selected' : ''; ?>>Georgia</option>
              <option value="Lato" <?php echo $_COOKIE['fontfamily']=='Lato' ? 'selected' : ''; ?>>Lato</option>
            </select>
          </div>
          <div class="form-group">
            <label for="fontsize">Font Size:</label>
            <select name="fontsize" id="fontsize">
              <option value="12pt" <?php echo $_COOKIE['fontsize']=='12pt' ? 'selected' : ''; ?>>12 pt</option>
              <option value="14pt" <?php echo $_COOKIE['fontsize']=='14pt' ? 'selected' : ''; ?>>14 pt</option>
              <option value="16pt" <?php echo $_COOKIE['fontsize']=='16pt' ? 'selected' : ''; ?>>16 pt</option>
              <option value="18pt" <?php echo $_COOKIE['fontsize']=='18pt' ? 'selected' : ''; ?>>18 pt</option>
            </select>
          </div>
          <div class="form-group">
            <label for="lineheight">Line height:</label>
            <select name="lineheight" id="lineheight">
              <option value="1.5" <?php echo $_COOKIE['lineheight']=='1.5' ? 'selected' : ''; ?>>1.5 Lines</option>
              <option value="2" <?php echo $_COOKIE['lineheight']=='2' ? 'selected' : ''; ?>>2</option>
              <option value="2.5" <?php echo $_COOKIE['lineheight']=='2.5' ? 'selected' : ''; ?>>2.5</option>
              <option value="3" <?php echo $_COOKIE['lineheight']=='3' ? 'selected' : ''; ?>>3</option>

            </select>
          </div>
          <div class="form-group">
            <label for="alignment">Text alignment:</label>
            <select name="alignment" id="alignment">
              <option value="left" <?php echo $_COOKIE['alignment']=='left' ? 'selected' : ''; ?>>Left</option>
              <option value="center" <?php echo $_COOKIE['alignment']=='center' ? 'selected' : ''; ?>>Center</option>
              <option value="right" <?php echo $_COOKIE['alignment']=='right' ? 'selected' : ''; ?>>Right</option>
              <option value="justify" <?php echo $_COOKIE['alignment']=='justify' ? 'selected' : ''; ?>>Justify</option>
            </select>
          </div>
          <div class="form-group">
            <label for="mode">Display mode:</label>
            <select name="mode" id="mode">
              <option value="light" <?php echo $_COOKIE['mode']=='light' ? 'selected' : ''; ?>>Light</option>
              <option value="sepia" <?php echo $_COOKIE['mode']=='sepia' ? 'selected' : ''; ?>>Sepia</option>
              <option value="dark" <?php echo $_COOKIE['mode']=='dark' ? 'selected' : ''; ?>>Dark</option>
            </select>
          </div>
          <div class="form-group">
            <label for="assistedlearning">Learning mode:</label>
            <select name="assistedlearning" id="assistedlearning">
              <option value="1" <?php echo $_COOKIE['assistedlearning']==true ? 'selected' : ''; ?>>Assisted</option>
              <option value="0" <?php echo $_COOKIE['assistedlearning']==false ? 'selected' : ''; ?>>Free</option>
            </select>
          </div>
          <button type="button" id="cancelbtn" name="cancel" class="btn btn-danger" onclick="window.location='/'">Cancel</button>
          <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
        </form>
      </div>
    </div>
  </div>

  <?php require_once('footer.php') ?>

  <script type="text/javascript" src="js/preferences.js"></script>