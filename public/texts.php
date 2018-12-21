<?php 

require_once('header.php'); 

$search_text = '';
$filter = -1;
$filter_sql = '';
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
        $filter_sql = $show_archived ? "AND atextType=$filter" : "AND textType=$filter";
    }
}

?>

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="texts.php">Home</a>
                </li>
                <li>
                    <a class="active">My texts</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-xs-12">
                    <form class="form-flex-row" action="" method="get">
                        <input id="f" name="f" value="<?php echo $filter; ?>" type="hidden">
                        <input id="sa" name="sa" value="<?php echo $show_archived ? '1' : '0'; ?>" type="hidden">
                        <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                        <div id="search-wrapper-div" class="input-group searchbox">
                            <div id="filter-wrapper-div" class="input-group-btn">
                                <button type="button" id="btn-filter" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">Filter
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-header">Type</li>
                                    <li onclick="$('#f').val(-1);" <?php echo $filter==-1 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">All</a>
                                    </li>
                                    <li onclick="$('#f').val(1);" <?php echo $filter==1 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Articles</a>
                                    </li>
                                    <li onclick="$('#f').val(2);" <?php echo $filter==2 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Conversations</a>
                                    </li>
                                    <li onclick="$('#f').val(3);" <?php echo $filter==3 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Letters</a>
                                    </li>
                                    <li onclick="$('#f').val(4);" <?php echo $filter==4 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Songs</a>
                                    </li>
                                    <!-- <li onclick="$('#f').val(5);" <?php echo $filter==5 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Videos</a>
                                    </li> -->
                                    <li onclick="$('#f').val(6);" <?php echo $filter==6 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Ebooks</a>
                                    </li>
                                    <li onclick="$('#f').val(7);" <?php echo $filter==7 ? ' class="active" ' : ''; ?> >
                                        <a role="menuitem">Others</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li id="show_archived" onclick="var show_archived = $('#sa'); show_archived.val(1 - show_archived.val());" <?php echo $show_archived==true
                                        ? 'class="active"' : ''; ?> >
                                        <a href="#">Archived</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /btn-group -->
                            <input type="text" id="s" name="s" class="form-control" placeholder="Search..." value="<?php echo isset($search_text) ? $search_text : '' ?>">
                            <div class="input-group-btn">
                                <button id="btn-search" type="submit" name="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Split button -->
                        <div id="add-wrapper-div" class="btn-group btn-searchbox searchbox">
                            <a class="btn btn-success" href="addtext.php">
                                <i class="fas fa-plus"></i> Add</a>
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="addtext.php">Plain text</a>
                                </li>
                                <li>
                                    <a href="addvideo.php">Youtube video</a>
                                </li>
                                <li <?php echo $user->premium_until !== NULL ? '' : 'class="disabled" title="Premium users only"'; ?> >
                                    <a href="addebook.php">Ebook (epub)</a>
                                </li>
                                <li <?php echo $user->premium_until !== NULL ? '' : 'class="disabled" title="Premium users only"'; ?> >
                                    <a href="addrss.php">RSS text</a>
                                </li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
            <?php require_once('listtexts.php') ?>
        </div>
    </div>
</div>

<?php require_once('footer.php') ?>