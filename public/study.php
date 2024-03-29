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
                        <div id="card" class="card text-center" style="min-width: 100%;"
                            data-lang="<?php echo $user->lang;?>">
                            <div id="card-header" class="card-header fw-bold">...</div>
                            <div class="card-body">
                                <div id="card-loader" class="lds-ellipsis m-auto">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                                <p id="card-text" class="card-text"></p>
                            </div>
                            <div id="card-footer" class="card-footer">
                                <p id="card-counter"></p>
                                <p class="fw-bold">How well did you remember the meaning of this word?</p>
                                <button id="btn-answer-no-recall" type="button" value="3"
                                    class="btn btn-lg btn-danger btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I am unsure about the meaning of this
                                    word, or I might be confusing it with another, even after reviewing example uses.">
                                    1. No recall
                                </button>
                                <button id="btn-answer-fuzzy" type="button" value="2"
                                    class="btn btn-lg btn-warning btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I have a general idea of what this
                                    word means, but my understanding is hazy and uncertain, even after reviewing
                                    example uses.">
                                    2. Fuzzy
                                </button>
                                <button id="btn-answer-partial" type="button" value="1"
                                    class="btn btn-lg btn-info btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I have a good overall understanding of the
                                    meaning of this word, but I am unable to recall all the details or provide a
                                    complete and accurate definition, even after reviewing example uses.">
                                    3. Partial
                                </button>
                                <button id="btn-answer-excellent" type="button" value="0"
                                    class="btn btn-lg btn-success btn-answer mb-3"
                                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                                    data-bs-placement="bottom" data-bs-title="I thoroughly understand and can
                                    confidently use this word.">
                                    4. Excellent
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
require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
?>

<script defer src="/js/study.min.js"></script>
<script defer src="/js/dictionary.min.js"></script>
<script defer src="/js/helpers.min.js"></script>
<script defer src="/js/tooltips.min.js"></script>

<?php require_once 'footer.php' ?>
