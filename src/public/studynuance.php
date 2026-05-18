<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;

$user = AuthGuard::requirePageUser();

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
                                        <button id="new-set-btn" type="button" class="btn btn-sm btn-outline-primary">
                                            New Set
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="confusion-set-list" class="list-group"></div>
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
                                                    maxlength="255" placeholder="Light verbs, motion verbs, shine words">
                                            </div>
                                            <div class="mb-3">
                                                <label for="set-words" class="form-label">Words</label>
                                                <textarea id="set-words" name="words" class="form-control" rows="8"
                                                    placeholder="glittering&#10;glinting&#10;shimmering&#10;glowing"></textarea>
                                                <div class="form-text">
                                                    Add one word per line, or separate words with commas.
                                                </div>
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
                                        <button type="button" class="btn btn-secondary" disabled>
                                            Game Coming Soon
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-secondary">
                                    The battle UI is reserved here. Set management is ready first.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
</div>

<script defer src="/js/studynuance.js"></script>

<?php require_once 'footer.php'; ?>
