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
        $btn_add_html = <<<HTML_BTN_ADD
            <kbd class="badge bg-success"
                onclick="window.scrollTo({top:0,behavior:'smooth'});
                setTimeout(()=>{document.getElementById('btn-add-text').click();},400)">
                + Add
            </kbd>
        HTML_BTN_ADD;

        $btn_filter_html = <<<HTML_BTN_FILTER
            <kbd class="bg-secondary"
                onclick="window.scrollTo({top:0,behavior:'smooth'});
                setTimeout(()=>{document.getElementById('btn-filter').click();},400)">
                Filter
            </kbd>
        HTML_BTN_FILTER;

        $user_menu_html = <<<HTML_USER_MENU
            <kbd class="badge bg-primary"
                onclick="window.scrollTo({top:0,behavior:'smooth'});
                setTimeout(()=>{const t=document.querySelector('.navbar-toggler');
                if(t&&window.getComputedStyle(t).display!=='none')t.click();
                document.getElementById('user-menu').click();},400)">
                <i class="bi bi-person-circle"></i> menu
            </kbd>
        HTML_USER_MENU;

        $langs_menu_html = <<<HTML_LANGS_MENU
            <kbd class='badge bg-primary'
                onclick="window.scrollTo({top:0,behavior:'smooth'});
                setTimeout(()=>{const t=document.querySelector('.navbar-toggler');
                if(t&&window.getComputedStyle(t).display!=='none')t.click();
                document.getElementById('language-menu').click();},400)">
                <i class='bi bi-globe'></i> menu
            </kbd>
        HTML_LANGS_MENU;

        if (!isset($_GET) || empty($_GET)) {
            if (!isset($_COOKIE['hide_welcome_msg'])) {

                $html = <<<HTML_WELCOME_MSG
                    <div id="alert-box" class="alert alert-info alert-dismissible fade show">
                        <div class="alert-flag fs-5">
                            <i class="bi bi-lightbulb-fill"></i> Welcome!
                        </div>
                        <div class="alert-msg">
                            <p>
                                <strong>Aprelendo</strong> helps you learn languages naturally through real content. 
                                Instead of memorising lists, you explore stories, videos, and articles, absorbing vocabulary through context and use.
                            </p>
                            <p>
                                This method takes curiosity and a bit of patience, especially at the beginning. You will find and upload the content that interests you, then read and listen to it deeply. It may feel challenging at first, yet it builds lasting understanding and real confidence.
                            </p>
                            <p>
                                To begin, follow the steps in the yellow box below. Once you add your first text, visit your 
                                $user_menu_html to access your <a href="/words" class="alert-link">Word list</a>, 
                                <a href="/study" class="alert-link">Study</a> sessions, and 
                                <a href="/stats" class="alert-link">Statistics</a>. You can also open 
                                <a href="/preferences" class="alert-link">Preferences</a> or 
                                <a href="/userprofile" class="alert-link">My Profile</a> to tailor your experience and enable
                                <a href="https://blog.aprelendo.com/2024/11/boost-your-vocabulary-with-aprelendos-proven-ai-language-tool/"
                                target="_blank" rel="noopener noreferrer" class="alert-link">Lingobot</a>, our AI assistant.
                            </p>
                            <p>
                                You can switch languages and choose dictionaries and translators anytime from the 
                                $langs_menu_html.
                            </p>
                            <p>
                                Prefer a quick tour? Watch our 
                                <a href="https://www.youtube.com/watch?v=AmRq3tNFu9I"
                                target="_blank" rel="noopener noreferrer" class="alert-link">intro video</a>.
                            </p>
                        </div>
                        <button id="welcome-close" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    HTML_WELCOME_MSG;
            }
            
            $html .= <<<HTML_EMPTY_LIBRARY
            <div id="alert-box" class="alert alert-warning">
                <div class="alert-flag fs-5">
                    <i class="bi bi-stars"></i> Get Started
                </div>
                <div class="alert-msg">
                    <p>Your private library is waiting for its first entry.</p>
                    <p>Click $btn_add_html to add an ebook, video or text, or try our  <a href="/extensions"
                    target="_blank" rel="noopener noreferrer" class="alert-link">browser extensions</a> to capture
                    content as you explore the Web.
                    </p>
                </div>
            </div>
            HTML_EMPTY_LIBRARY;

        } else {
            $html = <<<HTML_SEARCH_RESULT
            <div id="alert-box" class="alert alert-danger">
                <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>No matches found</div>
                <div class="alert-msg">
                    <p>Consider refining your search using the $btn_filter_html options on the left.</p>
                    <ul>
                        <li><strong>Type</strong>: you can narrow down your search by specifying the type of text you're
                            interested in, such as Articles, Conversations, Letters, Lyrics, Ebooks, or Others.</li>
                        <li><strong>Archived texts</strong>: if you have archived texts, you can choose to include or 
                        exclude them from your search.</li>
                        <li><strong>Level</strong>: filter texts based on their difficulty level (Beginner, 
                        Intermediate, or Advanced).</li>
                    </ul>
                    <p>Additionally, keep in mind that searches are case insensitive and include partial matches (i.e.
                        'cat' can find 'Cats').</p>
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