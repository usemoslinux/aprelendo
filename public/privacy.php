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

require_once '../includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Privacy policy</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-sm-12">
                    <h4>Our privacy policy</h4>
                    <i>Last updated: May 2019</i>
                    <p>Protecting the privacy of Aprelendo website users is important to us. Our Online Privacy Policy is designed to inform you about our collection and use of personal information on this website. From time to time, we may make changes to this Privacy Policy, so we encourage you to check back and review it regularly to ensure you are aware of current practices.</p>
                    <p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us through email at <a href="mailto:aprelendo@gmail.com">aprelendo@gmail.com</a></p>
                    <br>
                    <h6>Personal information</h6>
                    <p>We collect some minimal information about you on this website. This information includes only your user name and email address, which you provide directly when you register to our service. Additionally, we may log the IP address and web browser details of the computer or device you use to connect with us. You provide this indirectly, by using our service.</p>
                    <br>
                    <h6>Cookies</h6>
                    <p>When you use and access Aprelendo we place a number of cookies in your web browser.</p>
                    <p>Unlike most websites, we won't use them to provide analytics, store your preferences, enable advertisements delivery or include behavioral advertising. We only use cookies to enable auto-login ("remember me") functionality and to remember the last reading chapter of ebooks.</p>
                    <p>Here is a detailed description of the cookies we store in your computer:</p>
                    <ul>
                        <li>user_token: user token to enable auto-login</li>
                        <li>accept_cookies: tells us you have accepted to use cookies</li>
                        <li>hide_welcome_msg: hides welcome message, which introduces Aprelendo to new users</li>
                        <li>[ebook_key]-lastpos: indicates ebook's last reading position</li>
                    </ul>
                    <br>
                    <strong>Third-party cookies</strong>
                    <p>We don't use third-party cookies.</p>
                    <br>
                    <strong>Deleting/blocking cookies</strong>
                    <p>Please note that if you delete cookies or refuse to accept them, you might not be able to use all of the features we offer and some of our pages might not display properly.</p>
                    <br>
                    <h6>Consent</h6>
                    <p>By using our website, you hereby consent to our Privacy Policy and agree to its Terms and Conditions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>