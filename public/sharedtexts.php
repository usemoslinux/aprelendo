<?php 

require_once('header.php'); 

$search_text = '';
$filter = -1;
$sort_by = 0;

if (!empty($_GET)) {
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $filter = isset($_GET['f']) ? $_GET['f'] : -1;  
    $sort_by = isset($_GET['o']) ? $_GET['o'] : 0;  
}

// set filter sql

$filter_sql = !empty($filter) && $filter > -1 ? "AND stextType=$filter" : '';

?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Shared texts</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-sm-12">
                    <form class="form-flex-row" action="" method="get">
                        <input id="f" name="f" value="<?php echo $filter; ?>" type="hidden">
                        <input id="o" name="o" value="<?php echo $sort_by; ?>" type="hidden">
                        <div id="search-wrapper-div" class="input-group searchbox">
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
                                    <a onclick="$('#f').val(5);" <?php echo $filter==5 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Videos
                                    </a>
                                    <a onclick="$('#f').val(6);" <?php echo $filter==6 ?
                                        ' class="dropdown-item active" ' : 'class="dropdown-item"' ; ?> >
                                        Others
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
                        <div class="btn-group btn-searchbox searchbox">
                            <a class="btn btn-success" href="addtext.php">
                                <i class="fas fa-plus"></i> Add</a>
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="addtext.php?sh">Plain text</a>
                                <a class="dropdown-item" href="addvideo.php">Youtube video</a>
                                <a href="addrss.php" <?php echo $user->isPremium() ? 
                                'class="dropdown-item"' : 'class="dropdown-item disabled" title="Premium users only"'; ?> >
                                    RSS text
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require_once('listsharedtexts.php') ?>
        </div>
    </div>
</div>

<?php require_once('footer.php') ?>