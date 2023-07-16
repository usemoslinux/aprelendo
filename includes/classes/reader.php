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
use Aprelendo\Includes\Classes\Likes;
use Aprelendo\Includes\Classes\AprelendoException;
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
    protected $is_long_text = false;
    private const MAX_TEXT_LENGTH = 10000;
    
    /**
     * Constructor
     * Initializes class variables (id, title, author, etc.)
     *
     * @param \PDO $pdo
     * @param int $id
     * @param bool $is_shared
     */
    public function __construct(\PDO $pdo, int $id, bool $is_shared)
    {
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
            $this->is_long_text = mb_strlen($this->text) > self::MAX_TEXT_LENGTH;
        } catch (\PDOException $e) {
            throw new AprelendoException($e->getMessage());
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
    protected function estimatedReadingTime(): int
    {
        $word_count = str_word_count($this->text);
        $reading_time = $word_count / 200;
        $mins = floor($reading_time);
        $secs = $reading_time - $mins;
        return $mins + (($secs < 30) ? 0 : 1);
    } // end estimatedReadingTime()

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    } // end getId()

    /**
     * Get the value of title
     */
    public function getTitle(): string
    {
        return $this->title;
    } // end getTitle()

    /**
     * Get the value of author
     */
    public function getAuthor(): string
    {
        return $this->author;
    } // end getAuthor()

    /**
     * Get the value of source_uri
     */
    public function getSourceUri(): string
    {
        return is_null($this->source_uri) ? '' : $this->source_uri;
    } // end getSourceUri()

    /**
     * Get the value of audio_uri
     */
    public function getAudioUri(): string
    {
        return is_null($this->audio_uri) ? '' : $this->audio_uri;
    } // end getAudioUri()

    /**
     * Get the value of text
     */
    public function getText(): string
    {
        return $this->text;
    } // end getText()

    /**
     * Get the value of type
     */
    public function getType(): int
    {
        return $this->type;
    } // end getType()

    /**
     * Get the value of word_count
     */
    public function getWordCount(): int
    {
        return is_null($this->word_count) ? 0 : $this->word_count;
    } // end getWordCount()

    /**
     * Get the value of level
     */
    public function getLevel(): int
    {
        return is_null($this->level) ? 0 : $this->level;
    } // end getLevel()

    /**
     * Get the value of is_shared
     */
    public function getIsShared(): bool
    {
        return $this->is_shared;
    } // end getIsShared()

    /**
     * Get the value of is_long_text
     */
    public function getIsLongText(): bool
    {
        return $this->is_long_text;
    } // end getIsLongText()
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
    public function __construct()
    {
        $argv = func_get_args();
        $num_args = func_num_args();

        if ($num_args == 3) {
            $this->createMiniReader($argv[0], $argv[1], $argv[2]);
        } elseif ($num_args == 5) {
            $this->createFullReader($argv[0], $argv[1], $argv[2], $argv[3], $argv[4]);
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
    private function createFullReader($pdo, $is_shared, $text_id, $user_id, $lang_id)
    {
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
    private function createMiniReader(\PDO $pdo, int $user_id, int $lang_id): void
    {
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
     * The return value of this method is used as an input for underlinewords.js to underline & colorize words.
     *
     * @param string $text
     * @return string
     */
    public function getWordsForText(string $text): string
    {
        try {
            // return value will be composed of 3 arrays:
            // $result['text']: text to be reviewed
            // $result['user_words']: words user added to his private library
            // $result['high_freq']: high frequency words for the language the user is learning

            $result['text'] = $text;
            
            // 1. get user words & phrases
            $words_table = new Words($this->pdo, $this->user_id, $this->lang_id);
            $result['user_words'] = $words_table->getAll(6);
          
            // 2. get frequency list words
            if ($this->show_freq_words) {
                $user = new User($this->pdo);
                if ($user->isLoggedIn()) {
                    $lang = new Language($this->pdo, $this->user_id);
                    $lang->loadRecord($this->lang_id);
                    $freq_words = WordFrequency::getHighFrequencyList($this->pdo, $lang->getName());
                    $result['high_freq'] = \array_column($freq_words, 'word');
                }
            }
            return json_encode($result);
        } catch (\PDOException $e) {
            throw new AprelendoException('There was an unexpected problem loading the private list of words for this user.');
        }
    } // end getWordsForText()

    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showText(): string
    {
        $html = '<div id="text-container" class="my-3" data-type="text" data-textID="' . $this->id
            . '" data-assisted-learning="' . (int)$this->prefs->getAssistedLearning() . '" data-is-long-text="'
            . (int)$this->is_long_text . '">';
        
        // display source, if available
        if (!empty($this->source_uri)) {
            $html .= '<a class="source" href="' . $this->source_uri . '" target="_blank" rel="noopener noreferrer">'
                . Url::getDomainName($this->source_uri) . '</a>';
        }
        
        $html .= '<h2 class="my-3">' . $this->title . '</h2>'; // display title
        
        // display author, if available
        if (!empty($this->author)) {
            $html .= '<div class="author">' . $this->author . '</div>';
        }
       
        if ($this->prefs->getAssistedLearning() && !$this->getIsLongText()) {
            // display audio player
            $html .= '<hr>';

            $html .=   '<div id="alert-msg-audio" class="alert alert-danger d-none"></div>';

            $html .=   '<div id="audioplayer-loader" class="lds-facebook mx-auto" title="Loading audio...">
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
                                        <label id="label-speed" class="basic" for="range-speed">
                                            Speed: <span id="currentpbr">1.0</span> x</label>
                                        <input id="range-speed" type="range" class="form-range" value="1" min="0.5"
                                            max="2" step="0.1">
                                        <label id="label-abloop" class="px-1 basic">A-B Loop:</label>
                                        <button id="btn-abloop" class="btn btn-outline-secondary btn-sm"
                                            title="Toggle A-B Loop">A</button>
                                    </div>
                                </form>
                            </div>';
            
            $html .= '<hr>';
            
            // display assisted learning message
            $html .=   '<div id="alert-msg-phase" class="alert alert-info alert-dismissible show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                            <h5 class="alert-heading">Assisted learning - Phase 1: Reading</h5>
                            <span class="small">Read the text and try to understand what is going on. When you come
                                across a word or phrase you don\'t understand, keep reading until the end of the
                                sentence, or better yet, the paragraph. If by the end of the passage you still
                                haven\'t guessed its meaning, click on it to look it up in the dictionary or right
                                click on any word to translate the whole sentence.</span>
                        </div>';
        }
        
        // display text
        $html .= '<div id="text" style="line-height:' . $this->prefs->getLineHeight() . ';">';
        
        $html .= $this->getText() . '</div>';
        $html .= '<p></p>';
        
        $html .= '<p></p></div>';
        return $html;
    } // end showText()

    /**
     * Constructs HTML code to show text in reader
     *
     * @param string $yt_id YouTube Id
     * @return string
     */
    public function showVideo(string $yt_id): string
    {
        $yt_id = $yt_id ? $yt_id : '';
        $likes = new Likes($this->pdo, $this->id, $this->user_id, $this->lang_id);
        $user_liked_class = $likes->userLiked($this->user_id, $this->id) ? 'fas' : 'far';

        $html = '<div class="col-lg-6 offset-lg-3">' .
                    '<div id="main-container" style="height: 100vh; height: calc(var(--vh, 1vh) * 100);"
                        class="d-flex flex-column">';

        $html .= '<div class="d-flex flex-row-reverse  my-2">
                        <button type="button" id="btn-save-ytvideo"
                            title="Close and save the learning status of your words" class="btn btn-sm btn-success">
                            Save</button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-sm btn-secondary me-2" title="Reader settings">
                            <span class="fas fa-cog"></span>
                        </button>
                        <button type="button" title="Like" class="btn btn-sm btn-link me-2">
                            <span title="Like">
                                <span class="'
                                    . $user_liked_class
                                    . ' fa-heart" data-idText="' . $this->id .'"></span>
                                <small>' . $likes->get($this->id) . '</small>
                            </span>
                        </button>
                  </div>';

        $html .= '<div class="ratio ratio-16x9" style="max-height: 60%;">' .
                  '<div data-ytid="' . $yt_id . '" id="player"></div>' .
               '</div>';

        $html .= "<div id='text-container' class='overflow-auto mb-1' data-type='video' data-textID='"
            . $this->id . "'>";
        $xml = new SimpleXMLElement($this->text);

        for ($i=0; $i < sizeof($xml); $i++) {
            $start = (string)$xml->e[$i]->start;
            $dur = (string)$xml->e[$i]->duration;
            $text = html_entity_decode((string)$xml->e[$i]->text, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $html .= "<div class='text-center' data-start='$start' data-dur='$dur' >". $text .'</div>';
        }
        
        $html .= '</div></div></div>';

        return $html;
    } // end showVideo()

    /**
     * Constructs HTML code to show an offline video
     *
     * @param string $file file path
     * @return string html
     */
    public function showOfflineVideo(string $file): string
    {
        $html = '<div class="col-xl-8 offset-xl-2">' .
                    '<div style="height: 100vh; height: calc(var(--vh, 1vh) * 100);" class="d-flex flex-column">' .
                        '<div id="offline-video-container" class="ratio ratio-16x9 mt-1">' .
                        '<input id="video-file-input" type="file" name="video-file-input"
                            accept="video/mp4,video/ogg,video/webm" style="display: none;">' .
                        '<input id="subs-file-input" type="file" name="subs-file-input" accept=".srt"
                            style="display: none;">' .
                            '<video id="video-stream" controls controlsList="nofullscreen nodownload noremoteplayback"
                                playsinline disablePictureInPicture>' .
                                '<source src="' . $file . '"/>'.
                                'Your browser does not support HTML5 video.' .
                            '</video>' .
                        '</div>';

        $html .= '<div class="d-flex flex-wrap">'.
                    '<button type="button" id="btn-selvideo" title="Select video" class="btn btn-sm btn-primary
                        me-2 my-2"><span class="fa-solid fa-video"></span></button>'.
                    '<button type="button" id="btn-selsubs" title="Select subtitles" class="btn btn-sm btn-primary
                        me-2 my-2"><span class="fa-solid fa-closed-captioning"></span></button>'.
                    '<button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                        class="btn btn-sm btn-secondary me-2 my-2" title="Reader settings">
                        <span class="fas fa-cog"></span>
                    </button>' .
                    '<button type="button" id="btn-save-offline-video" title="Save the learning status of your words"
                        class="btn btn-sm btn-success ms-auto my-2">Save</button>'.
                 '</div>'.
                 '<div id="text-container" class="overflow-auto mb-1"></div>';

        $html .= '</div></div>';

        return $html;
    } // end showOfflineVideo()

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
