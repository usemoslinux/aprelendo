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
                            <div class="dropdown dropdown-add ms-md-2 mb-3">
                                <button id="btn-import-words" type="button" class="btn btn-success"
                                    data-bs-toggle="modal" data-bs-target="#import-words-modal">
                                    <span class="bi bi-upload"></span> Import
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
require_once PUBLIC_PATH . 'showdicactionmenu.php';
require_once PUBLIC_PATH . 'showimportwordsmodal.php';
require_once PUBLIC_PATH . 'footer.php';
?>

<script defer src="/js/listwords.js"></script>
<script defer src="/js/dictionaries.js"></script>
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>
