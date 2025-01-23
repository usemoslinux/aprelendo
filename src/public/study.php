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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Study</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-12">
            <main>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="alert-box" class="d-none"></div>
                    </div>
                    <div class="col-sm-12">
                        <div id="study-card" class="card text-center" data-lang="<?php echo $user->lang;?>">
                            <div id="study-card-header" class="study-card-header py-3 placeholder-glow">
                                <h4 id="study-card-word-title" class="my-0 fw-bold placeholder">&nbsp;</h4>
                                <span id="study-card-freq-badge" class="badge">&nbsp;</span>
                            </div>
                            <div id="study-card-body" class="card-body">
                                <div id="examples-placeholder" class="card-examples placeholder-glow">
                                    <p>
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                    <p>
                                        <span class="placeholder col-8"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-4"></span>
                                    </p>
                                    <p>
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                </div>
                                <p id="study-card-examples" class="card-examples"></p>
                            </div>
                            <div id="study-card-footer" class="card-footer">
                                <p id="card-counter"></p>
                                <p class="fw-bold">How well did you remember the meaning of this word?</p>
                                <button id="btn-answer-no-recall" type="button" value="3"
                                    class="btn btn-lg btn-danger btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I am unsure about the meaning of this
                                    word, or I might be confusing it with another, even after reviewing example uses.">
                                    <span class="fw-bold">1. No recall</span>
                                    <br><span class="small">Forgotten</span>
                                </button>
                                <button id="btn-answer-fuzzy" type="button" value="2"
                                    class="btn btn-lg btn-primary btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I have a general idea of what this
                                    word means, but my understanding is hazy and uncertain, even after reviewing
                                    example uses.">
                                    <span class="fw-bold">2. Fuzzy</span>
                                    <br><span class="small">New</span>
                                </button>
                                <button id="btn-answer-partial" type="button" value="1"
                                    class="btn btn-lg btn-warning btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I was only able to fully understand the
                                    meaning of this word after reviewing example uses.">
                                    <span class="fw-bold">3. Partial</span>
                                    <br><span class="small">Learning</span>
                                </button>
                                <button id="btn-answer-excellent" type="button" value="0"
                                    class="btn btn-lg btn-success btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I thoroughly understand and can
                                    confidently use this word.">
                                    <span class="fw-bold">4. Excellent</span>
                                    <br><span class="small">Learned</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</div>

<?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    if (!empty($user->hf_token)) {
        require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    }
?>

<script defer src="/js/study.min.js"></script>
<script defer src="/js/underlinewords.min.js"></script>
<script defer src="/js/wordselection.min.js"></script>
<script defer src="/js/actionbtns.min.js"></script>
<script defer src="/js/dictionaries.min.js"></script>
<script defer src="/js/helpers.min.js"></script>
<script defer src="/js/tooltips.min.js"></script>

<?php require_once 'footer.php' ?>
