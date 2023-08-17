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

use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\Language;
use Aprelendo\Includes\Classes\SearchTextParameters;
use Aprelendo\Includes\Classes\AprelendoException;

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo, $user_id, $lang_id);
        $this->table = 'shared_texts';
    } // end __construct()

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

            $lang = new Language($this->pdo, $this->user_id);
            $lang->loadRecord($this->lang_id);

            $sql = "SELECT t.id,
                        (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`,
                        t.title,
                        t.author,
                        t.audio_uri,
                        t.source_uri,
                        t.type,
                        t.word_count,
                        CHAR_LENGTH(t.text) AS `char_length`,
                        t.level,
                        l.name,
                        (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                        (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id
                        AND `user_id` = :user_id) AS `user_liked`
                    FROM `{$this->table}` t
                    INNER JOIN `languages` l ON t.lang_id = l.id
                    WHERE `name`= :name
                    AND `title` LIKE :search_text
                    AND t.level $filter_level_sql
                    AND t.type $filter_type_sql
                    ORDER BY $sort_sql
                    LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindValue(':name', $lang->getName());
            $stmt->bindParam(':filter_level', $search_params->filter_level, \PDO::PARAM_INT);
            $stmt->bindParam(':filter_type', $search_params->filter_type, \PDO::PARAM_INT);
            $stmt->bindValue(':search_text', '%' . $search_params->search_text . '%');
            $stmt->bindParam(':offset', $search_params->offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $search_params->limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result || empty($result)) {
                throw new AprelendoException('Oops! There are no texts meeting your search criteria.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new AprelendoException('Error processing your search request.');
        } finally {
            $stmt = null;
        }
    } // end search()

    /**
     * Checks if text was already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return bool
     */
    public function exists(string $source_url): bool
    {
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
}
