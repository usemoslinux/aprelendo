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

if(!isset($_COOKIE['accept_cookies']))
{ ?>
    <script defer src="js/cookies-min.js"></script>
<?php } ?>

<?php
if(!isset($_COOKIE['accept_cookies']))
{ ?>
    <div id="eucookielaw">
        <p>We use cookies. <a href="/privacy" id="more-privacy-policy">Learn more</a>.</p>
        <button id="removecookie" class="btn btn-success">OK</button>
    </div>
    <script defer src="js/eucookies-min.js"></script>
<?php } ?>