<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once "../Includes/bootstrap.php"; // initialize application

use Aprelendo\AuthGuard;

$user = AuthGuard::requirePageUser();

require_once PUBLIC_PATH . "head.php";
require_once PUBLIC_PATH . "header.php";
?>

<div id="nuance-page" class="container mtb d-flex flex-grow-1 flex-column"
    data-has-lingobot="<?php echo !empty($user->hf_token) ? "1" : "0"; ?>">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/studylauncher">Study</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Nuance Battle</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <main>
                <div id="alert-box" class="d-none"></div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-2">Nuance Battle</h5>
                                <p class="mb-0 text-secondary">
                                    Build small sets of similar words, then practice choosing the right nuance.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills mb-3" id="nuance-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sets-tab" data-bs-toggle="pill"
                            data-bs-target="#sets-panel" type="button" role="tab" aria-controls="sets-panel"
                            aria-selected="true">
                            Create / Edit Sets
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="play-tab" data-bs-toggle="pill"
                            data-bs-target="#play-panel" type="button" role="tab" aria-controls="play-panel"
                            aria-selected="false">
                            Play
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="nuance-tab-content">
                    <section class="tab-pane fade show active" id="sets-panel" role="tabpanel"
                        aria-labelledby="sets-tab" tabindex="0">
                        <div class="row g-3">
                            <div class="col-12 col-lg-5">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <span>Your Sets</span>
                                        <div class="d-flex gap-2">
                                            <button id="browse-sets-btn" type="button"
                                                class="btn btn-sm btn-outline-secondary">
                                                Browse Sets
                                            </button>
                                            <button id="new-set-btn" type="button" class="btn btn-sm btn-outline-primary">
                                                New Set
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="confusion-set-list" class="list-group overflow-auto"
                                            style="max-height: 420px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-7">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header">Set Details</div>
                                    <div class="card-body">
                                        <form id="confusion-set-form">
                                            <input type="hidden" id="set-id" name="id" value="">
                                            <div class="mb-3">
                                                <label for="set-title" class="form-label">Title</label>
                                                <input type="text" id="set-title" name="title" class="form-control"
                                                    maxlength="255" placeholder="Give the set a descriptive title">
                                            </div>
                                            <div class="mb-3">
                                                <label for="set-words" class="form-label">Words</label>
                                                <textarea id="set-words" name="words" class="form-control" rows="8"
                                                    placeholder="Add one word per line, or separate words with commas."></textarea>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    Save Set
                                                </button>
                                                <button id="delete-set-btn" type="button"
                                                    class="btn btn-outline-danger d-none">
                                                    Delete Set
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="tab-pane fade" id="play-panel" role="tabpanel" aria-labelledby="play-tab"
                        tabindex="0">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-md-8">
                                        <label for="play-set-select" class="form-label">Set</label>
                                        <select id="play-set-select" class="form-select"></select>
                                    </div>
                                    <div class="col-12 col-md-4 d-grid">
                                        <button id="start-battle-btn" type="button" class="btn btn-success">
                                            Start Battle
                                        </button>
                                    </div>
                                </div>
                                <p id="play-help" class="mt-3 mb-0 text-secondary">
                                    Lingobot creates contrastive cards where one word is the clearest fit.
                                </p>
                                <p id="battle-goal" class="mt-2 mb-0 small text-secondary d-none"></p>
                            </div>
                        </div>
                        <div id="battle-card" class="card shadow-sm mt-3 text-center d-none">
                            <div class="card-header d-flex flex-column align-items-center gap-1">
                                <span id="battle-title">Nuance Battle</span>
                                <span id="battle-counter" class="badge text-bg-secondary"></span>
                            </div>
                            <div class="card-body">
                                <div id="battle-stage" class="nuance-battle-stage rounded mb-3" aria-hidden="true">
                                    <div class="nuance-beam nuance-beam-user"></div>
                                    <div class="nuance-beam nuance-beam-lexicus"></div>
                                    <div class="nuance-clash"></div>
                                </div>
                                <div id="battle-loading" class="d-none">
                                    <div class="placeholder-glow">
                                        <p><span class="placeholder col-8"></span></p>
                                        <p><span class="placeholder col-6"></span></p>
                                        <p><span class="placeholder col-7"></span></p>
                                    </div>
                                </div>
                                <div id="battle-question" class="d-none">
                                    <p id="battle-sentence" class="fs-5"></p>
                                    <div id="battle-choices" class="d-grid gap-2 d-md-flex flex-md-wrap justify-content-md-center"></div>
                                    <div id="battle-feedback" class="alert mt-3 d-none"></div>
                                    <div class="d-flex justify-content-center">
                                        <button id="next-card-btn" type="button" class="btn btn-primary d-none">
                                            Next
                                        </button>
                                    </div>
                                </div>
                                <div id="battle-results" class="d-none"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
</div>

<div class="modal fade" id="public-sets-modal" tabindex="-1" aria-labelledby="public-sets-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="public-sets-modal-label">Browse Sets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="public-set-list" class="list-group overflow-auto" style="max-height: 60vh;"></div>
            </div>
        </div>
    </div>
</div>

<?php require_once PUBLIC_PATH . "showactionbuttons.php"; ?>
<?php require_once PUBLIC_PATH . "showaibotmodal.php"; ?>

<script defer src="/js/dictionaries.js"></script>
<script defer src="/js/underlinewords.js"></script>
<script defer src="/js/wordselection.js"></script>
<script defer src="/js/actionbtns.js"></script>
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>
<script defer src="/js/studynuance.js"></script>

<?php require_once "footer.php"; ?>
