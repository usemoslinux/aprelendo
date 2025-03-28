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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Reader;

$reader = new Reader($pdo, $user->id, $user->lang_id);
$prefs = $reader->prefs;

$font_family       = $prefs->font_family;
$font_size         = $prefs->font_size;
$line_height       = $prefs->line_height;
$text_align        = $prefs->text_alignment;
$display_mode      = $prefs->display_mode;
$assisted_learning = $prefs->assisted_learning;

$sel = ' selected ';

?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Preferences</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row">
                    <div class="col-12">
                        <div id="alert-box" class="d-none"></div>
                        <form id="prefs-form" method="post">
                            <div class="card">
                                <div class="card-header">Reader</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="fontfamily">Font Family:</label>
                                        <div>
                                            <select name="fontfamily" id="fontfamily" class="form-control form-select"
                                                autocomplete="off">
                                                <option value="var(--bs-body-font-family)" <?php echo $font_family=="var(--bs-body-font-family)"
                                                    ? $sel                                     : ''; ?>>
                                                    System default
                                                </option>
                                                <option value="Arial, sans-serif"
                                                    <?php echo $font_family=='Arial, sans-serif' ? $sel : ''; ?>>
                                                    Arial
                                                </option>
                                                <option value="Courier, monospace"
                                                    <?php echo $font_family=='Courier, monospace' ? $sel : ''; ?>>
                                                    Courier
                                                </option>
                                                <option value="Georgia, serif"
                                                    <?php echo $font_family=='Georgia, serif' ? $sel : ''; ?>>
                                                    Georgia
                                                </option>
                                                <option value="Roboto, sans-serif"
                                                    <?php echo $font_family=='Roboto, sans-serif' ? $sel : ''; ?>>
                                                    Roboto
                                                </option>
                                                <option value="Times New Roman, serif"
                                                    <?php echo $font_family=='Times New Roman, serif' ? $sel : ''; ?>>
                                                    Times New Roman
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fontsize">Font Size:</label>
                                        <div>
                                            <select name="fontsize" id="fontsize" class="form-control form-select"
                                                autocomplete="off">
                                                <option value="12pt" <?php echo $font_size=='12pt' ? $sel : ''; ?>>
                                                    12 pt
                                                </option>
                                                <option value="14pt" <?php echo $font_size=='14pt' ? $sel : ''; ?>>
                                                    14 pt
                                                </option>
                                                <option value="16pt" <?php echo $font_size=='16pt' ? $sel : ''; ?>>
                                                    16 pt
                                                </option>
                                                <option value="18pt" <?php echo $font_size=='18pt' ? $sel : ''; ?>>
                                                    18 pt
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lineheight">Line height:</label>
                                        <div>
                                            <select name="lineheight" id="lineheight" class="form-control form-select"
                                                autocomplete="off">
                                                <option value="1.5" <?php echo $line_height=='1.5' ? $sel : ''; ?>>
                                                    1.5 Lines
                                                </option>
                                                <option value="2" <?php echo $line_height=='2' ? $sel : ''; ?>>
                                                    2
                                                </option>
                                                <option value="2.5" <?php echo $line_height=='2.5' ? $sel : ''; ?>>
                                                    2.5
                                                </option>
                                                <option value="3" <?php echo $line_height=='3' ? $sel : ''; ?>>
                                                    3
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alignment">Text alignment:</label>
                                        <div>
                                            <select name="alignment" id="alignment" class="form-control form-select"
                                                autocomplete="off">
                                                <option value="left" <?php echo $text_align=='left' ? $sel : ''; ?>>
                                                    Left
                                                </option>
                                                <option value="center" <?php echo $text_align=='center' ? $sel : ''; ?>>
                                                    Center
                                                </option>
                                                <option value="right" <?php echo $text_align=='right' ? $sel : ''; ?>>
                                                    Right
                                                </option>
                                                <option value="justify"
                                                    <?php echo $text_align=='justify' ? $sel : ''; ?>>
                                                    Justify
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mode">Display mode:</label>
                                        <div>
                                            <select name="mode" id="mode" class="form-control form-select"
                                                autocomplete="off">
                                                <option value="light" <?php echo $display_mode=='light' ? $sel : ''; ?>>
                                                    Light
                                                </option>
                                                <option value="sepia" <?php echo $display_mode=='sepia' ? $sel : ''; ?>>
                                                    Sepia
                                                </option>
                                                <option value="dark" <?php echo $display_mode=='dark' ? $sel : ''; ?>>
                                                    Dark
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="card">
                                <div class="card-header">
                                    Learning mode
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="assistedlearning">Assisted learning:</label>
                                        <div>
                                            <select name="assistedlearning" id="assistedlearning"
                                                class="form-control form-select">
                                                <option value="1" <?php echo $assisted_learning ? $sel : ''; ?>>
                                                    On
                                                </option>
                                                <option value="0" <?php echo !$assisted_learning ? $sel : ''; ?>>
                                                    Off
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <a href="javascript:;" title="Help" data-bs-toggle="collapse"
                                            data-bs-target="#help-learning-mode">Help
                                            <span class="bi bi-question-circle"></span>
                                        </a>
                                    </div>
                                    <div id="help-learning-mode" class="collapse small">
                                        <hr>
                                        <p>
                                            Assisted mode is designed to aid you in your language learning process. It
                                            typically consists of 5 phases:
                                        </p>
                                        <ol>
                                            <li><strong>Reading</strong>: try to understand what the text is about. If
                                                you see words or phrases that you don&#39;t understand, look them up in
                                                the built-in dictionary.</li>
                                            <li><strong>Listening</strong>: listen to the -automagically created- audio
                                                version of the text and pay attention to the different sounds.</li>
                                            <li><strong>Speaking</strong>: speak on top of the recording, trying to
                                                imitate the pronunciation of each word. You can reduce the speed of the
                                                recording if necessary.</li>
                                            <li><strong>Dictation</strong>: type the words you marked for learning as
                                                they are spoken.</li>
                                            <li><strong>Review</strong>: this is the most <a
                                                    href="https://en.wikipedia.org/wiki/Testing_effect" target="_blank"
                                                    rel="noopener noreferrer">critical phase</a> for long-term language
                                                acquisition. Review all the underlined words. Make an effort to remember
                                                their meaning and pronunciation, while also paying attention to their
                                                spelling. Speak out alternative sentences using these words. The latter
                                                is essential to turn your <a
                                                    href="https://en.wiktionary.org/wiki/passive_vocabulary"
                                                    target="_blank" rel="noopener noreferrer">passive vocabulary</a>
                                                into <a href="https://en.wiktionary.org/wiki/active_vocabulary"
                                                    target="_blank" rel="noopener noreferrer">active vocabulary</a>.
                                            </li>
                                        </ol>
                                        Remember: assisted mode only works for simple texts (articles, lyrics,
                                        conversation transcripts, etc.), not videos or ebooks.
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="text-end">
                                <button id="cancelbtn" name="cancel" type="button" class="btn btn-link"
                                    onclick="window.location='/'">Cancel</button>
                                <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script defer src="/js/preferences.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php'; ?>
