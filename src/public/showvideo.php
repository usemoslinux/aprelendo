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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

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

    $body_class = "class='dvh-100 dvw-100 ";
    
    switch ($prefs->display_mode) {
        case 'light':
            $body_css = $body_class . "lightmode'";
            break;
        case 'sepia':
            $body_css = $body_class . "sepiamode'";
            break;
        case 'dark':
            $body_css = $body_class . "darkmode'";
            break;
        default:
            $body_class = $body_class . "'";
            break;
    }
    $font_family = $prefs->font_family;
    $font_size = $prefs->font_size;
    $line_height = $prefs->line_height;
    $text_align = $prefs->text_alignment;

    $reader_css = "font-family:$font_family;font-size:$font_size;line-height:$line_height;text-align:$text_align";

    $likes = new Likes($pdo, $text_id, $user->id, $user->lang_id);
    $user_liked_class = $likes->userLiked() ? 'bi-heart-fill' : 'bi-heart';
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

<body id="readerpage" <?php echo $body_css; ?>>
    <div id="main-container" class="container h-100">
        <div class="row h-100">
            <div class="col d-flex flex-column h-100">
                <div class="video-controls-row d-flex flex-row-reverse my-1">
                    <button type="button" id="btn-save-ytvideo" class="btn btn-success ms-2"
                        data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom"
                        data-bs-title="Close & mark underlined words as reviewed">
                        Save&nbsp;<span class="bi bi-save"></span>
                    </button>
                    <span class="ms-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom"
                            data-bs-title="Reader settings">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-secondary">
                            <span class="bi bi-gear-fill"></span>
                        </button>
                    </span>
                    <div class="d-flex ms-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Like" class="btn btn-link ms-2">
                        <div class="d-flex align-items-center">
                            <span class="bi <?php echo $user_liked_class; ?>" data-idText="<?php echo $text_id; ?>">
                            </span>
                            <small class="px-1"><?php echo $likes->get($text_id);?></small>
                        </div>
                    </div>
                    <?php if (!IS_SELF_HOSTED): ?>
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom" data-bs-title="Flag content for review">
                            <button type="button" class="btn btn-link" data-bs-toggle="modal"
                                data-bs-target="#report-text-modal">
                                <span id="report-flag" class="bi bi-flag"></span>
                            </button>
                        </span>
                    <?php endif; ?>
                </div>
                <?php
                if (isset($reader)) {
                    echo $reader->showVideo($yt_id, $reader_css);
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    if (!IS_SELF_HOSTED) require_once PUBLIC_PATH . 'showreporttextmodal.php'; // load report text modal window
    if (!empty($user->hf_token)) require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    ?>

    <script defer src="/js/ytvideoplayer.min.js"></script>
    <script defer src="/js/dictionaries.min.js"></script>
    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/wordselection.min.js"></script>
    <script defer src="/js/actionbtns.min.js"></script>
    <script defer src="/js/showvideo.min.js"></script>
    <script defer src="/js/likes.min.js"></script>
    <script defer src="/js/helpers.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>
