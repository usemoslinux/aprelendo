<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Terms of Service</span>
                    </li>
                </ol>
            </nav>
            <main class="simple-text">
                <section aria-labelledby="terms-of-service-title">
                    <h1 id="terms-of-service-title" class="h4">Terms of Service</h1>
                    <p><em><time datetime="2026-04-05">Last updated: April 5, 2026</time></em></p>
                    <p>These Terms of Service govern your use of Aprelendo. By creating an account or using the
                        service, you agree to these terms.</p>
                    <div class="alert alert-light border my-4">
                        <p class="mb-2"><strong>In short</strong></p>
                        <ul class="mb-0">
                            <li>Aprelendo is a language-learning service, not a general-purpose publishing platform.</li>
                            <li>You remain responsible for the content you upload or share.</li>
                            <li>Private content stays private unless you choose to publish it to shared areas.</li>
                            <li>Optional third-party services such as Google Sign-In, YouTube, Hugging Face, and
                                PayPal have their own terms and privacy practices.</li>
                        </ul>
                    </div>
                </section>

                <section aria-labelledby="using-aprelendo">
                    <h2 id="using-aprelendo" class="h6">1. Using Aprelendo</h2>
                    <p>Aprelendo is designed to help learners improve active vocabulary through reading, review,
                        and feedback. You agree to use the service lawfully and in a way that does not harm the
                        platform, other users, or third parties.</p>
                </section>
                <br>

                <section aria-labelledby="accounts-and-security">
                    <h2 id="accounts-and-security" class="h6">2. Accounts and security</h2>
                    <p>When you create an account, you must provide accurate information and keep it reasonably
                        up to date. You are responsible for activity that occurs through your account.</p>
                    <p>You may create an account with email and password or, if you prefer, use optional Google
                        Sign-In. You are responsible for keeping your login credentials and connected services
                        secure.</p>
                </section>
                <br>

                <section aria-labelledby="your-content">
                    <h2 id="your-content" class="h6">3. Your content</h2>
                    <p>You can upload, import, save, and organize texts, ebooks, videos, audio, vocabulary data,
                        and related material in Aprelendo.</p>
                    <ul>
                        <li>Content stored in <a href="/texts">My texts</a> remains private to your account unless
                            you choose to share it.</li>
                        <li>Content submitted to <a href="/sharedtexts">Shared texts</a> or other public areas may
                            be displayed to other users, reported, reviewed, moderated, and removed.</li>
                        <li>If you share content publicly, you grant Aprelendo a non-exclusive permission to host,
                            display, reproduce within the service, and moderate that content so it can be made
                            available to other users.</li>
                        <li>You remain responsible for making sure your content is lawful and does not infringe
                            copyright, privacy, publicity, contract, or other rights.</li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="prohibited-use">
                    <h2 id="prohibited-use" class="h6">4. Prohibited content and behavior</h2>
                    <p>The following are not allowed on Aprelendo:</p>
                    <ul>
                        <li>Sexual or otherwise inappropriate content.</li>
                        <li>Violent, abusive, hateful, or harassing content.</li>
                        <li>Misleading, spammy, low-value, or obviously useless content.</li>
                        <li>Content that creates legal problems, including copyright violations.</li>
                        <li>Content uploaded under the wrong language or with deliberately false metadata.</li>
                        <li>Attempts to abuse, disrupt, scrape, or interfere with the service or other users.</li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="moderation-and-reports">
                    <h2 id="moderation-and-reports" class="h6">5. Moderation, reports, and takedowns</h2>
                    <p>Users may report shared texts, videos, and other public content that appears to violate
                        these terms. Private content in <a href="/texts">My texts</a> is not part of the normal
                        community reporting flow, but Aprelendo still reserves the right to act if necessary to
                        protect the service, comply with legal obligations, or investigate abuse.</p>
                    <p>We may remove content, restrict access, or suspend accounts if we believe this is necessary
                        to enforce these terms, respond to complaints, or address legal or security concerns.</p>
                </section>
                <br>

                <section aria-labelledby="third-party-services">
                    <h2 id="third-party-services" class="h6">6. Third-party services and donations</h2>
                    <p>Some features depend on third-party services. These include optional Google Sign-In,
                        YouTube-based content, Hugging Face for Lingobot, PayPal for donations, and external
                        resources such as dictionaries or embedded assets. Your use of those services may also be
                        subject to their own terms and privacy policies.</p>
                    <p>Donations are optional. Payment processing is handled by PayPal, not by Aprelendo directly,
                        and donating does not change your access rights inside the service.</p>
                </section>
                <br>

                <section aria-labelledby="communications">
                    <h2 id="communications" class="h6">7. Communications</h2>
                    <p>We do not send marketing emails. We may send account, security, and service-related emails,
                        such as activation emails, password reset messages, or important notices about the service
                        or these terms.</p>
                </section>
                <br>

                <section aria-labelledby="availability-and-termination">
                    <h2 id="availability-and-termination" class="h6">8. Availability, suspension, and termination</h2>
                    <p>We work to keep Aprelendo available and useful, but features may change, pause, or be
                        removed over time.</p>
                    <p>We may suspend or terminate your access if you breach these terms, create legal or security
                        risk, or abuse the service.</p>
                </section>
                <br>

                <section aria-labelledby="privacy-reference">
                    <h2 id="privacy-reference" class="h6">9. Privacy</h2>
                    <p>Our <a href="/privacy">Privacy Policy</a> explains how Aprelendo handles personal data.</p>
                </section>
                <br>

                <section aria-labelledby="terms-changes">
                    <h2 id="terms-changes" class="h6">10. Changes to these terms</h2>
                    <p>We may update these terms from time to time. If a change is material, we will update the
                        date above and post the revised terms on this page. Continued use of the service after the
                        updated terms take effect means you accept them.</p>
                </section>
                <br>

                <section aria-labelledby="terms-contact">
                    <h2 id="terms-contact" class="h6">11. Contact us</h2>
                    <p>If you have questions or concerns about these terms, please <a href="/contact">contact us</a>.</p>
                </section>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
