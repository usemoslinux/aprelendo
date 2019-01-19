<div class="row flex">
<div class="col-xs-12">
<?php 
require_once('../includes/dbinit.php'); // connect to database
require_once(APP_ROOT . 'includes/checklogin.php'); // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\PopularSources;

$pop_sources = new PopularSources($con);
$sources = $pop_sources->getAllByLang($user->learning_lang);

echo printSources($sources);

function printSources($sources) {

    if (!isset($sources) || empty($sources)) {
        echo "<div class='simple-text'>Hmm, that's weird. We couldn't find any texts in the selected language.</div>";    
    }

    $html = '<div class="list-group">';

    foreach ($sources as $source) {
        $html .= 
        "<a href='//{$source['popsources_domain']}' target='_blank' class='list-group-item'>
            <span class='badge'>{$source['popsources_times_used']}</span> 
            {$source['popsources_domain']}
        </a>";
    }

    $html .= '</div>';

    return $html;
}


?>
</div>
</div>