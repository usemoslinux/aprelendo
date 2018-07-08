<?php 

require_once('connect.php');

class Words extends DBEntity {
    private $learning_lang_id;

    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'words';
    }

    public function add($word, $status, $isphrase) {
        $result = $this->con->query("INSERT INTO words (wordUserId, wordLgId, word, wordStatus, isPhrase)
            VALUES ('$this->user_id', '$this->learning_lang_id', '$word', $status, $isphrase) ON DUPLICATE KEY UPDATE
            wordUserId='$this->user_id', wordLgId=$this->learning_lang_id, word='$word', wordStatus=$status, isPhrase=$isphrase, wordModified=now()");

        return $result;
    }

    public function updateByName($words) {
        $csvwords = $this->convertJSONtoCSV($words);
        
        $result = $this->con->query("UPDATE words SET wordStatus=wordStatus-1, wordModified=now() 
            WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' AND word IN ('$csvwords') ");

        return $result;
    }

    public function deleteByName($word) {
        $word = $this->con->real_escape_string($word);
        $result = $this->con->query("DELETE FROM words WHERE word='$word'");

        return $result;
    }

    // ids must be in json format
    public function deleteByIds($ids) {
        $wordIDs = $this->convertJSONtoCSV($ids);
        $result = $this->con->query("DELETE FROM words WHERE wordID IN ($wordIDs)");

        return $result;
    }

    public function countRowsFromSearch($search_text) {
        $result = $this->con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' AND word LIKE '%$search_text%'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    public function countAllRows() {
        $result = $this->con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    public function getSearch($search_text, $offset, $limit) {
        $result = $this->con->query("SELECT wordID, word, wordStatus FROM words WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' AND word LIKE '%$search_text%' ORDER BY wordID DESC LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    public function getAll($offset, $limit) {
        $result = $this->con->query("SELECT wordID, word, wordStatus FROM words WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' ORDER BY wordID DESC LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }   
}

?>