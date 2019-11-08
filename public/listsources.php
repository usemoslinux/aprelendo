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
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\PopularSources;

try {
    $pop_sources = new PopularSources($pdo);
    $sources = $pop_sources->getAllByLang($user->getLang());
    echo printSources($sources);
} catch (\Exception $e) {
    //throw $e;
}

function printSources($sources) {
    if (!isset($sources) || empty($sources)) {
        echo "<div class='simple-text'>Hmm, that's weird. We couldn't find any texts in the selected language.</div>";    
    }

    $html = '<div class="list-group">';

    foreach ($sources as $source) {
        $html .= 
        "<a href='//{$source['domain']}' target='_blank' class='list-group-item d-flex justify-content-between align-items-center list-group-item-action'>
            {$source['domain']}
            <span class='badge badge-secondary badge-pill'>{$source['times_used']}</span> 
        </a>";
    }

    $html .= '</div>';

    return $html;
}


?>