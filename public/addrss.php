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

use Aprelendo\Includes\Classes\User;

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
?>

    <div class="container mtb d-flex flex-grow-1 flex-column">
        <div class="row">
            <div class="col-12">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/texts">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active">Add RSS article</span>
                        </li>
                    </ol>
                </nav>
                <div class="alert alert-info">
                    <span class="fas fa-info-circle"></span>
                    &nbsp;All RSS texts you add to Aprelendo will be shared with the rest of our community.
                    You will find them in the "<a class="alert-link" href="/sharedtexts">shared texts</a>" section.
                </div>
            </div>
            <div class="col-12">
                <div class="row flex">
                    <div class="col-sm-12">
                        <main>
                            <div class="lds-ellipsis text-center mx-auto">
                                <div></div><div></div><div></div><div></div>
                                <small>loading...</small>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="js/addrss-min.js"></script>
    <?php require_once 'footer.php'; ?>
