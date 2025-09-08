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

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
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
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <h4>Terms of Service</h4>
                            <em>Last updated: August 2024</em>
                            <p>By using our service, you agree to these terms. Please read them carefully.</p>
                            <br>
                            <h6>1. Accounts</h6>
                            <p>When you create an account on Aprelendo, you must provide information that is accurate
                                and complete. Please keep your information updated. Failure to maintain accurate
                                information could lead to the termination of your account.</p>
                            <br>
                            <h6>2. Content</h6>
                            <p>You can upload, share, and manage your texts, ebooks, videos, or other material
                                ("Content") on our service. You are responsible for ensuring that your Content does not
                                violate any laws or rights of others, including copyright, privacy rights, publicity
                                rights, contract rights, or any other rights of any person or entity.</p>
                            <p>Content that is considered inappropriate or unacceptable includes, but is not limited to:
                            </p>
                            <ol>
                                <li>Sexual or inappropriate content;</li>
                                <li>Violent, abusive, or hateful content;</li>
                                <li>Misleading, spam, or useless content;</li>
                                <li>Content that raises legal issues, such as copyright violations;</li>
                                <li>Content that is in a language other than the one for which it was uploaded.</li>
                            </ol>
                            <p>Users are allowed to report any of the above for texts and videos included in the
                                "<a href="/sharedtexts">Shared texts</a>" section. Any other content under
                                "<a href="/texts">My texts</a>" will remain private and will not be subject to these
                                reports.</p>
                            <p>We reserve, however, the right to remove any content that violates these directives or
                                any other terms outlined in this agreement.</p>
                            <br>
                            <h6>3. Privacy</h6>
                            <p>Our <a href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                                details how we handle your personal data. By using Aprelendo, you consent to our
                                data practices as described in the Privacy Policy.</p>
                            <br>
                            <h6>4. Donations</h6>
                            <p>Aprelendo is supported by donations. If you value our service, consider
                                <a href="/donate" target="_blank" rel="noopener noreferrer">donating</a> to help us
                                keep it running.
                            </p>
                            <br>
                            <h6>5. Communication</h6>
                            <p>We will not send you marketing emails. You will only receive emails necessary for
                                managing your subscription.</p>
                            <br>
                            <h6>6. Termination</h6>
                            <p>We reserve the right to terminate or suspend your account at any time, without prior
                                notice, if you breach these terms of service.</p>
                            <br>
                            <h6>7. Changes</h6>
                            <p>We may update these terms occasionally. If a change is significant, we will provide a
                                30-day notice on our website. Continued use of the service after changes means you
                                accept the new terms. If you disagree with the changes, you must stop using our
                                services.</p>
                            <br>
                            <h6>8. Contact Us</h6>
                            <p>If you have questions or concerns about these terms, please
                                <a href="/contact" target="_blank" rel="noopener noreferrer">contact us</a>.
                            </p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>