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

namespace Aprelendo;

use Aprelendo\Texts;
use Aprelendo\SharedTexts;
use Aprelendo\Url;
use Aprelendo\Language;
use Aprelendo\Preferences;
use Aprelendo\Likes;
use Aprelendo\UserException;
use SimpleXMLElement;

class Reader
{
    private $pdo;
    private $text;
    public $is_long_text = false;
    public $prefs;
    public $show_freq_words   = false;
    
    public const MAX_TEXT_LENGTH = 10000;

    /**
     * Constructor
     * Calls createFullReader or createMiniReader depending on the nr. of args
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id, int $text_id = 0, bool $is_shared = false)
    {
        $this->pdo = $pdo;

        $this->prefs = new Preferences($pdo, $user_id);
        $this->prefs->loadRecord();
        
        $lang = new Language($pdo, $user_id);
        $lang->loadRecordById($lang_id);
        $this->show_freq_words = $lang->show_freq_words;

        if (func_num_args() == 5) {
            if ($is_shared) {
                $this->text = new SharedTexts($pdo, $user_id, $lang_id);
                $this->text->loadRecord($text_id);
            } else {
                $this->text = new Texts($pdo, $user_id, $lang_id);
                $this->text->loadRecord($text_id);
            }
            
            $this->is_long_text = mb_strlen($this->text->text) > self::MAX_TEXT_LENGTH;
        }
    } // end __construct()

    /**
     * Constructs HTML code to show text in reader
     *
     * @return string
     */
    public function showText(): string
    {
        $html = '<div id="text-container" class="my-3" data-type="text" data-IdText="' . $this->text->id
            . '" data-assisted-learning="' . (int)$this->prefs->assisted_learning . '" data-is-long-text="'
            . (int)$this->is_long_text . '">';
        
        // display source, if available
        if (!empty($this->text->source_uri)) {
            $html .= '<a class="source" href="' . $this->text->source_uri
                . '" target="_blank" rel="noopener noreferrer">'
                . Url::getDomainName($this->text->source_uri) . '</a>';
        }
        
        $html .= '<h2 class="my-3">' . $this->text->title . '</h2>'; // display title
        
        // display author, if available
        if (!empty($this->text->author)) {
            $html .= '<div class="author">' . $this->text->author . '</div>';
        }
        
        // display audio player, if necessary
        if (!empty($this->text->audio_uri)) {
            $html .= $this->showAudioPlayer(false);
        }

        if ($this->prefs->assisted_learning) {
            if (!$this->is_long_text && empty($this->text->audio_uri)) {
                $html .= $this->showAudioPlayer(true);
            }

            // display assisted learning message
            $html .=   '<div id="alert-box-phase" class="alert alert-info alert-dismissible show" role="alert">
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
        $html .= '<div id="text" style="line-height:' . $this->prefs->line_height . ';">';
        
        $html .= $this->text->text . '</div>';
        $html .= '<p></p>';
        
        $html .= '<p></p></div>';
        return $html;
    } // end showText()

    /**
     * Prints audio player html
     *
     * @param string $audio_url
     * @return string
     */
    private function showAudioPlayer(bool $show_loading): string
    {
        $audio_url = $this->text->audio_uri;
        $audio_mime_type = $this->getAudioMimeType();

        $html = '<hr>';

        $html .= '<div id="alert-box-audio" class="alert alert-danger d-none"></div>';

        if ($show_loading) {
            $html .= '<div id="audioplayer-loader" class="lds-facebook mx-auto" title="Loading audio...">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>';
        }
        
        $display_mode_css = $this->prefs->display_mode . 'mode';
        $audio_controls_class = $show_loading ? 'class="d-none"' : '';

        $html .= <<<AUDIOPLAYER_CONTAINER
            <div id="audioplayer-container" class="$display_mode_css">
                <audio controls id="audioplayer" $audio_controls_class>
                    <source id="audio-source" src="$audio_url" type="$audio_mime_type">
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
                            data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-title="Toggle A-B Loop">A</button>
                    </div>
                </form>
            </div>
            AUDIOPLAYER_CONTAINER;
        
        $html .= '<hr>';

        return $html;
    }

    /**
     * Return audio MIME type based on URI file extension
     *
     * @return string
     */
    private function getAudioMimeType(): string {
        // Get file extension
        $file_extension = pathinfo($this->text->audio_uri, PATHINFO_EXTENSION);

        // Map file extensions to audio types
        $audio_types = array(
            'mp3'  => 'audio/mpeg',
            'ogg'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'aac'  => 'audio/aac',
            'm4a'  => 'audio/x-m4a',
            'webm' => 'audio/webm',
            'flac' => 'audio/flac',
            'opus' => 'audio/opus',
        );

        // Set the appropriate MIME type based on the file extension
        return isset($audio_types[$file_extension]) ? $audio_types[$file_extension] : 'audio/mpeg';
    }

    /**
     * Constructs HTML code to show text in reader
     *
     * @param string $yt_id YouTube Id
     * @return string
     */
    public function showVideo(string $yt_id): string
    {
        $yt_id = $yt_id ? $yt_id : '';
        $likes = new Likes($this->pdo, $this->text->id, $this->text->user_id, $this->text->lang_id);
        $user_liked_class = $likes->userLiked($this->text->user_id, $this->text->id) ? 'fas' : 'far';

        $html = '<div class="col-lg-6 offset-lg-3">' .
                    '<div id="main-container" style="height: 100vh; height: calc(var(--vh, 1vh) * 100);"
                        class="d-flex flex-column">';

        $html .= '<div class="d-flex flex-row-reverse my-2">
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                            data-bs-title="Close and save the learning status of your words">
                            <button type="button" id="btn-save-ytvideo" class="btn btn-sm btn-success">
                            Save</button>
                        </span>
                        <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Reader settings">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                                class="btn btn-sm btn-secondary me-2">
                                <span class="bi bi-gear-fill"></span>
                            </button>
                        </span>
                        <button type="button" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                            data-bs-placement="bottom" data-bs-title="Like" class="btn btn-sm btn-link me-2">
                            <span class="'
                                . $user_liked_class
                                . ' bi-heart-fill" data-idText="' . $this->text->id .'"></span>
                            <small>' . $likes->get($this->text->id) . '</small>
                        </button>
                    </div>';

        $html .= '<div class="ratio ratio-16x9" style="max-height: 60%;">' .
                    '<div data-ytid="' . $yt_id . '" id="player"></div>' .
                '</div>';

        $html .= "<div id='text-container' class='overflow-auto text-center mb-1' data-type='video' data-IdText='"
            . $this->text->id . "'>";
        $xml = new SimpleXMLElement($this->text->text);

        for ($i=0; $i < sizeof($xml); $i++) {
            $start = (string)$xml->e[$i]->start;
            $dur = (string)$xml->e[$i]->duration;
            $text = html_entity_decode((string)$xml->e[$i]->text, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $html .= "<div data-start='$start' data-dur='$dur' >". $text .'</div>';
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
                        '<div id="offline-video-container" class="ratio ratio-16x9 bg-dark mt-1">' .
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

        $html .= '<div class="d-flex flex-wrap m-1 mx-xl-0">'.
                    '<button type="button" id="btn-selvideo" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-title="Select video (MP4/OGG/WEBM)"
                        data-bs-placement="bottom" class="btn btn-sm btn-primary me-2">
                        <span class="bi bi-file-earmark-play"></span></button>'.
                    '<button type="button" id="btn-selsubs" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Select subtitles (SRT)" class="btn btn-sm btn-primary me-2">
                        <span class="bi bi-badge-cc-fill"></span></button>'.
                    '<span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Reader settings">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-sm btn-secondary me-2">
                        <span class="bi bi-gear-fill"></span>
                    </button></span>' .
                    '<button type="button" id="btn-save-offline-video" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Save the learning status of your words"
                        class="btn btn-sm btn-success ms-auto">Save</button>'.
                '</div>'.
                '<div id="text-container" class="overflow-auto mb-1"></div>';

        $html .= '</div></div>';

        return $html;
    } // end showOfflineVideo()
}
