<?php 

class Text 
{
    public $con;
    public $id;
    public $title;
    public $author;
    public $source_uri;
    public $audio_uri;
    public $text;
    
    /**
     * Constructor
     * Initializes class variables (id, title, author, etc.)
     *
     * @param mysqli $con
     * @param integer $id
     */
    public function __construct($con, $id) {
        $this->con = $con;
        $id = $con->escape_string($id);
        $result = $con->query("SELECT text, textTitle, textAuthor, textSourceURI, textAudioURI FROM texts WHERE textID='$id'");
        
        if ($result) {
            $row = $result->fetch_assoc();

            $this->id = $id;
            $this->title = $row['textTitle'];
            $this->author = $row['textAuthor'];
            $this->source_uri = $row['textSourceURI'];
            $this->audio_uri = $row['textAudioURI'];
            $this->text = $row['text'];
        }
    }

    /**
     * Calculates how much time it would take to read $text to a native speaker
     * Returns that estimation
     *
     * @param string $text
     * @return integer
     */
    protected function estimatedReadingTime()
    {
        $word_count = str_word_count($this->text);
        $reading_time = $word_count / 200;
        $mins = floor($reading_time);
        $secs = $reading_time - $mins;
        $reading_time = $mins + (($secs < 30) ? 0 : 1);

        return $reading_time;
    }
}

class Reader extends Text
{
    public $font_family;
    public $font_size;
    public $line_height;
    public $text_align;
    public $display_mode;
    public $assisted_learning;
    public $show_freq_list;
    protected $learning_lang_id;
    protected $user_id; 
    
    /**
     * Constructor
     * Calls createFullReader or createMiniReader depending on the nr. of args
     */
    public function __construct() {
        $argv = func_get_args();
        switch(func_num_args()) {
            case 3:
                self::createMiniReader($argv[0], $argv[1], $argv[2]);
                break;
            case 4:
                self::createFullReader($argv[0], $argv[1], $argv[2], $argv[3]);
                break;
         }
    }

    /**
     * Constructor
     * Used for full reader (whole text document). Used by showtext.php
     *
     * @param mysqli $con
     * @param integer $text_id
     * @param integer $user_id
     * @param integer $learning_lang_id
     * @return void
     */
    private function createFullReader($con, $text_id, $user_id, $learning_lang_id) {
        parent::__construct($con, $text_id);
        $this->createMiniReader($con, $user_id, $learning_lang_id);
    }

    /**
     * Constructor
     * Used for mini reader (word/phrase). Used by underlinewords.php
     *
     * @param mysqli $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     * @return void
     */
    private function createMiniReader($con, $user_id, $learning_lang_id) {
        $this->con = $con;
        $this->user_id = $user_id = $con->escape_string($user_id);
        $this->learning_lang_id =  $con->escape_string($learning_lang_id);
        

        if ($result = $con->query("SELECT * FROM preferences WHERE prefUserId = '$user_id'")) {
            $row = $result->fetch_assoc();
            
            $this->font_family = isset($row['prefFontFamily']) ? $row['prefFontFamily'] : 'Helvetica';
            $this->font_size = isset($row['prefFontSize']) ? $row['prefFontSize'] : '12px';
            $this->line_height = isset($row['prefLineHeight']) ? $row['prefLineHeight'] : '1';
            $this->text_align = isset($row['prefAlignment']) ? $row['prefAlignment'] : 'left';
            $this->display_mode = isset($row['prefMode']) ? $row['prefMode'] : 'light';
            $this->assisted_learning = isset($row['prefAssistedLearning']) ? $row['prefAssistedLearning'] : true;  
            
            if ($result = $con->query("SELECT LgShowFreqList FROM languages WHERE LgId='$learning_lang_id'")) {
                $row = $result->fetch_assoc();
                $this->show_freq_list = $row['LgShowFreqList'];
            }
        }
    }

    /**
    * Replaces line-breaks (\n) with <P></P> tags
    * Returns the modified $text, which includes the new HTML code
    *
    * @param string $text
    * @return string
    */
    public function addParagraphs($text) // Add paragraph elements to text
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
    public function addLinks($text)
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
    public function colorizeWords($text)
    {
        $user_id = $this->user_id;
        $learning_lang_id = $this->learning_lang_id;
        
        // 1. colorize phrases & words that are being reviewed
        $result = $this->con->query("SELECT word FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordStatus>0");
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $word = $row['word'];
                $text = preg_replace("/\b".$word."\b/ui",
                "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
            }
            
            // 2. colorize phrases & words that are were already learned
            $result = $this->con->query("SELECT word FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordStatus=0");
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $phrase = $row['word'];
                    $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b" . $phrase . "\b/iu",
                    "<span class='word learned' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
                }
                
