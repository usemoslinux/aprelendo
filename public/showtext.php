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
use Aprelendo\Includes\Classes\Likes;
use Aprelendo\Includes\Classes\AprelendoException;

try {
    $html = '';
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;

        $text_id = (int)$_GET['id'];

        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement('shared_texts', $text_id)) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $reader = new Reader($pdo, $is_shared, $text_id, $user->getId(), $user->getLangId());
        $is_long_text = $reader->getIsLongText();
        $prefs = $reader->getPrefs();

        switch ($prefs->getDisplayMode()) {
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
        $font_family = $prefs->getFontFamily();
        $font_size = $prefs->getFontSize();
        $text_align = $prefs->getTextAlignment();
        $assisted_learning = $prefs->getAssistedLearning();

        $likes = new Likes($pdo, $text_id, $user->getId(), $user->getLangId());
        $user_liked_class = $likes->userLiked($user->getId(), $text_id) ? 'fas' : 'far';
        $nr_of_likes = $likes->get($text_id);
        
        $html .= " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;'";
    } else {
        throw new AprelendoException('Oops! There was an error trying to fetch that text.');
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
                    <div class="sidebar-sticky-item my-4"><button type="button" data-bs-toggle="modal"
                            data-bs-target="#reader-settings-modal" class="btn btn-sm btn-secondary d-block"
                            title="Reading settings">
                            <span class="fas fa-cog"></span>
                        </button>
                        
                        <?php if ($assisted_learning && !$is_long_text) : ?>
                            <button id="btn-toggle-audio-player-controls" type="button"
                                class="btn btn-sm btn-primary d-block mt-2" title="Toggle sticky audio controls">
                                <span class="fas fa-headphones"></span>
                            </button>
                        <?php endif ?>
                        
                        <button id="<?php echo $assisted_learning && !$is_long_text
                            ? 'btn-next-phase'
                            : 'btn-save-text'; ?>" type="button" class="btn btn-sm btn-success d-block mt-2"
                            title="<?php echo $assisted_learning && !$is_long_text
                                ? 'Go to phase 2: Listening'
                                : 'Save'; ?>">
                            <span class="fas fa-chevron-circle-right"></span>
                        </button>
                        <?php if ($is_shared) : ?>
                            <button type="button" title="Like" class="btn btn-sm btn-link mt-1 px-0">
                                <span title="Like">
                                    <span class="<?php echo $user_liked_class ?> fa-heart"
                                        data-idText="<?php echo $text_id ?>">
                                    </span>
                                    <small class="px-1"><?php echo $nr_of_likes ?></small>
                                </span>
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

    <script defer src="js/underlinewords-min.js"></script>
    <script defer src="js/showtext-min.js"></script>
    <script defer src="js/likes-min.js"></script>
</body>

</html>
