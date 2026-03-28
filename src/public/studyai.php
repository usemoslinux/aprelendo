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
                        <div id="study-card" class="card text-center" data-lang="<?php echo $user->lang; ?>">
                            <div id="study-card-header" class="study-card-header py-3 text-light placeholder-glow">
                                <h4 id="study-card-word-title" class="my-0 fw-bold placeholder">&nbsp;</h4>
                                <span id="study-card-freq-badge" class="badge">&nbsp;</span>
                            </div>
                            <div id="study-card-body" class="card-body">                
                                <div id="ai-card">
                                    <div class="mb-3">
                                        <label for="select-prompt" class="form-label">Select a question for Lingobot:</label>
                                        <select id="select-prompt" class="form-select mb-3">
                                            <option value="example_sentences" selected>Give one or more example sentences using {word}</option>
                                            <option value="synonym_antonym">Give one synonym & antonym for {word}</option>
                                            <option value="register">Is {word} formal, informal, or neutral?</option>
                                            <option value="multiple_meanings">If {word} has multiple meanings, give at least two</option>
                                        </select>

                                        <label for="text-user-answer" class="form-label">Your answer:</label>
                                        <textarea id="text-user-answer" class="form-control" rows="6"></textarea>
                                        
                                        <button id="btn-submit-user-answer" type="button" class="btn btn-primary my-2">Ask Lingobot to Evaluate</button>
                                    </div>
                                    <div id="studyai-answer">
                                        <textarea id="text-studyai-answer" class="form-control" rows="6" readonly></textarea>
                                        <small>Lingobot can make mistakes. Use its answers as a reference only.</small>
                                    </div>
                                </div>
                            </div>
                            <div id="live-progress" class="progress rounded-0" style="height: 1px;">
                                <div id="live-progress-bar" class="progress-bar bg-secondary"
                                    role="progressbar"
                                    aria-label="Study progress"
                                    aria-valuenow="0"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <div id="study-card-footer" class="card-footer">
                                <p id="card-counter"></p>
                                <div id="answer-card" class="mx-auto" style="max-width: 550px;">
                                    <div id="answer-card-page-1" class="smooth-transition">
                                        <div class="card-body">
                                            <h5 id="h-answer-title" class="card-title">My answer was...</h5>
                                            <div class="d-flex flex-column">
                                                <button id="btn-answer-no-recall" type="button" value="3"
                                                    class="btn btn-lg btn-danger btn-answer my-3">
                                                    <span class="fw-bold">(1) Completely incorrect</span>
                                                    <br><span class="small">Missing target word/phrase use, wrong meaning, or too broken.</span>
                                                </button>
                                                <button id="btn-answer-fuzzy" type="button" value="2"
                                                    class="btn btn-lg btn-primary btn-answer mb-3">
                                                    <span class="fw-bold">(2) Incorrect</span>
                                                    <br><span class="small">Intended use is clear, but still incorrect.</span>
                                                </button>
                                                <button id="btn-answer-partial" type="button" value="1"
                                                    class="btn btn-lg btn-warning btn-answer mb-3">
                                                    <span class="fw-bold">(3) Mostly correct</span>
                                                    <br><span class="small">Correct use, with only minor errors elsewhere.</span>
                                                </button>
                                                <button id="btn-answer-excellent" type="button" value="0"
                                                    class="btn btn-lg btn-success btn-answer mb-3">
                                                    <span class="fw-bold">(4) Perfect</span>
                                                    <br><span class="small">Correct, natural, and complete.</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="fw-lighter fst-italic text-secondary">Use keyboard shortcuts: 1, 2, 3, or 4</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php
require_once PUBLIC_PATH . 'showactionbuttons.php'; // load dictionary modal window
require_once PUBLIC_PATH . 'showaibotmodal.php'; // load AI bot modal window
?>

<script defer src="/js/dictionaries.js"></script>
<script defer src="/js/underlinewords.js"></script>
<script defer src="/js/wordselection.js"></script>
<script defer src="/js/actionbtns.js"></script>
<script defer src="/js/aibot.js"></script>
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>
<script defer src="/js/studyai.js"></script>

<?php require_once 'footer.php' ?>
