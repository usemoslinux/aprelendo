<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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

use Aprelendo\Includes\Classes\Language;

$lang = new Language($con, $user->id);
$available_langs = $lang->getAvailableLangs();
        
if ($available_langs) {
    $html = '<div id="accordion" class="accordion">';

    foreach ($available_langs as $lang_record) {
        $lg_id = $lang_record['id'];  
        $lg_iso_code = $lang_record['name'];

        $item_id = 'item-' . $lg_iso_code;
        $heading_id = 'heading-' . $lg_iso_code;
        $lgname = ucfirst(Language::getNameFromIso($lg_iso_code));
        
        $is_active = $lg_id == $user->learning_lang_id ? 'bg-primary text-white' : '';
        $html .= "<div class='card'>
                    <div class='card-header $is_active' id='$heading_id'>
                        <button class='btn btn-link collapsed' data-toggle='collapse' data-target='#$item_id' aria-expanded='false' aria-controls='$item_id'>
                            <i class='fas fa-chevron-right'></i>
                            $lgname
                        </button>
                    </div>";
    
        $html .= "<div id='$item_id' class='collapse' aria-labelledby='$lgname' data-parent='#accordion'>
                    <div class='card-body'>";

        if ($lg_id == $user->learning_lang_id) {
            $html .= "<button type='button' onclick='location.href=\"languages.php?act=$lg_id\"' class='btn btn-primary disabled'>Set as active</button>
                      <button type='button' onclick='location.href=\"languages.php?chg=$lg_id\"' class='btn btn-secondary'>Edit</button>
                         <span class='message'></span>
                      </div></div></div>";
        } else {
            $html .= "<button type='button' onclick='location.href=\"languages.php?act=$lg_id\"' class='btn btn-primary'>Set as active</button>
                      <button type='button' onclick='location.href=\"languages.php?chg=$lg_id\"' class='btn btn-secondary'>Edit</button>
                         <span class='message'></span>
                      </div></div></div>";
        }
    }
    $html .= '</div>';

    echo $html;
}

?>