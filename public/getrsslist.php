<?php
$articles = simplexml_load_string(file_get_contents('https://feeds.feedburner.com/Rtlinfos-ALaUne'));
echo '<h2>' . $articles->channel->title . '</h2>';
foreach ($articles->channel->item as $article) {

  $artdate = date("d/m/Y - H:i", strtotime($article->pubDate));
  echo '<article>';
  echo '<h5>' . $article->title . '</h5>';
  echo 'Date: ' . $artdate;
  echo 'Link: ' . $article->link;
  echo strip_tags($article->description, '<p>') ;
  echo '</article>';
}

// structure: https://jsfiddle.net/ann7tctp/
?>


