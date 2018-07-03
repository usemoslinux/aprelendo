<?php 

class Language 
{
    public $con;
    public $id;
    public $user_id;
    public $name;
    public $dictionary_uri;
    public $translator_uri;
    public $rss_feed_1_uri;
    public $rss_feed_2_uri;
    public $rss_feed_3_uri;
    public $show_freq_list;

    /**
     * Constructor
     * Initializes class variables (id, name, etc.)
     *
     * @param mysqli $con
     * @param integer $id
     * @param integer $user_id
     */
    public function __construct ($con, $id, $user_id) {
        $result = mysqli_query($con, "SELECT * FROM languages WHERE LgID='$id'");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            
            $this->con = $con;
            $this->id = mysqli_real_escape_string($con, $id);
            $this->user_id = mysqli_real_escape_string($con, $user_id);
            $this->name = mysqli_real_escape_string($con, $row['LgName']);
            $this->dictionary_uri = mysqli_real_escape_string($con, $row['LgDict1URI']);
            $this->translator_uri = mysqli_real_escape_string($con, $row['LgTranslatorURI']);
            $this->rss_feed_1_uri = mysqli_real_escape_string($con, $row['LgRSSFeed1URI']);
            $this->rss_feed_2_uri = mysqli_real_escape_string($con, $row['LgRSSFeed2URI']);
            $this->rss_feed_3_uri = mysqli_real_escape_string($con, $row['LgRSSFeed3URI']);
            $this->show_freq_list = mysqli_real_escape_string($con, $row['LgShowFreqList']);
        }
    }

    /**
     * Updates language settings in db
     *
     * @param array $array
     * @return bool
     */
    public function edit($array) {
        $id = $this->id;
        $user_id = $this->user_id;
        $name = $this->name;
        $dictionary_uri = $array['dictionaryURI'];
        $translator_uri = $array['translatorURI'];
        $rss_feed_1_uri = $array['rssfeedURI1'];
        $rss_feed_2_uri = $array['rssfeedURI2'];
        $rss_feed_3_uri = $array['rssfeedURI3'];
        $show_freq_list = $array['freq-list'];
        
        $result = mysqli_query($this->con, "UPDATE languages SET LgName='$name', LgDict1URI='$dictionary_uri',
        LgTranslatorURI='$translator_uri', LgRSSFeed1URI='$rss_feed_1_uri', LgRSSFeed2URI='$rss_feed_2_uri', 
        LgRSSFeed3URI='$rss_feed_3_uri', LgShowFreqList=$show_freq_list WHERE LgUserId='$user_id' AND LgID='$id'");

        return $result;
    }
}


?>