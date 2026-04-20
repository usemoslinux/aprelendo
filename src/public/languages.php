<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\UserException;
use Aprelendo\Language;
use Aprelendo\SupportedLanguages;

if (isset($_GET['act'])) {
    try {
        $user->setActiveLang((int)$_GET['act']);
    } catch (UserException $e) {
        http_response_code(403);
        exit;
    }
}

$user_id = $user->id;
$lang = new Language($pdo, $user_id);

if (isset($_GET['chg'])) {
    if (!$lang->loadRecordById((int)$_GET['chg'])) {
        http_response_code(403);
        exit;
    }
} elseif (isset($_GET['act'])) {
    $lang->loadRecordById($user->lang_id);
}

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

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
                            <span <?php echo isset($_GET['chg']) ? '' : 'class="active"'; ?> >Languages</span>
                        </li>
                        <?php
                            if (isset($_GET['chg'])) {
                                echo '<li class="breadcrumb-item"><span class="active">'
                                . ucfirst(SupportedLanguages::get($lang->name, 'name')) . '</span></li>';
                            }
                        ?>
                    </ol>
                </nav>

                <main>
                <?php

                if (isset($_GET['chg'])) { // chg parameter = show edit language page
                    include_once 'editlanguage.php';
                } elseif (isset($_GET['act'])) { // act parameter = set active language
                    include_once 'listlanguages.php';
                } else { // just show list of languages
                    include_once 'listlanguages.php';
                }
                ?>
                </main>
            </div>
        </div>
    </div>

    <script defer src="/js/languages.js"></script>
    <script defer src="/js/helpers.js"></script>

    <?php require_once 'footer.php'; ?>
