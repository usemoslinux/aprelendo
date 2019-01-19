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
                    <a class="active">Extensions & Bookmarklet</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-xs-12">
                    <h4>Extensions</h4>
                    <p>Simply download the corresponding extension file and install it in your favorite Web browser.</p>
                    <div class="btn-group">
                        <a href="#" class="btn btn-default"><i class="fab fa-chrome"></i> Download Chrome extension</a>
                        <a href="#" class="btn btn-default"><i class="fab fa-firefox"></i> Download Firefox extension</a>
                    </div>
                    <p>Once installed, click the Aprelendo button to import the content of the page being displayed in
                        the active tab.</p>
                    <br/>
                    <h4>Bookmarklets</h4>
                    <p>Bookmarklets are a "one-click" tool which add functionality to the browser. From a user
                        perspective, they work very much like regular bookmarks.</p>
                    <p>For more information on bookmarklets, we suggest reading
                        <a href="https://en.wikipedia.org/wiki/Bookmarklet">Wikipedia's article</a> on this subject.
                    </p>
                    <br/>
                    <h4>How are bookmarklets different from extensions?</h4>
                    <ul>
                        <li>They do basic tasks on clicking.</li>
                        <li>They are universal, i.e. they usually work on any browser and whatever the platform, mobile
                            or desktop.</li>
                        <li>They are managed as any bookmarks.</li>
                    </ul>
                    <br/>
                    <h4>What does Aprelendo use bookmarklets for?</h4>
                    <p>Aprelendo uses bookmarklets to automagically parse the text of the current web page and add it
                        to your library.</p>
                    <p>It's an alternative to creating specific addons for different browsers. It's easier to implement
                        and has the added advantage that it works in almost any device and/or browser.</p>
                    <br/>
                    <h4>How do I install Aprelendo's bookmarklet in my web browser?</h4>
                    <p>To install the bookmarklet, simply:</p>
                    <h5>Desktops</h5>
                    <ol>
                        <li>Show the Bookmarks Toolbar:
                            <p>In Firefox: Go to
                                <kbd>View</kbd> >
                                <kbd>Toolbars</kbd> >
                                <kbd>Bookmarks toolbar</kbd>
                            </p>
                            <p>In Google Chrome: Go to
                                <kbd>View</kbd> >
                                <kbd>Show bookmarks bar</kbd>
                            </p>
                        </li>
                        <li><p>Drag the following link to your Bookmarks Toolbar.</p> 
                        <a href="javascript:(function()%7Bvar%20is_yt_url%20%3D%20false%3B%0A%20%20%20%20%20%20%20%20var%20url%20%3D%20location.href%3B%0A%20%20%20%20%20%20%20%20var%20yt_urls%20%3D%20new%20Array('https%3A%2F%2Fwww.youtube.com%2Fwatch'%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20'https%3A%2F%2Fm.youtube.com%2Fwatch'%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20'https%3A%2F%2Fyoutu.be%2F')%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20for%20(let%20i%20%3D%200%3B%20i%20%3C%20yt_urls.length%3B%20i%2B%2B)%20%7B%0A%20%20%20%20%20%20%20%20%09if%20(url.lastIndexOf(yt_urls%5Bi%5D)%20%3D%3D%3D%200)%20%7B%0A%09%09%09%09location.href%3D'https%3A%2F%2Fwww.aprelendo.com%2Faddvideo.php%3Furl%3D'%2BencodeURIComponent(url)%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20is_yt_url%20%3D%20true%3B%0A%20%20%20%20%20%20%20%20%09%7D%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20if%20(!is_yt_url)%0A%20%20%20%20%20%20%20%20%09location.href%3D'https%3A%2F%2Fwww.aprelendo.com%2Faddtext.php%3Furl%3D'%2BencodeURIComponent(url)%3B%7D)()%3B" class="btn btn-default"><i class="fas fa-bookmark"></i> Add
                                to Aprelendo</a>
                                <p>It should now appear on the toolbar. </li>
                    </ol>
                    <h5>Mobile devices</h5>
                    <p>The easiest way is to add the bookmarklet in your desktop device, synchronize your favorite
                        Internet browser and wait for the bookmarklet to be automatically added to your mobile device.</p>
                    <br/>
                    <h4>How to use Aprelendo's bookmarklet once installed</h4>
                    <h5>Desktops</h5>
                    <p>Simply go to the web page you would like to add to you library and click on Aprelendo's
                        bookmarklet. It's as easy as it gets.</p>
                    <h5>Mobile devices</h5>
                    <p>Go to the web page you would like to add to you library, tap on the URL bar and start to write
                        "Aprelendo". Choose Aprelendo's bookmarklet and let the magic happen.</p>
                    <p>In both cases, you'll be redirected to Aprelendo so that you can do
                        changes to the text before uploading it to your library.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>