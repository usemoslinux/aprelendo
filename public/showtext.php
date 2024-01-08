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

require_once '../Includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if user is logged in and set $user object

use Aprelendo\Reader;
use Aprelendo\Likes;
use Aprelendo\UserException;

try {
    $html = '';
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
                $html = "class='lightmode'";
                break;
            case 'sepia':
                $html = "class='sepiamode'";
                break;
            case 'dark':
                $html = "class='darkmode'";
                break;
            default:
                break;
        }
        $font_family = $prefs->font_family;
        $font_size = $prefs->font_size;
        $text_align = $prefs->text_alignment;
        $assisted_learning = $prefs->assisted_learning;

        $likes = new Likes($pdo, $text_id, $user->id, $user->lang_id);
        $user_liked_class = $likes->userLiked() ? 'bi-heart-fill' : 'bi-heart';
        $nr_of_likes = $likes->get($text_id);
        
        $html .= " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;'";
    } else {
        throw new UserException('Error fetching this text.');
    }
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

require_once PUBLIC_PATH . 'head.php';

?>

<body id="readerpage" <?php echo $html; ?>>
    <div class="container-fluid">
        <div class="row">
            <div id="sidebar" class="col-2">
                <div class="sidebar">
                    <div class="sidebar-sticky-item my-4">
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="right" data-bs-title="Reading settings">
                            <button type="button" data-bs-toggle="modal"
                                data-bs-target="#reader-settings-modal" class="btn btn-secondary d-block">
                                <span class="bi bi-gear-fill"></span>
                            </button>
                        </span>
                        
                        <?php if ($assisted_learning && !$is_long_text) : ?>
                            <button id="btn-toggle-audio-player-controls" type="button"
                                class="btn btn-primary d-block mt-2"
                                data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                data-bs-placement="right" data-bs-title="Toggle sticky audio controls">
                                <span class="bi bi-headphones"></span>
                            </button>
                        <?php endif ?>
                        
                        <button id="<?php echo $assisted_learning && !$is_long_text
                            ? 'btn-next-phase'
                            : 'btn-save-text'; ?>" type="button" class="btn btn-success d-block mt-2"
                            data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="right" data-bs-title="<?php echo $assisted_learning && !$is_long_text
                                ? 'Go to phase 2: Listening'
                                : 'Save'; ?>">
                            <span class="bi bi-skip-end-circle-fill"></span>
                        </button>
                        <?php if ($is_shared) : ?>
                            <button type="button" class="btn btn-link mt-1" data-bs-toggle="tooltip"
                                data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Like">
                                <span class="<?php echo $user_liked_class ?>"
                                    data-idText="<?php echo $text_id ?>">
                                </span>
                                <small class="px-1"><?php echo $nr_of_likes ?></small>
                            </button>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="col-10 col-sm-8 ps-0 pe-4 pe-sm-0">
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
        require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    ?>

    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/showtext.min.js"></script>
    <script defer src="/js/audioplayer.min.js"></script>
    <script defer src="/js/likes.min.js"></script>
    <script defer src="/js/dictionary.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>
