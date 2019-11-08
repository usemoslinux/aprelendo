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
?>

<!-- FOOTER -->
<div id="footerwrap" class="footer <?php echo $curpage == 'gopremium.php' ? 'text-white' : '' ?> ">
    <div class="container">
        <div class="row text-center">
            <div class="col-sm-12">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="aboutus.php">About us</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="privacy.php">Privacy</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="attributions.php">Attributions</a>
                    </li>
                    <li class="list-inline-item">
                        <a id="extensions" href="extensions.php">Extensions</a>
                    </li>
                    <li class="list-inline-item">
                        <a id="support" href="support.php">Support</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://www.facebook.com/aprelendo" aria-label="Aprelendo's Facebook Page" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="https://twitter.com/aprelendo" aria-label="Aprelendo's Twitter Page" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'eucookies.php'?>
</body>

</html>