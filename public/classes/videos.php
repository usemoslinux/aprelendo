<?php 

require_once('connect.php');

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
        $this->table = 'texts';
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
        $transcript_xml = file_get_contents("https://video.google.com/timedtext?lang=$learning_lang&v=$youtube_id");
        $transcript_xml = array ('text' => $transcript_xml);
        
        $file = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=$youtube_id&key=" . YOUTUBE_API_KEY . "&part=snippet");
        $file = json_decode($file, true);
        $title = array('title' => $file['items'][0]['snippet']['title']);
        $author = array('author' => $file['items'][0]['snippet']['channelTitle']);

        $merged = array_merge($title, $author, $transcript_xml); 
        header('Content-Type: application/json');
        return json_encode($merged);
    }

    /**
     * Extract YouTube Id from a given URL
     *
     * @param string $url
     * @return string|boolean string representation of YT Id or false if $url has wrong format
     */
    public function extractYTId($url) {
        $is_yt_uri = strpos($url, 'https://www.youtube.com/watch?v=');
        $is_yt_uri = $is_yt_uri === 0 ? 0 : strpos($url, 'https://youtu.be/');

        if ($is_yt_uri === 0) {
            return substr($url, 32, 11);
        } else {
            $is_yt_uri =  url.lastIndexOf("https://youtu.be/");
            if ($is_yt_uri === 0) {
                return url.substring(17, 28);
            } else {
                return false;
            }
        }
    }

    public function getById($id) {
        $result = $this->con->query("SELECT * 
            FROM $this->table 
            WHERE textId='$id'");
        
        return $result ? $result->fetch_assoc() : false;
    }

}

?>