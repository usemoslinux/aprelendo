<?php 

require_once('connect.php');

class ArchivedTexts extends Texts
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
        $this->table = 'archivedtexts';
        $this->cols = array(
            'id' => 'atextId',
            'userid' => 'atextUserId', 
            'lgid' => 'atextLgId', 
            'title' => 'atextTitle', 
            'author' => 'atextAuthor', 
            'text' => 'atext', 
            'sourceURI' => 'atextSourceURI', 
            'audioURI' => 'atextAudioURI', 
            'type' => 'atextType', 
            'isshared' => 'atextIsShared', 
            'likes' => 'atextLikes',
            'nrofwords' => 'atextNrOfWords',
            'level' => 'atextLevel');
    }

    /**
     * Unarchives text by using their $ids
     *
     * @param string $ids JSON with text ids to unarchive
     * @return boolean
     */
    public function unarchiveByIds($ids) {
        $textIDs = $this->convertJSONtoCSV($ids);

        $insertsql = "INSERT INTO texts (textUserId, textLgID, textTitle, textAuthor, text, textAudioURI, textSourceURI, textType, textIsShared, textLikes)
                SELECT atextUserId, atextLgID, atextTitle, atextAuthor, atext, atextAudioURI, atextSourceURI, atextType, atextIsShared, atextLikes 
                FROM archivedtexts WHERE atextID IN ($textIDs)";
        $deletesql = "DELETE FROM archivedtexts WHERE atextID IN ($textIDs)";
        
        if ($result = $this->con->query($insertsql)) {
            $result = $this->con->query($deletesql);
        }
        
        return $result;
    }
}


?>