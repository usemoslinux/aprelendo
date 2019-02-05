<?php

use Aprelendo\Includes\Classes\Language;

// show list of available languages
$result = $con->query("SELECT LgID, LgName FROM languages WHERE LgUserId='$user_id'");

if ($result) {
    $html = '<div id="accordion" class="accordion">';

    while ($row = $result->fetch_assoc()) {
        $lg_id = $row['LgID'];  
        $lg_iso_code = $row['LgName'];

        $item_id = 'item-' . $lg_iso_code;
        $heading_id = 'heading-' . $lg_iso_code;
        $lgname = ucfirst(Language::getLanguageName($lg_iso_code));
        
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