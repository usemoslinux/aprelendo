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

$user_is_premium = $user->isPremium();
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
                                <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                                <input type="text" id="s" name="s" class="form-control" aria-label="Search text"
                                    placeholder="Search..." value="<?php echo $search_text ?>">
                                <button type="submit" name="submit" aria-label="Search" class="btn btn-secondary">
                                    <span class="fas fa-search"></span>
                                </button>
                            </div>
                            <!-- Split button -->
                            <div class="dropdown dropdown-add ms-md-2 mb-3">
                                <button type="button"
                                    class="btn btn-success dropdown-toggle
                                        <?php echo $user_is_premium ? '' : 'disabled'?>"
                                        <?php echo $user_is_premium ? '' : 'title="Premium users only"'?>
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fas fa-file-export"></span> Export
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="ajax/exportwords"
                                        <?php echo $user_is_premium
                                            ? 'class="dropdown-item"'
                                            : 'class="dropdown-item disabled" title="Premium users only"'; ?>>
                                        Export all
                                    </a>
                                    <a href="ajax/exportwords<?php echo !empty($query_str) ? $query_str : '' ?>"
                                        <?php echo $user_is_premium
                                            ? 'class="dropdown-item"'
                                            : 'class="dropdown-item disabled" title="Premium users only"'; ?>>
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
