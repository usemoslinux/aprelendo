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
                        <a href="/index">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Attributions</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <p>Aprelendo was built using the following free and open source tools:</p>
                            <ul>
                                <li>
                                    <a href="https://nginx.org/" target="_blank" rel="noopener noreferrer">Nginx</a>, by
                                    Igor Sysoev
                                </li>
                                <li>
                                    <a href="https://php.net/" target="_blank" rel="noopener noreferrer">PHP</a>, by The
                                    PHP Group
                                </li>
                                <li>
                                    <a href="https://jquery.com/" target="_blank" rel="noopener noreferrer">jQuery</a>,
                                    by the jQuery Foundation
                                </li>
                                <li>
                                    <a href="https://getbootstrap.com/" target="_blank"
                                        rel="noopener noreferrer">Bootstrap</a>, by Bootstrap Team
                                </li>
                                <li>
                                    <a href="https://huggingface.co/" target="_blank" rel="noopener noreferrer">Hugging
                                        Face</a>, for providing access to machine learning models used to power Aprelendo's
                                    AI capabilities. The <a href="https://huggingface.co/microsoft/Phi-3.5-mini-instruct"
                                        target="_blank" rel="noopener noreferrer">Phi 3.5 mini instruct model</a> by
                                    Microsoft is used to power Lingobot.
                                </li>
                                <li>
                                    <a href="https://github.com/mozilla/readability" target="_blank"
                                        rel="noopener noreferrer">Readability</a>, by Mozilla (used to fetch texts from
                                    external web sources)
                                </li>
                                <li>
                                    <a href="https://github.com/cure53/DOMPurify" target="_blank"
                                        rel="noopener noreferrer">DOMPurify</a>, by cure53 (used for security
                                    before fetching texts from external web sources)
                                </li>
                                <li>
                                    <a href="https://github.com/bazh/subtitles-parser" target="_blank"
                                        rel="noopener noreferrer">Subtitles parser</a>, by <a
                                        href="https://github.com/bazh" target="_blank" rel="noopener noreferrer">Anton
                                        Bazhenov</a> (used to parse offline video subtitles).
                                </li>
                                <li>
                                    <a href="https://github.com/futurepress/epub.js/">EpubJS</a>, by Futurepress (used
                                    to render epub files)
                                </li>
                                <li>
                                    <a href="https://www.chartjs.org/" target="_blank"
                                        rel="noopener noreferrer">Chart.js</a>, by Chart.js Team (used to render
                                    statistics charts)
                                </li>
                            </ul>
                            <p>Other attributions:</p>
                            <ul>
                                <li>
                                    <a href="https://matomo.org/" target="_blank" rel="noopener noreferrer">Matomo</a>,
                                    by Matomo (web analytics platform that provides privacy-focused tracking)
                                </li>
                                <li>
                                    <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener noreferrer">
                                        Bootstrap Icons</a>, by Bootstrap Team
                                </li>
                                <li>Flag icons designed by
                                    <a href="https://github.com/HatScripts/circle-flags" target="_blank"
                                        rel="noopener noreferrer">HatScripts</a>
                                </li>
                                <li>Frequency lists by
                                    <a href="https://github.com/hermitdave/FrequencyWords" target="_blank"
                                        rel="noopener noreferrer">Hermit Dave</a>
                                </li>
                                <li>Theme based on
                                    <a href="https://blacktie.co/demo/solid/index.html" target="_blank"
                                        rel="noopener noreferrer">Solid</a>, by
                                    <a href="https://blacktie.co/" target="_blank"
                                        rel="noopener noreferrer">Blacktie</a>
                                </li>
                                <li>Wallpaper by
                                    <a href="https://unsplash.com/photos/MKeQGpkPgwc" target="_blank"
                                        rel="noopener noreferrer">JD X</a> from <a href="https://unsplash.com"
                                        target="_blank" rel="noopener noreferrer">Unsplash</a>
                                </li>
                            </ul>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>