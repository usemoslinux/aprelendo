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

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

use Aprelendo\Words;
use Aprelendo\WordTable;
use Aprelendo\SearchWordsParameters;
use Aprelendo\Pagination;
use Aprelendo\Url;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    // set variables used for pagination
    $page = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $limit = 25; // number of rows per page
    $adjacents = 2; // adjacent page numbers
    $sort_by = (int)($_GET['o'] ?? 0);
    $search_text = !empty($_GET['s']) ? $_GET['s'] : '';
    $init_page = empty($search_text);

    $words_table = new Words($pdo, $user_id, $lang_id);
    $total_rows = $words_table->countSearchRows($search_text);
    $pagination = new Pagination($total_rows, $page, $limit, $adjacents);
    $offset = $pagination->offset;

    // get search result
    $search_params = new SearchWordsParameters($search_text, $sort_by, $offset, $limit);
    $rows = $words_table->search($search_params);

    $html = '';
    if ($rows) {
        $table = new WordTable($rows);
        $html = $table->print($sort_by);

        // pagination html
        $url_query_options = compact("search_text", "sort_by");
        $page_url = new Url('words', $url_query_options);
        $html .= $pagination->print($page_url);
    } else {
        if ($init_page) {
            $html = <<<HTML_EMPTY_LIST
            <div id="alert-box" class="alert alert-warning">
                <div class="alert-flag fs-5"><i class="bi bi-stars"></i> Get Started</div>
                <div class="alert-msg">
                    <p>Your word list for the currently active language is looking a bit lonely.</p>
                    <p>But don't worry, it's super easy to add new words to your learning journey while you read texts
                        using Aprelendo. Just check out this <a href="https://www.youtube.com/watch?v=AmRq3tNFu9I"
                        target="_blank" rel="noopener noreferrer" class="alert-link">helpful video</a> for a quick
                        guide!
                    </p>
                </div>
            </div>
            HTML_EMPTY_LIST;            
        } else {
            $html = <<<HTML_SEARCH_RESULT
            <div id="alert-box" class="alert alert-danger">
                <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
                <div class="alert-msg">
                    <p>No words with that search criteria were found for the active language.</p>
                    <p>Please, take a moment to fine-tune your search to improve your results. Keep in mind that
                        searches are case-insensitive and include partial matches (i.e. 'cat' can find 'Cats').
                    </p>
                    <p>With this in mind, feel free to modify your search query and try again.</p>
                </div>
            </div>
            HTML_SEARCH_RESULT;
        }
    }

    $response = [
        'success' => true,
        'payload' => [
            'html' => $html,
            'total_rows' => $total_rows
        ]
    ];
    echo json_encode($response);
} catch (Throwable $e) {
    $response['error_msg'] = $e->getMessage();
    echo json_encode($response);
}
