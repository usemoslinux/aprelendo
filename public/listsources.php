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
    echo "<div class='text-center'>Hmm, that's weird. We couldn't find any popular sources for the selected language.</div>";
}

function printSources($sources) {
    if (!isset($sources) || empty($sources)) {
        echo "<div class='simple-text'>Hmm, that's weird. We couldn't find any popular sources for the selected language.</div>";    
    }

    $html = '<div class="alert alert-info">These are the most popular sources for the currently selected language. They are probably a good starting place to find new content to practice. Remember to use our <a href="/extensions" class="alert-link" target="_blank" rel="noopener noreferrer">extensions</a> to add articles from these or other sources to your Aprelendo library.</div>'; 

    $html .= '<div id="list-group-popular-sources" class="list-group">';

    foreach ($sources as $source) {
        $html .= 
        "<a href='//{$source['domain']}' target='_blank'  rel='noopener noreferrer' class='list-group-item d-flex justify-content-between align-items-center list-group-item-action'>
            {$source['domain']}
            <span class='badge badge-secondary badge-pill ml-2'>{$source['times_used']}</span> 
        </a>";
    }

    $html .= '</div>';

    return $html;
}


?>