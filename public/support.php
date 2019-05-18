<?php 
require_once('../includes/dbinit.php'); // connect to database

use Aprelendo\Includes\Classes\User;

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once('simpleheader.php');
} else {
    require_once('header.php');
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Support</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-sm-12">
                    <p>Use the form below to drop us an e-mail.<br><small>Please note that, for security reasons, your IP and user agent details will be stored.</small></p>
                    <div id="alert-msg" class="d-none"></div>
                    <form id="form-support" action="" class="add-form" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" maxlength="100"
                                    placeholder="Your name (required)" value="" autofocus required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Your email address (required)"
                                    maxlength="100" value="" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12">
                                <label for="message">Message:</label>
                                <textarea id="message" name="message" class="form-control" rows="5" cols="80"
                                    maxlength="5000" placeholder="Include your comments here (required), max. length = 5,000 chars"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-sm-12 text-right">
                                <a type="button" id="btn-cancel" name="cancel" class="btn btn-static" onclick="window.location='/'">Cancel</a>
                                <button type="submit" id="btn-add-text" name="submit" class="btn btn-success">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="js/support.js"></script>

<?php require_once 'footer.php';?>