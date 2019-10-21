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

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\Url;
use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\WordFrequency;
use SimpleXMLElement;

class Text 
{
    protected $con;
    protected $id;
    protected $title;
    protected $author;
    protected $source_uri;
    protected $audio_uri;
    public $text;
    protected $is_shared;
    
    /**
     * Constructor
     * Initializes class variables (id, title, author, etc.)
     *
     * @param \PDO $con
     * @param integer $id
     * @param bool $is_shared
     */
    public function __construct(\PDO $con, int $id, bool $is_shared) {
        try {
            $this->con = $con;
            $this->is_shared = $is_shared;

            if ($is_shared) {
                $sql = "SELECT `text`, `title`, `author`, `source_uri` FROM `shared_texts` WHERE `id`=?";
            } else {
                $sql = "SELECT `text`, `title`, `author`, `source_uri` FROM `texts` WHERE `id`=?";
            }

            $stmt = $this->con->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->id           = $id;
            $this->text         = $row['text'];
            $this->title        = $row['title'];
            $this->author       = $row['author'];
            $this->source_uri   = $row['source_uri'];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // end __construct()

    /**
     * Calculates how much time it would take to read $text to a native speaker
     * Returns that estimation
     *
     * @return integer
     */
    protected function estimatedReadingTime(): int {
        $word_count = str_word_count($this->text);
        $reading_time = $word_count / 200;
        $mins = floor($reading_time);
        $secs = $reading_time - $mins;
        $reading_time = $mins + (($secs < 30) ? 0 : 1);

        return $reading_time;
    } // end estimatedReadingTime()
}

class Reader extends Text
{
    public $font_family;
    public $font_size;
    public $line_height;
    public $text_align;
    public $display_mode;
    public $assisted_learning;
    public $show_freq_words;
    protected $lang_id;
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
            case 5:
                self::createFullReader($argv[0], $argv[1], $argv[2], $argv[3], $argv[4]);
                break;
         }
    } // end __construct()

    /**
     * Constructor
     * Used for full reader (whole text document). Used by showtext.php
     *
     * @param \PDO $con
     * @param integer $text_id
     * @param integer $user_id
     * @param integer $lang_id
     * @return void
     */
    private function createFullReader($con, $is_shared, $text_id, $user_id, $lang_id) {
        parent::__construct($con, $text_id, $is_shared);
        $this->createMiniReader($con, $user_id, $lang_id);
    } // end createFullReader()

