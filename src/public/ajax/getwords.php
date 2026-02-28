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
    $sort_by = isset($_GET['o']) ? (int)$_GET['o'] : 0;
    $search_text = !empty($_GET['s']) ? $_GET['s'] : '';

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
        $html = '<div id="alert-box" class="alert alert-info">No words found.</div>';
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
