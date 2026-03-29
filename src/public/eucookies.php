<?php
// SPDX-License-Identifier: GPL-3.0-or-later

if (!isset($_COOKIE['accept_cookies'])): ?>

    <div id="eucookielaw" class="fade">
        <img src="/img/other/cookie-dude.gif" class="d-md-block float-start" alt="Cookie Gif">
        <p class="px-3">We use cookies to enhance your experience on our site. By continuing to browse, you agree to our use
            of cookies as detailed in our <a href="/privacy" id="more-privacy-policy">Privacy Policy</a> and <a
            href="/termsofservice">Terms of Service</a>.</p>
            <button id="removecookie" class="btn btn-success">OK</button>
        </div>
        
    <script defer src="/js/cookies.js"></script>
    <script defer src="/js/eucookies.js"></script>

<?php endif; ?>
