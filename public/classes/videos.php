<?php 

require_once('connect.php');

class Videos extends DBEntity {
    protected $learning_lang_id;
    protected $title;
    protected $author;
    protected $youtube_id;
    protected $source_url;
    protected $transcript_xml;

    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'texts';
    }

    public function fetchVideo($learning_lang, $youtube_id) {
        $transcript_xml = file_get_contents("https://video.google.com/timedtext?lang=$learning_lang&v=$youtube_id");
        $transcript_xml = array ('text' => $transcript_xml);
        
        $file = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=$youtube_id&key=AIzaSyCrLewIG56vdL5TN4ls4S4E64aRogUaiz0&part=snippet");
        $file = json_decode($file, true);
        $title = array('title' => $file['items'][0]['snippet']['title']);
        $author = array('author' => $file['items'][0]['snippet']['channelTitle']);

        $merged = array_merge($title, $author, $transcript_xml); 
        header('Content-Type: application/json');
        return json_encode($merged);
    }

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