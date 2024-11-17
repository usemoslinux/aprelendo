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

try {
    $body_css = $reader_css = '';

    $reader = new Reader($pdo, $user->id, $user->lang_id);
    $prefs = $reader->prefs;

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

    $body_css .= ' style="position:fixed;width:100%;"';
    $reader_css = 'font-family:' . $font_family . ';font-size:' . $font_size . ';text-align:' . $text_align;
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

<body id="readerpage" <?php echo $body_css; ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex flex-column vh-100">
                <div class="d-flex flex-wrap my-1">
                    <button type="button" id="btn-selvideo" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-title="Select video (MP4/OGG/WEBM)"
                        data-bs-placement="bottom" class="btn btn-primary me-2">
                        <span class="bi bi-file-earmark-play"></span></button>
                    <button type="button" id="btn-selsubs" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Select subtitles (SRT)" class="btn btn-primary me-2">
                        <span class="bi bi-badge-cc-fill"></span></button>
                    <button type="button" id="btn-fullscreen" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Toggle fullscreen" class="btn btn-warning me-2">
                        <span class="bi bi-arrows-fullscreen"></span></button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                        class="btn btn-secondary me-2">
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom" data-bs-title="Reader settings">
                            <span class="bi bi-gear-fill"></span>
                        </span>
                    </button>
                    <button type="button" id="btn-save-offline-video" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Save the learning status of your words"
                        class="btn btn-success ms-auto">Save</button>
                </div>
                <?php
                if (isset($reader)) {
                    echo $reader->showOfflineVideo($reader_css);
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    if (!empty($user->hf_token)) {
        require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    }
    ?>

    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/showofflinevideo.min.js"></script>
    <script defer src="/js/dictionary.min.js"></script>
    <script defer src="/js/videoplayer.min.js"></script>
    <script defer src="/js/subtitles-parser/subtitles.parser.min.js"></script>
    <script defer src="/js/helpers.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>