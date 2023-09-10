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
require_once APP_ROOT . 'includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Includes\Classes\Language;

$lang = new Language($pdo, $user->id);
$available_langs = $lang->getAvailableLangs();
        
if ($available_langs) {
    $html = '<div id="accordion" class="accordion">';

    // add long language name to $available_langs array
    $available_langs = array_map(function ($record) {
        $record['long_name'] = ucfirst(Language::getNameFromIso($record['name']));
        return $record;
    }, $available_langs);

    // sort $available_langs by long language name
    $col = array_column($available_langs, 'long_name');
    array_multisort($col, SORT_ASC, $available_langs);

    // create html
    foreach ($available_langs as $lang_record) {
        $lg_id = $lang_record['id'];
        $lg_iso_code = $lang_record['name'];

        $item_id = 'item-' . $lg_iso_code;
        $heading_id = 'heading-' . $lg_iso_code;
        $lgname = $lang_record['long_name'];
        
        $is_active = $lg_id == $user->lang_id;
        $html .= "<div class='accordion-item'>"
            . "<div class='accordion-header' id='$heading_id'>"
            . "<button class='accordion-button "
            . ($is_active ? '' : 'collapsed')
            . "' type='button' data-bs-toggle='collapse' data-bs-target='#$item_id'"
            . "aria-expanded='false' aria-controls='$item_id'>$lgname"
            ."</button>"
            ."</div>";
    
        $html .= "<div id='$item_id' class='accordion-collapse collapse "
            . ($is_active ? 'show' : '')
            . "' aria-labelledby='$lgname' data-bs-parent='#accordion'>"
            . "<div class='accordion-body'>";

        $btn_disabled = $lg_id == $user->lang_id ? 'disabled' : '';
        $html .= "<button type='button' onclick='location.href=\"languages?act=$lg_id\"'"
                . "class='btn btn-primary $btn_disabled'>Set as active</button>"
                . "<button type='button' onclick='location.href=\"languages?chg=$lg_id\"'"
                . " class='btn btn-secondary ms-1'>Edit</button>"
                . "</div></div></div>";
    }
    $html .= '</div>';

    echo $html;
}
