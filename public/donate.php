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

require_once '../Includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

if (!$user_auth->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<main class="h-100">
    <section id="wgp" class="my-5 mx-4 text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h1 id="hiw" class="col-12"><u>Free</u> language learning for everyone!</h1>
                    <h4>No ads, no subscriptions &amp; open source</h4>
                    <span class="fa-brands fa-pagelines fs-1 my-5"></span>
                </div>

                <div class="col-12">
                    <h6>I believe everyone should have access to a free, world-class language learning platform.</h6>
                    
                    <br><h6>I rely on support from people like you to make it possible.</h6>
                    <br>
                    <h6>If you enjoy using Aprelendo, please consider supporting me by donating and becoming
                        a Patron!</h6>
                    <form id="paypal-form" class="mt-5" action="https://www.paypal.com/cgi-bin/webscr" method="post"
                        target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="6MJ8GBHPRXAC4">
                        <input type="hidden" name="currency_code" value="USD">
                        <button class="btn btn-warning btn-lg" name="submit"
                            title="Pay via PayPal - The safer, easier way to pay online!">
                            Say thanks via <span class="fa-brands fa-paypal"></span> PayPal
                            </button>
                        <img alt="" border="0" src="https://www.paypalobjects.com/en\_US/i/scr/pixel.gif" width="1"
                        height="1">
                    </form>
                    <p>or</p>
                    <a href="https://www.patreon.com/aprelendo/" class="btn btn-danger btn-lg">become a
                        <span class="fa-brands fa-patreon"></span> Patron</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'?>
