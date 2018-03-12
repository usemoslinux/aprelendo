<?php

function addParagraphs($text) // Add paragraph elements to text
{
    $text = preg_replace('/\n/', '</p><p>', $text);
    $text = '<p>'.$text.'</p>';

    return $text;
}

function addLinks($text)
{
    $find = array('/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|([-\w’]+)/iu', '/(?:<span[^>]*>.*?<\/span>(*SKIP)(*F))|[^\w<]+/u');
    $replace = array("<span class='word' data-toggle='modal' data-target='#myModal'>$0</span>", "<span>$0</span>");

    return preg_replace($find, $replace, $text);
}

function colorizeWords($text, $con)
{
    // require 'db/dbconnect.php';

    $result = mysqli_query($con, 'SELECT word, wordStatus FROM words WHERE isPhrase=TRUE') or die(mysqli_error($con));
    while ($row = mysqli_fetch_assoc($result)) {
        $phrase = $row['word'];
        $text = preg_replace("/\b".$phrase."\b/ui",
            "<span class='word learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");

    }

    $result = mysqli_query($con, 'SELECT word, wordStatus FROM words WHERE isPhrase=FALSE') or die(mysqli_error($con));
    while ($row = mysqli_fetch_assoc($result)) {
        $word = $row['word'];
        $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b".$word."\b/iu",
            "<span class='word learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");

    }

    return $text;
}

function estimatedReadingTime($text)
{
    $word_count = str_word_count($text);
    $reading_time = $word_count / 200;
    $mins = floor($reading_time);
    $secs = $reading_time - $mins;
    $reading_time = $mins + (($secs < 30) ? 0 : 1);

    return $reading_time;
}
