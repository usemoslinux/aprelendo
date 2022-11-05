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

use Aprelendo\Includes\Classes\Language;

$search_text    = '';
$filter_type    = 0;
$filter_level   = 0;
$sort_by        = 0;

$user_id = $user->getId();
$lang_id = $user->getLangId();

if (!empty($_GET)) {
    $search_text    = isset($_GET['s'])     ? $_GET['s']    : '';
    $filter_type    = isset($_GET['ft'])    ? $_GET['ft']   : 0;
    $filter_level   = isset($_GET['fl'])    ? $_GET['fl']   : 0;
    $sort_by        = isset($_GET['o'])     ? $_GET['o']    : 0;
} else {
    // set default language level
    $lang = new Language($pdo, $user_id);
    $lang->loadRecord($lang_id);
    $filter_level = $lang->getLevel();
}

$type_dropdown_class = 'class="dropdown-item ft"';
$type_active_dropdown_class = ' class="dropdown-item ft active" ';

$lvl_dropdown_class = 'class="dropdown-item fl"';
$lvl_active_dropdown_class = ' class="dropdown-item fl active" ';

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
                        <span class="active">Shared texts</span>
                    </li>
                </ol>
            </nav>
            <div class="row flex">
                <div class="col-sm-12">
                    <form class="form-flex-row" method="get">
                        <input id="ft" name="ft" value="<?php echo $filter_type; ?>" type="hidden">
                        <input id="fl" name="fl" value="<?php echo $filter_level; ?>" type="hidden">
                        <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                        <div id="search-wrapper-div" class="input-group mb-3">
                            <button type="button" id="btn-filter" class="btn btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter
                                <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <h6 class="dropdown-header">Type</h6>
                                <a onclick="$('#ft').val(0);" <?php echo $filter_type==0 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    All
                                </a>
                                <a onclick="$('#ft').val(1);" <?php echo $filter_type==1 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Articles
                                </a>
                                <a onclick="$('#ft').val(2);" <?php echo $filter_type==2 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Conversations
                                </a>
                                <a onclick="$('#ft').val(3);" <?php echo $filter_type==3 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Letters
                                </a>
                                <a onclick="$('#ft').val(4);" <?php echo $filter_type==4 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Lyrics
                                </a>
                                <a onclick="$('#ft').val(5);" <?php echo $filter_type==5 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Videos
                                </a>
                                <a onclick="$('#ft').val(7);" <?php echo $filter_type==7 ?
                                    $type_active_dropdown_class : $type_dropdown_class ; ?> >
                                    Others
                                </a>
                                <div role="separator" class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Level</h6>
                                <a onclick="$('#fl').val(0);" <?php echo $filter_level==0 ?
                                    $lvl_active_dropdown_class : $lvl_dropdown_class ; ?>>
                                    All
                                </a>
                                <a onclick="$('#fl').val(1);" <?php echo $filter_level==1 ?
                                    $lvl_active_dropdown_class : $lvl_dropdown_class ; ?>>
                                    Beginner
                                </a>
                                <a onclick="$('#fl').val(2);" <?php echo $filter_level==2 ?
                                    $lvl_active_dropdown_class : $lvl_dropdown_class ; ?>>
                                    Intermediate
                                </a>
                                <a onclick="$('#fl').val(3);" <?php echo $filter_level==3 ?
                                    $lvl_active_dropdown_class : $lvl_dropdown_class ; ?>>
                                    Advanced
                                </a>
                            </div>
                            <input type="text" id="s" name="s" class="form-control" aria-label="Search text"
                                placeholder="Search..." value="<?php echo isset($search_text) ? $search_text : '' ?>">
                            <button id="btn-search" type="submit" name="submit" class="btn btn-secondary"
                                aria-label="Search">
                                <span class="fas fa-search"></span>
                            </button>
                        </div> <!-- /btn-group -->
                        <!-- Split button -->
                        <div class="dropdown dropdown-add ms-md-2 mb-3">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="fas fa-plus"></span> Add
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