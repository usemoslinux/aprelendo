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

use Aprelendo\Language;
use Aprelendo\TextTypes;

$search_text    = '';
$filter_type    = 0;
$filter_level   = 0;
$sort_by        = 0;

$user_id = $user->id;
$lang_id = $user->lang_id;

if (!empty($_GET)) {
    $search_text    = isset($_GET['s'])     ? $_GET['s']    : '';
    $filter_type    = isset($_GET['ft'])    ? $_GET['ft']   : 0;
    $filter_level   = isset($_GET['fl'])    ? $_GET['fl']   : 0;
    $sort_by        = isset($_GET['o'])     ? $_GET['o']    : 0;
} else {
    // set default language level
    $lang = new Language($pdo, $user_id);
    $lang->loadRecordById($lang_id);
    $filter_level = $lang->level;
}

$type_dropdown_class = 'dropdown-item ft';
$type_active_dropdown_class = 'dropdown-item ft active';

$lvl_dropdown_class = 'dropdown-item fl';
$lvl_active_dropdown_class = 'dropdown-item fl active';

$text_types = new TextTypes($pdo);
$text_types_arr = $text_types->getAll(true);

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
                        <span class="active">Shared texts</span>
                    </li>
                </ol>
            </nav>
            <div class="row flex">
                <div class="col-sm-12">
                    <form class="form-flex-row" method="get">
                        <div id="search-wrapper-div" class="input-group mb-3">
                            <button type="button" id="btn-filter" class="btn btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter
                                <span class="caret"></span>
                            </button>
                            <div id="filter-dropdown" class="dropdown-menu">
                                <h6 class="dropdown-header">Type</h6>
                                <?php foreach ($text_types_arr as $type): ?>
                                        <a data-value="<?= $type['id'] ?>"
                                            class="<?= $filter_type == $type['id']
                                                ? $type_active_dropdown_class
                                                : $type_dropdown_class ?>">
                                            <?= $type['name'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                <div role="separator" class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Level</h6>
                                <?php
                                    $levels = [0 => 'All', 1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];

                                    foreach ($levels as $level => $level_name): ?>
                                        <a data-value="<?= $level ?>"
                                            class="<?= $filter_level == $level
                                                ? $lvl_active_dropdown_class
                                                : $lvl_dropdown_class ?>">
                                            <?= $level_name ?>
                                        </a>
                                    <?php endforeach; ?>
                            </div>
                            <input type="text" id="s" name="s" class="form-control" aria-label="Search text"
                                placeholder="Search..." value="<?php echo isset($search_text) ? $search_text : '' ?>">
                            <button id="btn-search" type="submit" name="submit" class="btn btn-secondary"
                                aria-label="Search">
                                <span class="bi bi-search"></span>
                            </button>
                        </div> <!-- /btn-group -->
                        <!-- Split button -->
                        <div class="dropdown dropdown-add ms-md-2 mb-3">
                            <button id="btn-add-text" type="button" class="btn btn-success dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <span class="bi bi bi-plus-lg"></span> Add
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/addtext?sh">Plain text</a>
                                <a class="dropdown-item" href="/addvideo">YouTube video</a>
                                <a href="/addrss" class="dropdown-item">RSS text</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require_once 'listsharedtexts.php'; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
