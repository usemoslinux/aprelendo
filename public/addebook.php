<?php
require_once('header.php');
?>

<div class="container mtb">
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                    <a href="texts.php">Home</a>
                </li>
                <li>
                    <a class="active">Add ebook</a>
                </li>
            </ol>
            <div id="alert-error-msg" class="hidden"></div>
            <form id="form-addebook" action="" class="add-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php if (isset($id)) {echo $id;}?>" />
                <input type="hidden" name="mode" value="ebook" />
                <input type="hidden" name="type" value="6">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" maxlength="200" placeholder="Text title (required)" autofocus
                            required value="<?php if (isset($art_title)) {echo $art_title;}?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="author">Author:</label>
                        <input type="text" id="author" name="author" class="form-control" maxlength="100" placeholder="Author full name (required)"
                            required value="<?php if (isset($art_author)) {echo $art_author;}?>">
                    </div>
                    <div class="form-group col-md-4">
                        <input class="hidden" id="url" name="url" type="file" accept=".epub">
                        <button id="btn-upload-epub" type="button" class="btn btn-primary">Upload epub file</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-xs-12 text-right">
                    <a type="button" id="btn_cancel" name="cancel" class="btn btn-static" onclick="window.location='/'">Cancel</a>
                    <button type="submit" id="btn_add_text" name="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <script type="text/javascript" src="js/addtext.js"></script> -->
<script type="text/javascript" src="js/addebook.js"></script>

<!-- Epub.js & jszip -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>

<?php require_once 'footer.php'?>