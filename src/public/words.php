<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

$search_text = !empty($_GET['s']) ? $_GET['s'] : '';
$sort_by     = !empty($_GET['o']) ? (int)$_GET['o'] : 0;

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
                        <span class="active">My words</span>
                    </li>
                </ol>
            </nav>

            <main>
                <div class="row flex">
                    <div class="col-sm-12">
                        <form id="words-filter-form" class="form-flex-row" method="get">
                            <div id="search-wrapper-div" class="input-group mb-3">
                                <input type="text" id="s" name="s" class="form-control" placeholder="Search..."
                                    aria-label="Search word"
                                    value="<?php echo htmlspecialchars($search_text); ?>">
                                <button id="btn-search" type="submit" name="submit" class="btn btn-secondary"
                                    aria-label="Search">
                                    <span class="bi bi-search"></span>
                                </button>
                            </div>
                            <div class="dropdown dropdown-add ms-md-2 mb-3 flex-shrink-0">
                                <button id="btn-import-words" type="button"
                                    class="btn btn-success d-inline-flex align-items-center gap-2 text-nowrap"
                                    data-bs-toggle="modal" data-bs-target="#import-words-modal">
                                    <span class="bi bi-upload"></span>
                                    <span>Import</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="words-list-container" class="position-relative">
                    <div id="words-loader" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="words-content">
                        <!-- Content loaded via AJAX -->
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php
require_once PUBLIC_PATH . 'showactionbuttons.php';
require_once PUBLIC_PATH . 'showimportwordsmodal.php';
require_once PUBLIC_PATH . 'showaibotmodal.php';
require_once PUBLIC_PATH . 'footer.php';
?>

<script defer src="/js/dictionaries.js"></script>
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>
<script defer src="/js/listwords.js"></script>
