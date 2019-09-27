<?php

require_once '../../includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if user is logged in and set $user object

use Aprelendo\Includes\Classes\Reader;

$html = <<<'CSS'
<style>
.word {
cursor: pointer;
}

.word:hover {
    background-color: #e8e8e8;
}

.highlighted {
    background-color: #e8e8e8 !important;
}

.reviewing {
    border-bottom-width: 2px;
    border-bottom-style: solid;
}

.new {
    border-color: DodgerBlue;
}

.new:hover {
    background-color: DodgerBlue;
}

.learning {
    border-color: orange;
}

.learning:hover {
    background-color: orange;
}

.learned {
    border-bottom-width: 2px;
    border-bottom-style: solid;
    border-color: yellowgreen;
}

.learned:hover {
    background-color: yellowgreen;
}

.forgotten {
    border-color: red;
}

.forgotten:hover {
    background-color: red;
}

.frequency-list {
    border-bottom-width: 1px;
    border-bottom-style: dotted;
    border-color: gray;
}
</style>
CSS;

$reader = new Reader($con, false, 55, 5, 1);

$time_start = microtime(true);
$text = $reader->colorizeWords($reader->text);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$time_str = '<b>ColorizeWords:</b> ' . $execution_time . ' Secs'. '<br>';

echo $html, $text, '<br><br>', $time_str;

// optimized

$time_start = microtime(true);
$text = $reader->colorizeWordsFast($reader->text);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$time_str = '<b>colorizeWordsFast:</b> ' . $execution_time . ' Secs'. '<br>';

echo $text, '<br><br>', $time_str;
