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

use Aprelendo\Includes\Classes\Files;
use Aprelendo\Includes\Classes\PopularSources;
use Aprelendo\Includes\Classes\Url;

class Texts extends DBEntity {
    protected $learning_lang_id;
    protected $cols;
    protected $order_col;
    protected $nr_of_words;

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
            'nrofwords' => 'textNrOfWords',
            'level' => 'textLevel');
    }

    /**
     * Adds a new text to the database
     *
     * @param string $title
     * @param string $author
     * @param string $text
     * @param string $source_url
     * @param string $audio_url
     * @param integer $type
     * @return boolean
     */
    public function add($title, $author, $text, $source_url, $audio_url, $type) {
        // escape parameters
        $title = $this->con->real_escape_string($title);
        $author = $this->con->real_escape_string($author);
        $source_url = $this->con->real_escape_string($source_url);
        $text = $this->con->real_escape_string($text); // escape $text
        $audio_url = $this->con->real_escape_string($audio_url);
        $type = $this->con->real_escape_string($type);
        $level = 0;
        $nr_of_words = 0;
        
        if (isset($text) && !empty($text))  {
            $level = $this->calcTextLevel($text);
            $nr_of_words = $this->nr_of_words;
        }

        // add text to table
        $result = $this->con->query("INSERT INTO $this->table ({$this->cols['userid']}, {$this->cols['lgid']}, 
                {$this->cols['title']}, {$this->cols['author']}, {$this->cols['text']}, {$this->cols['sourceURI']}, 
                {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['nrofwords']}, {$this->cols['level']})
                VALUES ('$this->user_id', '$this->learning_lang_id', '$title', '$author', '$text', '$source_url', 
                '$audio_url', '$type', $nr_of_words, $level)");
        
        if ($result) {
            // add entry to popularsources
            $pop_sources = new PopularSources($this->con);
            $lang = new Language($this->con, $this->learning_lang_id, $this->user_id);
            $result = $pop_sources->add($lang->name, Url::getDomainName($source_url));
        }

        return $result;
    }

    /**
     * Updates existing text in database
     *
     * @param integer $id
     * @param string $title
     * @param string $author
     * @param string $text
     * @param string $source_url
     * @param string $audio_url
     * @param integer $type
     * @return boolean
     */
    public function update($id, $title, $author, $text, $source_url, $audio_url, $type) {
        // escape parameters
        $title = $this->con->real_escape_string($title);
        $author = $this->con->real_escape_string($author);
        $text = $this->con->real_escape_string($text);
        $source_url = $this->con->real_escape_string($source_url);
        $audio_url = $this->con->real_escape_string($audio_url);
        $type = $this->con->real_escape_string($type);

        $result = $this->con->query("UPDATE $this->table SET {$this->cols['userid']}='$this->user_id', {$this->cols['lgid']}='$this->learning_lang_id', 
                {$this->cols['title']}='$title', {$this->cols['author']}='$author', text='$text', {$this->cols['audioURI']}='$audio_url', 
                {$this->cols['sourceURI']}='$source_url', {$this->cols['type']}='$type' WHERE {$this->cols['id']}='$id'");

        return $result;
    }

    /**
     * Deletes texts in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return boolean
     */
    public function deleteByIds($ids) {
        $textIDs = $this->JSONtoCSV($ids);
        $audio_uri_col_name = $this->cols['audioURI'];
        $source_uri_col_name = $this->cols['sourceURI'];

        $selectsql = "SELECT $audio_uri_col_name, $source_uri_col_name FROM $this->table WHERE {$this->cols['id']} IN ($textIDs)";
        $deletesql = "DELETE FROM $this->table WHERE {$this->cols['id']} IN ($textIDs)";

        $result = $this->con->query($selectsql);

        if ($result) {
            $uris = $result->fetch_all();
        
            // delete entries from db
            $deletedfromdb = $this->con->query($deletesql);
            
            // delete audio (mp3, oggs) & source files (epubs, etc.)
            if ($deletedfromdb) {
                $file = new File();
                $pop_sources = new PopularSources($this->con);
                $lang = new Language($this->con, $this->learning_lang_id, $this->user_id);

                // delete associated file
                foreach ($uris as $key => $value) {
                    if (!empty($value[0]) && (strpos($value[0], '.ogg') !== false || strpos($value[0], '.mp3') !== false)) {
                        $file->delete($value[0]);
                    }
                    if (!empty($value[1]) && (strpos($value[1], '.epub') !== false)) {
                        $file->delete($value[1]);
                    }
                    
                    $result = $pop_sources->update($lang->name, Url::getDomainName($value[1]));
                }
            }
        }
        return $result;
    }

    /**
     * Archives texts in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be archived
     * @return boolean
     */
    public function archiveByIds($ids) {
        $textIDs = $this->JSONtoCSV($ids);

        $insertsql = "INSERT INTO archivedtexts (atextUserId, atextLgID, atextTitle, atextAuthor, atext, atextAudioURI, atextSourceURI, atextType)
            SELECT textUserId, textLgID, textTitle, textAuthor, text, textAudioURI, textSourceURI, textType 
            FROM texts WHERE textID IN ($textIDs)";
        $deletesql = "DELETE FROM texts WHERE textID IN ($textIDs)";
        
        if ($result = $this->con->query($insertsql)) {
            $result = $this->con->query($deletesql);
        }
        
        return $result;
    }

    /**
     * Counts the number of rows (i.e. texts) for a specific search
     * 
     * Used for pagination
     *
     * @param string $filter_sql A string with the SQL statement to be used as a filter for the search
     * @param string $search_text
     * @return integer|boolean
     */
    public function countRowsFromSearch($filter_sql, $search_text) {
        // escape parameters
        $filter_sql = $this->con->real_escape_string($filter_sql);
        $search_text = $this->con->real_escape_string($search_text);

        $result = $this->con->query("SELECT COUNT({$this->cols['id']}) FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' 
            AND {$this->cols['lgid']}='$this->learning_lang_id' $filter_sql AND {$this->cols['title']} LIKE '%$search_text%'");
        
        if ($result) {
            $row = $result->fetch_array(MYSQLI_NUM);
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    /**
     * Counts the number of rows (i.e. texts) for the current user & language combination
     * It differs from countRowsFromSearch in that this function does not apply any additional filter
     *
     * @return integer|boolean
     */
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

    /**
     * Gets texts by using a search pattern ($search_text) and a filter ($filter_sql).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $filter_sql SQL statement specifying the filter to be used
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getSearch($filter_sql, $search_text, $offset, $limit, $sort_by) {
        // escape parameters
        $filter_sql = $this->con->real_escape_string($filter_sql);
        $search_text = $this->con->real_escape_string($search_text);
        $offset = $this->con->real_escape_string($offset);
        $limit = $this->con->real_escape_string($limit);
        $sort_sql = $this->con->real_escape_string($this->getSortSQL($sort_by));

        $result = $this->con->query("SELECT {$this->cols['id']}, NULL, {$this->cols['title']}, {$this->cols['author']}, 
            {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['nrofwords']}, {$this->cols['level']} 
            FROM $this->table 
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' $filter_sql 
            AND {$this->cols['title']} LIKE '%$search_text%' ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    /**
     * Gets all the texts for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getAll($offset, $limit, $sort_by) {
        // escape parameters
        $offset = $this->con->real_escape_string($offset);
        $limit = $this->con->real_escape_string($limit);
        $sort_sql = $this->con->real_escape_string($this->getSortSQL($sort_by));

        $result = $this->con->query("SELECT {$this->cols['id']}, NULL, {$this->cols['title']}, {$this->cols['author']}, 
            {$this->cols['audioURI']}, {$this->cols['type']}, {$this->cols['nrofwords']}, {$this->cols['level']} 
            FROM $this->table
            WHERE {$this->cols['userid']}='$this->user_id' AND {$this->cols['lgid']}='$this->learning_lang_id' 
            ORDER BY $sort_sql LIMIT $offset, $limit");
        
        return $result ? $result->fetch_all() : false;
    }

    /**
     * Determines if $text is valid XML code & extracts text from it
     *
     * @param string $xml 
     * @return string|boolean
     */
    public function extractTextFromXML($xml) {
         // check if $text is valid XML (video transcript) or simple text
         libxml_use_internal_errors(true); // used to avoid raising Exceptions in case of error
         $xml = simplexml_load_string(html_entity_decode(stripslashes($xml)));
 
         if ($xml) {
             $temp_array = (array)$xml->text;
             $temp_array = array_splice($temp_array, 2, -1);
             return implode(" ", $temp_array); 
         } else {
             return false;
         }
    }
  
    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    public function getSortSQL($sort_by) {
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

    /**
     * Calculates difficulty level of a given $text (possible subject to improvements)
     * 
     * The algorithm is simple: 
     * 
     * 1. determine how many words in $texts are NOT present in the 5000 most frequently 
     * used words table of the current language.
     * 
     * 2. Divide that by the total amount of words in $text ($unknown_words / $total_words)
     * 
     * 3. This will give us a difficulty index of sorts that will allow us to classify texts by their difficulty level:
     * 
     * Advanced:             index > 0.25  (>25% of words in $text where not in the 5000 most frequently used table)
     * Intermediate: 0.15 >= index >= 0.25 (between 15% and 25% of words in $text where not in the 5000 most freq. used table)
     * Beginner:             index < 0.15  (<15% of words in $text where not in the 5000 most frequently used table)
     * 
     * @param string $text
     * @return integer|boolean
     */
    private function calcTextLevel($text) {
        // $text is XML code (video transcript), extract text from XML string
        $xml_text = $this->extractTextFromXML($text);

        if ($xml_text != false) {
            $text = $xml_text;
        } 

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

                switch (true) {
                    case ($index < 0.15): // beginner
                        return 1;
                        break;
                    case ($index >= 0.15 && $index <= 0.25): // intermediate
                        return 2;
                        break;
                    case ($index > 0.25): // advanced
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