<?php
require_once('header.php');
?>

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="texts.php">Home</a>
                </li>
                <li>
                    <a class="active">Add video</a>
                </li>
            </ol>
            <div class="alert alert-info"><i class="fas fa-info-circle"></i> Any Youtube videos you add to Aprelendo will be automatically "shared" with the rest of our community.</div>
        </div>
        <!-- VIDEO CONTAINER -->
        <div id="add-video-container" class="col-xs-12 col-sm-6">
            <div id="add-video-wrapper">
                <i id="yt-logo" class="fab fa-youtube fa-3x"></i>
                <iframe id="yt-video" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
        <!-- FORM CONTAINER -->
        <div id="div-form-container" class="col-xs-12 col-sm-6">

            <!-- <div class="col-lg-12"> -->

            <?php
        //   if (isset($_GET['id'])) { // modify text
        //       $id = $_GET['id'];
        //       $result = $con->query("SELECT textTitle, textAuthor, text, textSourceURI FROM texts WHERE textID='$id'") or die(mysqli_error($con));
        //       $row = $result->fetch_assoc();
        //       $art_title = $row['textTitle'];
        //       $art_author = $row['textAuthor'];
        //       $art_url = $row['textSourceURI'];
        //       $art_content = $row['text'];
        //   } elseif (isset($_POST['art_title'])) { // rss
        //       $art_title = $_POST['art_title'];
        //       $art_author = $_POST['art_author'];
        //       $art_url = $_POST['art_url'];
        //       $art_content = $_POST['art_content'];
        //   } elseif (isset($_GET['url'])) { // external call (bookmarklet, addon)
        //       $art_url = $_GET['url'];
        //       $external_call = true;
        //   }

                if (isset($_GET['url'])) { // external call (bookmarklet, addon)
                    $video_url = $_GET['url'];
                    $external_call = true;
                }
            ?>
                <div id="error-msg" class="hidden"></div>
                <form id="form-addvideo" action="" class="add-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="video" />
                    <input type="hidden" name="type" id="type" class="form-control" value="5">
                    <div class="form-row">
                        <div class="form-group col-xs-12">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" maxlength="200" placeholder="Video title (required)" autofocus
                                required value="">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" class="form-control" maxlength="100" placeholder="Author channel name (required)"
                                required value="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-xs-12">
                            <div class="input-group">
                                <label for="url">Source URL:</label>
                                <input type="url" id="url" name="url" class="form-control" placeholder="Source URL (required) >> start here: copy URL & press fetch button"
                                    value="<?php if (isset($_GET['url']) && !empty($_GET['url'])) { echo $_GET['url']; } ?>" required>
                                <div class="input-group-btn">
                                    <button id="btn-fetch" class="btn btn-default" type="button">
                                        <i id="btn-fetch-img" class="fas fa-arrow-down"></i> Fetch</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="text" id="text" class="form-control" value="5">

                    <div class="form-row">
                        <div class="form-group col-xs-12 text-right">
                            <?php 
                                if (isset($external_call)) { echo '<input id="external_call" type="hidden">'; } 
                            ?>
                            <a type="button" id="btn_cancel" name="cancel" class="btn btn-static" onclick="window.location='/'">Cancel</a>
                            <button type="submit" id="btn_add_text" name="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/addvideo.js"></script>

<?php require_once 'footer.php'?>