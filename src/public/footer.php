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
?>

<!-- FOOTER -->
<div id="footerwrap" class="footer <?php echo $curpage == 'donate'  ? 'text-white' : '' ?> ">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-12">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="aboutus">About us</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="/termsofservice">Terms of Service</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="/privacy">Privacy</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="attributions">Attributions</a>
                    </li>
                    <li class="list-inline-item">
                        <a id="extensions" href="/extensions">Extensions</a>
                    </li>
                    <li class="list-inline-item">
                        <a id="contact" href="/contact">Contact</a>
                    </li>
                    <li class="list-inline-item">
                        <a id="donate" href="/donate">Donate</a>
                    </li>
                </ul>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="https://blog.aprelendo.com" aria-label="Aprelendo Blog"
                            target="_blank" rel="noopener noreferrer">
                            <span class="bi bi-wordpress"></span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://www.facebook.com/aprelendo.fb" aria-label="Aprelendo's Facebook Page"
                            target="_blank" rel="noopener noreferrer">
                            <span class="bi bi-facebook"></span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://www.instagram.com/apre.lendo/" aria-label="Aprelendo's Instagram Page"
                            target="_blank" rel="noopener noreferrer">
                            <span class="bi bi-instagram"></span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://twitter.com/aprelendo" aria-label="Aprelendo's Twitter Page" target="_blank"
                            rel="noopener noreferrer">
                            <span class="bi bi-twitter-x"></span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://www.youtube.com/channel/UCc5gaiAsGU4sC09mvVjibCA"
                            aria-label="Aprelendo's YouTube channel" target="_blank" rel="noopener noreferrer">
                            <span class="bi bi-youtube"></span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://github.com/usemoslinux/aprelendo" aria-label="Github"
                            target="_blank" rel="noopener noreferrer">
                            <span class="bi bi-github"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
<?php require_once 'eucookies.php'?>
</body>

</html>
