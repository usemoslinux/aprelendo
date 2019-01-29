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
                            <a href="http://php.net/" target="_blank" rel="noopener noreferrer">PHP</a>, by The PHP Group</li>
                        <li>
                            <a href="https://jquery.com/" target="_blank" rel="noopener noreferrer">JQuery</a>, by the JQuery Foundation</li>
                        <li>
                            <a href="https://www.apache.org/" target="_blank" rel="noopener noreferrer">Apache</a>, by the Apache Software Foundation</li>
                        <li>
                            <a href="https://getbootstrap.com/" target="_blank" rel="noopener noreferrer">Bootstrap 3.3</a>, by Bootstrap Core Team</li>
                        <li>
                            <a href="https://github.com/mozilla/readability" target="_blank" rel="noopener noreferrer">Readability</a>, by Mozilla (used to fetch texts from web sources)</li>
                        <li>
                            <a href="https://github.com/futurepress/epub.js/">EpubJS</a>, by Futurepress (used to render epub files)</li>
                            <li><a href="https://github.com/w3c/epubcheck" target="_blank" rel="noopener noreferrer">EPUBCheck</a>, by <a href="http://www.daisy.org/" target="_blank" rel="noopener noreferrer">DAISY Consortium</a> on behalf of <a href="https://www.w3.org/publishing/epubcheck_fundraising" target="_blank" rel="noopener noreferrer">W3C</a> (used to validate epub files)</li>
                        <li><a href="https://bootstraptour.com/" target="_blank" rel="noopener noreferrer">Bootstrap Tour</a>, by Bootstrap Tour Team (used for introductory tour)</li>
                    </ul>
                    <p>Other attributions:</p>
                    <ul>
                        <li>
                            <a href="http://fontawesome.io/" target="_blank" rel="noopener noreferrer">Font Awesome</a>, by Dave Gandy</li>
                        <li>Flag icons designed by
                            <a href="https://www.flaticon.com/authors/freepik" target="_blank" rel="noopener noreferrer">Freepik</a>
                        </li>
                        <li>Frequency lists by
                            <a href="https://github.com/hermitdave/FrequencyWords" target="_blank" rel="noopener noreferrer">Hermit Dave</a>
                        </li>
                        <li>Logo, by
                            <a href="https://www.flaticon.com/" target="_blank" rel="noopener noreferrer">Flaticon</a>
                        </li>
                        <li>Theme based on
                            <a href="https://blacktie.co/demo/solid/index.html" target="_blank" rel="noopener noreferrer">Solid</a>, by
                            <a href="https://blacktie.co/" target="_blank" rel="noopener noreferrer">Blacktie</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>