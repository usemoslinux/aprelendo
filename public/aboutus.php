<?php 

require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . 'classes/users.php'); // load Users class

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once('simpleheader.php');
} else {
    require_once('header.php');
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li>
                    <a class="active">About us</a>
                </li>
            </ol>
        </div>
        <!-- /col -->
    </div>
    <!--/row -->

    <!-- ABOUT -->

    <div class="row">
        <div class="col-lg-6">
            <img class="img-responsive" src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg/1200px-Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg"
                alt="">
        </div>

        <div class="col-lg-6">
            <h4>About LangX.</h4>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
                standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it
                to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                typesetting, remaining essentially unchanged. </p>
            <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more
                recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
            </p>
            <p>Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure
                Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical
                literature, discovered the undoubtable source.</p>
        </div>
    </div>
    <!--/row -->

    <!-- TEAM MEMBERS -->

    <div class="row centered">
        <h3 class="mb">Our team</h3>

<!-- team member 1 -->
        <div class="row">
        <div class="col-xs-12 col-sm-3">
            <img class="img-responsive" src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg/1200px-Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg"
                alt="Cecilia">
        </div>

        <div class="col-xs-12 col-sm-9">
            <h4>Cecilia</h4>
            <h5>CEO</h5>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            <p>Contact Me:</p>
            <a href="mailto:cecilia@langx.com">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="https://twitter.com/langx" target="_blank">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
        </div>

<!-- team member 2 -->
<div class="row">
        <div class="col-xs-12 col-sm-3">
            <img class="img-responsive" src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg/1200px-Sir_Anthony_Van_Dyck_-_Charles_I_%281600-49%29_-_Google_Art_Project.jpg"
                alt="Pablo">
        </div>

        <div class="col-xs-12 col-sm-9">
            <h4>Pablo</h4>
            <h5>Lead developer</h5>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            <p>Contact Me:</p>
            <a href="pablo@langx.com">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="https://twitter.com/langx">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    </div>
    </div>
    <!-- /row -->

</div>
<!--/container -->

<?php require_once 'footer.php';?>