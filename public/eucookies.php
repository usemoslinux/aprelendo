<?php
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