    /**
     * Constructor
     * Used for mini reader (word/phrase). Used by underlinewords.php
     *
     * @param \PDO $con
     * @param integer $user_id
     * @param integer $lang_id
     * @return void
     */
    private function createMiniReader(\PDO $con, int $user_id, int $lang_id): void {
        try {
            $this->con = $con;
            $this->user_id = $user_id;
            $this->lang_id = $lang_id;

            $sql = "SELECT * FROM `preferences` WHERE `user_id` = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $this->font_family = isset($row['font_family']) ? $row['font_family'] : 'Helvetica';
            $this->font_size = isset($row['font_size']) ? $row['font_size'] : '12px';
            $this->line_height = isset($row['line_height']) ? $row['line_height'] : '1';
            $this->text_align = isset($row['text_alignment']) ? $row['text_alignment'] : 'left';
            $this->display_mode = isset($row['learning_mode']) ? $row['learning_mode'] : 'light';
            $this->assisted_learning = isset($row['assisted_learning']) ? $row['assisted_learning'] : true;  
            
            $sql = "SELECT `show_freq_words` FROM `languages` WHERE `id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$lang_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->show_freq_words = $row['show_freq_words'];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // end createMiniReader()

    /**
    * Makes all words clickable by wrapping them in SPAN tags
    * Returns the modified $text, which includes the new HTML code
    * 
    * @param string $text
    * @return string
    */
    public function addLinks(string $text): string
    {
        $find = array('/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|<[^>]*>(*SKIP)(*F)|(\w+)/iu', '/<[^>]*>(*SKIP)(*F)|[^\w<]+/u');
        
        $replace = array("<span class='word' data-toggle='modal' data-target='#myModal'>$0</span>", "<span>$0</span>");
        
        return preg_replace($find, $replace, $text);
    } // end addLinks()
    
    /**
    * Underlines words with different colors depending on their status
    * Returns the modified $text, which includes the new HTML code
    * It is used for ebooks, as they use HTML code as input ($text).
    * Because of this, colorizeWords is much slower than colorizeWordsFast. 
    * Also, span creation for the rest of the words and separators is done by AddLinks()
    *
    * @param string $text
    * @param \PDO $con
    * @return string
    */
    public function colorizeWords(string $text): string {
        try {
            $user_id = $this->user_id;
            $lang_id = $this->lang_id;
            
            // 1. colorize phrases & words that are being reviewed
            $sql = "SELECT `word` 
                    FROM `words` 
                    WHERE `user_id`=? AND `lang_id`=? AND `status`>0 
                    ORDER BY `is_phrase` ASC";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$user_id, $lang_id]);
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $phrase = $row['word'];
                
                $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b" . $phrase . "\b/iu",
                "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
            }
            
            // 2. colorize phrases & words that were already learned
            $sql = "SELECT `word` 
                    FROM `words` 
                    WHERE `user_id`=? AND `lang_id`=? AND `status`=0";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$user_id, $lang_id]);

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $phrase = $row['word'];
                $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b" . $phrase . "\b/iu",
                "<span class='word learned' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
            }
            
            // 3. colorize frequency list words
            if ($this->show_freq_words) {
                $user = new User($this->con);
                if ($user->isLoggedIn() && $user->isPremium()) {
                    $sql = "SELECT `name` FROM languages WHERE `id`=?";
                    $stmt = $this->con->prepare($sql);
                    $stmt->execute([$this->lang_id]);

                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $freq_table_name = $row['name'];
                    
                    $sql = "SELECT `word`, `frequency_index` 
                            FROM ? 
                            WHERE `frequency_index` < 80";
                    $stmt = $this->con->prepare($sql);
                    $stmt->execute(['frequency_list_' . $freq_table_name]);
                    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        $word = $row['word'];
                        $text = preg_replace("/\s*<span[^>]+>.*?<\/span>(*SKIP)(*F)|\b" . $word . "\b/iu",
                        "<span class='word frequency-list' data-toggle='modal' data-target='#myModal'>$0</span>", "$text");
                    }
                }
            }
            return $text;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }        
    }

    /**
    * Underlines words with different colors depending on their status and creates spans
    * for the rest of the words & separators.
    * Returns the modified $text, which includes the new HTML code
    * It is used for simple texts, videos & RSS feeds, as they all use plain text as input ($text).
    * Because of this, colorizeWordsFast is much faster than colorizeWords even though it does
    * much more (it's like doing colorizeWords + AddLinks).
    *
    * @param string $text
    * @return string
    */
    public function colorizeWordsFast(string $text): string {
        $user_id = $this->user_id;
        $lang_id = $this->lang_id;
        
        // divide text in two arrays: words & word separators
        \preg_match_all("/(\w+)/u", $text, $words);
        \preg_match_all("/(\W+)/u", $text, $separators);
        
        // check if text starts with a word or a word separator (this will be used when merging again words & separators)
        if (\preg_match("/\W/u", $text, $first_separator, PREG_OFFSET_CAPTURE)) {
            $separator_first = $first_separator[0][1] === 0;
        } else {
            $separator_first = false;
        }
        
        try {
            // get words in personal dictionary
            $sql = "SELECT `word`, `status` 
                    FROM `words` 
                    WHERE `user_id`=? AND `lang_id`=?  
                    ORDER BY `is_phrase` ASC";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$user_id, $lang_id]);

            $dic_words = $stmt->fetch(\PDO::FETCH_ASSOC);

            // get high frequency words list, only if necessary
            if ($this->show_freq_words) {
                $user = new User($this->con);
                if ($user->isLoggedIn() && $user->isPremium()) {
                    $sql = "SELECT `name` FROM `languages` WHERE `id`=?";
                    $stmt = $this->con->prepare($sql);
                    $stmt->execute([$this->lang_id]);
                    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    $freq_words = WordFrequency::getHighFrequencyList($this->con, $row['name']);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }   
        
        // replace words & separators with corresponding html code
        foreach ($words[0] as &$word) {
            $search_in_dic = \array_search($word, array_column($dic_words, 'word'));
            if ($search_in_dic === false) {
                // if necessary, underline frequency words
                if ($this->show_freq_words) { 
                    $search_in_freq_dic = \array_search($word, array_column($freq_words, 'word'));
                    if ($search_in_freq_dic === false) {
                        $word = "<span class='word' data-toggle='modal' data-target='#myModal'>$word</span>";
                    } else {
                        $word = "<span class='word frequency-list' data-toggle='modal' data-target='#myModal'>$word</span>";
                    }
                } else {
                    $word = "<span class='word' data-toggle='modal' data-target='#myModal'>$word</span>";
                }
            } else {
                $learning_level = $dic_words[$search_in_dic]['status'] > 0 ? 'learning' : 'learned';
                $word = "<span class='word reviewing $learning_level' data-toggle='modal' data-target='#myModal'>$word</span>";
            }
        }

        foreach ($separators[0] as &$separator) {
            $separator = "<span>$separator</span>";
        }

        // merge words & separators and to create complete html code
        $html = [];
        if ($separator_first) {
            array_map(function ($a, $b) use (&$html) { array_push($html, $a, $b); }, $separators[0], $words[0]);
        } else {
            array_map(function ($a, $b) use (&$html) { array_push($html, $a, $b); }, $words[0], $separators[0]);
        }
        
        return implode($html);
    } // end colorizeWordsFast()
    
    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showText(): string {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        // $time_start = microtime(true);
        $html = "<div id='text-container' data-textID='" . $this->id . "'>";
        
        // display source, if available
        if (!empty($this->source_uri)) {
            $html .= '<a class="source" href="' . $this->source_uri . '" target="_blank" rel="noopener noreferrer">' . Url::getDomainName($this->source_uri) . '</a>'; 
        }
        
        $html .= '<h1>' . $this->title . '</h1>'; // display title
        
        // display author, if available
        if (!empty($this->author)) {
            $html .= '<div class="author">' . $this->author . '</div>';
        }
       
        // display audio player
        $html .= '<hr>';

        $html .=   '<div id="audioplayer-loader" class="lds-facebook mx-auto">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>';

        $html .=   '<audio controls id="audioplayer" class="d-none">
                        <source type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <form id="audioplayer-speedbar" class="d-none">
                        <div class="form-group flex-pbr-form">
                            <label id="label-speed" class="basic" for="pbr">Speed: <span id="currentpbr">1.0</span> x</label>
                            <input id="pbr" type="range" class="custom-range flex-pbr" value="1" min="0.5" max="2" step="0.1">
                        </div>
                    </form>
                    ';
        
        $html .= '<hr>';
        
        // display assisted learning message
        if ($this->assisted_learning) {
            $html .=   '<div id="alert-msg-phase" data-phase="1" class="alert alert-info alert-dismissible show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Assisted learning - Phase 1:</strong> Reading
                            <br>
                            <span class="small">Look up words in the dictionary. Try to understand the meaning of both the text as a whole and each word/phrase.</span>
                        </div>';   
        }
        
        // display text
        $html .= '<div id="text" style="line-height:' . $this->line_height . ';">';
        
        $text = $this->colorizeWordsFast($this->text);
        $text = nl2br($text);

        $html .= $text . '</div>';
        
        // display total execution time
        // $time_end = microtime(true);
        // $execution_time = ($time_end - $time_start);
        // $html .= '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';
        
        $html .= '<p></p>';
        
        if ($this->assisted_learning) {
            $html .= '<button type="button" id="btn-next-phase" class="basic btn btn-lg btn-primary btn-block">Go to phase 2
                      <br>
                      <span class="small">Listening</span>
                      </button>';
        } else {
            $html .= '<button type="button" id="btn-save" title="Save the learning status of your words & archive this text" class="btn btn-lg btn-success btn-block">Finish & Save</button>';
            
            // if there is audio available & at least 1 learning word in current document
            $learningwords = strpos($html, "<span class='word learning'") || strpos($html, "<span class='new learning'");
            if (!empty($this->audio_uri && $learningwords === true)) {
                $html .= '<button type="button" id="btndictation" class="btn btn-lg btn-info btn-block">Toggle dictation on/off</button>';
            }
        }
        
        $html .= '<p></p></div>';
        return $html;
    } // end showText()


    /**
     * Constructs HTML code to show text in reader
     *
     * @param string $yt_id YouTube Id
     * @return string
     */
    public function showVideo(string $yt_id): string {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        // $time_start = microtime(true);

        $yt_id = $yt_id ? $yt_id : '';

        $html = '<div id="show-video-container" class="col-md-6">' .
            '<div class="video-wrapper">' .
                '<div data-ytid="' . $yt_id . '" id="player"></div>' .
            '</div>' .
            '</div>' .
            '<div id="show-transcript-container" class="col-md-6">';

        $html .= "<div id='text-container' data-textID='" . $this->id . "'>";
        $html .= '<div id="message-window"></div>';
        $xml = new SimpleXMLElement($this->text);

        for ($i=0; $i < sizeof($xml)-1; $i++) { 
            $start = $xml->text[$i]['start'];
            $dur = $xml->text[$i]['dur'];

            $text = $this->colorizeWordsFast(html_entity_decode($xml->text[$i], ENT_QUOTES | ENT_XML1, 'UTF-8'));

            $html .= "<div class='text-center' data-start='$start' data-dur='$dur' >". $text .'</div>';
        }
        
        $html .= '</div>';
        
        $html .= '<br><button type="button" id="btn-save" title="Save the learning status of your words" class="basic btn btn-lg btn-success btn-block">Finish & Save</button>';
        
        // $time_end = microtime(true);
        // $execution_time = ($time_end - $time_start);
        // $html .= '<b>Total Execution Time:</b> ' . $execution_time . ' Secs';

        return $html.'<br></div>';
    } // end showVideo()
}

?>