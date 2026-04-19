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
                            <div id="study-card-header" class="py-3 text-light d-flex flex-column justify-content-center align-items-center placeholder-glow" style="min-height: 86px;">
                                <h4 id="study-card-word-title" class="my-0 fw-bold placeholder w-50 rounded">&nbsp;</h4>
                                <span id="study-card-freq-badge" class="badge mt-1 placeholder w-25">&nbsp;</span>
                            </div>
                            <div id="study-card-body" class="card-body">
                                <div id="ai-card">
                                    <div class="mb-3">
                                        <label id="label-user-answer" for="text-user-answer" class="form-label">Write one or more sentences using this word:</label>
                                        <textarea id="text-user-answer" class="form-control" rows="6"></textarea>

                                        <button id="btn-submit-user-answer" type="button" class="btn btn-primary my-2">Ask Lingobot to Evaluate</button>
                                    </div>
                                    <div id="studyai-answer" class="d-none">
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
                            <p id="card-counter" class="text-center"></p>
                        </div>
                    </div>
                    <div id="review-column" class="col-12 col-md-6 mt-3 mt-md-0">
                        <div id="answer-card" class="card text-center border-secondary h-100 overflow-hidden">
                            <div id="answer-card-header" class="py-3 text-light d-flex flex-column justify-content-center align-items-center bg-gradient bg-secondary border-secondary" style="min-height: 86px;">
                                <h4 id="answer-card-title" class="my-0 fw-bold">Review your answer</h4>
                            </div>
                            <div id="answer-card-prompt" class="card-body d-flex flex-column justify-content-center">
                                <div class="border rounded p-3 text-start bg-body-tertiary h-100">
                                    <p class="fw-semibold mb-2">First...</p>
                                    <p class="small text-secondary mb-2">
                                        Write your answer in the text box.
                                    </p>
                                    <p class="small text-secondary mb-2">
                                        Press <strong>Ask Lingobot to Evaluate</strong> or use <strong>Ctrl/Cmd + Enter</strong>.
                                    </p>
                                    <p class="small text-secondary mb-0">
                                        Your self-rating buttons will appear here after you request feedback.
                                    </p>
                                </div>
                            </div>
                            <div id="answer-card-body" class="card-body d-none">
                                <div class="d-flex flex-column card-body">
                                    <h5 class="card-title">My answer was...</h5>
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
<script defer src="/js/studyai.js"></script>

<?php require_once 'footer.php' ?>
