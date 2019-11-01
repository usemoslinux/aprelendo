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

use Aprelendo\Includes\Classes\File;
use Aprelendo\Includes\Classes\PopularSources;
use Aprelendo\Includes\Classes\Url;
use Aprelendo\Includes\Classes\Language;
use Aprelendo\Includes\Classes\Conversion;

class Texts extends DBEntity {
    use Curl;

    protected $id          = 0;
    protected $lang_id     = 0;
    protected $title       = '';
    protected $author      = '';
    protected $text        = '';
    protected $audio_uri   = '';
    protected $source_uri  = '';
    protected $type        = 0;
    protected $nr_of_words = 0;
    protected $level       = 0;
    
    /**
    * Constructor
    * 
    * Sets 3 basic variables used to identify any text: $pdo, $user_id & lang_id
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        parent::__construct($pdo, $user_id);
        $this->lang_id = $lang_id;
        $this->table = 'texts';
    } // end __construct() 

    /**
     * Loads text record data
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$row) {
                throw new \Exception('There was an unexpected error trying to load record from texts table.');
            }

            $this->id         = $row['id']; 
            $this->user_id    = $row['user_id']; 
            $this->lang_id    = $row['lang_id']; 
            $this->title      = $row['title'];
            $this->author     = $row['author']; 
            $this->text       = $row['text']; 
            $this->audio_uri  = $row['audio_uri'];
            $this->source_uri = $row['source_uri'];
            $this->type       = $row['type'];
            $this->word_count = $row['word_count'];
            $this->level      = $row['level'];
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load record from texts table.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()
        
    /**
    * Adds a new text to the database
    *
    * @param string $title
    * @param string $author
    * @param string $text
    * @param string $source_url
    * @param string $audio_url
    * @param int $type
    * @return int
    */
    public function add(string $title, string $author, string $text, string $source_url, 
                        string $audio_url, int $type): int {
        $level = 0;
        $nr_of_words = 0;

        if (isset($text) && !empty($text))  {
            $level = $this->calculateDifficulty($text);
            $nr_of_words = $this->nr_of_words;
        }
        
        try {
            // add text to table
            $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `title`, `author`, 
                        `text`, `source_uri`, `type`, `word_count`, `level`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id,$this->lang_id, $title, $author, $text, 
                                      $source_url, $type, $nr_of_words, $level]);
            $insert_id = $this->pdo->lastInsertId();

            if ($stmt->rowCount() == 0 || $insert_id == 0) {
                throw new \Exception('There was an unexpected error trying to add record to texts table.');
            }

