<?php

/**
 * Replaces line-breaks (\n) with <P></P> tags
 * Returns the modified $text, which includes the new HTML code
 *
 * @param string $text
 * @return string
 */
function addParagraphs($text) // Add paragraph elements to text
{
  $text = preg_replace('/\n/', '</p><p>', $text);
  $text = '<p>'.$text.'</p>';

  return $text;
}

/**
 * Makes all words clickable by wrapping them in SPAN tags
 * Returns the modified $text, which includes the new HTML code
 * 
 * @param string $text
 * @return string
 */
function addLinks($text)
{
  $find = array('/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|([-\w’]+)/iu', '/(?:<span[^>]*>.*?<\/span>(*SKIP)(*F))|[^\w<]+/u');
  $replace = array("<span class='word' data-toggle='modal' data-target='#myModal'>$0</span>", "<span>$0</span>");

  return preg_replace($find, $replace, $text);
}

/**
 * Underlines words with different colors depending on their status
 * Returns the modified $text, which includes the new HTML code
 *
 * @param string $text
 * @param mysqli $con
 * @return string
 */
function colorizeWords($text, $con, $freq_list)
{
  // 1. colorize phrases & words that are being reviewed

  // colorize phrases & words
  $result = mysqli_query($con, 'SELECT word FROM words WHERE wordStatus>0') or die(mysqli_error($con));
  while ($row = mysqli_fetch_assoc($result)) {
    $word = $row['word'];
    $text = preg_replace("/\b".$word."\b/ui",
    "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
  }

  // // colorize words
  // $result = mysqli_query($con, 'SELECT word FROM words WHERE wordStatus>0 AND isPhrase=FALSE') or die(mysqli_error($con));
  // while ($row = mysqli_fetch_assoc($result)) {
  //   $word = $row['word'];
  //   $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b".$word."\b/iu",
  //   "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
  // }

  // 2. colorize phrases & words that are were already learned

  // colorize phrases
  $result = mysqli_query($con, 'SELECT word FROM words WHERE wordStatus=0') or die(mysqli_error($con));
  while ($row = mysqli_fetch_assoc($result)) {
    $phrase = $row['word'];
    $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b".$phrase."\b/iu",
    "<span class='word learned' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
  }

  // // colorize words
  // $result = mysqli_query($con, 'SELECT word FROM words WHERE wordStatus=0 AND isPhrase=FALSE') or die(mysqli_error($con));
  // while ($row = mysqli_fetch_assoc($result)) {
  //   $word = $row['word'];
  //   $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b".$word."\b/iu",
  //   "<span class='word learned' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
  // }

  // 3. colorize frequency list words

  if ($freq_list) {
    $result = mysqli_query($con, 'SELECT freqWord FROM frequencylist_fr LIMIT 5000') or die(mysqli_error($con));
    while ($row = mysqli_fetch_assoc($result)) {
      $word = $row['freqWord'];
      $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b".$word."\b/iu",
      "<span class='word frequency-list' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
    }
  }

  return $text;
}

/**
 * Calculates how much time it would take to read $text to a native speaker
 * Returns that estimation
 *
 * @param string $text
 * @return integer
 */
function estimatedReadingTime($text)
{
  $word_count = str_word_count($text);
  $reading_time = $word_count / 200;
  $mins = floor($reading_time);
  $secs = $reading_time - $mins;
  $reading_time = $mins + (($secs < 30) ? 0 : 1);

  return $reading_time;
}

/**
 * Get host of URL passed as parameter 
 * Used in showtext.php to show a short version of the text's source URL
 *
 * @param string $url
 * @return string
 */
function getHost($url) { 
  $parseUrl = parse_url(trim($url)); 
  return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2))); 
} 

/**
 * Get RSS feed elements and show them in a list group fashion for easier access
 * Returns the result as HTML code
 * 
 * @param string $url Url of the feed to parse & show
 * @param integer $groupindex List group item index
 * @return string
 */
function getRSSFeedElements($url, $groupindex) {
  $feed = file_get_contents($url);

  if ($feed) {
    $feed = simplexml_load_string($feed);
    $isatom = isset($feed->entry);
    $isrss = isset($feed->channel);
  
    if ($isatom || $isrss) {
      $title = $isatom ? $feed->title: $feed->channel->title; // ATOM: feed>title; RSS: rss>channel>title
      $entry = $isatom ? $feed->entry: $feed->channel->item; // ATOM: feed>entry; RSS: rss>channel>item
  
      if(isset($title)) {
        $result = "<a href='#item-$groupindex' class='list-group-item' data-toggle='collapse'>
                <i class='glyphicon glyphicon-chevron-right'></i>$title</a>" .
                "<div class='list-group collapse' id='item-$groupindex'>";
  
        if (isset($entry)) {
          $itemindex = 1;
          foreach ($entry as $article) {
            $artdate = $isatom ? $article->updated : $article->pubDate; // ATOM: feed>entry>updated; RSS: rss>channel>item>pubDate
            $artdate = date("d/m/Y - H:i", strtotime($artdate));
            $artauthor = $article->author; // ATOM: feed>entry>author; RSS: rss>channel>item>author
            $artsrc = $isatom ? $article->link->attributes()->href : $article->link;  // ATOM: feed>entry>link>href attr; RSS: rss>channel>item>link
            $content = $isatom ? $article->content : $article->description; // ATOM: feed>entry>content; rss>channel>item>description
  
            $result .= "<a href='#item-$groupindex-$itemindex' class='list-group-item entry-info' data-toggle='collapse'" . 
                       "data-pubdate='$artdate' data-author='$artauthor' data-src='$artsrc' >" .
                        "<i class='glyphicon glyphicon-chevron-right'></i>$article->title</a>";
  
            $result .= "<div class='list-group collapse' id='item-$groupindex-$itemindex'>
                        <div class='list-group-item entry-text'>" . strip_tags($content, '<p>') .
                        "<button type='button' class='btn btn-default btn-addsound'>Add sound file</button>
                        <button type='button' class='btn btn-default btn-readnow'>Read now</button>
                        <button type='button' class='btn btn-default btn-readlater'>Read later</button>
                        <span class='message'></span></div></div>";
            $itemindex++;
          }
        }
  
        $result .= '</div>';
      }
      
      if (isset($result)) {
        return $result;
      }
    }
  }
}