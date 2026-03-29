<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
                        <a href="/extensions">Extensions</a>
                    </li>
                    <?php if (!IS_SELF_HOSTED): ?>
                        <li class="list-inline-item">
                            <a href="/contact">Contact</a>
                        </li>
                    <?php endif; ?>
                    <li class="list-inline-item">
                        <a href="/donate">Donate</a>
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
<?php
if (!IS_SELF_HOSTED) {
    require_once 'eucookies.php';
    require_once 'jsonldbase.php';
}
?>

<script defer src="/js/logout.js"></script>

</body>

</html>