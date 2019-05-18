<?php
require_once('header.php');
?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Add video</a>
                </li>
            </ol>
            <div class="alert alert-info"><i class="fas fa-info-circle"></i> All Youtube videos you add to Aprelendo will be shared with the rest of our community. You will find them in the "<a href="sharedtexts.php">shared texts</a>" section.</div>
            <div id="alert-msg" class="d-none"></div>
        </div>
        <!-- VIDEO CONTAINER -->
        <div id="add-video-container" class="col-lg-6">
            <div id="add-video-wrapper">
                <i id="yt-logo" class="fab fa-youtube fa-3x"></i>
                <iframe id="yt-video" style="border:none;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
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
                <div id="error-msg" class="d-none"></div>
                <form id="form-addvideo" action="" class="add-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="video" />
                    <input type="hidden" name="type" id="type" class="form-control" value="5">
                    <div class="form-row">
                        <div class="form-group col-sm-12">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" maxlength="200" placeholder="Video title (required)" autofocus
                                required value="">
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" class="form-control" maxlength="100" placeholder="Author channel name (required)"
                                required value="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-12">
                            <label for="url">Source URL:</label>
                            <div class="input-group">
                                <input type="url" id="url" name="url" class="form-control" placeholder="Source URL (required) >> start here: copy URL & press fetch button"
                                    value="<?php if (isset($_GET['url']) && !empty($_GET['url'])) { echo $_GET['url']; } ?>" required>
                                <div class="input-group-append">
                                    <button id="btn-fetch" class="btn btn-secondary" type="button">
                                        <i id="btn-fetch-img" class="fas fa-arrow-down"></i> Fetch</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="text" id="text" class="form-control" value="5">

                    <div class="form-row">
                        <div class="form-group col-sm-12 text-right">
                            <?php 
                                if (isset($external_call)) { echo '<input id="external_call" type="hidden">'; } 
                            ?>
                            <button id="btn_cancel" name="cancel" type="button" class="btn btn-link" onclick="window.location='/'">Cancel</button>
                            <button type="submit" id="btn-save" name="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>

<script defer src="js/addvideo.js"></script>

<?php require_once 'footer.php'?>