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

if (empty($_POST)) {
    header('Location:/texts');
    exit;
}

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

$total = !empty($_POST['total']) ? $_POST['total'] : 0;
$created = !empty($_POST['created']) ? $_POST['created'] : 0;
$learning = !empty($_POST['learning']) ? $_POST['learning'] : 0;
$learned = !empty($_POST['learned']) ? $_POST['learned'] : 0;
$forgotten = !empty($_POST['forgotten']) ? $_POST['forgotten'] : 0;
$other = $total - $created - $learning - $learned - $forgotten;

$gems_earned = !empty($_POST['gems_earned']) ? (int)$_POST['gems_earned'] : 0;
$gems_message = '';
$gems_message = ($gems_earned > 0)
    ? ' You earned ' . $gems_earned . ' gems! Keep it up!'
    : $gems_message;
$gems_message = ($gems_earned < 0)
    ? ' You lost ' . abs($gems_earned) . ' gems! Keep trying and you\'ll get better.'
    : $gems_message;

const TWO_DECIMALS = "%.2f%%";

$array_table1 = [
    ['<span class="word reviewing new">New</span>', $created, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($created / $total) * 100)],
    ['<span class="word reviewing learning">Learning</span>', $learning, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($learning / $total) * 100)],
    ['<span class="word reviewing learned">Learned</span>', $learned, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($learned / $total) * 100)],
    ['<span class="word reviewing forgotten">Forgotten</span>', $forgotten, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($forgotten / $total) * 100)],
    ['<span class="word frequency-list">Other</span>', $other, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($other / $total) * 100)],
    ['Total', $total, '100%']
];

$learning_group = $created + $forgotten + $learning;
$learned_group = $learned + $other;

$array_table2 = [
    [
        'Still learning (<span class="word reviewing forgotten">Forgotten</span> &#43; '
            . '<span class="word reviewing new">New</span> &#43; '
            . '<span class="word reviewing learning">Learning</span>)',
        $learning_group, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($learning_group / $total) * 100)
    ],
    [
        'Already learned (<span class="word reviewing learned">Learned</span> &#43; '
            . '<span class="word frequency-list">Other</span>)',
        $learned_group, $total === 0 ? '-' : sprintf(TWO_DECIMALS, ($learned_group / $total) * 100)
    ],
    ['Total', $total, '100%']
];

function printTableRows($array_table_rows)
{
    $html = '';

    $total_rows = count($array_table_rows)-1;
    for ($i=0; $i < $total_rows; $i++) {
        $html .= "<tr>";

        $total_cols = count($array_table_rows[$i]);
        for ($j=0; $j < $total_cols; $j++) {
            $html .= $j == 0 ? '<td>' : '<td class="text-center">';
            $html .= $array_table_rows[$i][$j] . '</td>';
        }

        $html .= '</tr>';
    }
    
    return $html;
}

function printTableFooter($array_table_rows)
{
    $html = "<tr>";

    $last_row = count($array_table_rows)-1;
    $total_cols = count($array_table_rows[$last_row]);

    for ($i=0; $i < $total_cols; $i++) {
        $html .= $i == 0 ? '<th>' : '<th class="text-center">';
        $html .= $array_table_rows[$last_row][$i] . '</th>';
    }

    $html .= '</tr>';
    
    return $html;

}

?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Review Statistics</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <main>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-success float-end mb-3" type="button"
                    onclick="window.location.replace('/texts');">
                    <span class="bi bi-skip-start-circle-fill"></span> Go back to
                    your library</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Congrats! You've finished reviewing this text.
                </div>
            </div>
        </div>
        <?php if ($gems_earned != 0): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert <?php echo ($gems_earned > 0) ? 'alert-warning' : 'alert-danger'  ?>" role="alert">
                    <img src="/img/gamification/gems.webp" alt="Gems" title="Gems earned"
                        style="width: 1rem;height: 1rem;">
                    <?php echo $gems_message; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <table class="table table-borderless" aria-label="Statistics by word/phrase category">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%">Category</th>
                            <th class="text-center" style="width: 25%">Words/Phrases</th>
                            <th class="text-center" style="width: 25%">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo printTableRows($array_table1); ?>
                    </tbody>
                    <tfoot>
                        <?php echo printTableFooter($array_table1); ?>
                    </tfoot>
                </table>

                <table class="table table-borderless" aria-label="Statistics summary">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%">Group</th>
                            <th class="text-center" style="width: 25%">Words/Phrases</th>
                            <th class="text-center" style="width: 25%">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo printTableRows($array_table2); ?>
                    </tbody>
                    <tfoot>
                        <?php echo printTableFooter($array_table2); ?>
                    </tfoot>
                </table>
            </div>
            <div class="col-12">
                <p><strong class="word reviewing new">New</strong>: words at the start of a learning cycle, either newly
                    added or reintroduced after being forgotten.</p>
                <p><strong class="word reviewing learning">Learning</strong>: words you are actively practicing, which
                    you have already reviewed at least once but haven't mastered yet.</p>
                <p><strong class="word learned">Learned</strong>: words you consistently recall correctly and are
                    considered mastered.</p>
                <p><strong class="word reviewing forgotten">Forgotten</strong>: words you previously learned or
                    practiced but recently failed to recall, and which will restart the learning cycle.</p>
                <p><strong class="word frequency-list">Other</strong>: words you haven't marked for learning and that
                    you appear to understand without active practice.</p>
                <p><small>Note: if a word appears multiple times in the text, each occurrence is counted.</small></p>
            </div>

        </div>
    </main>
</div>

<?php require_once 'footer.php'; ?>