            // add entry to popularsources
            $pop_sources = new PopularSources($this->pdo);
            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);
            
            $pop_sources->add($lang->getName(), Url::getDomainName($source_url));

            return $insert_id;
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to add record to texts table.');
        } finally {
            $stmt = null;
        }
    } // end add()
            
    /**
    * Updates existing text in database
    *
    * @param int $id
    * @param string $title
    * @param string $author
    * @param string $text
    * @param string $source_url
    * @param string $audio_url
    * @param int $type
    * @return void
    */
    public function update(int $id, string $title, string $author, string $text, string $source_url, 
                           string $audio_url, int $type): void {
        try {
            $sql = "UPDATE `{$this->table}` 
                    SET `user_id`=?, `lang_id`=?, `title`=?, `author`=?, `text`=?, `source_uri`=?, `type`=? 
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $title, $author, $text, $source_url, $type, $id]);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to update record from texts table.');
        } finally {
            $stmt = null;
        }
    } // end update()
    
    /**
    * Deletes texts in database using ids as a parameter to select them
    *
    * @param string $ids JSON that identifies the texts to be deleted
    * @return void
    */
    public function delete(string $ids): void {
        try {
            $ids_array = json_decode($ids);
            $id_params = str_repeat("?,", count($ids_array)-1) . "?";
        
            $select_sql =  "SELECT `source_uri` 
                            FROM `{$this->table}` 
                            WHERE `id` IN ($id_params)";
            $stmt = $this->pdo->prepare($select_sql);
            $stmt->execute($ids_array);
            $uris = $stmt->fetchall();
            
            // delete entries from db
            $delete_sql =  "DELETE FROM `{$this->table}` 
                            WHERE `id` IN ($id_params)";
            $stmt = $this->pdo->prepare($delete_sql);
            $stmt->execute($ids_array);

            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to delete record from texts table.');
            }

            // delete audio (mp3, oggs) & source files (epubs, etc.)
            $pop_sources = new PopularSources($this->pdo);
            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);
            
            // delete associated file
            foreach ($uris as $key => $value) {
                if (!empty($value['source_uri']) && (strpos($value['source_uri'], '.epub') !== false)) {
                    $file = new File($value['source_uri']);
                    $file->delete();
                }
                
                $result = $pop_sources->update($lang->getName(), Url::getDomainName($value['source_uri']));
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to delete record from texts table.');
        } finally {
            $stmt = null;
        }
    } // end delete()
    
    /**
    * Archives texts in database using ids as a parameter to select them
    *
    * @param string $ids JSON that identifies the texts to be archived
    * @return void
    */
    public function archive(string $ids): void {
        try {
            $ids_array = json_decode($ids);
            $id_params = str_repeat("?,", count($ids_array)-1) . "?";
        
            $insert_sql =  "INSERT INTO `archived_texts`
                            SELECT *
                            FROM `{$this->table}` 
                            WHERE `id` IN ($id_params)";

            $stmt = $this->pdo->prepare($insert_sql);
            $stmt->execute($ids_array);
            
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to insert record into texts table.');
            }

            $delete_sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
            $stmt = $this->pdo->prepare($delete_sql);
            $stmt->execute($ids_array);

            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to delete record from archived texts table.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to archive text.');
        } finally {
            $stmt = null;
        }
    } // end archive()

    /**
     * Checks if text already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return boolean
     */
    public function exists(string $source_url): bool {
        try {
            if (empty($source_url)) {
                return false;
            }
    
            $sql = "SELECT COUNT(*)
                    FROM `{$this->table}`
                    WHERE `user_id` = ? AND `source_uri` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $source_url]);
            $num_rows = $stmt->fetchColumn(); 
    
            return $num_rows > 0;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end exists()
    
    /**
    * Counts the number of rows (i.e. texts) for a specific search
    * 
    * Used for pagination
    *
    * @param string $search_filter A string with the SQL statement to be used as a filter for the search
    * @param string $search_text
    * @return int
    */
    public function countSearchRows(string $search_filter, string $search_text): int {
        try {
            $search_text = '%' . $search_text . '%';
            
            if (empty($search_filter)) {
                $sql = "SELECT COUNT(`id`) FROM `{$this->table}` 
                        WHERE `user_id`=? 
                        AND `lang_id`=? AND `title` LIKE ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->user_id, $this->lang_id, $search_text]);
            } else {
                $sql = "SELECT COUNT(`id`) FROM `{$this->table}` 
                        WHERE `user_id`=? 
                        AND `lang_id`=? AND `type`=? AND `title` LIKE ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->user_id, $this->lang_id, $search_filter, $search_text]);
            }
            
            $total_rows = $stmt->fetchColumn();

            return (int)$total_rows;
        } catch (\PDOException $e) {
            return 0;
        } finally {
            $stmt = null;
        }
    } // end countSearchRows()
    
    /**
    * Counts the number of rows (i.e. texts) for the current user & language combination
    * It differs from countSearchRows in that this function does not apply any additional filter
    *
    * @return int
    */
    public function countAllRows(): int {
        try {
            $sql = "SELECT COUNT(`id`) FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $total_rows = $stmt->fetchColumn();

            return (int)$total_rows;
        } catch (\PDOException $e) {
            return 0;
        } finally {
            $stmt = null;
        }     
    } // end countAllRows()
    
    /**
    * Gets texts by using a search pattern ($search_text) and a filter ($search_filter).
    * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
    * Values are returned using a sort pattern ($sort_by)
    *
    * @param string $search_filter SQL statement specifying the filter to be used
    * @param string $search_text
    * @param int $offset
    * @param int $limit
    * @param int $sort_by Is converted to a string using buildSortSQL()
    * @return array
    */
    public function getSearch(string $search_filter, string $search_text, int $offset, 
                              int $limit, int $sort_by): array {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            $filter_sql = empty($search_filter) ? '' : 'AND `type` = :filter';
            
            $sql = "SELECT `id`, 
                            NULL, 
                            `title`, 
                            `author`, 
                            `source_uri`, 
                            `type`, 
                            `word_count`, 
                            `level`  
                    FROM `{$this->table}` 
                    WHERE `user_id` = :user_id 
                    AND `lang_id` = :lang_id 
                    AND `title` LIKE :search_str $filter_sql 
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindValue(':search_str', "%$search_text%");
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            if (!empty($filter_sql)) {
                $stmt->bindParam(':filter', $search_filter);
            }

            $stmt->execute();
            $result = $stmt->fetchall();

            if (!$result || empty($result)) {
                throw new \Exception('Oops! There are no texts meeting your search criteria.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end getSearch()
    
    /**
    * Gets all the texts for the current user & language combination
    * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
    * Values are returned using a sort pattern ($sort_by)
    *
    * @param int $offset
    * @param int $limit
    * @param int $sort_by Is converted to a string using buildSortSQL()
    * @return array
    */
    public function getAll(int $offset, int $limit, int $sort_by): array  {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            $sql = "SELECT `id`, 
                    NULL, 
                    `title`, 
                    `author`, 
                    `source_uri`, 
                    `type`, 
                    `word_count`, 
                    `level`  
                    FROM `{$this->table}` 
                    WHERE `user_id` = :user_id  
                    AND `lang_id` = :lang_id 
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchall();

            if (!$result || empty($result)) {
                throw new \Exception('Oops! There are no texts in your private library yet. Feel free to add one or access the shared texts section.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end getAll()
    
    /**
    * Determines if $text is valid XML code & extracts text from it
    *
    * @param string $xml 
    * @return string|boolean
    */
    public function extractFromXML(string $xml) {
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
    } // end extractFromXML()
    
    /**
    * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
    * to valid SQL strings
    *
    * @param int $sort_by
    * @return string
    */
    protected function buildSortSQL(int $sort_by): string {
        switch ($sort_by) {
            case '0': // new first
                return '`id` DESC';
                break;
            case '1': // old first
                return '`id`';
                break;
            default:
                return '';
                break;
        }
    } // end buildSortSQL
    
    /**
    * Calculates difficulty level of a given $text
    * 
    * Each freqlist table contains the most used words for that specific language, based on opensubtitles data (2018). 
    * This means there will be different records for "be, was, were", etc. This data was then filtered with MS Windows and 
    * LibreOffice (hunspell) spellcheckers, and entries with strange characters, numbers, names, etc. were all removed. 
    * From that filtered list, the % of use of each word was calculated. By adding them, it was possible to determine what 
    * percentage of a text a person can understand if he or she knows that word and all the words that appear before in the list. 
    * In other words, a frequency_index of 85 means that if a person knows that word and the previous ones, he or she will understand 
    * around 85% of any text. Each freqlist table includes words with a WordFreq index of up to 95. This was done to reduce table size
    * and increase speed.
    *
    * So, the higher the WordFreq index, the rarer the word will be. We arbitrarily determined that an index of >95 will mean that the 
    * word will only be known by advanced users. A word with an index between 85 and 95 will be known by intermediate users and a 
    * word with an index lower than 85 will be known by novice users.
    *
    * With this in mind, the algorithm goes as follows:
    * 
    * 1. Filter the corresponding freqlist table with words having an index <=85 (this will give all the words a beginner would know)
    * 
    * 2. Compare this with all the words in $text. The difference will be the total amount of $unknown_words.
    * 
    * 3. Divide that by the total amount of words in $text ($unknown_words / $total_words)
    * 
    * 4. This will give us an index representing the % of unknow words. If it's < 25%, meaning a beginner would understand at least 75% of 
    * the text, we can say the text has a "beginner" difficulty.
    * 
    * 5. Otherwise, re-test points 1-4, but this time with an unfiltered freqlist table (i.e. with words having a WordFreq <=95). In case
    * unknow words index is < 25%, tag the text as "intermediate", otherwise, tag it as "advanced".
    * 
    * @param string $text
    * @return int
    */
    private function calculateDifficulty(string $text = ''): int {
        $level_thresholds = array('85', '95'); // <=80: beginner; >80 & <=95: intermediate; >95: advanced
        $frequency_list_table = ''; // frequency list table name: should be something like frequency_list_ + ISO 639-1 (2 letter language) code
        
        $frequency_list = []; // array with all the words in the corresponding frequency list table 
        $words_in_text = []; // array with all the valid words in $text 
        $diff = []; // array with elements in the $words_in_text array not present in the $frequency_list array
        
        $total_words = 0; // number of valid words in $text
        $unknown_words = 0; // number of words in $diff
        $index = 0; // $unknown_words / $total_words
        
        $xml_text = ''; // used to check if $text parameter is XML code
        $accented_chars = ''; // holds list of accented characters that should be considered legal when counting words in a text
        
        $result = 0; // function return value
        $row = []; // array with all the rows from a successful SQL query

        // if $text is XML code (video transcript), extract text from XML string
        $xml_text = $this->extractFromXML($text);
        
        if ($xml_text != false) {
            $text = $xml_text;
        }

        // build array with words in text
        $text = stripcslashes(str_replace('\r\n', ' ', $text));
        // list of special characters in the supported languages (english, spanish, portuguese, french, german & italian)
        $accented_chars = 'àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœ';
        $this->nr_of_words = preg_match_all('/[A-Za-z' . $accented_chars . ']+/u', $text, $words_in_text);

        try {
            // get learning language ISO name
            $sql = "SELECT `name` 
                    FROM `languages` 
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->lang_id]);

            // build frequency list table name based on learning language name
            $row = $stmt->fetch(\PDO::FETCH_NUM);
            $frequency_list_table = 'frequency_list_' . $row[0];
            
            foreach ($level_thresholds as $threshold) {
                // build frequency list array for "beginner" level words (80%)
                $sql = "SELECT `word` 
                        FROM `$frequency_list_table` WHERE `frequency_index` <= ?";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$threshold]);

                while($row = $stmt->fetch(\PDO::FETCH_NUM)){
                    $frequency_list[] = $row[0];
                }
                
                // get total amount of words & how many words in the text don't appear in the frequency list
                //$diff = array_diff(array_map('strtolower', $words_in_text[0]), array_map('strtolower', $frequency_list));
                $diff = array_udiff($words_in_text[0], $frequency_list, 'strcasecmp');
                
                $total_words = sizeof($words_in_text[0]);
                $unknown_words = sizeof($diff);
                
                // $index is calculated as the relation between $unknown_words and $total_words
                // there's no need to remove duplicates from $diff 
                // because they won't be removed from $words_in_text either
                $index = $unknown_words / $total_words;
                if ($threshold === '85' && $index < 0.25) {
                    $result = 1; // beginner
                } elseif ($threshold === '95' && $index < 0.25) {
                    $result = 2; // intermediate
                } elseif ($threshold === '95' && $index > 0.24) {
                    $result = 3; // advanced
                }
            } 

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to calculate text difficulty level.');
        } finally {
            $stmt = null;
        }
    } // calculateDifficulty()

    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of lang_id
     */ 
    public function getLangId(): int
    {
        return $this->lang_id;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Get the value of author
     */ 
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Get the value of text
     */ 
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get the value of audio_uri
     */ 
    public function getAudioUri(): string
    {
        return $this->audio_uri;
    }

    /**
     * Get the value of source_uri
     */ 
    public function getSourceUri(): string
    {
        return $this->source_uri;
    }

    /**
     * Get the value of type
     */ 
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get the value of nr_of_words
     */ 
    public function getNrOfWords(): int
    {
        return $this->nr_of_words;
    }

    /**
     * Get the value of level
     */ 
    public function getLevel(): int
    {
        return $this->level;
    }
} 
    
?>