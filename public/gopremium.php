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

<!-- WHY GO PREMIUM -->
<section id="wgp" class="why-go-premium">
    <div class="container">
        <div class="row text-center">
            <h1 id="hiw" class="col-12">Why go premium?<br><br></h1>
            
            <div class="col-lg-3">
                <i class="fas fa-assistive-listening-systems"></i>
                <h4>Text-to-Speech</h4>
                <p>Text-to-Speech (TTS) conversion is an inherent part of <a href="totalreading.php" target="_blank" rel="noopener noreferrer">total reading</a>. Premium users are able to listen up to 3 texts per day, instead of 1.</p>
            </div>
            <div class="col-lg-3">
                <i class="fas fa-rss"></i>
                <h4>RSS/Atom feeds</h4>
                <p>Old good <a href="https://en.wikipedia.org/wiki/RSS" target="_blank" rel="noopener noreferrer">RSS/Atom</a>... By going premium you will be allowed to add texts from up to 3 feeds. Each one will show its last 10 publications.</p>
            </div>
            <div class="col-lg-3">
                <i class="fas fa-book-open"></i>
                <h4>Epub support</h4>
                <p>Are you a book lover? As a premium user you will be able to upload 1 epub file per day (&lt;2MB in size) and read as many ebooks as you like.</p>
            </div>
            <div class="col-lg-3">
                <i class="fas fa-cloud-download-alt"></i>
                <h4>Export words</h4>
                <p>If you are an <a href="https://apps.ankiweb.net/" target="_blank" rel="noopener noreferrer">Anki</a> user or like creating flashcards to learn new vocabulary, you will appreciate this.</p>
            </div>
        </div>

        <div class="row text-center">
            <div class="offset-lg-3 col-lg-3">
                <i class="fas fa-fire"></i>
                <h4>High frequency words</h4>
                <p>Premium users will get visual aids indicating which are the most important words to learn, i.e. those that are among the most used by native speakers.</p>
            </div>
            <div class="col-lg-3">
                <i class="fab fa-osi"></i>
                <h4>Support Open Source</h4>
                <p>We decided to open Aprelendo's source code so that anyone -specially schools or academic institutions- can install it locally and access all premium functionalities without any cost. </p>
            </div>
        </div>
    </div>
</section>

<section class="pricing py-5">
    <div id="pricing" class="container">
        <div class="row">
            <!-- Free -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Free</h5>
                        <h6 class="card-price text-center">$0<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser
                                extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support
                                (1 text p/day)</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Up to 3 RSS
                                feeds</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Ebook support
                                (&lt;2 MB)</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Show
                                high frequency words</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Export words
                                (CSV)</li>
                        </ul>
                        <a href="login.php" role="button" class="btn btn-block btn-primary text-uppercase">Sign In</a>
                    </div>
                </div>
            </div>
            <!-- 1 month subscription -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                        <h6 class="card-price text-center">$10<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser
                                extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support (3
                                texts p/day)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Up to 3 RSS feeds
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Ebook support
                                (&lt;2 MB)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Show high frequency words</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Export words
                                (CSV)</li>
                        </ul>
                        
                        <form name="form-monthly-subscription" action="/payment.php" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_xclick-subscriptions">
                            <input type="hidden" name="lc" value="US">
                            <input type="hidden" name = "item_name" value = "Aprelendo - Monthly Subscription">
                            <input type="hidden" name = "item_number" value = "1">
                            <input type="hidden" name="no_note" value="1">
                            <input type="hidden" name="src" value="1">
                            <input type="hidden" name="sra" value="1">
                            <input type="hidden" name="a3" value="10.00">
                            <input type="hidden" name="currency_code" value="USD">
                            <input type="hidden" name="t3" value="M">
                            <input type="hidden" name="p3" value="1">
                            <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
                            <input type="submit" name="submit" class="btn btn-block btn-primary text-uppercase" value="Subscribe"/>
                        </form>
                    </div>
                </div>
            </div>
            <!-- 1 year subscription -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                        <h6 class="card-price text-center">$99<span class="period">/year</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser
                                extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support (3
                                texts p/day)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Up to 3 RSS feeds
                            </li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Ebook support
                                (&lt;2 MB)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Show high frequency words</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Export words
                                (CSV)</li>
                        </ul>
                        
                        <form name="form-monthly-subscription" action="/payment.php" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_xclick-subscriptions">
                            <input type="hidden" name="lc" value="US">
                            <input type="hidden" name = "item_name" value = "Aprelendo - Yearly Subscription">
                            <input type="hidden" name = "item_number" value = "1">
                            <input type="hidden" name="no_note" value="1">
                            <input type="hidden" name="src" value="1">
                            <input type="hidden" name="sra" value="1">
                            <input type="hidden" name="a3" value="99.00">
                            <input type="hidden" name="currency_code" value="USD">
                            <input type="hidden" name="t3" value="Y">
                            <input type="hidden" name="p3" value="1">
                            <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
                            <input type="submit" name="submit" class="btn btn-block btn-primary text-uppercase" value="Subscribe"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'?>