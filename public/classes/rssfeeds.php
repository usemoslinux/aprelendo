<?php 

class RSSFeed
{
    public $title;
    public $url;
    public $xmlfeed;
    public $articles = array();
    
    /**
    * Get RSS feed elements and show them in a list group fashion for easier access
    * Returns the result as HTML code
    * 
    * @param string $url Url of the feed to parse & show
    * @param integer $groupindex List group item index
    * @return string
    */
    public function getAndPrintElements($url, $groupindex) {
        $this->feed = file_get_contents($url);
        
        if ($this->feed) {
            $this->feed = simplexml_load_string($this->feed);
            $isatom = isset($this->feed->entry);
            $isrss = isset($this->feed->channel);
            
            if ($isatom || $isrss) {
                $this->title = $isatom ? $this->feed->title: $this->feed->channel->title; // ATOM: feed>title; RSS: rss>channel>title
                $entry = $isatom ? $this->feed->entry: $this->feed->channel->item; // ATOM: feed>entry; RSS: rss>channel>item
                
                if(isset($this->title)) {
                    $result = "<a href='#item-$groupindex' class='list-group-item' data-toggle='collapse'>
                    <i class='glyphicon glyphicon-chevron-right'></i>$this->title</a>" .
                    "<div class='list-group collapse' id='item-$groupindex'>";
                    
                    if (isset($entry)) {
                        $itemindex = 1;
                        foreach ($entry as $article) {
                            $artdate = $isatom ? $article->updated : $article->pubDate; // ATOM: feed>entry>updated; RSS: rss>channel>item>pubDate
                            $this->articles[$itemindex]['artdate'] = $artdate = date("d/m/Y - H:i", strtotime($artdate));
                            $this->articles[$itemindex]['artauthor'] = $artauthor = $article->author; // ATOM: feed>entry>author; RSS: rss>channel>item>author
                            $this->articles[$itemindex]['artsrc'] = $artsrc = $isatom ? $article->link->attributes()->href : $article->link;  // ATOM: feed>entry>link>href attr; RSS: rss>channel>item>link
                            $this->articles[$itemindex]['content'] = $content = $isatom ? $article->content : $article->description; // ATOM: feed>entry>content; rss>channel>item>description
                            
                            $result .= "<a href='#item-$groupindex-$itemindex' class='list-group-item entry-info' data-toggle='collapse'" . 
                            " data-pubdate='{$this->articles[$itemindex]['artdate']}'" .
                            " data-author='{$this->articles[$itemindex]['artauthor']}'" .
                            " data-src='{$this->articles[$itemindex]['artsrc']}' >" .
                            "<i class='glyphicon glyphicon-chevron-right'></i>$article->title</a>";
                            
                            $result .= "<div class='list-group collapse' id='item-$groupindex-$itemindex'>
                            <div class='list-group-item entry-text'>" . strip_tags($this->articles[$itemindex]['content'], '<p>') .
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
}

?>