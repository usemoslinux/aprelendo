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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
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
                        <span class="active">Add video</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-12">
            <main>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-info">All YouTube videos you
                            add to Aprelendo will be shared with the rest of our community. You will find them in the
                            <a href="/sharedtexts" class="alert-link">shared texts</a> section.</div>
                        <div id="alert-box" class="d-none"></div>
                    </div>
                    <!-- VIDEO CONTAINER -->
                    <div class="col-lg-6 mb-3">
                        <div id="add-video-wrapper" class="ratio ratio-16x9">
                            <span id="yt-logo" class="fab fa-youtube fa-3x"></span>
                            <iframe id="yt-video" style="border:none;" title="YouTube video thumbnail"
                                allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        </div>
                    </div>
                    <!-- FORM CONTAINER -->
                    <div id="div-form-container" class="col-lg-6">
                        <?php
                            if (isset($_GET['url'])) { // external call (bookmarklet, addon)
                                $video_url = $_GET['url'];
                                $external_call = true;
                            }
                        ?>
                        <form id="form-addvideo" class="add-form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="mode" value="video">
                            <input type="hidden" name="type" id="type" class="form-control" value="5">
                            <div class="row">
                                <div class="mb-3 col-sm-12">
                                    <label for="title">Title:</label>
                                    <input type="text" id="title" name="title" class="form-control" maxlength="200"
                                        placeholder="Video title (required)" autofocus required value="">
                                </div>
                                <div class="mb-3 col-sm-12">
                                    <label for="author">Author:</label>
                                    <input type="text" id="author" name="author" class="form-control" maxlength="100"
                                        placeholder="Author channel name (required)" required value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-sm-12">
                                    <label for="url">Source URL:</label>
                                    <div class="input-group">
                                        <input type="url" id="url" name="url" class="form-control"
                                            placeholder="Source URL (required) >> start here: copy URL & press fetch button"
                                            value="<?php if (!empty($_GET['url'])) { echo $_GET['url']; } ?>"
                                            required>
                                        <button id="btn-fetch" class="btn btn-secondary" type="button">
                                            <i id="btn-fetch-img" class="fas fa-arrow-down text-warning"></i>
                                            Fetch</button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="text" id="text" class="form-control" value="5">

                            <div class="row">
                                <div class="mb-3 col-sm-12 text-end">
                                    <?php
                                            if (isset($external_call)) {
                                                echo '<input id="external_call" type="hidden">';
                                            }
                                    ?>
                                    <button id="btn_cancel" name="cancel" type="button" class="btn btn-link"
                                        onclick="window.location='/'">Cancel</button>
                                    <button type="submit" id="btn-save" name="submit"
                                        class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script defer src="/js/addvideo.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php'?>
