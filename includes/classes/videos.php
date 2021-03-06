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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\DBEntity;
use Aprelendo\Includes\Classes\Curl;

class Videos extends DBEntity {
    private $lang_id        = 0;
    private $title          = '';
    private $author         = '';
    private $youtube_id     = '';
    private $source_url     = '';
    private $transcript_xml = '';

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify videos: $pdo, $user_id & lang_id
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        parent::__construct($pdo, $user_id);
        
        $this->lang_id = $lang_id;
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Fetches video from youtube
     *
     * @param string $lang ISO representation of the video's language
     * @param string $youtube_id YouTube video ID
     * @return string JSON string representation of video's $title, $author and subtitles ($transcript_xml)
     *  
     */
    public function fetchVideo(string $lang, string $youtube_id): string {
        header('Content-Type: application/json');
        $lang = urlencode($lang);
        $youtube_id = urlencode($youtube_id);
        
        $transcript_xml = Curl::getUrlContents("https://video.google.com/timedtext?lang=$lang&v=$youtube_id");

        if (empty($transcript_xml)) {
            throw new \Exception("Oops! There was a problem trying to fetch this video's subtitles.");
        } else {
            $transcript_xml = array ('text' => $transcript_xml);
        
            $file = Curl::getUrlContents("https://www.googleapis.com/youtube/v3/videos?id=$youtube_id&key=" . YOUTUBE_API_KEY . "&part=snippet");
            $file = json_decode($file, true);

            if (isset($file['error']) && !empty($file['error'])) {
                throw new \Exception('Oops! There was a problem trying to fetch author & title information for this video.');
            } else {
                if (count($file['items']) == 0) {
                    throw new \Exception('Oops! There are no YouTube videos with this URL. Check and try again.');
                }

                $title = array('title' => $file['items'][0]['snippet']['title']);
                $author = array('author' => $file['items'][0]['snippet']['channelTitle']);

                $result = array_merge($title, $author, $transcript_xml); 
            }
        }
        
        return json_encode($result);
    } // end fetchVideo()

    /**
     * Extract YouTube Id from a given URL
     *
     * @param string $url
     * @return string string representation of YT Id or false if $url has wrong format
     */
    public function extractYTId(string $url): string {
        // check if user copied the url by right-clicking the video (Google's recommended method)
        $result = '';

        if (strpos($url, 'https://youtu.be/') === 0) {
            $result = substr($url, 17);
            if ($result === false) {
                throw new \Exception('Malformed YouTube link');
            }
        } else {
            // check if user copied the url directly from the url bar (alternative method)
            $yt_urls = array('https://www.youtube.com/watch',
                'https://m.youtube.com/watch');

            $url_split = explode('?', $url);
            $url_params =  explode('&', $url_split[1]);
            
            // check if it's a valid youtube URL
            foreach ($yt_urls as $yt_url) {
                if (strpos($url_split[0], $yt_url) === 0) {
                    // extract youtube video id
                    foreach ($url_params as $url_param) {
                        if(strpos($url_param, 'v=') === 0) {
                            $result = substr($url_param, 2);
                            break;
                        } else {
                            throw new \Exception('Malformed YouTube link');
                        }
                    }
                }
            }
        }
        return $result;
    } // end extractYTId()

    /**
     * Loads video record data by Id
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void {
        try {
            $sql = "SELECT * 
                    FROM `{$this->table}` 
                    WHERE `id`=?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                $this->id             = $row['id']; 
                $this->lang_id        = $row['lang_id']; 
                $this->author         = $row['author']; 
                $this->author         = $row['author']; 
                $this->source_url     = $row['source_uri']; 
                $this->transcript_xml = $row['text']; 
                $this->youtube_id     = $this->extractYTId($this->source_url);
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load record from texts table.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Get the value of lang_id
     */ 
    public function getLangId(): int
    {
        return $this->lang_id;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of author
     */ 
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Get the value of youtube_id
     */ 
    public function getYoutubeId(): string
    {
        return $this->youtube_id;
    }

    /**
     * Get the value of source_url
     */ 
    public function getSourceUrl(): string
    {
        return $this->source_url;
    }

    /**
     * Get the value of transcript_xml
     */ 
    public function getTranscriptXml(): string
    {
        return $this->transcript_xml;
    }
}

?>