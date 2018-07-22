<?php 

require_once('connect.php');

class Texts extends DBEntity {
    protected $learning_lang_id;
    protected $cols;
    protected $order_col;

    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        
        $this->learning_lang_id = $learning_lang_id;
        
        $this->table = 'texts';
        $this->cols = array(
            'id' => 'textId',
            'userid' => 'textUserId', 
            'lgid' => 'textLgId', 
            'title' => 'textTitle', 
            'author' => 'textAuthor', 
            'text' => 'text', 
            'sourceURI' => 'textSourceURI', 
            'audioURI' => 'textAudioURI', 
            'type' => 'textType', 
            'isshared' => 'textIsShared', 
            'likes' => 'textLikes');
    }

    public function add($title, $author, $text, $source_url, $audio_url, $type) {
        $result = $this->con->query("INSERT INTO $this->table ({$this->cols['userid']}, {$this->cols['lgid']}, 
                {$this->cols['title']}, {$this->cols['author']}, {$this->cols['text']}, {$this->cols['sourceURI']}, 
                {$this->cols['audioURI']}, {$this->cols['type']})
                VALUES ('$this->user_id', '$this->learning_lang_id', '$title', '$author', '$text', '$source_url', '$audio_url', '$type')");

        return $result;
    }

    public function update($id, $title, $author, $text, $source_url, $audio_url, $type) {
        $result = $this->con->query("UPDATE $this->table SET {$this->cols['userid']}='$this->user_id', {$this->cols['lgid']}='$this->learning_lang_id', 
                {$this->cols['title']}='$title', {$this->cols['author']}='$author', text='$text', {$this->cols['audioURI']}='$audio_url', 
                {$this->cols['sourceURI']}='$source_url', {$this->cols['type']}='$type' WHERE {$this->cols['id']}='$id'");

        return $result;
    }

    // $ids must be in json format
    public function deleteByIds($ids) {
        $textIDs = $this->convertJSONtoCSV($ids);

        $selectsql = "SELECT {$this->cols['audioURI']} FROM $this->table WHERE {$this->cols['id']} IN ($textIDs)";
        $deletesql = "DELETE FROM $this->table WHERE {$this->cols['id']} IN ($textIDs)";

        $result = $this->con->query($selectsql);
        $error = $this->con->error;

        if ($result) {
            $audiouris = $result->fetch_all();
        
            // delete entries from db
            $deletedfromdb = $this->con->query($deletesql);
            
            // delete audio files
            if ($deletedfromdb) {
                // check if there is an audio file associated to this text and store its URI
                foreach ($audiouris as $key => $value) {
                    $filename = PRIVATE_PATH . 'uploads/' . $audiouris[$key][0];
                    if (is_file($filename) && file_exists($filename)) {
                        unlink($filename);
                    }
                }
            }
        }
        return $result;
    }

    // ids must be in json format
    public function archiveByIds($ids) {
        $textIDs = $this->convertJSONtoCSV($ids);

        $insertsql = "INSERT INTO archivedtexts (atextUserId, atextLgID, atextTitle, atextAuthor, atext, atextAudioURI, atextSourceURI, atextType, atextIsShared, atextLikes)
            SELECT textUserId, textLgID, textTitle, textAuthor, text, textAudioURI, textSourceURI, textType, textIsShared, textLikes
            FROM texts WHERE textID IN ($textIDs)";
        $deletesql = "DELETE FROM texts WHERE textID IN ($textIDs)";
        
        if ($result = $this->con->query($insertsql)) {
            $result = $this->con->query($deletesql);
        }
        
        return $result;
    }

    public function countRowsFromSearch($filter_sql, $search_text) {
        $result = $this->con->query("SELECT COUNT({$this->cols['id']}) FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' 
            AND {$this->cols['lgid']}='$this->learning_lang_id' $filter_sql AND {$this->cols['title']} LIKE '%$search_text%'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    public function countAllRows() {
        $result = $this->con->query("SELECT COUNT({$this->cols['id']}) FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    public function getSearch($filter_sql, $search_text, $offset, $limit, $sort_by) {
        $sort_sql = $this->GetSortSQL($sort_by);
        $result = $this->con->query("SELECT {$this->cols['id']}, {$this->cols['title']}, {$this->cols['author']}, {$this->cols['type']} 
            FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' $filter_sql 
            AND {$this->cols['title']} LIKE '%$search_text%' ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    public function getAll($offset, $limit, $sort_by) {
        $sort_sql = $this->GetSortSQL($sort_by);
        $result = $this->con->query("SELECT {$this->cols['id']}, {$this->cols['title']}, {$this->cols['author']}, {$this->cols['type']} 
            FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' 
            ORDER BY $sort_sql LIMIT $offset, $limit");
        
        return $result ? $result->fetch_all() : false;
    }

    public function getAllById($id, $sort_by) {
        $sort_sql = $this->GetSortSQL($sort_by);
        $result = $this->con->query("SELECT * 
            FROM $this->table 
            WHERE {$this->cols['id']}='$id'
            ORDER BY $sort_sql");
        
        return $result ? $result->fetch_all() : false;
    }

    private function GetSortSQL($sort_by) {
        switch ($sort_by) {
            case '0': // new first
                return $this->cols['id'] . ' DESC';
                break;
            case '1': // old first
                return $this->cols['id'];
                break;
            default:
                return '';
                break;
        }
        
    }
}

?>