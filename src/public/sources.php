<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;

$user = AuthGuard::requirePageUser();

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
                            <span class="active">Popular sources</span>
                        </li>
                    </ol>
                </nav>
                <main>
                    <div class="row flex">
                        <div class="col-sm-12">
                            <?php require_once 'listsources.php'; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
