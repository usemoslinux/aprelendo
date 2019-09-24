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

if (!isset($_POST) || empty($_POST)) {
    header('Location:texts.php');
    exit;
}

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

$total = isset($_POST['total']) && !empty($_POST['total']) ? $_POST['total'] : 0;
$created = isset($_POST['created']) && !empty($_POST['created']) ? $_POST['created'] : 0;
$reviewed = isset($_POST['reviewed']) && !empty($_POST['reviewed']) ? $_POST['reviewed'] : 0;
$learned = isset($_POST['learned']) && !empty($_POST['learned']) ? $_POST['learned'] : 0;
$forgotten = isset($_POST['forgotten']) && !empty($_POST['forgotten']) ? $_POST['forgotten'] : 0;
$other = $total - $created - $reviewed - $learned - $forgotten;

$array_table1 = array(
                    array('New', $created, $total === 0 ? '-' : sprintf("%.2f%%", ($created / $total) * 100)),
                    array('Reviewed', $reviewed, $total === 0 ? '-' : sprintf("%.2f%%", ($reviewed / $total) * 100)),
                    array('Learned', $learned, $total === 0 ? '-' : sprintf("%.2f%%", ($learned / $total) * 100)),
                    array('Forgotten', $forgotten, $total === 0 ? '-' : sprintf("%.2f%%", ($forgotten / $total) * 100)),
                    array('Other', $other, $total === 0 ? '-' : sprintf("%.2f%%", ($other / $total) * 100)),
                    array('Total', $total, '100%')
                );

$learning_group = $created + $forgotten + $reviewed;
$learned_group = $learned + $other;

$array_table2 = array(
                    array('Learning (new &#43; forgotten &#43; reviewed)', $learning_group, $total === 0 ? '-' : sprintf("%.2f%%", ($learning_group / $total) * 100)),
                    array('Already learned (learned &#43; other)', $learned_group, $total === 0 ? '-' : sprintf("%.2f%%", ($learned_group / $total) * 100)),
                    array('Total', $total, '100%')
                );

function print_table_rows($array_table_rows) {
    $html = '';

    for ($i=0; $i < count($array_table_rows)-1; $i++) { 
        $html .= "<tr>";

        for ($j=0; $j < count($array_table_rows[$i]); $j++) { 
            $html .= $j == 0 ? '<td>' : '<td class="text-center">';
            $html .= $array_table_rows[$i][$j] . '</td>';
        }

        $html .= '</tr>';
    }
    
    return $html;
}

function print_table_footer($array_table_rows) {
    $html = "<tr>";

    $last_row = count($array_table_rows)-1;

    for ($i=0; $i < count($array_table_rows[$last_row]) ; $i++) { 
        $html .= $i == 0 ? '<th>' : '<th class="text-center">';
        $html .= $array_table_rows[$last_row][$i] . '</th>';
    }

    $html .= '</tr>';
    
    return $html;

}

?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Review Statistics</a>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-success float-right mb-3" type="button"
                onclick="window.location.replace('texts.php');"><i class="fas fa-chevron-circle-left"></i> Go back to
                your library</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Congrats! You've finished reviewing this text. It will now be marked as "archived".
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50%">Category</th>
                            <th class="text-center" style="width: 25%">Words/Phrases</th>
                            <th class="text-center" style="width: 25%">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo print_table_rows($array_table1); ?>
                    </tbody>
                    <tfoot>
                        <?php echo print_table_footer($array_table1); ?>
                    </tfoot>
                </table>
            </div>

            <table class="table table-light">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50%">Group</th>
                        <th class="text-center" style="width: 25%">Words/Phrases</th>
                        <th class="text-center" style="width: 25%">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo print_table_rows($array_table2); ?>
                </tbody>
                <tfoot>
                    <?php echo print_table_footer($array_table2); ?>
                </tfoot>
            </table>
        </div>
        <div class="col-12">
            <p><strong class="word reviewing new">New</strong>: words you've just added to your learning list.</p>
            <p><strong class="word reviewing learning">Reviewed</strong>: words that you already reviewed at least once,
                but still need to review more times.</p>
            <p><strong class="word learned">Learned</strong>: words that the system thinks you have already reviewed
                enough times.</p>
            <p><strong class="word reviewing forgotten">Forgotten</strong>: words you reviewed or learned in the past
                and you marked for learning once again.</p>
            <p><strong class="word frequency-list">Other</strong>: words that you never marked for learning and you seem
                to understand well.</p>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>