                // 3. colorize frequency list words
                if ($this->show_freq_list) {
                    $result = $this->con->query("SELECT LgName FROM languages WHERE LgId='$this->learning_lang_id'");
                    
                    if ($result) {
                        $row = $result->fetch_assoc();
                        $freq_table_name = $this->con->escape_string($row['LgName']);
                        $result = $this->con->query('SELECT freqWord FROM frequencylist_' . $freq_table_name . ' LIMIT 5000');
                        
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                $word = $row['freqWord'];
                                $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b" . $word . "\b/iu",
                                "<span class='word frequency-list' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
                            }
                        }
                    }
                }
            }
        }
        
        return $text;
    }
    
    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showText() {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $time_start = microtime(true);
        $html = "<div id='container' data-textID='" . $this->id . "'>";
        
        // display source, if available
        if (!empty($this->source_uri)) {
            $html .= '<a class="source" href="' . $this->source_uri . '">' . $this->getHost($this->source_uri) . '</a>'; 
        }
        
        $html .= '<h1>' . $this->title . '</h1>'; // display title
        
        // display author, if available
        if (!empty($this->author)) {
            $html .= '<div class="author">' . $this->author . '</div>';
        }
        
        // $html .= $this->text; // display text
        
        $html .= '<div id="reader-estimated-time" class="meta-data">' .
        $this->estimatedReadingTime() . ' minutes</div>'; // show estimated reading time
        
        // if there an associated audio file, show audio player
        if (!empty($this->audio_uri)) {
            $html .= '<hr>';
            
            $html .= "<audio controls id='audioplayer'>";
            
            $textAudioURI = 'getaudio.php?file=' . $this->audio_uri;
            if (strcasecmp(pathinfo($textAudioURI, PATHINFO_EXTENSION), 'mp3') == 0) {
                $html .= "<source src='$textAudioURI' type='audio/mpeg'>";
            } else {
                $html .= "<source src='$textAudioURI' type='audio/ogg'>";
            }
            
            $html .= 'Your browser does not support the audio element.</audio>
            <form>
            <div class="form-group flex-pbr-form">
            <label for="pbr">Playback rate: <span id="currentpbr">1.0</span></label>
            <input id="pbr" type="range" class="flex-pbr" value="1" min="0.5" max="2" step="0.1">
            </div>
            </form>';
        }
        
        $html .= '<hr>';
        
        if ($this->assisted_learning) {
            $html .= '<div id="alert-msg-phase" class="alert alert-info alert-dismissible show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <strong>Assisted learning - Phase 1:</strong> Reading & listening.</div>';   
        }
        
        $html .= '<div id="text" style="line-height:' . $this->line_height . ';">';
        
        $text = $this->colorizeWords($this->text);
        $text = $this->addLinks($text);
        $text = $this->addParagraphs($text); // convert /n to HTML <p>

        $html .= $text . '</div>';
        
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $html .= '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';
        
        $html .= '<p></p>';
        
        if ($this->assisted_learning) {
            $html .= '<button type="button" id="btn_next_phase" class="btn btn-lg btn-primary btn-block">Go to phase 2
            <div class="small">Look up words/phrases</div></button>';
        } else {
            $html .= '<button type="button" id="btn_save" class="btn btn-lg btn-success btn-block">Finish & Save</button>';
            
            // if there is audio available & at least 1 learning word in current document
            $learningwords = strpos($html, "<span class='word learning'") || strpos($html, "<span class='new learning'");
            if (!empty($this->audio_uri && $learningwords === true)) {
                $html .= '<button type="button" id="btndictation" class="btn btn-lg btn-info btn-block">Toggle dictation on/off</button>';
            }
        }
        
        $html .= '<p></p></div>';
        return $html;
    }


    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showVideo($yt_id) {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $time_start = microtime(true);

        $yt_id = $yt_id ? $yt_id : '';

        $html = '<div id="show-video-container" class="col-xs-12 col-sm-6">' .
            '<div class="video-wrapper">' .
                '<div data-ytid="' . $yt_id . '" id="player"></div>' .
            '</div>' .
            '</div>' .
            '<div id="show-transcript-container" class="col-xs-12 col-sm-6">';

        $html .= "<div id='container' data-textID='" . $this->id . "'>";
        
        $xml = new SimpleXMLElement($this->text);

        for ($i=0; $i < sizeof($xml)-1; $i++) { 
            $start = $xml->text[$i]['start'];
            $dur = $xml->text[$i]['dur'];

            $text = $this->colorizeWords(html_entity_decode($xml->text[$i], ENT_QUOTES | ENT_XML1, 'UTF-8'));
            $text = $this->addLinks($text);

            $html .= "<div class='text-center' data-start='$start' data-dur='$dur' >". $text .'</div>';
        }
        
        $html .= '</div><p></p>';
        
        $html .= '<button type="button" id="btn_save" class="btn btn-lg btn-success btn-block">Finish & Save</button>';
        
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $html .= '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';

        return $html.'</div>';
    }

    
    /**
     * Get host of URL passed as parameter 
     * Used in showtext.php to show a short version of the text's source URL
     *
     * @param string $url
     * @return string
     */
    protected function getHost($url) { 
        $parseUrl = parse_url(trim($url)); 
        return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2))); 
    }
}

?>