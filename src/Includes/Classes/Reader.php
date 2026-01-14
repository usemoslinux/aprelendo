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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

use SimpleXMLElement;

class Reader
{
    private $pdo;
    private $user_id;
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
        $this->user_id = $user_id;

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
     * @param string $reader_css CSS style to include in reader HTML
     * @return string
     */
    public function showText(string $reader_css): string
    {
        $audio_uri = TextsUtilities::isGoogleDriveLink($this->text->audio_uri)
            ? TextsUtilities::getGoogleDriveAudioUri($this->text->audio_uri)
            : $this->text->audio_uri;
        
        $html = '<div id="text-container" style="' . $reader_css . '" class="d-flex flex-column m-2 p-3" data-type="text" data-IdText="'
            . $this->text->id . '" data-assisted-learning="' . (int)$this->prefs->assisted_learning
            . '" data-is-long-text="' . (int)$this->is_long_text . '">';
        
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
        if (!empty($audio_uri)) {
            $audio_player = new AudioPlayerForTexts($audio_uri);
            $html .= $audio_player->show($this->prefs->display_mode, false);
        }

        if ($this->prefs->assisted_learning) {
            if (!$this->is_long_text && empty($audio_uri)) {
                $audio_player = new AudioPlayerForTexts($audio_uri);
                $html .= $audio_player->show($this->prefs->display_mode, true);
            }

            // display assisted learning message
            $html .= <<<HTML_PHASE1_MSG
            <div id="alert-box-phase" class="alert alert-info alert-dismissible mt-2 show" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <h5 class="alert-heading">📖 Phase 1: Reading Comprehension</h5>
                <span class="small">
                    Focus on understanding the overall meaning. Try to guess unknown words from context before
                    clicking them for a definition or right-clicking for a translation.
                    <br><br>
                        Once finished, click
                        <kbd class="bg-success me-1">
                            Next <span class="bi bi-skip-end-circle-fill"></span>
                        </kbd>
                        to continue to the next phase. <strong>Tip</strong>: prefer a standard reading experience?
                        Disable the assisted learning 5-phase mode in
                        <a href="/preferences" class="alert-link">preferences</a>.
                </span>
            </div>
            HTML_PHASE1_MSG;
        }
        
        // display text
        $html .= '<div id="text">' . $this->text->text . '</div>';
        $html .= '</div>';
        return $html;
    } // end showText()

    /**
     * Constructs HTML code to show text in reader
     *
     * @param string $yt_id YouTube Id
     * @param string $reader_css CSS style to include in reader HTML
     * @return string
     */
    public function showVideo(string $yt_id, string $reader_css): string
    {
        $yt_id = $yt_id ? $yt_id : '';

        $html = '<div class="video-player">' .
                    '<div data-ytid="' . $yt_id . '" id="videoplayer"></div>' .
                '</div>';

        $html .= "<div id='text-container' class='overflow-auto m-0 my-1 p-2 z-1' data-type='video' data-IdText='"
            . $this->text->id . "' style='" . $reader_css . "'><div id='text' class='text-center'>";
        $xml = new SimpleXMLElement($this->text->text);

        for ($i=0; $i < sizeof($xml); $i++) {
            $start = (string)$xml->e[$i]->start;
            $dur = (string)$xml->e[$i]->duration;
            $text = html_entity_decode((string)$xml->e[$i]->text, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $html .= "<span data-start='$start' data-dur='$dur' >". $text .'</span>' . PHP_EOL;
        }
        
        $html .= '</div></div>';

        return $html;
    } // end showVideo()

    /**
     * Constructs HTML code to show an offline video
     *
     * @param string $reader_css CSS style to include in reader HTML
     * @return string html
     */
    public function showOfflineVideo(string $reader_css): string
    {
        $html = '<div id="offline-video-container" class="video-player bg-dark">' .
                    '<input id="video-file-input" type="file" name="video-file-input"
                        accept="video/mp4,video/ogg,video/webm" style="display: none;">' .
                    '<input id="subs-file-input" type="file" name="subs-file-input" accept=".srt"
                        style="display: none;">' .
                        '<video id="videoplayer" controls controlsList="nofullscreen nodownload noremoteplayback"
                            playsinline disablePictureInPicture>' .
                            '<source id="video-source" src=""/>'.
                            'Your browser does not support HTML5 video.' .
                        '</video>' .
                '</div>';

        $html .= '<div id="text-container" class="overflow-auto m-0 my-1 p-2 z-1" style="'
            . $reader_css
            . '"><div id="text" class="text-center"></div>'
            . '<div id="nosubs" class="d-flex justify-content-center align-items-center h-100">'
            . '<div class="text-muted">No video or subtitles loaded yet. Use the blue buttons above to load them.</div>'
            . '</div></div>';
        return $html;
    } // end showOfflineVideo()

    /**
     * Returns true if text has an external audio_uri
     *
     * @return boolean
     */
    public function hasExternalAudio(): bool
    {
        return !empty($this->text->audio_uri);
    } // end hasExternalAudio()
}
