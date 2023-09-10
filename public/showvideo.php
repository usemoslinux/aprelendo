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
use Aprelendo\Includes\Classes\Videos;
use Aprelendo\Includes\Classes\UserException;

try {
    $id_is_set = !empty($_GET['id']);
    $body_css = '';
    if ($id_is_set) {
        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;

        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement('shared_texts', (int)$_GET['id'])) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
        
        $reader = new Reader($pdo, $user->id, $user->lang_id, $_GET['id'], $is_shared);
        $prefs = $reader->prefs;

        $video = new Videos($pdo, $user->id, $user->lang_id);
        $video->loadRecord($_GET['id']);

        $yt_id = $video->youtube_id;

        switch ($prefs->display_mode) {
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
        $font_family = $prefs->font_family;
        $font_size = $prefs->font_size;
        $text_align = $prefs->text_alignment;
        
        $body_css .= " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;'";
    } else {
        throw new UserException('Error fetching that video.');
    }
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

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
            require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
        ?>

        <script defer src="/js/underlinewords.min.js"></script>
        <script defer src="/js/showvideo.min.js"></script>
        <script defer src="/js/dictionary.min.js"></script>
        <script defer src="/js/ytvideo.min.js"></script>
        <script defer src="/js/likes.min.js"></script>
    </body>
</html>
