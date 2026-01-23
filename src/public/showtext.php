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
use Aprelendo\Likes;
use Aprelendo\UserException;

try {
    $reader_class = "class='dvh-100 dvw-100 ";
    if (!empty($_GET['id'])) {
        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;

        $text_id = (int)$_GET['id'];

        $text_table = $is_shared ? 'shared_texts' : 'texts';

        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement($text_table, $text_id)) {
            http_response_code(403);
            exit;
        }

        $reader = new Reader($pdo, $user->id, $user->lang_id, $text_id, $is_shared);
        $is_long_text = $reader->is_long_text;
        $prefs = $reader->prefs;

        switch ($prefs->display_mode) {
            case 'light':
                $reader_class .= "lightmode'";
                break;
            case 'sepia':
                $reader_class .= "sepiamode'";
                break;
            case 'dark':
                $reader_class .= "darkmode'";
                break;
            default:
                $reader_class .= "'";
                break;
        }
        $font_family = $prefs->font_family;
        $font_size = $prefs->font_size;
        $text_align = $prefs->text_alignment;
        $reader_css = "font-family:$font_family;font-size:$font_size;text-align:$text_align;";

        $assisted_learning = $prefs->assisted_learning;

        $likes = new Likes($pdo, $text_id, $user->id, $user->lang_id);
        $user_liked_class = $likes->userLiked() ? 'bi-heart-fill' : 'bi-heart';
        $nr_of_likes = $likes->get($text_id);

        $has_next_phase = $assisted_learning && !$is_long_text;
        $has_external_audio = $reader->hasExternalAudio();
    } else {
        throw new UserException('Error fetching this text.');
    }
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

<body id="readerpage" <?php echo $reader_class; ?>>
    <div class="offset-lg-2 col-lg-8 h-100">
        <div class="d-flex flex-column h-100">
            <div id="sidebar" class="d-flex flex-row-reverse mt-2 mx-3">
                <button id="<?php echo $has_next_phase ? 'btn-next-phase' : 'btn-save-text'; ?>"
                    type="button" class="btn btn-success ms-2"
                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                    data-bs-placement="bottom"
                    data-bs-title="<?php echo $has_next_phase 
                        ? 'Go to phase 2: Listening'
                        : 'Close & mark underlined words as reviewed'; ?>">
                    <?php echo $has_next_phase
                        ? 'Next&nbsp;<span class="bi bi-skip-end-circle-fill"></span>'
                        : 'Save&nbsp;<span class="bi bi-save"></span>'; ?>
                    
                </button>
                
                <?php if ($has_next_phase || $has_external_audio) : ?>
                    <button id="btn-toggle-audio-player-controls" type="button"
                    class="btn btn-primary ms-2 active"
                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                    data-bs-placement="bottom" data-bs-title="Hide audio controls while scrolling">
                    <span class="bi bi-headphones"></span>
                </button>
                <?php endif ?>
                
                <span class="ms-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                    data-bs-placement="bottom" data-bs-title="Reading preferences">
                    <button type="button" data-bs-toggle="modal"
                        data-bs-target="#reader-settings-modal" class="btn btn-secondary">
                        <span class="bi bi-gear-fill"></span>
                    </button>
                </span>

                <?php if ($is_shared) : ?>
                    <div class="d-flex ms-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Like">
                        <div class="d-flex align-items-center">
                            <span class="<?php echo $user_liked_class ?>"
                                data-idText="<?php echo $text_id ?>">
                            </span>
                            <small class="px-1"><?php echo number_format($nr_of_likes) ?></small>
                        </div>
                    </div>
                    <?php if (!IS_SELF_HOSTED): ?>
                    <div data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Flag content for review">
                        <button type="button" class="btn btn-link" data-bs-toggle="modal"
                            data-bs-target="#report-text-modal">
                            <span id="report-flag" class="bi bi-flag"></span>
                        </button>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php
            echo $reader->showText($reader_css);
            if ($is_shared) {
                echo '<input type="hidden" id="is_shared">';
            }
            ?>
        </div>
    </div>

    <?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    if (!IS_SELF_HOSTED && $is_shared) require_once PUBLIC_PATH . 'showreporttextmodal.php'; // load report text modal window
    if (!empty($user->hf_token)) require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    ?>

    <script defer src="/js/showtext.min.js"></script>
    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/wordselection.min.js"></script>
    <script defer src="/js/actionbtns.min.js"></script>
    <script defer src="/js/dictation.min.js"></script>
    <script defer src="/js/audioplayer.min.js"></script>
    <script defer src="/js/likes.min.js"></script>
    <script defer src="/js/dictionaries.min.js"></script>
    <script defer src="/js/helpers.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>