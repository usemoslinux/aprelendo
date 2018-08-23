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
    <script type="text/javascript">
        if (document.cookie.indexOf("accept_cookies") === -1) {
            $("#eucookielaw").fadeIn(1200, function(){ $(this).show();});
        }
        $("#removecookie").click(function () {
            SetCookie('accept_cookies', true, 365 * 10);
            $("#eucookielaw").fadeOut(1200, function(){ $(this).remove();});
        });
    </script>
<?php } ?>