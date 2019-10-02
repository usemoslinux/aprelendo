<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if user is logged in and set $user object
require_once PUBLIC_PATH . 'head.php';
?>

<body id="readerpage"
<?php
use Aprelendo\Includes\Classes\Reader;

try {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // check if user has access to view this text
        $table = isset($_GET['sh']) && $_GET['sh'] != 0 ? 'shared_texts' : 'texts';
        if (!$user->isAllowedToAccessElement($table, $_GET['id'])) {
            throw new Exception ('User is not authorized to access this file.');
        }

        $is_shared = $table == 'shared_texts' ? true : false;
        $reader = new Reader($con, $is_shared, $_GET['id'], $user->id, $user->learning_lang_id);
        
        switch ($reader->display_mode) {
            case 'light':
            echo "class='lightmode'";
            break;
            case 'sepia':
            echo "class='sepiamode'";
            break;
            case 'dark':
            echo "class='darkmode'";
            break;
            default:
            break;
        }
        $font_family = $reader->font_family;
        $font_size = $reader->font_size;
        $text_align = $reader->text_align;
        
        echo " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;'";
    } else {
        throw new Exception ('>Oops! There was an error trying to fetch that text.');
    }
} catch (Exception $e) {
    header('Location:/login.php');
    exit;
}
?>
>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-6 offset-lg-3">
                <?php
                    echo $reader->showText();
                    if ($is_shared) {
                        echo '<input type="hidden" id="is_shared">';
                    }
                ?>
                </div>
                
            </div>
        </div>

        <?php 
        require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
        ?>

        <script defer src="js/showtext.js"></script>
        
</body>

</html>