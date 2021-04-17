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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\Url;
use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\Language;
use Aprelendo\Includes\Classes\Preferences;
use Aprelendo\Includes\Classes\WordFrequency;
use SimpleXMLElement;

class Text 
{
    protected $pdo;
    protected $id         = 0;
    protected $title      = '';
    protected $author     = '';
    protected $source_uri = '';
    protected $audio_uri  = '';
    protected $text       = '';
    protected $type       = 0;
    protected $word_count = 0;
    protected $level      = 0;
    protected $is_shared  = false;
    
    /**
     * Constructor
     * Initializes class variables (id, title, author, etc.)
     *
     * @param \PDO $pdo
     * @param int $id
     * @param bool $is_shared
     */
    public function __construct(\PDO $pdo, int $id, bool $is_shared) {
        try {
            $this->pdo = $pdo;
            $this->is_shared = $is_shared;

            if ($is_shared) {
                $sql = "SELECT `text`, `title`, `author`, `source_uri` FROM `shared_texts` WHERE `id`=?";
            } else {
                $sql = "SELECT `text`, `title`, `author`, `source_uri` FROM `texts` WHERE `id`=?";
            }

            $stmt = $this->pdo->prepare($sql);
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
     * @return int
     */
    protected function estimatedReadingTime(): int {
        $word_count = str_word_count($this->text);
        $reading_time = $word_count / 200;
        $mins = floor($reading_time);
        $secs = $reading_time - $mins;
        $reading_time = $mins + (($secs < 30) ? 0 : 1);

        return $reading_time;
    } // end estimatedReadingTime()

    /**
     * Get the value of id
     */ 
    public function getId(): int {
        return $this->id;
    } // end getId()

    /**
     * Get the value of title
     */ 
    public function getTitle(): string {
        return $this->title;
    } // end getTitle()

    /**
     * Get the value of author
     */ 
    public function getAuthor(): string {
        return $this->author;
    } // end getAuthor()

    /**
     * Get the value of source_uri
     */ 
    public function getSourceUri(): string {
        return is_null($this->source_uri) ? '' : $this->source_uri;
    } // end getSourceUri()

    /**
     * Get the value of audio_uri
     */ 
    public function getAudioUri(): string {
        return is_null($this->audio_uri) ? '' : $this->audio_uri;
    } // end getAudioUri()

    /**
     * Get the value of text
     */ 
    public function getText(): string {
        return $this->text;
    } // end getText()

    /**
     * Get the value of type
     */ 
    public function getType(): int {
        return $this->type;
    } // end getType()

    /**
     * Get the value of word_count
     */ 
    public function getWordCount(): int {
        return is_null($this->word_count) ? 0 : $this->word_count;
    } // end getWordCount()

    /**
     * Get the value of level
     */ 
    public function getLevel(): int {
        return is_null($this->level) ? 0 : $this->level;
    } // end getLevel()

    /**
     * Get the value of is_shared
     */ 
    public function getIsShared(): bool {
        return $this->is_shared;
    } // end getIsShared()
}

class Reader extends Text
{
    private $prefs; // Preferences object
    private $show_freq_words   = false;
    private $lang_id           = 0;
    private $user_id           = 0;
    
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
     * @param \PDO $pdo
     * @param int $text_id
     * @param int $user_id
     * @param int $lang_id
     * @return void
     */
    private function createFullReader($pdo, $is_shared, $text_id, $user_id, $lang_id) {
        parent::__construct($pdo, $text_id, $is_shared);
        $this->createMiniReader($pdo, $user_id, $lang_id);
    } // end createFullReader()

    /**
     * Constructor
     * Used for mini reader (word/phrase). Used to access reader preferences without 
     * the need to display some text
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     * @return void
     */
    private function createMiniReader(\PDO $pdo, int $user_id, int $lang_id): void {
        $this->pdo               = $pdo;
        $this->user_id           = $user_id;
        $this->lang_id           = $lang_id;

        $this->prefs = new Preferences($pdo, $user_id);
        $this->prefs->loadRecord();
        
        $lang = new Language($pdo, $user_id);
        $lang->loadRecord($lang_id);
        $this->show_freq_words   = $lang->getShowFreqWords();
    } // end createMiniReader()
    
    /**
     * Gets words that the user added to his private library, as well as high frequency words for the 
     * current language user is learning. Returns an array containing 3 subarrays: text, user_words & high_freq.
     * The return value of this method is used as an input for colorizewords.js to underline & colorize words. 
     *
     * @param string $text
     * @return string
     */
    public function getWordsForText(string $text): string {
        try {
            // return value will be composed of 3 arrays: 
            // $result['text']: text to be reviewed
            // $result['user_words']: words user added to his private library
            // $result['high_freq']: high frequency words for the language the user is learning

            $result['text'] = $text;
            
            // 1. get user words & phrases
            $words_table = new Words($this->pdo, $this->user_id, $this->lang_id);
            $result['user_words'] = $words_table->getAll(0,1000000,4);
          
            // 2. get frequency list words
            if ($this->show_freq_words) {
                $user = new User($this->pdo);
                if ($user->isLoggedIn() && $user->isPremium()) {
                    $lang = new Language($this->pdo, $this->user_id);
                    $lang->loadRecord($this->lang_id);
                    $freq_words = WordFrequency::getHighFrequencyList($this->pdo, $lang->getName());
                    $result['high_freq'] = \array_column($freq_words, 'word');
                }
            }
            return json_encode($result);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected problem loading the private list of words for this user.');
        } finally {
            $stmt = null;
        }        
    } // end getWordsForText()

    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showText(): string {
        $html = "<div id='text-container' class='my-3' data-type='text' data-textID='" . $this->id . "'>";
        
        // display source, if available
        if (!empty($this->source_uri)) {
            $html .= '<a class="source" href="' . $this->source_uri . '" target="_blank" rel="noopener noreferrer">' . Url::getDomainName($this->source_uri) . '</a>'; 
        }
        
        $html .= '<h2 class="my-3">' . $this->title . '</h2>'; // display title
        
        // display author, if available
        if (!empty($this->author)) {
            $html .= '<div class="author">' . $this->author . '</div>';
        }
       
        // display audio player
        $html .= '<hr>';

        $html .=   '<div id="alert-msg-audio" class="alert alert-danger d-none"></div>';

        $html .=   '<div id="audioplayer-loader" class="lds-facebook mx-auto">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>';

                    
        $display_mode_css = $this->prefs->getDisplayMode() . 'mode';

        $html .=   '<div id="audioplayer-container" class="' . $display_mode_css . '">' .
                        '<audio controls id="audioplayer" class="d-none">
                                <source id="audio-mp3" src="" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <form id="audioplayer-speedbar" class="d-none">
                                <div id="audioplayer-speedbar-container">
                                    <label id="label-speed" class="basic" for="range-speed">Speed: <span id="currentpbr">1.0</span> x</label>
                                    <input id="range-speed" type="range" class="custom-range" value="1" min="0.5" max="2" step="0.1">
                                    <label id="label-abloop" class="px-1 basic">A-B Loop:</label>
                                    <button id="btn-abloop" class="btn btn-outline-secondary btn-sm">A</button>
                                </div>
                            </form>
                        </div>';
        
        $html .= '<hr>';
        
        // display assisted learning message
        if ($this->prefs->getAssistedLearning()) {
            $html .=   '<div id="alert-msg-phase" class="alert alert-info alert-dismissible show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="alert-heading">Assisted learning - Phase 1: Reading</h5>
                            <span class="small">Read the text and try to understand what is going on. When you come across a word or phrase you don\'t understand, keep reading until the end of the sentence, or better yet, the paragraph. If by the end of the passage you still haven\'t guessed its meaning, click on it to look it up in the dictionary or right click on any word to translate the whole sentence.</span>
                        </div>';   
        }
        
        // display text
        $html .= '<div id="text" style="line-height:' . $this->prefs->getLineHeight() . ';">';
        
        // $text = $this->colorizeWordsFast($this->text, $user_dic, $freq_words);
        // $text = nl2br($text);

        $html .= $this->getText() . '</div>';
        $html .= '<p></p>';
        
        if ($this->prefs->getAssistedLearning()) {
            $html .= '<button type="button" id="btn-next-phase" class="btn btn-lg btn-primary btn-block">Go to phase 2
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
        $yt_id = $yt_id ? $yt_id : '';

        $html = '<div class="col-lg-6 offset-lg-3">' .
                    '<div style="height: 100vh;" class="d-flex flex-column">' .
                        '<div class="embed-responsive embed-responsive-16by9" style="max-height: 50%;">' .
                            '<div data-ytid="' . $yt_id . '" id="player"></div>' .
                        '</div>';

        $html .= "<div id='text-container' class='overflow-auto' data-type='video' data-textID='" . $this->id . "'>";
        $xml = new SimpleXMLElement($this->text);

        for ($i=0; $i < sizeof($xml); $i++) { 
            $start = $xml->text[$i]['start'];
            $dur = $xml->text[$i]['dur'];

            // $text = $this->colorizeWordsFast(html_entity_decode($xml->text[$i], ENT_QUOTES | ENT_XML1, 'UTF-8'), $user_dic, $freq_words);
            $text = html_entity_decode($xml->text[$i], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $html .= "<div class='text-center' data-start='$start' data-dur='$dur' >". $text .'</div>';
        }
        
        $html .= '<div class="p-3"><button type="button" id="btn-save" title="Save the learning status of your words" class="btn btn-lg btn-success btn-block">Finish & Save</button></div>';

        $html .= '</div></div></div>';

        return $html;
    } // end showVideo()

    /**
     * Get the value of prefs
     */ 
    public function getPrefs(): Preferences
    {
        return $this->prefs;
    }

    /**
     * Get the value of show_freq_words
     */ 
    public function getShowFreqWords(): bool
    {
        return $this->show_freq_words;
    }
}

?>