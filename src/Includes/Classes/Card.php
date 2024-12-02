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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

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
    public function getWordsUserIsLearning(int $limit): array
    {
        $sql = "SELECT w.word, w.status, fl.frequency_index
                FROM {$this->table} AS w
                JOIN frequency_list_$this->lang_iso AS fl ON w.word = fl.word
                WHERE w.user_id = ?
                AND w.lang_id = ?
                AND w.status > 0
                ORDER BY w.status DESC, w.date_created DESC
                LIMIT $limit";
        
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
        $texts = $this->getTextsWithWord($word);
        $result = [];

        foreach ($texts as $text) {
            $re = '/^.*?\b(' . $word . ')\b.*$/miu';

            preg_match_all($re, $text['text'], $matches, PREG_SET_ORDER, 0);

            foreach ($matches as $match) {
                $match_to_add['title'] = $text['title'];
                $match_to_add['author'] = $text['author'];
                $match_to_add['text'] = $match[0];
                $match_to_add['source_uri'] = $text['source_uri'];
                $result[] = $match_to_add;
            }
        }
        
        return $result;
    } // end getExampleSentencesForWord()

    /**
     * Returns a list of texts that include a specific word/phrase
     *
     * @param string $word
     * @return array
     */
    private function getTextsWithWord(string $word): array
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

        return $this->sqlFetchAll($sql, [
            $this->lang_iso, $this->user_id, $word,
            $this->lang_iso, $this->user_id, $word,
            $this->lang_iso, $word,
            $this->lang_iso, $word
        ]);
    }
}
