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
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Texts;
use Aprelendo\TextTable;
use Aprelendo\ArchivedTexts;
use Aprelendo\SearchTextsParameters;
use Aprelendo\Pagination;
use Aprelendo\Url;

try {
    // set variables used for pagination
    $page = 1;
    $limit = 10; // number of rows per page
    $adjacents = 2; // adjacent page numbers

    $sort_by = !empty($_GET['o']) ? $_GET['o'] : 0;

    $html = ''; // HTML output to print

    // if the page is loaded because user searched for something, show search results
    // otherwise, show complete texts list

    // initialize pagination variables
    if (isset($_GET['p'])) {
        $page = !empty($_GET['p']) ? $_GET['p'] : 1;
    }

    $search_text = !empty($_GET['s']) ? $_GET['s'] : '';

    // calculate page count for pagination
    if ($show_archived) {
        $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
    } else {
        $texts_table = new Texts($pdo, $user_id, $lang_id);
    }

    $total_rows = $texts_table->countSearchRows($filter_type, $filter_level, $search_text);
    $pagination = new Pagination($total_rows, $page, $limit, $adjacents);
    $offset = $pagination->offset;

    // get search result
    $search_params = new SearchTextsParameters($filter_type, $filter_level, $search_text, $offset, $limit, $sort_by);
    $rows = $texts_table->search($search_params);

    // print table
    if ($rows) { // if there are any results, show them
        $table = new TextTable($rows, $show_archived);
        $html = $table->print($sort_by);

        // print pagination
        $url_query_options = compact("search_text", "sort_by", "filter_type", "filter_level", "show_archived");
        $page_url = new Url('texts', $url_query_options);
        $html .= $pagination->print($page_url);
    } else {
        if (!isset($_GET) || empty($_GET)) {
            if (!isset($_COOKIE['hide_welcome_msg'])) {
                $html = <<<'HTML_MODAL'
                    <div id="alert-box" class="alert alert-success alert-dismissible fade show">
                    <div class="alert-flag fs-5"><i class="bi bi-emoji-laughing-fill"></i>Welcome!</div>
                    <div class="alert-msg">
                    <p>It appears this is your first time using Aprelendo. To get started, follow these simple
                    steps:</p>
                    <ol>
                    <li>
                    <strong>Install our extensions:</strong> download and install our <a href="/extensions#extensions"
                    target="_blank" rel="noopener noreferrer" class="alert-link">extensions</a> for supported
                    browsers like <a href="https://chrome.google.com/webstore/detail/aprelendo/
                    aocicejjgilfkeeklfcomejgphjhjonj/related?hl=en-US" target="_blank" rel="noopener noreferrer"
                    class="alert-link">Chrome</a>, <a href="https://microsoftedge.microsoft.com/addons/detail/
                    aprelendo/ckgnfejigfdfppodkhfmdbockfilcefg" target="_blank" rel="noopener noreferrer"
                    class="alert-link">Edge</a> and <a href="https://addons.mozilla.org/en-US/firefox/addon/aprelendo/"
                    target="_blank" rel="noopener noreferrer" class="alert-link">Firefox</a>. If you're using a
                    different web browser (e.g., Safari, Opera, or Internet Explorer), try our bookmarklet.
                    </li>
                    <li>
                    <strong>Add articles:</strong> visit any website that has articles in the language you want to
                    learn. Make sure it's at your proficiency level or slightly higher. Look for the Aprelendo button in
                    your browser's toolbar (installed by the extension/bookmarklet) and click it to add the text to
                    your Aprelendo library.
                    </li>
                    <li>
                    <strong>Practice and learn:</strong> open the newly added article and follow the provided
                    instructions for each learning phase.
                    </li>
                    </ol>
                    <p>For more info, check our video on <a href="https://www.youtube.com/watch?v=AmRq3tNFu9I"
                    target="_blank" rel="noopener noreferrer" class="alert-link">how to get started</a>.</p>
                    </div>
                    <button id="welcome-close" type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close">
                    </button>
                    </div>
                    HTML_MODAL;
            }

            $html .= <<<'HTML_EMPTY_LIBRARY'
                <div id="alert-box" class="alert alert-danger">
                <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
                <div class="alert-msg">
                <p>Your private library is currently empty.</p>
                <p>To start building it, use the <kbd class="bg-success">Add</kbd> button above, or take advantage of
                our <a href="/extensions" target="_blank" rel="noopener noreferrer" class="alert-link">extensions</a>,
                which allow you to easily add texts as you browse the Web. Learn how to do this by watching
                this <a href="https://www.youtube.com/watch?v=AmRq3tNFu9I" target="_blank"
                rel="noopener noreferrer" class="alert-link">explanatory video</a>.</p>
                </div>
                </div>
                HTML_EMPTY_LIBRARY;
        } else {
            $html = <<<'HTML_SEARCH_RESULT'
            <div id="alert-box" class="alert alert-danger">
            <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
            <div class="alert-msg">
            <p>No texts found with that criteria.</p>
            <p>Consider refining your search using the <kbd class="bg-secondary">Filter</kbd> options on the left.</p>

            <ul><li><strong>Type</strong>: you can narrow down your search by specifying the type of text you're
            interested in, such as Articles, Conversations, Letters, Lyrics, Ebooks, or Others.</li>

            <li><strong>Archived texts</strong>: if you have archived texts, you can choose to include or exclude them
            from your search.</li>
                    
            <li><strong>Level</strong>: filter texts based on their difficulty level (Beginner, Intermediate, or
            Advanced).</li></ul>
        
            <p>Additionally, keep in mind that searches are case insensitive and include partial matches (i.e.
            'cat' can find 'cats').</p>
            <p>With this in mind, feel free to modify your search query and try again.</p>
            </div>
            </div>
            HTML_SEARCH_RESULT;
        }
    }
} catch (\Throwable $e) {
    $html = <<<'HTML_UNEXPECTED_ERROR'
    <div id="alert-box" class="alert alert-danger">
    <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
    <div class="alert-msg">
    <p>There was an unexpected error trying to list the texts in your private library.</p>
    </div>
    </div>
    HTML_UNEXPECTED_ERROR;
} finally {
    echo $html;
}
?>

<script defer src="/js/listtexts.min.js"></script>
<script defer src="/js/helpers.min.js"></script>
<script defer src="/js/cookies.min.js"></script>
