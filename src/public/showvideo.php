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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if user is logged in and set $user object

use Aprelendo\Reader;
use Aprelendo\Videos;
use Aprelendo\Likes;
use Aprelendo\UserException;

try {
    if (empty($_GET['id'])){
        throw new UserException('Error fetching that video.');
    }

    $text_id = $_GET['id'];
    $body_css = $reader_css = '';
    $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;

    // check if user has access to view this text
    if (!$user->isAllowedToAccessElement('shared_texts', (int)$text_id)) {
        http_response_code(403);
        exit;
    }

    $reader = new Reader($pdo, $user->id, $user->lang_id, $text_id, $is_shared);
    $prefs = $reader->prefs;

    $video = new Videos($pdo, $user->id, $user->lang_id);
    $video->loadRecord($text_id);

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

    $reader_css = "font-family:$font_family;font-size:$font_size;text-align:$text_align;";

    $likes = new Likes($pdo, $text_id, $user->id, $user->lang_id);
    $user_liked_class = $likes->userLiked() ? 'bi-heart-fill' : 'bi-heart';
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

<body id="readerpage" <?php echo $body_css; ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div id="main-container" class="d-flex flex-column vh-100">
                    <div class="d-flex flex-row-reverse my-1">
                        <button type="button" id="btn-save-ytvideo" class="btn btn-success"
                            data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom"
                            data-bs-title="Close and save the learning status of your words">
                            Save
                        </button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-secondary me-2">
                            <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                data-bs-placement="bottom"
                                data-bs-title="Reader settings">
                                <span class="bi bi-gear-fill"></span>
                            </span>
                        </button>
                        <button type="button" id="btn-fullscreen" data-bs-toggle="tooltip"
                            data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                            data-bs-title="Toggle fullscreen" class="btn btn-warning me-2">
                            <span class="bi bi-arrows-fullscreen"></span>
                        </button>
                        <button type="button" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom" data-bs-title="Like" class="btn btn-link me-2">
                            
                            <span class="bi <?php echo $user_liked_class; ?>" data-idText="<?php echo $text_id; ?>">
                            </span>
                            <small>
                                <?php echo $likes->get($text_id);?>
                            </small>
                        </button>
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom" data-bs-title="Report">
                            <button type="button" class="btn btn-link me-2" data-bs-toggle="modal"
                                data-bs-target="#report-text-modal">
                                <span id="report-flag" class="bi bi-flag"></span>
                            </button>
                        </span>
                    </div>
                    <?php
                    if (isset($reader)) {
                        echo $reader->showVideo($yt_id, $reader_css);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    require_once PUBLIC_PATH . 'showreporttextmodal.php'; // load report text modal window
    if (!empty($user->hf_token)) {
        require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    }
    ?>

    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/showvideo.min.js"></script>
    <script defer src="/js/dictionary.min.js"></script>
    <script defer src="/js/ytvideoplayer.min.js"></script>
    <script defer src="/js/likes.min.js"></script>
    <script defer src="/js/helpers.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>