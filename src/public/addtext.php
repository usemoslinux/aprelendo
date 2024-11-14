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

use Aprelendo\Texts;

?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-xl-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Add text</span>
                    </li>
                </ol>
            </nav>
            <?php
            $text_lang = $user->lang;

            if (isset($_GET['id'])) {
                // modify text
                $text_id = $_GET['id'];
                
                $text = new Texts($pdo, $user->id, $user->lang_id);
                $text->loadRecord($text_id);
                
                $text_title = $text->title;
                $text_author = $text->author;
                $text_url = $text->source_uri;
                $text_audio_url = $text->audio_uri;
                $text_content = $text->text;
            } elseif (isset($_POST['text_title'])) {
                // rss
                $text_title = $_POST['text_title'];
                $text_author = $_POST['text_author'];
                $text_url = $_POST['text_url'];
                $text_content = $_POST['text_content'];
                $text_is_shared = $_POST['text_is_shared'];
            } elseif (isset($_GET['sh'])) {
                // shared text
                $text_is_shared = true;
            } elseif (isset($_GET['url'])) {
                // external call (bookmarklet, addon)
                $text_url = $_GET['url'];
                $text_lang = $_GET['lang'] ?? $text_lang;
                $text_is_shared = true;
                $external_call = true;
            }
            ?>
            <main>
                <div id="alert-box" class="d-none"></div>
                <form id="form-addtext" class="add-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php if (isset($text_id)) {echo $text_id;}?>">
                    <input type="hidden" name="mode" value="simple">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="type">Type:</label>
                            <select name="type" id="type" class="form-control form-select">
                                <option value="1">Article</option>
                                <option value="2">Conversation</option>
                                <option value="3">Letter</option>
                                <option value="4">Lyrics</option>
                                <option value="7">Other</option>
                            </select>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="level">Level:</label>
                            <select name="level" id="level" class="form-control form-select">
                                <option value="1">Beginner</option>
                                <option value="2" selected>Intermediate</option>
                                <option value="3">Advanced</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" class="form-control"
                                maxlength="200" placeholder="Text title (required)"
                                autofocus required value="<?php if (isset($text_title)) {echo $text_title;}?>">
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" class="form-control"
                                maxlength="100" placeholder="Author full name (optional)"
                                value="<?php if (isset($text_author)) {echo $text_author;}?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12">
                            <label class="me-2 mt-2" for="url">Source URL:</label>
                            <div class="input-group">

                                <input type="url" id="url" name="url" class="form-control"
                                    placeholder="Source URL (optional)"
                                    value="<?php if (isset($text_url)) {echo $text_url;}?>">
                                <button id="btn-fetch" class="btn btn-secondary" type="button">
                                    <span id="btn-fetch-img" class="bi bi-arrow-down-right-square text-warning"></span>
                                    &nbsp;Fetch
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12">
                            <label class="me-2 mt-2" for="audio-url">Audio URL:</label>
                            <input type="text" id="audio-url" name="audio-url" class="form-control"
                                placeholder="Audio URL (optional)"
                                value="<?php if (isset($text_audio_url)) {echo $text_audio_url;}?>">
                            <div class="form-text" id="audio-url-helptext">
                                Accepts URLs from Google Drive or any standard audio source.
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <div id="shared-text-wrapper-div">
                                <input id="shared-text" class="form-check-input" type="checkbox" name="shared-text"
                                    <?php if (!empty($text_is_shared)) {echo 'checked' ;}?>>
                                <label class="form-check-label" for="shared-text" id="shared-text-label">
                                    &nbsp;Share text with our community
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12">
                            <div class="d-flex justify-content-between">
                                <label for="text">Text:</label>
                                <span id="span-chars-left" class="text-success">10,000 chars left</span>
                            </div>
                            <textarea id="text" name="text" class="form-control" rows="16" cols="80"
                                data-text-lang="<?php echo $text_lang; ?>"
                                placeholder="Text goes here (required), max. length = 10,000 chars" required><?php
                                    if (isset($text_content)) {echo $text_content;}
                            ?></textarea>
                            <label for="upload-text" id="upload-txtfile-label">Upload txt file:</label>
                            <input id="upload-text" type="file" name="upload-text" accept=".txt">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12 text-end">
                            <?php if (isset($external_call)) { echo '<input id="external_call" type="hidden">'; } ?>
                            <button id="btn_cancel" name="cancel" type="button" class="btn btn-link"
                                onclick="window.location='/'">Cancel</button>
                            <button type="submit" id="btn-save" name="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/readability/0.5.0/Readability.min.js"
    integrity="sha512-XZ0uNdz5h40R7SJavnsSp/TnGvu6WY+U8Q75gZx6orlKCIzutPOd4g+m/zcq7HA1XL8bAQtYLs1zhoCDb4L9sA=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.11/purify.min.js"
    integrity="sha512-ce0fmuEgWrpnIXWKQrSgJ5FsBsr/hnOsxdWvk5lu1GThckasLwc+TAFERLNIwWnWqBoWV4GPDJiz2PSPntinVA=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<script defer src="/js/addtext.min.js"></script>
<script defer src="/js/helpers.min.js"></script>


<?php require_once 'footer.php'?>
