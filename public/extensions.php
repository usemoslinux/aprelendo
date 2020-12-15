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
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="active">Extensions & Bookmarklet</a>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <h4>Extensions</h4>
                            <p>Install the extension that corresponds to your favorite Web browser by clicking on the matching button below.</p>
                            <div class="btn-group">
                                <a href="https://chrome.google.com/webstore/detail/aprelendo/aocicejjgilfkeeklfcomejgphjhjonj/related?hl=en-US" target="_blank" rel="noopener noreferrer" class="btn btn-primary"><i class="fab fa-chrome"></i> Install Chrome extension</a>
                                <a href="https://addons.mozilla.org/en-US/firefox/addon/aprelendo/" target="_blank" rel="noopener noreferrer" class="btn btn-danger"><i class="fab fa-firefox"></i> Install Firefox extension</a>
                            </div>
                            <p>Once installed, click the Aprelendo button (which should have been added to your browser's main toolbar) to import the content of the page being displayed in the active tab.</p>
                        </section>
                        <br>
                        <section>
                            <h4>Bookmarklets</h4>
                            <p>Bookmarklets are a "one-click" tool which add functionality to the browser. From a user perspective, they work very much like regular bookmarks.</p>
                            <p>For more information on bookmarklets, we suggest reading
                                <a href="https://en.wikipedia.org/wiki/Bookmarklet">Wikipedia's article</a> on this subject.
                            </p>
                            <br>
                            <h6>How are bookmarklets different from extensions?</h6>
                            <ul>
                                <li>They do basic tasks on clicking.</li>
                                <li>They are universal, i.e. they usually work on any browser and whatever the platform, mobile or desktop.</li>
                                <li>They are managed as any bookmarks.</li>
                            </ul>
                            <br>
                            <h6>What does Aprelendo use bookmarklets for?</h6>
                            <p>Aprelendo uses bookmarklets to automagically parse the text of the current web page and add it to your library.</p>
                            <p>It's an alternative to creating specific addons for different browsers. It's easier to implement and has the added advantage that it works in almost any device and/or browser.</p>
                            <br>
                            <h6>How do I install Aprelendo's bookmarklet in my web browser?</h6>
                            <p>To install the bookmarklet, simply:</p>
                            <strong>Desktops</strong>
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
                                <a href="javascript:(function()%7Bvar%20is_yt_url%20%3D%20false%3B%0A%20%20%20%20%20%20%20%20var%20url%20%3D%20location.href%3B%0A%20%20%20%20%20%20%20%20var%20yt_urls%20%3D%20new%20Array('https%3A%2F%2Fwww.youtube.com%2Fwatch'%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20'https%3A%2F%2Fm.youtube.com%2Fwatch'%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20'https%3A%2F%2Fyoutu.be%2F')%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20for%20(let%20i%20%3D%200%3B%20i%20%3C%20yt_urls.length%3B%20i%2B%2B)%20%7B%0A%20%20%20%20%20%20%20%20%09if%20(url.lastIndexOf(yt_urls%5Bi%5D)%20%3D%3D%3D%200)%20%7B%0A%09%09%09%09location.href%3D'https%3A%2F%2Fwww.aprelendo.com%2Faddvideo.php%3Furl%3D'%2BencodeURIComponent(url)%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20is_yt_url%20%3D%20true%3B%0A%20%20%20%20%20%20%20%20%09%7D%0A%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20if%20(!is_yt_url)%0A%20%20%20%20%20%20%20%20%09location.href%3D'https%3A%2F%2Fwww.aprelendo.com%2Faddtext.php%3Furl%3D'%2BencodeURIComponent(url)%3B%7D)()%3B" class="btn btn-primary"><i class="fas fa-bookmark"></i> Add
                                        to Aprelendo</a>
                                        <p>It should now appear on the toolbar. </li>
                            </ol>
                            <strong>Mobile devices</strong>
                            <p>The easiest way is to add the bookmarklet in your desktop device, synchronize your favorite Internet browser and wait for the bookmarklet to be automatically added to your mobile device.</p>
                            <br>
                            <h6>How to use Aprelendo's bookmarklet once installed</h6>
                            <strong>Desktops</strong>
                            <p>Simply go to the web page you would like to add to you library and click on Aprelendo's bookmarklet. It's as easy as it gets.</p>
                            <strong>Mobile devices</strong>
                            <p>Go to the web page you would like to add to you library, tap on the URL bar and start to write "Aprelendo". Choose Aprelendo's bookmarklet and let the magic happen.</p>
                            <p>In both cases, you'll be redirected to Aprelendo so that you can do changes to the text before uploading it to your library.</p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>