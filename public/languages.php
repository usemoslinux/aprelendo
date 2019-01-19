<?php
require_once('header.php');

use Aprelendo\Includes\Classes\Language;

$user_id = $user->id;

if (isset($_POST['submit'])) {                  // check if we need to save new language data
    $lang = new Language($con, $_POST['id'], $user_id);
    $lang->edit($_POST, $user->premium_until !== NULL);
} elseif (isset($_GET['chg'])) {        
    $lang = new Language($con, $_GET['chg'], $user_id);
} elseif(isset($_GET['act'])) { 
    $lang = new Language($con, $_GET['act'], $user_id);
} 
?>

    <div class="container mtb">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li>
                        <a href="texts.php">Home</a>
                    </li>
                    <li>
                        <a <?php echo isset($_GET['chg']) ? '' : 'class="active"'; ?> >Languages</a>
                    </li>
                    <?php 
                        if (isset($_GET['chg'])) {
                            echo '<li><a class="active">' . ucfirst(Language::getLanguageName($lang->name)) . '</a></li>';    
                        }
                    ?>
                </ol>

                <?php 

                if (isset($_GET['chg'])) { // chg parameter = show edit language page
                    include('editlanguage.php');
                } elseif(isset($_GET['act'])) { // act parameter = set active language
                    $user->setActiveLang($_GET['act']);
                    include('listlanguages.php');
                } else { // just show list of languages
                    include('listlanguages.php');
                }
                ?>

            </div>
        </div>
    </div>

    <?php require_once('footer.php') ?>