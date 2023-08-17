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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Includes\Classes\User;

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
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
                        <span class="active">Add ebook</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="alert alert-info">
                    <span class="fas fa-info-circle"></span>
                    &nbsp;Ebooks will remain in your "private" library. Only you will be able to access to them.
                </div>
                <div id="alert-msg" class="d-none"></div>
                <div class="progress d-none">
                    <div id="upload-progress-bar"
                        class="progress-bar progress-bar-success progress-bar-striped progress-bar-animated"
                        role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <form id="form-addebook" class="add-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php if (isset($id)) {echo $id;}?>" />
                    <input type="hidden" name="mode" value="ebook" />
                    <input type="hidden" name="type" value="6">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="level">Level:</label>
                            <select name="level" id="level" class="form-control form-select">
                                <option value="1">Beginner</option>
                                <option value="2" selected>Intermediate</option>
                                <option value="3">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <input class="d-none" id="url" name="url" type="file" accept=".epub">
                            <button id="btn-upload-epub" type="button" class="btn btn-primary btn-upload">
                                <span class="fas fa-upload"></span>&nbsp;Select epub file
                            </button>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" maxlength="200"
                                placeholder="Book title (required)" autofocus required
                                value="<?php if (isset($art_title)) {echo $art_title;}?>">
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" class="form-control" maxlength="100"
                                placeholder="Author (required)" required
                                value="<?php if (isset($art_author)) {echo $art_author;}?>">
                        </div>
                        <div class="mb-3 col-12">
                            <label for="audio-uri">Audio:</label>
                            <input type="text" id="audio-uri" name="audio-uri" class="form-control" maxlength="200"
                                placeholder="Google Drive link (optional)">
                            <div class="form-text" id="audio-url-helptext"><i class="fa-brands fa-google-drive"></i>
                                Remember to allow access to this file to anyone with the link.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-12 text-end">
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

<!-- Epub.js & jszip -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
    integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<script defer src="js/epubjs/epub.min.js"></script>

<script defer src="js/addebook.js"></script>

<?php require_once 'footer.php'?>
