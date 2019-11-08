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
$filter = -1;
$search_filter = '';
$show_archived = false;
$sort_by = 0;

if (!empty($_GET)) {
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $filter = isset($_GET['f']) ? $_GET['f'] : -1;  
    $show_archived = isset($_GET['sa']) ? $_GET['sa'] == '1' : '0';
    $sort_by = isset($_GET['o']) ? $_GET['o'] : 0;  
}

// set filter sql
if (!empty($filter)) {
    if ($filter == 10) {
        $show_archived = true;
    } elseif ($filter > -1) {
        $search_filter = $filter;
    }
}

?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">My texts</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-sm-12">
                    <form class="form-flex-row" method="get">
                        <input id="f" name="f" value="<?php echo $filter; ?>" type="hidden">
                        <input id="sa" name="sa" value="<?php echo $show_archived ? '1' : '0'; ?>" type="hidden">
                        <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                        <div id="search-wrapper-div" class="input-group my-2">
                            <div id="filter-wrapper-div" class="input-group-prepend">
                                <button type="button" id="btn-filter" class="btn btn-secondary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <a onclick="$('#f').val(-1);" <?php echo $filter==-1 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        All
                                    </a>
                                    <a onclick="$('#f').val(1);" <?php echo $filter==1 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Articles
                                    </a>
                                    <a onclick="$('#f').val(2);" <?php echo $filter==2 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Conversations
                                    </a>
                                    <a onclick="$('#f').val(3);" <?php echo $filter==3 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Letters
                                    </a>
                                    <a onclick="$('#f').val(4);" <?php echo $filter==4 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Lyrics
                                    </a>
                                    <!-- <a onclick="$('#f').val(5);" <?php echo $filter==5 ? ' class="dropdown-item active" ' : 'class="dropdown-item"'; ?> >
                                        Videos
                                    </a> -->
                                    <a onclick="$('#f').val(6);" <?php echo $filter==6 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Ebooks
                                    </a>
                                    <a onclick="$('#f').val(7);" <?php echo $filter==7 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Others
                                    </a>
                                    <div role="separator" class="dropdown-divider"></div>
                                    <a id="show_archived" onclick="var show_archived = $('#sa'); show_archived.val(1 - show_archived.val());"
                                        <?php echo $show_archived==true ? 'class="dropdown-item active"' :
                                        'class="dropdown-item"' ; ?> >
                                        Archived
                                    </a>
                                </div>
                            </div>
                            <!-- /btn-group -->
                            <input type="text" id="s" name="s" class="form-control" placeholder="Search..." value="<?php echo isset($search_text) ? $search_text : '' ?>">
                            <div class="input-group-append">
                                <button id="btn-search" type="submit" name="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Split button -->
                        <div class="dropdown dropdown-add ml-md-2 my-2">
                            <button type="button" class="btn btn-success dropdown-btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-plus"></i> Add
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="addtext.php">Plain text</a>
                                <a class="dropdown-item" href="addvideo.php">Youtube video</a>
                                <a href="addebook.php" <?php echo $user->isPremium() ? 'class="dropdown-item"' : 'class="dropdown-item disabled" title="Premium users only"';
                                    ?> >
                                    Ebook (epub)
                                </a>
                                <a href="addrss.php" <?php echo $user->isPremium() ? 'class="dropdown-item"' : 'class="dropdown-item disabled" title="Premium users only"';
                                    ?> >
                                    RSS text
                                </a>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
            <?php require_once 'listtexts.php'; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>