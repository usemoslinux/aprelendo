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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\Language;

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        parent::__construct($pdo, $user_id, $lang_id);
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Gets texts by using a search pattern ($search_text) and a filter ($search_filter).
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
            $filter_type_sql = $filter_type == 0 ? 'AND t.type > :filter_type' : 'AND t.type = :filter_type';
            $filter_level_sql = $filter_level == 0 ? 'AND t.level > :filter_level' : 'AND t.level = :filter_level';

            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);

            $sql = "SELECT t.id, 
                        (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`, 
                        t.title, 
                        t.author, 
                        t.source_uri,
                        t.type, 
                        t.word_count, 
                        t.level, 
                        l.name, 
                        (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                        (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id AND `user_id` = :user_id) AS `user_liked` 
                    FROM `{$this->table}` t 
                    INNER JOIN `languages` l ON t.lang_id = l.id
                    WHERE `name`= :name 
                    AND `title` LIKE :search_str 
                    $filter_level_sql 
                    $filter_type_sql 
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindValue(':name', $lang->getName());
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
     * It returns only specific ranges by using an $offset (specifies where to start) 
     * and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param int $offset
     * @param int $limit
     * @param int $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getAll(int $offset, int $limit, int $sort_by): array {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);

            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);

            $filter_level = $lang->getLevel();
            $filter_level_sql = $filter_level == 0 ? 'AND t.level > :filter_level' : 'AND t.level = :filter_level';

            $sql = "SELECT t.id, 
                    (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`, 
                    t.title, 
                    t.author, 
                    t.source_uri,
                    t.type, 
                    t.word_count, 
                    t.level, 
                    l.name,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id AND `user_id` = :user_id) AS `user_liked`
                    FROM `{$this->table}` t
                    INNER JOIN `languages` l ON t.lang_id = l.id
                    WHERE `name` = :lang 
                    $filter_level_sql 
                    ORDER BY $sort_sql 
                    LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindValue(':lang', $lang->getName());
            $stmt->bindParam(':filter_level', $filter_level, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
     * Checks if text was already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return bool
     */
    public function exists(string $source_url): bool {
        try {
            if (empty($source_url)) {
                return false;
            }
    
            $sql = "SELECT COUNT(*) AS `exists`
                    FROM `{$this->table}`
                    WHERE `source_uri` = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$source_url]);
            $row = $stmt->fetch();
                
            return ($row) && ($row['exists'] > 0);
        } catch (\PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end exists() 

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param int $sort_by
     * @return string
     */
    protected function buildSortSQL(int $sort_by): string {
        $result = parent::buildSortSQL($sort_by);

        if (!empty($result)) {
            return $result;
        } else {
            switch ($sort_by) {
                case '2': // more likes first
                    return '`total_likes` DESC';
                    break;
                case '3': // less likes first
                    return '`total_likes`';
                    break;
                default:
                    return '';
                    break;
            }
        }
    } // end buildSortSQL()
}


?>