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
use Aprelendo\Includes\Classes\SearchTextsParameters;
use Aprelendo\Includes\Classes\TextsUtilities;
use Aprelendo\Includes\Classes\AprelendoException;

class Texts extends DBEntity
{
    protected $id            = 0;
    protected $lang_id       = 0;
    protected $title         = '';
    protected $author        = '';
    protected $text          = '';
    protected $audio_uri     = '';
    protected $source_uri    = '';
    protected $type          = 0;
    protected $word_count    = 0;
    protected $level         = 0;
    protected $date_created  = '';
    protected $text_pos      = '';
    protected $audio_pos     = '';
    
    /**
    * Constructor
    *
    * Sets 3 basic variables used to identify any text: $pdo, $user_id & lang_id
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
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
    public function loadRecord(int $id): void
    {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$row) {
                throw new AprelendoException('Error loading record from texts table.');
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
            $this->text_pos      = $row['text_pos'];
            $this->audio_pos     = $row['audio_pos'];
        } catch (\PDOException $e) {
            throw new AprelendoException('Error loading record from texts table.');
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
    public function add(
        string $title,
        string $author,
        string $text,
        string $source_url,
        string $audio_url,
        int $type,
        int $level
        ): int {

        // get language iso
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecord($this->lang_id);
        $lang_iso = $lang->getName();

        // if $text is XML code (video transcript), extract text from XML string
        $xml_text = TextsUtilities::extractFromXML($text);

        // count words in text
        $word_count = ($xml_text !== false)
            ? preg_match_all('/\w+/u', $xml_text, $words)
            : preg_match_all('/\w+/u', $text, $words);

        // fix string casings before saving
        if (mb_strtoupper($title, 'UTF-8') == $title) {
            // don't allow all upper case titles
            $title =  mb_strtoupper(mb_substr($title, 0, 1)) . mb_strtolower(mb_substr($title, 1));
        }

        $author = TextsUtilities::formatAuthorCase($author);
        
        try {
            // add text to table
            $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `title`, `author`,
                        `text`, `audio_uri`, `source_uri`, `type`, `word_count`, `level`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id,$this->lang_id, $title, $author, $text, $audio_url,
                            $source_url, $type, $word_count, $level]);
            $insert_id = $this->pdo->lastInsertId();

            if ($stmt->rowCount() == 0 || $insert_id == 0) {
                throw new AprelendoException('Error adding record to texts table.');
            }

            // add entry to popularsources
            $pop_sources = new PopularSources($this->pdo);
            $pop_sources->add($lang_iso, Url::getDomainName($source_url));

            return $insert_id;
        } catch (\PDOException $e) {
            throw new AprelendoException('Error adding record to texts table.');
        } finally {
            $stmt = null;
        }
    } // end add()

    /**
    * Updates existing text in database
    *
    * @param int $id
    * @param array $columns
    * @return void
    */
    public function update(int $id, array $columns): void
    {
        // create sql string to use with all the columns to update
        $sql = "";
        foreach ($columns as $key => $value) {
            $sql .= empty($sql) ? "`$key`=?" : ", `$key`=?";
        }

        try {
            $sql = "UPDATE `{$this->table}`
                    SET $sql
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $params = array_values($columns);
            $params[] = $id; // add $id last

            $stmt->execute($params);
        } catch (\PDOException $e) {
            throw new AprelendoException('Error updating record in texts table.');
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
    public function delete(string $ids): void
    {
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
                throw new AprelendoException('Error deleting record from texts table.');
            }

            // delete audio (mp3, oggs) & source files (epubs, etc.)
            $pop_sources = new PopularSources($this->pdo);
            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);
            
            // delete associated file
            foreach ($uris as $uri) {
                if (!empty($uri['source_uri']) && (strpos($uri['source_uri'], '.epub') !== false)) {
                    $file = new File($uri['source_uri']);
                    $file->delete();
                }
                
                $pop_sources->update($lang->getName(), Url::getDomainName($uri['source_uri']));
            }
        } catch (\PDOException $e) {
            throw new AprelendoException('Error deleting record from texts table.');
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
    public function archive(string $ids): void
    {
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
                throw new AprelendoException('Error inserting record in texts table.');
            }

            $delete_sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
            $stmt = $this->pdo->prepare($delete_sql);
            $stmt->execute($ids_array);

            if ($stmt->rowCount() == 0) {
                throw new AprelendoException('Error deleting record from archived texts table.');
            }
        } catch (\PDOException $e) {
            throw new AprelendoException('Error moving text to archive texts table.');
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
    public function exists(string $source_url): bool
    {
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
    public function countSearchRows(int $filter_type, int $filter_level, string $search_text): int
    {
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
    * Returns the search results for a text using the parameters chosen by the user
    *
    * @param SearchTextsParameters $search_params
    * @return array
    */
    public function search(SearchTextsParameters $search_params): array
    {
        try {
            $sort_sql = $search_params->buildSortSQL();
            $filter_type_sql = $search_params->buildFilterTypeSQL();
            $filter_level_sql = $search_params->buildFilterLevelSQL();
            
            $sql = "SELECT `id`,
                            NULL,
                            `title`,
                            `author`,
                            `audio_uri`,
                            `source_uri`,
                            `type`,
                            `word_count`,
                            CHAR_LENGTH(`text`) AS `char_length`,
                            `level`
                    FROM `{$this->table}`
                    WHERE `user_id` = :user_id
                    AND `lang_id` = :lang_id
                    AND `level` $filter_level_sql
                    AND `type` $filter_type_sql
                    AND `title` LIKE :search_text
                    ORDER BY $sort_sql
                    LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindParam(':filter_level', $search_params->filter_level, \PDO::PARAM_INT);
            $stmt->bindParam(':filter_type', $search_params->filter_type, \PDO::PARAM_INT);
            $stmt->bindValue(':search_text', '%' . $search_params->search_text . '%', \PDO::PARAM_STR);
            $stmt->bindParam(':offset', $search_params->offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $search_params->limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result || empty($result)) {
                throw new AprelendoException('Oops! There are no texts meeting your search criteria.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new AprelendoException('Error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end search()
    
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
    } // end getAuthor()

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
        return $this->audio_uri ?? '';
    } // end getAudioUri()

    /**
     * Get the value of source_uri
     */
    public function getSourceUri(): string
    {
        return $this->source_uri;
    } // end getSourceUri()

    /**
     * Get the value of type
     */
    public function getType(): int
    {
        return $this->type;
    } // end getType()

    /**
     * Get the value of word_count
     */
    public function getWordCount(): int
    {
        return $this->word_count;
    } // end getWordCount()

    /**
     * Get the value of level
     */
    public function getLevel(): int
    {
        return $this->level;
    } // end getLevel()

    /**
     * Get the value of date_created
     */
    public function getDateCreated(): string
    {
        return $this->date_created;
    } // end getDateCreated()

    /**
     * Get the value of date_created
     */
    public function getTextPos(): ?string
    {
        return $this->text_pos;
    } // end getDateCreated()

    /**
     * Get the value of date_created
     */
    public function getAudioPos(): ?string
    {
        return $this->audio_pos;
    } // end getDateCreated()
}
