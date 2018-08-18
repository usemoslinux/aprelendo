<?php

// show list of available languages
$result = $con->query("SELECT LgID, LgName FROM languages WHERE LgUserId='$user_id'");

if ($result) {
    echo '<div class="list-group list-group-root well">';
    while ($row = $result->fetch_assoc()) {
        $lg_id = $row['LgID'];  
        $lg_iso_code = $row['LgName'];
        $lgname = ucfirst($user->getLanguageName($lg_iso_code));
        
        $is_active = $lg_id == $user->learning_lang_id ? 'active' : '';
        echo "<a href='#item-$lg_iso_code' class='list-group-item entry-info $is_active' data-toggle='collapse'>" .
              "<i class='fas fa-chevron-right'></i> $lgname</a>";
    
        echo "<div class='list-group collapse' id='item-$lg_iso_code'>
              <div class='list-group-item entry-text'>";
        
        if ($lg_id == $user->learning_lang_id) {
            echo "<button type='button' onclick='location.href=\"languages.php?act=$lg_id\"' class='btn btn-primary disabled'>Set as active</button>
                  <button type='button' onclick='location.href=\"languages.php?chg=$lg_id\"' class='btn btn-default'>Edit</button>
                  <span class='message'></span></div></div>";
        } else {
            echo "<button type='button' onclick='location.href=\"languages.php?act=$lg_id\"' class='btn btn-primary'>Set as active</button>
                  <button type='button' onclick='location.href=\"languages.php?chg=$lg_id\"' class='btn btn-default'>Edit</button>
                  <span class='message'></span></div></div>";
        }
    }
    echo '</div>';
}

?>