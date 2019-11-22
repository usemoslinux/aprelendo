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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
    
$search_text = '';
$sort_by = 0;

if (!empty($_GET)) {
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $sort_by = isset($_GET['o']) ? $_GET['o'] : 0;  
}

$query_str = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css" integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe"
    crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css" integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV"
    crossorigin="anonymous">

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Word list</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-sm-12">
                    <form class="form-flex-row" method="get">
                        <div class="input-group my-2">
                            <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                            <input type="text" id="s" name="s" class="form-control" placeholder="Search..." value="<?php echo $search_text ?>">
                            <div class="input-group-append">
                                <button type="submit" name="submit" class="btn btn-secondary">
                                <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Split button -->
                        <div class="dropdown ml-md-2 my-2">
                            <button type="button" class="btn btn-success dropdown-btn dropdown-toggle <?php echo $user->isPremium() ? '"' : 'disabled" title="Premium users only"'; ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-file-export"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="ajax/exportwords.php" <?php echo $user->isPremium() ? 'class="dropdown-item"' : 'class="dropdown-item disabled" title="Premium users only"'; ?> >
                                    Export all
                                </a>
                                <a href="ajax/exportwords.php<?php echo !empty($query_str) ? $query_str : '' ?>" <?php echo $user->isPremium() ? 'class="dropdown-item"' : 'class="dropdown-item disabled" title="Premium users only"'; ?> >
                                    Export search results
                                </a>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
            <?php require_once 'listwords.php'; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>