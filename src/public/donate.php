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

<main>
    <section id="donate-hero" class="my-5 mx-4 text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h1 class="col-12">Keep Aprelendo free for everyone</h1>
                    <h4>Open source. No ads. No subscriptions. Powered by your support.</h4>
                    <div class="bi bi-braces-asterisk fs-1 my-4"></div>
                </div>

                <div class="col-12 col-lg-10 mx-auto">
                    <p class="lead">
                        Aprelendo is a community project. Your contribution funds hosting, development,
                        and new features for learners who have moved beyond the basics.
                    </p>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="p-4 h-100 bg-dark bg-opacity-25 rounded-4 shadow-sm text-center">
                        <h5 class="mb-2">Donate funds</h5>
                        <p class="mb-3">Financial support pays for servers, storage, bandwidth, and ongoing development.</p>
                        <a href="https://www.paypal.com/ncp/payment/GJCS2645TD9GN" target="_blank"
                            class="btn btn-warning btn-lg" title="Support via PayPal" rel="noopener noreferrer">
                            Support via <span class="bi bi-paypal"></span> PayPal
                        </a>
                        <p class="mt-3 mb-0 small opacity-75">One-time or recurring. The platform remains free for everyone.</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-4 h-100 bg-dark bg-opacity-25 rounded-4 shadow-sm text-center">
                        <h5 class="mb-2">Contribute code</h5>
                        <p class="mb-3">Help build features, fix bugs, improve performance, and translate the UI.</p>
                        <a href="https://github.com/usemoslinux/aprelendo" target="_blank"
                            class="btn btn-outline-light btn-lg" title="Contribute on GitHub" rel="noopener noreferrer">
                            Contribute on <span class="bi bi-github"></span> GitHub
                        </a>
                        <p class="mt-3 mb-0 small opacity-75">Code contributions are different from donations. Both are welcome.</p>
                    </div>
                </div>
            </div>

            <p class="text-center mt-4">
                You can also star the repository, share Aprelendo with a friend, or help curate high-quality texts and audio.
            </p>

            <hr class="my-5 border-light opacity-50">

            <div class="row gy-4 text-start">
                <div class="col-md-4">
                    <div class="p-4 h-100 bg-dark bg-opacity-25 rounded-4 shadow-sm">
                        <h5 class="mb-2">Where your donation goes</h5>
                        <p class="mb-2">Servers, storage, bandwidth, and monitoring to keep Aprelendo fast and reliable.</p>
                        <p class="mb-0">Development time for features that strengthen reading, listening, speaking, and writing—especially for learners stuck on a plateau.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 h-100 bg-dark bg-opacity-25 rounded-4 shadow-sm">
                        <h5 class="mb-2">Our promise</h5>
                        <p class="mb-2">Aprelendo will remain free and open source. No bait-and-switch, no locked lessons, no surprise paywalls.</p>
                        <p class="mb-0">Registration is only to save progress and personalize practice.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 h-100 bg-dark bg-opacity-25 rounded-4 shadow-sm">
                        <h5 class="mb-2">Why it matters</h5>
                        <p class="mb-2">Intermediate and advanced learners need targeted practice to break plateaus.</p>
                        <p class="mb-0">Your support keeps advanced features and corpora accessible to everyone.</p>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-10 mx-auto">
                    <h3 class="text-center mb-3">FAQ</h3>
                    <div class="accordion" id="donateFAQ">
                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqOne">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqOneCollapse" aria-expanded="false"
                                    aria-controls="faqOneCollapse">
                                    Is my donation a subscription?
                                </button>
                            </h2>
                            <div id="faqOneCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    You can make a one-time or recurring contribution. Either way, the platform stays free for everyone.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqTwo">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqTwoCollapse" aria-expanded="false"
                                    aria-controls="faqTwoCollapse">
                                    Can I contribute code or translations instead of donating?
                                </button>
                            </h2>
                            <div id="faqTwoCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    Absolutely. Visit the GitHub repository to see open issues, roadmap items, and translation needs. Code and translation contributions are separate from financial donations, and both help the project grow.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqThree">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqThreeCollapse" aria-expanded="false"
                                    aria-controls="faqThreeCollapse">
                                    Will Aprelendo ever show ads?
                                </button>
                            </h2>
                            <div id="faqThreeCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    No. Aprelendo is funded by the community, not advertisers.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqFour">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqFourCollapse" aria-expanded="false"
                                    aria-controls="faqFourCollapse">
                                    How can I help curate shared texts?
                                </button>
                            </h2>
                            <div id="faqFourCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    Curated texts are the heart of Aprelendo. Please add texts that credit the original source, include audio when possible, and avoid copyright violations. Check that the language and metadata are correct, and report items that don’t meet these standards.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqFive">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqFiveCollapse" aria-expanded="false"
                                    aria-controls="faqFiveCollapse">
                                    How do I report issues with a text or audio?
                                </button>
                            </h2>
                            <div id="faqFiveCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    Reporting can be done directly inside Aprelendo. When viewing a text, click the
                                    <span class="bi bi-flag-fill text-danger"></span> report button and choose the reason that applies.
                                    Options include sexual or inappropriate content, violent or hateful content, spam or misleading content,
                                    legal issues (such as copyright violations), or wrong language. This helps us keep the library safe,
                                    accurate, and high quality for everyone.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqSix">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqSixCollapse" aria-expanded="false"
                                    aria-controls="faqSixCollapse">
                                    How else can I support Aprelendo?
                                </button>
                            </h2>
                            <div id="faqSixCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    Follow us on social media, share Aprelendo with colleagues and friends, and recommend it to language programs or institutions. Word-of-mouth helps the project reach more learners.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-light">
                            <h2 class="accordion-header" id="faqSeven">
                                <button class="accordion-button collapsed"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqSevenCollapse" aria-expanded="false"
                                    aria-controls="faqSevenCollapse">
                                    Why does Aprelendo require registration if it’s free?
                                </button>
                            </h2>
                            <div id="faqSevenCollapse" class="accordion-collapse collapse" data-bs-parent="#donateFAQ">
                                <div class="accordion-body">
                                    Registration lets you save progress, personalize practice, and sync across devices. There are no paywalls and no locked lessons.
                                </div>
                            </div>
                        </div>

                    </div> <!-- /accordion -->
                </div>
            </div>

        </div>
    </section>
</main>


<?php require_once 'footer.php' ?>