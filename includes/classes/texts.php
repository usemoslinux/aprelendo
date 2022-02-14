<?php 
/**
* Copyright (C) 2019 Pablo Castagnino
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

class Texts extends DBEntity {
    protected $id            = 0;
    protected $lang_id       = 0;
    protected $title         = '';
    protected $author        = '';
    protected $text          = '';
    protected $audio_uri     = '';
    protected $source_uri    = '';
    protected $type          = 0;
    protected $nr_of_words   = 0;
    protected $level         = 0;
    protected $date_created  = '';
    
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

            $this->id            = $row['id']; 
            $this->user_id       = $row['user_id']; 
            $this->lang_id       = $row['lang_id']; 
            $this->title         = $row['title'];
            $this->author        = $row['author']; 
            $this->text          = $row['text']; 
            $this->audio_uri     = $row['audio_uri'];
            $this->source_uri    = $row['source_uri'];
            $this->type          = $row['type'];
            $this->word_count    = $row['word_count'];
            $this->level         = $row['level'];
            $this->date_created  = $row['date_created'];
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
                        string $audio_url, int $type, int $level): int {

        // get language iso
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecord($this->lang_id);
        $lang_iso = $lang->getName();

        // if $text is XML code (video transcript), extract text from XML string
        $xml_text = $this->extractFromXML($text);

        // count words in text
        $nr_of_words = ($xml_text != false) ? preg_match_all('/\w+/u', $xml_text, $words) : preg_match_all('/\w+/u', $text, $words);

        // fix string casings before saving
        if (mb_strtoupper($title, 'UTF-8') == $title) {
            // don't allow all upper case titles
            $title =  mb_strtoupper(mb_substr($title, 0, 1)) . mb_strtolower(mb_substr($title, 1));
        }

        $author =  mb_convert_case($author, MB_CASE_TITLE, 'UTF-8');
        
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
            $pop_sources->add($lang_iso, Url::getDomainName($source_url));

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
            $uris = $stmt->fetchall(\PDO::FETCH_ASSOC);
            
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
    
            // check if source_url exists in 'texts' or 'archived_texts' table
            $sql = "SELECT
                    (SELECT COUNT(*) FROM `{$this->table}` WHERE `user_id` = ? AND `source_uri` = ?) +
                    (SELECT COUNT(*) FROM `archived_texts` WHERE `user_id` = ? AND `source_uri` = ?)
                    AS SumCount";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $source_url, $this->user_id, $source_url]);
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
    * @param string $filter_type A string with the SQL statement to be used as a filter for the search
    * @param string $search_text
    * @return int
    */
    public function countSearchRows(int $filter_type, int $filter_level, string $search_text): int {
        try {
            $search_text = '%' . $search_text . '%';
            $filter_type_sql = $filter_type == 0 ? 'AND `type`>=?' : 'AND `type`=?';
            $filter_level_sql = $filter_level == 0 ? 'AND `level`>=?' : 'AND `level`=?';
            
            $sql = "SELECT COUNT(`id`) FROM `{$this->table}` 
                    WHERE `user_id`=? 
                    AND `lang_id`=? $filter_level_sql $filter_type_sql AND `title` LIKE ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $filter_level, $filter_type, $search_text]);
        
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
    public function countAllRows(int $filter_level = 0): int {
        try {
            $filter_level_sql = $filter_level == 0 ? 'AND `level`>=?' : 'AND `level`=?';
            $sql = "SELECT COUNT(`id`) FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=? $filter_level_sql";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $filter_level]);
            $total_rows = $stmt->fetchColumn();

            return (int)$total_rows;
        } catch (\PDOException $e) {
            return 0;
        } finally {
            $stmt = null;
        }     
    } // end countAllRows()
    
    /**
    * Gets texts by using a search pattern ($search_text) and a filter ($filter_type + $filter_level).
    * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
    * Values are returned using a sort pattern ($sort_by)
    *
    * @param int $filter_type: 0 = All; 1 = Articles; 2 = Conversations; 3 = Letters; 4 = Lyrics; 6 = Ebooks; 7 = Others
    * @param int $filter_level: 0 = All; 1 = Beginner; 2 = Intermediate; 3 = Advanced 
    * @param string $search_text
    * @param int $offset
    * @param int $limit
    * @param int $sort_by Is converted to a string using buildSortSQL()
    * @return array
    */
    public function getSearch(int $filter_type, int $filter_level, string $search_text, int $offset, 
                              int $limit, int $sort_by): array {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            $filter_type_sql = $filter_type == 0 ? 'AND `type` >= :filter_type' : 'AND `type` = :filter_type';
            $filter_level_sql = $filter_level == 0 ? 'AND `level` >= :filter_level' : 'AND `level` = :filter_level';
            
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
                    $filter_level_sql 
                    $filter_type_sql 
                    AND `title` LIKE :search_str  
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindParam(':filter_level', $filter_level, \PDO::PARAM_INT);
            $stmt->bindParam(':filter_type', $filter_type, \PDO::PARAM_INT);
            $stmt->bindValue(':search_str', "%$search_text%");
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
    public function getAll(int $filter_level, int $offset, int $limit, int $sort_by): array  {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            $filter_level_sql = $filter_level == 0 ? 'AND `level`>= :level' : 'AND `level` = :level';
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
                    $filter_level_sql
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':level', $filter_level, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result || empty($result)) {
                throw new \Exception('Oops! There are no texts in your private library yet. Feel free to add one or access the <a href="sharedtexts.php">shared texts</a> section.');
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
        $xml = (array)simplexml_load_string(html_entity_decode(stripslashes($xml)));
        
        return array_key_exists('text', $xml) ? implode(" ", $xml['text']) : false;
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
    * In other words, a frequency_index of 80 means that if a person knows that word and the previous ones, he or she will understand 
    * around 80% of any text. Each freqlist table includes words with a WordFreq index of up to 96 (around 10k words). 
    * This was done to reduce table size and increase speed.
    *
    * If Score < 60, text difficulty is set to "beginner".
    * If Score < 75, text difficulty is set to "intermediate".
    * Else, text difficulty is set to "advanced".
    * 
    * @param string $text
    * @return int
    */
    public function calculateDifficulty(string $text = ''): int {
        try {
            $frequency_list_table = '';     // frequency list table name: should be something like frequency_list_en 
            $frequency_list_words = [];     // array with all the words in the corresponding frequency list table 
            $frequency_list_indexes = [];   // array with all the scores of the words in $frequency_list_words
            $words_in_text = [];            // array with all the valid words in $text
            $sentences_in_text = [];        // array with all sentences in $text (only used to calculate $nr_of_words)
            $nr_of_words = 0;               // number of words in $text
            $nr_of_sentences = 0;           // number of sentences in $text
            $nr_of_difficult_words = 0;     // number of words in $words_in_text that don't appear in $frequency_list
            $per_difficult_words = 0;       // percentage of words in $text that are difficult
            $score = 0;                     // readability score of $text
            $xml_text = '';                 // used to check if $text parameter is XML code
            $lang_iso = '';                 // two letter long iso code of the text's language

            // if $text is XML code (video transcript), extract text from XML string
            $xml_text = $this->extractFromXML($text);
            
            if ($xml_text != false) {
                $text = $xml_text;
            }

            // get learning language ISO name
            $sql = "SELECT `name` 
                    FROM `languages` 
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->lang_id]);

            // build frequency list table name based on learning language name
            $row = $stmt->fetch(\PDO::FETCH_NUM);
            $lang_iso = $row[0];
            $frequency_list_table = 'frequency_list_' . $lang_iso;
            
            // build frequency list array (around 10.000 words)
            $sql = "SELECT `word`, `frequency_index`  
                    FROM `$frequency_list_table`";
            // "WHERE `frequency_index` < 97" is not necessary as db is optimized to only contain frequecy_index values < 97

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $frequency_list_words[] = $row['word'];
                $frequency_list_indexes[] = $row['frequency_index'];
            }    

            // if there is no frequency list for this language, return unknown level for text
            if (empty($frequency_list_words)) {
                return 0;
            }

            // calculate nr. of words & nr. of sentences in text
            $nr_of_words = $this->countVocabTokens($lang_iso, $text, $words_in_text);
            $nr_of_sentences = preg_match_all("/([?!.] \p{L}|^\s*?\p{L})/um", $text . ' ', $sentences_in_text);

            // check how many words in the frequency list where found in the "tokenized" list of words of the text
            // and calculate score
            $words_found = array_uintersect($words_in_text[0], $frequency_list_words, 'strcasecmp');
            
            foreach ($words_found as $word_found) {
                $word_index = array_search(strtolower($word_found), $frequency_list_words);
                $score += $frequency_list_indexes[$word_index];
            }
            
            $nr_of_unknown_words = sizeof($words_in_text[0]) - sizeof($words_found);
            $score = (0.6 * ($score + $nr_of_unknown_words * 100 ) / $nr_of_words) + (2 * $nr_of_words / $nr_of_sentences);

            if ($score < 60) {
                $result = 1; // beginner
            } elseif ($score < 75) {
                $result = 2; // intermediate
            } else {
                $result = 3; // advanced
            }
            
            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to calculate text difficulty level.');
        } finally {
            $stmt = null;
        }
    } // end calculateDifficulty()

    /**
     * Calculates the nr. of words in a text
     *
     * @param string $lang_iso
     * @param string $text
     * @param array $words_in_text
     * @return integer
     */
    private function countVocabTokens(string $lang_iso, string $text, ?array &$words_in_text): int {
        /*
            explanation of regex to extract list of words from the text
            
            1st part: select all words including word characters except for those having an apostrophe or single
            quote in the middle, abbreviations (USA or U.S.A.) and words starting with a capital letter
            (?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?!\p{Lu})\b(\p{L}+)\b(?!['’][\p{L}])
            
            ignore contractions before and after apostrophes or single quotes (often used as apostrophes)
            (?<![\p{L}]['’])                           >>>>> ignore contractions before apostrophes or single quotes (ISN't)
            (?!['’][\p{L}])                            >>>>> ignore contractions after apostrophes or single quotes (isn'T)
            
            (?![A-Z]{2,})                              >>>>> ignore abbreviations (EU, USA, etc.)
            (?![a-zA-Z]\.){2,}                         >>>>> ignore abbreviations (E.U, U.S.A., e.g., i.e., etc.)
            (?!\p{Lu})                                 >>>>> ignore words starting with a capital letter
            \b(\p{L}+)\b                               >>>>> select only words word including unicode characters (ignore numbers, etc.)
            
            2nd part: select all words that start with a capital letter which are at the beginning of a line or sentence. Ignore all
            words starting with a capital letter in the middle of a sentence (e.g. names of people or places)
            (?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?<=^|[\.\!\?]\s)(?<![\p{L}]['’])\b(\p{L}+)\b(?!['’][\p{L}])
            
            (?<![\p{L}]['’])                           >>>>> ignore contractions before apostrophes or single quotes (ISN't)
            (?!['’][\p{L}])                            >>>>> ignore contractions after apostrophes or single quotes (isn'T)
            
            (?![A-Z]{2,})                              >>>>> ignore abbreviations (EU, USA, etc.)
            (?![a-zA-Z]\.){2,}                         >>>>> ignore abbreviations (E.U, U.S.A., e.g., i.e., etc.)
            (?<=^|[\.\!\?]\s)                          >>>>> only include words starting a line or a sentence
            \b(\p{L}+)\b                               >>>>> select only words word including unicode characters (ignore numbers, etc.)
            
            Note: it is important to include the /u (unicode) and /m (multiline) flags. Unicode, so that word selection works as
            expected for any language. Multiline because otherwise the string would be considered a very long string instead
            of one composed of multiple lines and the beginning of line selector (^) would not work correctly.
            
            Note 2: for German we need to use a special regex string as all nouns are capitalized, not only names of people or places.
        */
        if ($lang_iso == 'de') {
            $regex_word_filter = "/(?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}\b(\p{L}+)\b(?!['’][\p{L}])/um";
        } else {
            $regex_word_filter = "/(?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?!\p{Lu})\b(\p{L}+)\b(?!['’][\p{L}])|(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?<=^|[\.\!\?]\s)(?<![\p{L}]['’])\b(\p{L}+)\b(?!['’][\p{L}])/um";
        }
        
        return preg_match_all($regex_word_filter, $text, $words_in_text);
    } // end countVocabTokens()

    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
    } // end getId()

    /**
     * Get the value of lang_id
     */ 
    public function getLangId(): int
    {
        return $this->lang_id;
    } // end getLangId()

    /**
     * Get the value of title
     */ 
    public function getTitle(): string
    {
        return $this->title;
    } // end getTitle()


    /**
     * Get the value of author
     */ 
    public function getAuthor(): string
    {
        return $this->author;
    } // getAuthor()

    /**
     * Get the value of text
     */ 
    public function getText(): string
    {
        return $this->text;
    } // end getText()

    /**
     * Get the value of audio_uri
     */ 
    public function getAudioUri(): string
    {
        return $this->audio_uri;
    } // getAudioUri()

    /**
     * Get the value of source_uri
     */ 
    public function getSourceUri(): string
    {
        return $this->source_uri;
    } // getSourceUri()

    /**
     * Get the value of type
     */ 
    public function getType(): int
    {
        return $this->type;
    } // getType()

    /**
     * Get the value of nr_of_words
     */ 
    public function getNrOfWords(): int
    {
        return $this->nr_of_words;
    } // end getNrOfWords()

    /**
     * Get the value of level
     */ 
    public function getLevel(): int
    {
        return $this->level;
    } // getLevel()

    /**
     * Get the value of date_created
     */ 
    public function getDateCreated(): string
    {
        return $this->date_created;
    } // end getDateCreated()
} 
    
?>