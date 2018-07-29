<?php 

require_once('connect.php');

class Texts extends DBEntity {
    protected $learning_lang_id;
    protected $cols;
    protected $order_col;
    protected $nr_of_words;

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
            'likes' => 'textLikes',
            'nrofwords' => 'textNrOfWords',
            'level' => 'textLevel');
    }

    public function add($title, $author, $text, $source_url, $audio_url, $type) {
        $level = $this->calcTextLevel($text);
        $nr_of_words = $this->nr_of_words;

        // $level = 'A1';
        
        $result = $this->con->query("INSERT INTO $this->table ({$this->cols['userid']}, {$this->cols['lgid']}, 
                {$this->cols['title']}, {$this->cols['author']}, {$this->cols['text']}, {$this->cols['sourceURI']}, 
                {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['nrofwords']}, {$this->cols['level']})
                VALUES ('$this->user_id', '$this->learning_lang_id', '$title', '$author', '$text', '$source_url', 
                '$audio_url', '$type', '$nr_of_words', '$level')");

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
                    } else {
                        throw new Exception('There was an error deleting the associated audio file.');
                        log_error("Error: removing audio file $filename");
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
        $sort_sql = $this->getSortSQL($sort_by);
        $result = $this->con->query("SELECT {$this->cols['id']}, {$this->cols['title']}, {$this->cols['author']}, 
            {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['isshared']}, {$this->cols['likes']},
            {$this->cols['nrofwords']}, {$this->cols['level']} 
            FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' $filter_sql 
            AND {$this->cols['title']} LIKE '%$search_text%' ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    public function getAll($offset, $limit, $sort_by) {
        $sort_sql = $this->getSortSQL($sort_by);
        $result = $this->con->query("SELECT {$this->cols['id']}, {$this->cols['title']}, {$this->cols['author']}, 
            {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['isshared']}, {$this->cols['likes']},
            {$this->cols['nrofwords']}, {$this->cols['level']} 
            FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' 
            ORDER BY $sort_sql LIMIT $offset, $limit");
        
        return $result ? $result->fetch_all() : false;
    }

    public function getAllById($id, $sort_by) {
        $sort_sql = $this->getSortSQL($sort_by);
        $result = $this->con->query("SELECT * 
            FROM $this->table 
            WHERE {$this->cols['id']}='$id'
            ORDER BY $sort_sql");
        
        return $result ? $result->fetch_all() : false;
    }
    
    private function getSortSQL($sort_by) {
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

    private function calcTextLevel($text) {
        // get learning language ISO name
        $result = $this->con->query("SELECT LgName 
            FROM languages 
            WHERE LgID={$this->learning_lang_id}");
        
        if ($result) {
            // build frequency list table name based on learning language name
            $row = $result->fetch_array();
            $frequency_list_table = 'frequencylist_' . $row[0];

            // build frequency list array for the corresponding language
            $result = $this->con->query("SELECT freqWord 
                FROM $frequency_list_table ORDER BY freq LIMIT 5000");

            if ($result) {
                $frequency_list = array();
                while($row = $result->fetch_array()){
                    $frequency_list[] = $row[0];
                }

                // build array with words in text
                $text = str_replace('\r\n', '', $text);
                $this->nr_of_words = preg_match_all('/\w+/u', $text, $words_in_text);

                // get total amount of words & how many words in the text don't appear in the frequency list
                $diff = array_diff(array_map('strtolower', $words_in_text[0]), array_map('strtolower', $frequency_list));
                $total_words = sizeof($words_in_text[0]);
                $unknown_words = sizeof($diff);

                $index = $unknown_words / $total_words;

                // if index > 25% => level = proficient; if 25% >= index >=15% => level = intermediate; if index < 15% => level = beginner
                switch (true) {
                    case ($index < 0.15):
                        return 1;
                        break;
                    case ($index >= 0.15 && $index <= 0.25):
                        return 2;
                        break;
                    case ($index > 0.25):
                        return 3;
                        break;
                    default:
                        break;
                }
            } else {
                return false;
            } 
        } else {
            return false;
        }
    }
}

?>