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
<main>
    <section id="wgp" class="why-go-premium">
        <div class="container">
            <div class="row text-center">
                <h1 id="hiw" class="col-12">Why go premium?<br><br></h1>

                <div class="col-lg-4">
                    <i class="fas fa-assistive-listening-systems"></i>
                    <h4>Text-to-Speech</h4>
                    <p>Text-to-Speech (TTS) conversion is an inherent part of <a href="/totalreading" target="_blank"
                            rel="noopener noreferrer">total reading</a>. Premium users are able to listen up to 3 texts per
                        day, instead of 1.</p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-film"></i>
                    <h4>Movies & TV shows</h4>
                    <p>Watch your favorite movies and TV shows using Aprelendo. Our built-in tools will allow you to look up words and phrases in the dictionary or translator.</p>
                </div>
                
                <div class="col-lg-4">
                    <i class="fas fa-book-open"></i>
                    <h4>Epub support</h4>
                    <p>Are you a book lover? As a premium user you will be able to upload 1 epub file per day (&lt;2MB in
                        size) and read as many ebooks as you like.</p>
                </div>
                
            </div>

            <div class="row text-center">
                <div class="col-lg-4">
                    <i class="fas fa-rss"></i>
                    <h4>RSS/Atom feeds</h4>
                    <p>Old good <a href="https://en.wikipedia.org/wiki/RSS" target="_blank"
                            rel="noopener noreferrer">RSS/Atom</a>... By going premium you will be allowed to add texts from
                        up to 3 feeds. Each one will show its last 10 publications.</p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-cloud-download-alt"></i>
                    <h4>Export words</h4>
                    <p>If you are an <a href="https://apps.ankiweb.net/" target="_blank" rel="noopener noreferrer">Anki</a>
                        user or like creating flashcards to learn new vocabulary, you will appreciate this.</p>
                </div>
                <div class="col-lg-4">
                    <i class="fas fa-fire"></i>
                    <h4>High frequency words</h4>
                    <p>Premium users will get visual aids indicating which are the most important words to learn, i.e. those
                        that are among the most used by native speakers.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing py-5">
        <div id="pricing" class="container">
            <div class="row">
                <!-- Free -->
                <div class="col-lg-6">
                    <div class="card mb-5 mb-lg-0">
                        <div class="card-body">
                            <h6 class="card-price text-center">Free as<br>in <br>beer <small><i class="fas fa-beer text-warning"></i></small><span class="period"></span></h6>
                            <div style="height:17px"></div>
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
                                <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Watch movies 
                                &amp; TV shows</li>
                                <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Up to 3 RSS
                                    feeds</li>
                                <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Ebook support
                                    (&lt;2 MB)</li>
                                <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Underline
                                    high frequency words</li>
                                <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Export words
                                    (CSV)</li>
                            </ul>
                            <div class="d-grid gap-2">
                                <a href="/login" role="button" class="btn btn-primary text-uppercase">Sign In</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium -->
                <div class="col-lg-6">
                    <div class="card mb-5 mb-lg-0">
                        <div class="card-body">
                            <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                            <h6 id="lbl-premium-price" class="card-price text-center">$10<span id="lbl-premium-period" class="period">/1 month pass</span></h6>
                            <hr>
                            <div class="text-center">
                                <div class="btn-group text-center" role="group" aria-label="Premium">
                                    <button id="btn-premium-1m" type="button" data-item-nbr="1" data-price="10" class="btn btn-secondary active">1 month</button>
                                    <button id="btn-premium-3m" type="button" data-item-nbr="2" data-price="25" class="btn btn-secondary">3 months</button>
                                    <button id="btn-premium-6m" type="button" data-item-nbr="3" data-price="50" class="btn btn-secondary">6 months</button>
                                    <button id="btn-premium-1y" type="button" data-item-nbr="4" data-price="99" class="btn btn-secondary">1 year</button>
                                </div>
                            </div>
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
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>TTS support
                                    (3 texts p/day)</li>
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>Watch movies 
                                &amp; TV shows</li>
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>Up to 3 RSS
                                    feeds</li>
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>Ebook support
                                    (&lt;2 MB)</li>
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>Underline
                                    high frequency words</li>
                                <li class="text-success"><span class="fa-li"><i class="fas fa-check"></i></span>Export words
                                    (CSV)</li>
                            </ul>

                            <form name="form-subscription" action="/payment" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_xclick">
                                <input type="hidden" name="lc" value="US">
                                <input id="inp-item-nbr" type="hidden" name="item_number" value="1">
                                <input type="hidden" name="bn" value="PP-BuyNowBF">
                                <div class="d-grid gap-2">
                                    <input type="submit" name="submit" class="btn btn-primary text-uppercase" value="Buy Now" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>        
        </div>
    </section>
</main>

<script defer src="js/gopremium-min.js"></script>
<?php require_once 'footer.php'?>