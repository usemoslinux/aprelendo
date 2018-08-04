<?php 

require_once('connect.php');

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id, $learning_lang_id);
        $this->table = 'sharedtexts';
        $this->cols = array(
            'id' => 'stextId',
            'userid' => 'stextUserId', 
            'lgid' => 'stextLgId', 
            'title' => 'stextTitle', 
            'author' => 'stextAuthor', 
            'text' => 'stext', 
            'sourceURI' => 'stextSourceURI', 
            'audioURI' => 'stextAudioURI', 
            'type' => 'stextType', 
            'nrofwords' => 'stextNrOfWords',
            'level' => 'stextLevel');
    }
}


?>