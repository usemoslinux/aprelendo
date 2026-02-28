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

use Aprelendo\Texts;
use Aprelendo\TextTable;
use Aprelendo\ArchivedTexts;
use Aprelendo\SearchTextsParameters;
use Aprelendo\Pagination;
use Aprelendo\Url;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    // set variables used for pagination
    $page = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $limit = 10; // number of rows per page
    $adjacents = 2; // adjacent page numbers
    
    $sort_by        = isset($_GET['o'])  ? (int)$_GET['o'] : 0;
    $filter_type    = isset($_GET['ft']) ? (int)$_GET['ft'] : 0;
    $filter_level   = isset($_GET['fl']) ? (int)$_GET['fl'] : 0;
    $search_text    = !empty($_GET['s']) ? $_GET['s'] : '';
    $show_archived  = isset($_GET['sa']) && $_GET['sa'] == '1';

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
    
    $html = '';
    if ($rows) {
        $table = new TextTable($rows, $show_archived);
        $html = $table->print($sort_by);
        
        // pagination
        $url_query_options = compact("search_text", "sort_by", "filter_type", "filter_level", "show_archived");
        $page_url = new Url('texts', $url_query_options);
        $html .= $pagination->print($page_url);
    } else {
        $html = '<div id="alert-box" class="alert alert-info">No texts found.</div>';
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
