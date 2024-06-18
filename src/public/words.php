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
    
$search_text = '';
$sort_by = 0;

if (!empty($_GET)) {
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $sort_by = isset($_GET['o']) ? $_GET['o'] : 0;
}

$query_str = '?s=' . $search_text . '&o=0';

?>

<div class="container mtb d-flex flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Word list</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex">
                    <div class="col-sm-12">
                        <form class="form-flex-row" method="get">
                            <div class="input-group mb-3">
                                <input type="text" id="s" name="s" class="form-control" aria-label="Search text"
                                    placeholder="Search..." value="<?php echo $search_text ?>">
                                <button type="submit" name="submit" aria-label="Search" class="btn btn-secondary">
                                    <span class="bi bi-search"></span>
                                </button>
                            </div>
                            <!-- Import words button -->
                            <div class="dropdown-add">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#import-words-modal"
                                    class="btn btn-primary text-nowrap ms-md-2 mb-3">
                                    <span class="bi bi-cloud-upload-fill"></span> Import
                                </button>
                            </div>
                            <!-- Export words button -->
                            <div class="dropdown dropdown-add ms-md-2 mb-3">
                                <button type="button"
                                    class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="bi bi-cloud-download-fill"></span> Export
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a id="export-all" href="ajax/exportwords" class="dropdown-item">Export all</a>
                                    <a id="export-search"
                                        href="ajax/exportwords<?php echo !empty($query_str) ? $query_str : '' ?>"
                                        class="dropdown-item">
                                        Export search results
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php require_once 'listwords.php'; ?>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
