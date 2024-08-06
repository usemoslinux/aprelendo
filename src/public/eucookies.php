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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

if (!isset($_COOKIE['accept_cookies'])) { ?>
<script defer src="/js/cookies.min.js"></script>
<?php } ?>

<?php
if (!isset($_COOKIE['accept_cookies'])) { ?>

<div id="eucookielaw">
    <img src="/img/other/cookie-dude.gif" class="d-none d-md-block float-start" alt="Cookie Gif">
    <p class="px-3">We use cookies to enhance your experience on our site. By continuing to browse, you agree to our use
        of cookies as detailed in our <a href="/privacy" id="more-privacy-policy">Privacy Policy</a> and <a
            href="/termsofservice">Terms of Service</a>.</p>
    <button id="removecookie" class="btn btn-success">OK</button>
</div>

<script defer src="/js/eucookies.min.js"></script>
<?php } ?>
