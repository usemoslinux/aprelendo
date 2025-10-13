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

use Aprelendo\PopularSources;

try {
    $pop_sources = new PopularSources($pdo);
    $sources = $pop_sources->getAllByLang($user->lang);
    echo printSources($sources);
} catch (\Exception $e) {
    $html = <<<'HTML_UNEXPECTED_ERROR'
    <div id="alert-box" class="alert alert-danger">
        <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
        <div class="alert-msg">
            <p>There was an unexpected error trying to show sources for this language.</p>
        </div>
    </div>
    HTML_UNEXPECTED_ERROR;
    echo $html;
}

function printSources($sources)
{
    $html = <<<HTML_INFO_SOURCES
        <div class="alert alert-info">These are the most popular sources for the currently selected language. They
            are probably a good starting place to find new content to practice. Remember to use our <a
            href="/extensions" class="alert-link" target="_blank" rel="noopener noreferrer">extensions</a> to add
            articles from these or other sources to your Aprelendo library.
        </div>
    HTML_INFO_SOURCES;

    if (!isset($sources) || empty($sources)) {
        $html .= <<<HTML_EMPTY_LIBRARY
            <div id="alert-box" class="alert alert-warning">
                <div class="alert-flag fs-5">
                    <i class="bi bi-stars"></i> Nothing here… yet!
                </div>
                <div class="alert-msg">
                    <p>Hmm, that's unusual. We couldn't find any popular sources for the selected language.</p>
                    <p>
                        This probably means that there are no <a href="/sharedtexts" class="alert-link">shared texts</a>
                        available yet for this language. Try exploring another language for now, or be the first to
                        share something and help grow the collection!
                    </p>
                </div>
            </div>
        HTML_EMPTY_LIBRARY;
        return $html;
    }

    $html .= '<div id="list-group-popular-sources" class="list-group">';

    foreach ($sources as $source) {
        $html .=
        "<a href='//{$source['domain']}' target='_blank'  rel='noopener noreferrer'
            class='list-group-item d-flex justify-content-between align-items-center list-group-item-action'>
            {$source['domain']}
            <span class='badge bg-secondary badge-pill ms-2'>{$source['times_used']}</span>
        </a>";
    }

    $html .= '</div>';

    return $html;
}
