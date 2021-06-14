<?php 
/**
 * Copyright (C) 2019 Pablo Castagnino
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

use Aprelendo\Includes\Classes\Reader;

// only premium users are allowed to visit this page
if (!$user->isPremium()) {
    header('Location:texts.php');
    exit;
}

try {
    $body_css = '';
    // check if user has access to view this text
    // if (!$user->isAllowedToAccessElement('shared_texts', (int)$_GET['id'])) {
    //     header("HTTP/1.1 401 Unauthorized");
    //     exit;
    // }
    
    $reader = new Reader($pdo, $user->getId(), $user->getLangId());
    $prefs = $reader->getPrefs();

    switch ($prefs->getDisplayMode()) {
        case 'light':
        $body_css = "class='lightmode'";
        break;
        case 'sepia':
        $body_css = "class='sepiamode'";
        break;
        case 'dark':
        $body_css = "class='darkmode'";
        break;
        default:
        break;
    }
    $font_family = $prefs->getFontFamily();
    $font_size = $prefs->getFontSize();
    $text_align = $prefs->getTextAlignment();
    
    $body_css .= " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;position:fixed;width:100%;'";
} catch (Exception $e) {
    header('Location:/login.php');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

    <body id="readerpage" <?php echo $body_css; ?> >
        <div class="container-fluid">
            <div class="row">
                <?php
                    if (isset($reader)) {
                        echo $reader->showOfflineVideo('');
                    }
                ?>
            </div>
        </div>

        <?php 
            require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
            require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
        ?>

        <script defer src="js/underlinewords-min.js"></script>
        <script defer src="js/showofflinevideo-min.js"></script>
        <script defer src="js/subtitles-parser/subtitles.parser.min.js"></script>
    </body>
</html>

