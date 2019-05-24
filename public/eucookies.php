<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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
    <script type="text/javascript">
        function SetCookie(c_name, value, expiredays) {
            var exdate = new Date()
            exdate.setDate(exdate.getDate() + expiredays)
            document.cookie = c_name + "=" + escape(value) + ";path=/" + ((expiredays == null) ? "" : ";expires=" +
                exdate.toGMTString())
        }
    </script>
<?php } ?>

<?php
if(!isset($_COOKIE['accept_cookies']))
{ ?>
    <div id="eucookielaw">
        <p>We use cookies to improve your experience of our website. <a href="privacy.php" id="more-privacy-policy">Find out more</a>.</p>
        <a id="removecookie">Got it!</a>
    </div>
    <script defer src="js/cookies.js"></script>
<?php } ?>