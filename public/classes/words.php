<?php 

require_once('connect.php');

class Words extends DBEntity {
    private $learning_lang_id;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & learning_lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'words';
    }

    /**
     * Adds a new wod to the database
     *
     * @param string $word
     * @param integer $status
     * @param integer $isphrase It's an integer but it acts like a boolean (only uses 0 & 1)
     * @return boolean
     */
    public function add($word, $status, $isphrase) {
        $result = $this->con->query("INSERT INTO words (wordUserId, wordLgId, word, wordStatus, isPhrase)
            VALUES ('$this->user_id', '$this->learning_lang_id', '$word', $status, $isphrase) ON DUPLICATE KEY UPDATE
            wordUserId='$this->user_id', wordLgId=$this->learning_lang_id, word='$word', wordStatus=$status, isPhrase=$isphrase, wordModified=now()");

        return $result;
    }

    /**
     * Updates status of existing words in database
     * 
     * @param string $words JSON string containing all the words to update
     * @return boolean
     */
    public function updateByName($words) {
        $csvwords = $this->convertJSONtoCSV($words);
        
        $result = $this->con->query("UPDATE words SET wordStatus=wordStatus-1, wordModified=now() 
            WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' AND word IN ('$csvwords') ");

        return $result;
    }

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return boolean
     */
    public function deleteByName($word) {
        $word = $this->con->real_escape_string($word);
        $result = $this->con->query("DELETE FROM words WHERE word='$word'");

        return $result;
    }

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return boolean
     */
    public function deleteByIds($ids) {
        $wordIDs = $this->convertJSONtoCSV($ids);
        $result = $this->con->query("DELETE FROM words WHERE wordID IN ($wordIDs)");

        return $result;
    }

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return integer|boolean
     */
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

    /**
     * Counts the number of rows (i.e. words) for the current user & language combination
     * It differs from countRowsFromSearch in that this function does not apply any additional filter
     *
     * @return integer|boolean
     */
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

    /**
     * Gets words by using a search pattern ($search_text).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getSearch($search_text, $offset, $limit, $sort_by) {
        $sort_sql = $this->getSortSQL($sort_by);
        $result = $this->con->query("SELECT wordID, word, wordStatus 
            FROM words 
            WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' AND word LIKE '%$search_text%' 
            ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    /**
     * Gets all the words for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getAll($offset, $limit, $sort_by) {
        $sort_sql = $this->getSortSQL($sort_by);
        $result = $this->con->query("SELECT wordID, word, wordStatus 
            FROM words 
            WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' 
            ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }   

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    private function getSortSQL($sort_by) {
        switch ($sort_by) {
            case '0': // new first
                return 'wordID DESC';
                break;
            case '1': // old first
                return 'wordID';
                break;
            case '2': // learned first
                return 'wordStatus';
                break;
            case '3': // learning first
                return 'wordStatus DESC';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Exports words to a CSV file
     * 
     * It exports either the whole set of words corresponding to a user & language combination,
     * or the specific subset that results from applying additional filters (e.g. $search_text).
     * Results are ordered using $order_by.
     *
     * @param string $search_text
     * @param integer $order_by Is converted to a string using getSortSQL()
     * @return boolean
     */
    public function createCSVFile($search_text, $order_by) {
        $search_text = $this->con->real_escape_string($search_text);
        $sort_sql = $this->getSortSQL($order_by);
        $filter = !empty($search_text) ? "AND word LIKE '%$search_text%' " : '';
        $filter .= $order_by != '' ? "ORDER BY $sort_sql" : '';

        $result = $this->con->query("SELECT word 
            FROM words
            WHERE wordUserId='$this->user_id' AND wordLgId='$this->learning_lang_id' $filter");
        if ($result) {
            $num_fields = $this->con->field_count;
            $headers = array();

            for ($i = 0; $i < $num_fields; $i++) {
                $h = $result->fetch_field_direct($i);
                $headers[] = $h->name;
            }

            $fp = fopen('php://output', 'w');
            if ($fp) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $headers);
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    fputcsv($fp, array_values($row));
                }
                return true;
            }
        }
        return false;
    }
}

?>