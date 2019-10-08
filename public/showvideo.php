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

use Aprelendo\Includes\Classes\Reader;
use Aprelendo\Includes\Classes\Videos;

try {
    $id_is_set = isset($_GET['id']) && !empty($_GET['id']);
    $body_css = '';
    if ($id_is_set) {
        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;

        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement('shared_texts', $_GET['id']) || !$is_shared) {
            throw new \Exception ('User is not authorized to access this file.');
        }
        
        $reader = new Reader($con, $is_shared, $_GET['id'], $user->id, $user->learning_lang_id);
        
        $video = new Videos($con, $user->id, $user->learning_lang_id);
        $video_row = $video->getById($_GET['id']);
        $yt_id = $video->extractYTId($video_row['source_uri']);

        switch ($reader->display_mode) {
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
        $font_family = $reader->font_family;
        $font_size = $reader->font_size;
        $text_align = $reader->text_align;
        
        $body_css .= " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;'";
    } else {
        throw new \Exception ('Oops! There was an error trying to fetch that video.');
    }
} catch (Exception $e) {
    header('Location:/login.php');
    exit;
}
?>

    <body id="readerpage" <?php echo $body_css; ?> >
        <div class="container-fluid">
            <div class="row">
                <?php
                    if (isset($reader)) {
                        echo $reader->showVideo($yt_id);
                    }
                ?>
            </div>
        </div>

        <?php 
        require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
        ?>
        
        <script defer src="js/showvideo.js"></script>
        <script defer src="js/ytvideo.js"></script>
    </body>
</html>