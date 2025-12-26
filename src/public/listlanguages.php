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

use Aprelendo\Language;
use Aprelendo\SupportedLanguages;

$lang = new Language($pdo, $user->id);
$available_langs = $lang->getAvailableLangs();
        
if ($available_langs) {
    $html = '<div class="list-group">';

    // add long language name to $available_langs array
    $available_langs = array_map(function ($record) {
        $record['long_name'] = ucfirst(SupportedLanguages::get($record['name'], 'name'));
        return $record;
    }, $available_langs);

    // sort $available_langs by long language name
    $col = array_column($available_langs, 'long_name');
    array_multisort($col, SORT_ASC, $available_langs);

    // create html
    foreach ($available_langs as $lang_record) {
        $lg_id = $lang_record['id'];
        $lg_iso_code = $lang_record['name'];

        $lgname = $lang_record['long_name'];
        
        $is_active = $lg_id == $user->lang_id;
        $btn_disabled = $is_active ? 'd-none' : '';
        $active_background = $is_active ? 'active' : '';
        
        $html .= "<div class='list-group-item d-flex justify-content-between align-items-center $active_background'>"
            . "<div class='align-middle'><img class='language-flag me-1' src='/img/flags/$lg_iso_code.svg' "
            . "alt='$lgname flag'><span class='align-middle'>$lgname</span></div><div>"
            . "<button type='button' onclick='location.href=\"languages?act=$lg_id\"' class='btn btn-primary btn-sm $btn_disabled'>Activate</button>"
            . "<button type='button' onclick='location.href=\"languages?chg=$lg_id\"' class='btn btn-secondary btn-sm ms-2'>Edit</button>"
            . "</div></div>";
    
    }
    $html .= '</div>';

    echo $html;
}
