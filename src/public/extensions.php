<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

$forwarded_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';

if (!empty($forwarded_proto)) {
    $app_scheme = strtolower(trim(explode(',', $forwarded_proto)[0]));
} elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $app_scheme = 'https';
} else {
    $app_scheme = 'http';
}

$app_origin = $app_scheme . '://' . $_SERVER['HTTP_HOST'];

$bookmarklet_script = '(function(){'
    . 'var current_url=location.href;'
    . 'var youtube_prefixes=["https://www.youtube.com/watch","https://m.youtube.com/watch","https://youtu.be/"];'
    . 'var is_youtube_url=false;'
    . 'for(var i=0;i<youtube_prefixes.length;i++){'
    . 'if(current_url.indexOf(youtube_prefixes[i])===0){'
    . 'location.href=' . json_encode($app_origin . '/addvideo?url=') . '+encodeURIComponent(current_url);'
    . 'is_youtube_url=true;'
    . 'break;'
    . '}'
    . '}'
    . 'if(!is_youtube_url){'
    . 'location.href=' . json_encode($app_origin . '/addtext?url=') . '+encodeURIComponent(current_url);'
    . '}'
    . '})();';
$bookmarklet_href = 'javascript:' . rawurlencode($bookmarklet_script);

if (!$user_auth->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Extensions and bookmarklet</span>
                    </li>
                </ol>
            </nav>
            <main class="simple-text">
                <section aria-labelledby="extensions-title">
                    <h1 id="extensions-title" class="h4">Extensions and bookmarklet</h1>
                    <p>Capture articles and videos in fewer steps. Use an extension on Chrome, Edge, or Firefox
                        when possible, and use the bookmarklet as a fallback on unsupported browsers or mobile
                        devices.</p>
                    <div class="alert alert-light border my-4">
                        <p class="mb-2"><strong>Choose the right option</strong></p>
                        <ul class="mb-0">
                            <li><strong>Extension:</strong> the fastest option for supported desktop browsers.</li>
                            <li><strong>Bookmarklet:</strong> the fallback option for other browsers and mobile.</li>
                        </ul>
                    </div>
                </section>

                <section aria-labelledby="extensions-install-title" class="mt-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                        <div>
                            <h2 id="extensions-install-title" class="h5 mb-2">Install an extension</h2>
                            <p class="mb-0">After installation, open the page you want to import and click the
                                Aprelendo button in your browser toolbar.</p>
                        </div>
                        <p class="small text-muted mb-0">Shortcut support depends on the browser and operating
                            system. If needed, assign your own shortcut from the browser's extension settings.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="bi bi-browser-chrome fs-3 text-success me-3"
                                            aria-hidden="true"></span>
                                        <h3 class="h6 mb-0">Chrome</h3>
                                    </div>
                                    <p class="mb-4">Best for Chrome and most Chromium-based browsers on desktop.</p>
                                    <a href="https://chrome.google.com/webstore/detail/aprelendo/aocicejjgilfkeeklfcomejgphjhjonj/related?hl=en-US"
                                        class="btn btn-success mt-auto" target="_blank" rel="noopener noreferrer">
                                        Install Chrome extension
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="bi bi-browser-edge fs-3 text-primary me-3"
                                            aria-hidden="true"></span>
                                        <h3 class="h6 mb-0">Edge</h3>
                                    </div>
                                    <p class="mb-4">Use this if you browse with Microsoft Edge on desktop.</p>
                                    <a href="https://microsoftedge.microsoft.com/addons/detail/aprelendo/ckgnfejigfdfppodkhfmdbockfilcefg"
                                        class="btn btn-primary mt-auto" target="_blank" rel="noopener noreferrer">
                                        Install Edge extension
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="bi bi-browser-firefox fs-3 text-danger me-3"
                                            aria-hidden="true"></span>
                                        <h3 class="h6 mb-0">Firefox</h3>
                                    </div>
                                    <p class="mb-4">Use this if you browse with Firefox on desktop.</p>
                                    <a href="https://addons.mozilla.org/en-US/firefox/addon/aprelendo/"
                                        class="btn btn-danger mt-auto" target="_blank" rel="noopener noreferrer">
                                        Install Firefox extension
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="extensions-usage-title" class="mt-5">
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <h2 id="extensions-usage-title" class="h5 mb-3">How to use it</h2>
                            <ol class="mb-0">
                                <li>Open the article or video page you want to import.</li>
                                <li>Click the Aprelendo extension icon in the browser toolbar.</li>
                                <li>Review the imported content in Aprelendo before saving it to your library.</li>
                            </ol>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="alert alert-light border h-100 mb-0">
                                <p class="mb-2"><strong>Tip</strong></p>
                                <p class="mb-0">If you do not see the icon right away, your browser may place new
                                    extensions behind a toolbar or puzzle icon until you pin them.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="mb-3">Need a walkthrough? Watch the short video tutorial below.</p>
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube-nocookie.com/embed/dZ3-Jwn41mo"
                                title="How to install and use the Aprelendo browser extension"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="bookmarklet-title" class="mt-5">
                    <h2 id="bookmarklet-title" class="h5 mb-3">Use the bookmarklet when an extension is not available</h2>
                    <p>The bookmarklet acts like a normal browser bookmark, but it opens Aprelendo's import flow
                        for the page you are viewing.</p>

                    <div class="alert alert-light border my-4">
                        <p class="mb-2"><strong>What the bookmarklet does</strong></p>
                        <ul class="mb-0">
                            <li>On YouTube pages, it opens the video import screen.</li>
                            <li>On other web pages, it opens the text import screen.</li>
                        </ul>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <h3 class="h6">Install it on desktop</h3>
                            <ol class="mb-0">
                                <li>Show your browser's bookmarks bar or bookmarks toolbar.</li>
                                <li>Drag this button to it:
                                    <div class="mt-2">
                                        <a href="<?php echo htmlspecialchars($bookmarklet_href, ENT_QUOTES, 'UTF-8'); ?>"
                                            class="btn btn-primary">
                                            <span class="bi bi-bookmark-fill" aria-hidden="true"></span>
                                            Add to Aprelendo
                                        </a>
                                    </div>
                                </li>
                                <li>Open a page you want to import and click the bookmarklet.</li>
                            </ol>
                        </div>

                        <div class="col-12 col-lg-6">
                            <h3 class="h6">Use it on mobile or unsupported browsers</h3>
                            <p>The easiest setup is to add the bookmarklet on desktop first, then sync your
                                browser bookmarks to your mobile device.</p>
                            <p class="mb-0">Once it appears in your mobile browser, open the page you want to
                                import, tap the address bar, and search for the Aprelendo bookmarklet.</p>
                        </div>
                    </div>

                    <p class="mt-4 mb-0">After the redirect, you can review and adjust the imported content before
                        saving it to your library.</p>
                </section>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
