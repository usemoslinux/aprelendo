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

if (isset($_GET['act'])) {
    $user->setActiveLang($_GET['act']);
}

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Language;

$user_id = $user->id;
$lang = new Language($pdo, $user_id);

if (isset($_GET['chg'])) {
    $lang->loadRecordById($_GET['chg']);
} elseif (isset($_GET['act'])) {
    $lang->loadRecordById($_GET['act']);
}

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
                                . ucfirst(Language::getNameFromIso($lang->name)) . '</span></li>';
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
    <script defer src="/js/helpers.min.js"></script>

    <?php require_once 'footer.php'; ?>
