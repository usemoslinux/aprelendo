<?php require_once 'simpleheader.php'; ?>

<div class="container mtb nice-wallpaper-3">
  <div id="more_content" class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">
      <section>
        <header>
          <h1 class="text-center">
            <?php
            $title_array = array('English' => 'Welcome!',
                'Spanish' => '¡Bienvenido!',
                'Portuguese' => 'Bemvindo!',
                'French' => 'Bienvenue!',
                'Italian' => 'Benvenuto!',
                'German' => 'Willkommen!');
            $to_lang = isset($_GET['tolang']) ? ucfirst($_GET['tolang']) : 'English';
            $native_lang = isset($_GET['srclang']) ? ucfirst($_GET['srclang']) : 'English';
            echo '<img src="/images/flags/' . strtolower($to_lang) . '.svg" alt="' . $to_lang . '" class="flag-icon">';
            echo $title_array["$to_lang"];
            ?>
          </h1>
          <div class="text-muted text-center">You are only one step away from learning
            <?php echo $to_lang; ?>.</div>
        </header>
        <br/>
        <div id="error-msg" class="hidden"></div>
        <form action="" id="form_register">
          <div>
            <div class="form-group col-xs-6">
              <label for="native_lang">Native language:</label>
              <select name="native_lang" id="native_lang">
                <option value="en" selected>English</option>
                <option value="es">Spanish</option>
                <option value="pr">Portuguese</option>
                <option value="fr">French</option>
                <option value="it">Italian</option>
                <option value="de">German</option>
              </select>
            </div>
            <div class="form-group text-right nopadding col-xs-6">
              <label for="learning_lang">Want to learn:</label>
              <select name="learning_lang" id="learning_lang">
                <option value="en" <?php echo $to_lang=='English' ? 'selected' : ''; ?>>English</option>
                <option value="es" <?php echo $to_lang=='Spanish' ? 'selected' : ''; ?>>Spanish</option>
                <option value="pr" <?php echo $to_lang=='Portuguese' ? 'selected' : ''; ?>>Portuguese</option>
                <option value="fr" <?php echo $to_lang=='French' ? 'selected' : ''; ?>>French</option>
                <option value="it" <?php echo $to_lang=='Italian' ? 'selected' : ''; ?>>Italian</option>
                <option value="de" <?php echo $to_lang=='German' ? 'selected' : ''; ?>>German</option>
              </select>
            </div>
          </div>
          <div>
            <div class="form-group">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" class="form-control" maxlength="20" required>
            </div>
            <div class="form-group">
              <label for="email">E-mail address:</label>
              <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <small>
                <i>at least 8 characters long</i>
              </small>
              <input type="password" id="password" name="password" class="form-control" pattern=".{8,}" required>
            </div>
            <p>
              <a href="forgotpassword.php">Forgot password</a>
            </p>

            <button type="submit" id="btn_register" class="btn btn-success">Register</button>
          </div>

        </form>
        <br/>
        <footer>
          <p class="text-muted text-center font-italic">By registering, you declare to have read and accepted the
            <a href="privacy.php">privacy policy</a>.</p>
        </footer>
      </section>
    </div>
  </div>
</div>

<?php require_once 'footer.php'?>

<script type="text/javascript" src="js/register.js"></script>