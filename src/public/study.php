<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-12">
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
        <div class="col-12">
            <main>
                <div class="row">
                    <div class="col-12">
                        <div id="alert-box" class="d-none"></div>
                    </div>
                    <div id="study-column" class="col-12 col-md-6">
                        <div id="study-card" class="card text-center h-100 overflow-hidden" data-lang="<?php echo $user->lang; ?>">
                            <div id="study-card-header" class="py-3 text-light d-flex flex-column justify-content-center align-items-center placeholder-glow bg-gradient bg-success border-success" style="min-height: 86px;">
                                <h4 id="study-card-word-title" class="my-0 fw-bold placeholder w-50 rounded">&nbsp;</h4>
                                <span id="study-card-freq-badge" class="badge mt-1 placeholder w-25">&nbsp;</span>
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
                                <div id="study-card-examples" class="card-examples"></div>
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
                            <p id="card-counter" class="text-center"></p>
                        </div>
                    </div>
                    <div id="review-column" class="col-12 col-md-6 mt-3 mt-md-0">
                        <div id="answer-card" class="card text-center border-secondary h-100 overflow-hidden">
                            <div id="answer-card-header" class="py-3 text-light d-flex flex-column justify-content-center align-items-center bg-gradient bg-secondary border-secondary" style="min-height: 86px;">
                                <h4 id="answer-card-title" class="my-0 fw-bold">Review your answer</h4>
                            </div>
                            <div id="answer-card-body" class="card-body m-3">
                                <div id="answer-card-placeholder" class="placeholder-glow">
                                    <h5 class="card-title mb-3">
                                        <span class="placeholder col-8"></span>
                                    </h5>
                                    <div class="d-flex flex-column">
                                        <div class="btn btn-lg btn-danger disabled text-center my-3" aria-hidden="true">
                                            <span class="placeholder col-5"></span>
                                            <br><span class="placeholder col-8"></span>
                                        </div>
                                        <div class="btn btn-lg btn-success disabled text-center mb-3" aria-hidden="true">
                                            <span class="placeholder col-4"></span>
                                            <br><span class="placeholder col-7"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="answer-card-page-1" class="smooth-transition d-none">
                                    <h5 class="card-title">Did you know the full meaning before seeing the examples?</h5>
                                    <div class="d-flex flex-column">
                                        <button id="btn-answer-more" type="button"
                                            class="btn btn-lg btn-danger my-3">
                                            <span class="fw-bold">(+) No, not really</span>
                                            <br><span class="small">Let me explain why...</span>
                                        </button>
                                        <button id="btn-answer-excellent" type="button" value="0"
                                            class="btn btn-lg btn-success btn-answer mb-3">
                                            <span class="fw-bold">(4) Yes, of course!</span>
                                            <br><span class="small">I already knew exactly what this word meant</span>
                                        </button>
                                    </div>
                                </div>
                                <div id="answer-card-page-2" class="d-none smooth-transition">
                                    <h5 class="card-title">After reviewing the examples, how well do you understand it now?</h5>
                                    <div class="d-flex flex-column">
                                        <button id="btn-answer-no-recall" type="button" value="3"
                                            class="btn btn-lg btn-danger btn-answer my-3">
                                            <span class="fw-bold">(1) Still no clue</span>
                                            <br><span class="small">I have no idea what it means or confuse this word with something else</span>
                                        </button>
                                        <button id="btn-answer-fuzzy" type="button" value="2"
                                            class="btn btn-lg btn-primary btn-answer mb-3">
                                            <span class="fw-bold">(2) General idea</span>
                                            <br><span class="small">I have a sense, but can't give the exact meaning or translation</span>
                                        </button>
                                        <button id="btn-answer-partial" type="button" value="1"
                                            class="btn btn-lg btn-warning btn-answer mb-3">
                                            <span class="fw-bold">(3) Completely understand</span>
                                            <br><span class="small">I now fully understand what it means</span>
                                        </button>
                                        <button id="btn-answer-prev" class="btn btn-lg btn-outline-secondary mb-3">← Back</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="min-height:46px">
                                <small class="fst-italic text-secondary">Use keyboard shortcuts: 1, 2, 3, or 4</small>
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
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>
<script defer src="/js/study.js"></script>

<?php require_once 'footer.php' ?>
