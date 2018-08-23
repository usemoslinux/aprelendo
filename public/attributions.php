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
                    <a class="active">Attributions</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-xs-12">
                    <p>Aprelendo was built using the following free and open source tools:</p>
                    <ul>
                        <li>
                            <a href="http://php.net/">PHP</a>, by The PHP Group</li>
                        <li>
                            <a href="https://jquery.com/">JQuery</a>, by the JQuery Foundation</li>
                        <li>
                            <a href="https://www.apache.org/">Apache</a>, by the Apache Software Foundation</li>
                        <li>
                            <a href="https://getbootstrap.com/">Bootstrap 3.3</a>, by Bootstrap Core Team</li>
                        <li>
                            <a href="https://github.com/mozilla/readability">Readability</a>, by Mozilla (used to fetch texts from web sources)</li>
                        <li>
                            <a href="https://github.com/futurepress/epub.js/">EpubJS</a>, by Futurepress (used to render epub files)</li>
                    </ul>
                    <p>Other attributions:</p>
                    <ul>
                        <li>
                            <a href="http://fontawesome.io/">Font Awesome</a>, by Dave Gandy</li>
                        <li>Flag icons designed by
                            <a href="https://www.flaticon.com/authors/freepik">Freepik</a>
                        </li>
                        <li>Frequency lists by
                            <a href="https://github.com/hermitdave/FrequencyWords">Hermit Dave</a>
                        </li>
                        <li>Logo, by
                            <a href="https://www.flaticon.com/">Flaticon</a>
                        </li>
                        <li>Theme based on
                            <a href="https://blacktie.co/demo/solid/index.html">Solid</a>, by
                            <a href="https://blacktie.co/">Blacktie</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>