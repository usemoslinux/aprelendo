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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\DBEntity;

class Videos extends DBEntity {
    protected $learning_lang_id;
    protected $title;
    protected $author;
    protected $youtube_id;
    protected $source_url;
    protected $transcript_xml;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify videos: $con, $user_id & learning_lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'sharedtexts';
    }

    /**
     * Fetches video from youtube
     *
     * @param string $learning_lang ISO representation of the video's language
     * @param string $youtube_id YouTube video ID
     * @return string JSON string representation of video's $title, $author and subtitles ($transcript_xml)
     *  
     */
    public function fetchVideo($learning_lang, $youtube_id) {
        header('Content-Type: application/json');
        $learning_lang = urlencode($learning_lang);
        $youtube_id = urlencode($youtube_id);
        $transcript_xml = @file_get_contents("https://video.google.com/timedtext?lang=$learning_lang&v=$youtube_id");
        if (!$transcript_xml) {
            throw new \Exception("Oops! There was a problem trying to fetch this video's subtitles.");
        } else {
            $transcript_xml = array ('text' => $transcript_xml);
        
            $file = @file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=$youtube_id&key=" . YOUTUBE_API_KEY . "&part=snippet");
            
            if (!$file) {
                throw new \Exception('Oops! There was a problem trying to fetch author & title information for this video.');
            } else {
                $file = json_decode($file, true);
                $title = array('title' => $file['items'][0]['snippet']['title']);
                $author = array('author' => $file['items'][0]['snippet']['channelTitle']);

                $result = array_merge($title, $author, $transcript_xml); 
            }
        }
        
        return json_encode($result);
    }

    /**
     * Extract YouTube Id from a given URL
     *
     * @param string $url
     * @return string|boolean string representation of YT Id or false if $url has wrong format
     */
    public function extractYTId($url) {
        // check if user copied the url by right-clicking the video (Google's recommended method)
        if (strpos($url, 'https://youtu.be/') === 0) {
            return substr($url, 17);
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
                            return substr($url_param, 2);
                        }
                    }
                }
            }
        }
    }

    public function getById($id) {
        $result = $this->con->query("SELECT * 
            FROM $this->table 
            WHERE stextId='$id'");
        
        return $result ? $result->fetch_assoc() : false;
    }

}

?>