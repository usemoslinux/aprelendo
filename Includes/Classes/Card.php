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

namespace Aprelendo;

use Aprelendo\Language;

class Card extends DBEntity
{
    protected $user_id = 0;
    protected $lang_id = 0;
    protected $lang_iso = '';

    /**
     * Constructor
     *
     * @param $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'words';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;

        $lang = new Language($pdo, $user_id);
        $lang->loadRecordById($lang_id);
        $this->lang_iso = $lang->name;
    } // end __construct()

    /**
     * Gets a list of all the words the user is learning.
     * The result is ordered by `status` and `date_created`, meaning that the ones
     * with more difficulty and the newest will go first.
     *
     * @return array
     */
    public function getWordsUserIsLearning(): array
    {
        $sql = "SELECT `word`, `status` FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=? AND `status`>0
                ORDER BY `status` DESC, `date_created` DESC";
        
        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end getWordsUserIsLearning()

    /**
     * Gets example sentences in private texts (both active and archived), except ebooks, as well as
     * shared texts, except for video transcripts
     * To improve results, it includes all texts in the db, not only those uploaded by user
     *
     * @param string $word
     * @return array
     */
    public function getExampleSentencesForWord(string $word): array
    {
        $sql = "(SELECT texts.title, texts.author, texts.text, texts.source_uri
                FROM texts
                LEFT JOIN languages ON languages.id = texts.lang_id
                WHERE languages.name = ? AND texts.user_id = ? AND texts.type <> 6 AND
                MATCH(texts.text) AGAINST(? IN BOOLEAN MODE)
                LIMIT 3)
                UNION
                (SELECT archived_texts.title, archived_texts.author, archived_texts.text, archived_texts.source_uri
                FROM archived_texts
                LEFT JOIN languages ON languages.id = archived_texts.lang_id
                WHERE languages.name = ? AND archived_texts.user_id = ? AND archived_texts.type <> 6 AND
                MATCH(archived_texts.text) AGAINST(? IN BOOLEAN MODE)
                LIMIT 3)
                UNION
                (SELECT shared_texts.title, shared_texts.author, shared_texts.text, shared_texts.source_uri
                FROM shared_texts
                LEFT JOIN languages ON languages.id = shared_texts.lang_id
                WHERE languages.name = ? AND shared_texts.type <> 5 AND
                MATCH(shared_texts.text) AGAINST(? IN BOOLEAN MODE)
                LIMIT 3)
                UNION
                (SELECT examples.source_title, examples.source_author, examples.sentence, examples.source_uri
                FROM example_sentences AS examples
                WHERE examples.lang_iso = ? AND examples.word = ?
                LIMIT 3)";

        $result = $this->sqlFetchAll($sql, [
            $this->lang_iso, $this->user_id, $word,
            $this->lang_iso, $this->user_id, $word,
            $this->lang_iso, $word,
            $this->lang_iso, $word
        ]);
        
        // Avoid returning duplicate example sentences
        $result = $this->arrayUniqueMultidimensional($result);

        // Shuffle the array to randomize the order
        shuffle($result);

        // Limit the results to max 3 records
        $result = array_slice($result, 0, 3);

        return $result;
    } // end getExampleSentencesForWord()

    /**
     * Loops multi-dimensional array and filters unique entries only
     *
     * @param array $input
     * @return array
     */
    private function arrayUniqueMultidimensional(array $input): array
    {
        $serialized = array_map('serialize', $input);
        $unique = array_unique($serialized);
        return array_intersect_key($input, $unique);
    }
}
