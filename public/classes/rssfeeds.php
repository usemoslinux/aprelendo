<?php 

class RSSFeed
{
    public $title;
    public $url;
    public $xmlfeed;
    public $articles = array();

    /**
     * Constructor
     *
     * @param string $url
     */
    public function __construct($url) {
        if (!empty($url)) {
            $this->url = $url;
            $this->fetchXMLFeed($url);
        }
    }

    /**
    * Get RSS feed elements and initialize class variables
    * 
    * @param string $url Url of the feed to parse
    * @return string
    */
    public function fetchXMLFeed($url) {
        $this->xmlfeed = file_get_contents($url);
        
        if ($this->xmlfeed) {
            $this->xmlfeed = simplexml_load_string($this->xmlfeed);
            $isatom = isset($this->xmlfeed->entry);
            $isrss = isset($this->xmlfeed->channel);
            
            if ($isatom || $isrss) {
                $this->title = $isatom ? $this->xmlfeed->title: $this->xmlfeed->channel->title; // ATOM: feed>title; RSS: rss>channel>title
                $entry = $isatom ? $this->xmlfeed->entry: $this->xmlfeed->channel->item; // ATOM: feed>entry; RSS: rss>channel>item
                
                if(isset($this->title)) {
                    if (isset($entry)) {
                        $itemindex = 1;
                        foreach ($entry as $article) {
                            $artdate = $isatom ? $article->updated : $article->pubDate; // ATOM: feed>entry>updated; RSS: rss>channel>item>pubDate
                            $this->articles[$itemindex]['title'] = $article->title;
                            $this->articles[$itemindex]['date'] = date("d/m/Y - H:i", strtotime($artdate));
                            $this->articles[$itemindex]['author'] = $article->author; // ATOM: feed>entry>author; RSS: rss>channel>item>author
                            $this->articles[$itemindex]['src'] = $isatom ? $article->link->attributes()->href : $article->link;  // ATOM: feed>entry>link>href attr; RSS: rss>channel>item>link
                            $this->articles[$itemindex]['content'] = $isatom ? $article->content : $article->description; // ATOM: feed>entry>content; rss>channel>item>description
                            $itemindex++;
                        }
                    }
                }
            }
        } else {
            throw new Exception ('Oops! There was a problem trying to get this feed: ' . $url);
        }
        return true;
    }
}

class RSSFeeds
{
    public $feed1;
    public $feed2;
    public $feed3;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify feeds: $con, $user_id & learning_lang_id
     * 
     * Gets up to 3 rss feeds for that user & language combination
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        $result = $con->query("SELECT LgRSSFeed1URI, LgRSSFeed2URI, LgRSSFeed3URI FROM languages WHERE LgUserId='$user_id' AND LgID='$learning_lang_id'");

        if ($result) {
            $rows = $result->fetch_assoc();
            $feed1uri = $rows['LgRSSFeed1URI'];
            $feed2uri = $rows['LgRSSFeed2URI'];
            $feed3uri = $rows['LgRSSFeed3URI'];

            $this->feed1 = new RSSFeed($feed1uri);
            $this->feed2 = new RSSFeed($feed2uri);
            $this->feed3 = new RSSFeed($feed3uri);
        } else {
            throw new Exception ('Oops! There was an unexpected error trying to get your RSS feeds.');
        }
    }
}


?>