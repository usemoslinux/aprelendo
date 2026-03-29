<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Language;
use Aprelendo\TextTypes;

$search_text    = $_GET['s'] ?? '';
$filter_type    = (int)($_GET['ft'] ?? 0);
$filter_level   = (int)($_GET['fl'] ?? 0);
$sort_by        = (int)($_GET['o'] ?? 0);

if (empty($_GET)) {
    $lang = new Language($pdo, $user->id);
    $lang->loadRecordById($user->lang_id);
    $filter_level = $lang->level;
}

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

            <main>
                <div class="row flex">
                    <div class="col-sm-12">
                        <form id="shared-texts-filter-form" class="form-flex-row" method="get">
                            <div id="search-wrapper-div" class="input-group mb-3">
                                <button type="button" id="btn-filter" class="btn btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter
                                    <span class="caret"></span>
                                </button>
                                <div id="filter-dropdown" class="dropdown-menu">
                                    <h6 class="dropdown-header">Type</h6>
                                    <?php foreach ($text_types_arr as $type): ?>
                                        <a data-value="<?= $type['id'] ?>"
                                            class="dropdown-item ft <?= $filter_type == $type['id'] ? 'active' : '' ?>">
                                            <?= $type['name'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <div role="separator" class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Level</h6>
                                    <?php
                                        $levels = [0 => 'All', 1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];
                                        foreach ($levels as $level => $level_name): ?>
                                            <a data-value="<?= $level ?>"
                                                class="dropdown-item fl <?= $filter_level == $level ? 'active' : '' ?>">
                                                <?= $level_name ?>
                                            </a>
                                        <?php endforeach; ?>
                                </div>
                                <input type="text" id="s" name="s" class="form-control" placeholder="Search..."
                                    aria-label="Search shared text"
                                    value="<?php echo htmlspecialchars($search_text); ?>">
                                <button id="btn-search" type="submit" name="submit" class="btn btn-secondary"
                                    aria-label="Search">
                                    <span class="bi bi-search"></span>
                                </button>
                            </div>
                            
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

                <div id="shared-texts-list-container" class="position-relative">
                    <div id="shared-texts-loader" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="shared-texts-content">
                        <!-- Content loaded via AJAX -->
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php
require_once 'footer.php';
?>

<script defer src="/js/listsharedtexts.js"></script>
<script defer src="/js/likes.js"></script>
<script defer src="/js/helpers.js"></script>
