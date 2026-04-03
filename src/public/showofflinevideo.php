<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Reader;

try {
    $body_css = $reader_css = '';

    $reader = new Reader($pdo, $user->id, $user->lang_id);
    $prefs = $reader->prefs;

    $body_class = "class='dvh-100 dvw-100 ";

    $body_css = match ($prefs->display_mode) {
        'light' => $body_class . "lightmode'",
        'sepia' => $body_class . "sepiamode'",
        'dark' => $body_class . "darkmode'",
        default => '',
    };
    $font_family = $prefs->font_family;
    $font_size = $prefs->getFontSizeCssValue();
    $line_height = $prefs->line_height;
    $text_align = $prefs->text_alignment;

    $body_css .= ' style="position:fixed;width:100%;"';
    $reader_css = "font-family:$font_family;font-size:$font_size;line-height:$line_height;text-align:$text_align";
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
                    <button type="button" id="btn-save-offline-video" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Close & mark underlined words as reviewed"
                        class="btn btn-success ms-2 disabled">
                        Save&nbsp;<span class="bi bi-save"></span>
                    </button>
                    <span class="ms-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Reader settings">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-secondary">
                            <span class="bi bi-gear-fill"></span>
                        </button>
                    </span>
                    <button type="button" id="btn-selsubs" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Select subtitles (SRT)" class="btn btn-primary ms-2">
                        <span class="bi bi-badge-cc-fill"></span>
                    </button>
                    <button type="button" id="btn-selvideo" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-title="Select video (MP4/OGG/WEBM)"
                        data-bs-placement="bottom" class="btn btn-primary">
                        <span class="bi bi-file-earmark-play"></span>
                    </button>
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
    require_once PUBLIC_PATH . 'showactionbuttons.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    require_once PUBLIC_PATH . 'showaibotmodal.php'; // load AI bot modal window
    ?>

    <script defer src="/js/videoplayer.js"></script>
    <script defer src="/js/subtitles-parser/subtitles.parser.js"></script>
    <script defer src="/js/dictionaries.js"></script>
    <script defer src="/js/underlinewords.js"></script>
    <script defer src="/js/wordselection.js"></script>
    <script defer src="/js/actionbtns.js"></script>
    <script defer src="/js/readerhelpers.js"></script>
    <script defer src="/js/helpers.js"></script>
    <script defer src="/js/tooltips.js"></script>
    <script defer src="/js/showofflinevideo.js"></script>
</body>

</html>
