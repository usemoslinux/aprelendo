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

use Aprelendo\Language;

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

$lang = new Language($pdo, $user->id);
$lang->loadRecordById($user->lang_id);

$feed_uris = [ $lang->rss_feed1_uri,
               $lang->rss_feed2_uri,
               $lang->rss_feed3_uri ] ;

$feeds_count = sizeof($feed_uris);

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
                All RSS texts you add to Aprelendo will be shared with the rest of our community.
                You will find them in the <a class="alert-link" href="/sharedtexts">shared texts</a> section.
            </div>
            <div id="alert-box" class="d-none"></div>
        </div>
        <div class="col-12">
            <div class="row flex">
                <div class="col-sm-12">
                    <main>
                        <div id="accordion" class="accordion">
                            <?php

                            $html = '';
                            for ($feed_index=0; $feed_index < $feeds_count; $feed_index++) {
                                $feed_id = 'feed-' . $feed_index + 1;
                                $item_id = '#item-' . $feed_index + 1;
                                if (!empty($feed_uris[$feed_index])) {
                                    $html .= "<div class='accordion-item' id='$feed_id' data-feed-index='$feed_index'>
                                        <h2 class='accordion-header'>
                                            <button class='accordion-button rss-placeholder-glow collapsed' data-bs-toggle='collapse' data-bs-target='$item_id' aria-expanded='true' aria-controls='$item_id' type='button'>
                                                <span class='rss-placeholder-text'>Loading...</span>
                                            </button>
                                        </h2>
                                    </div>";
                                }
                            }

                            echo $html;
                            ?>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/addrss.min.js"></script>
<script defer src="/js/helpers.min.js"></script>
<?php require_once 'footer.php'; ?>