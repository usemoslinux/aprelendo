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

class ArchivedTexts extends Texts
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
        $this->table = 'archived_texts';
    } // end __construct()

    /**
     * Unarchives text by using their $ids
     *
     * @param array $text_ids
     * @return void
     */
    public function unarchive(array $text_ids): void
    {
        if (empty($text_ids)) throw new InternalException("Text id is empty");

        $id_params = str_repeat("?,", count($text_ids)-1) . "?";

        $sql_insert = "INSERT INTO `texts` SELECT * FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $sql_delete = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";

        try {
            $this->pdo->beginTransaction();
            $this->sqlExecute($sql_insert, $text_ids);
            $this->sqlExecute($sql_delete, $text_ids);
            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new InternalException('Could not unarchive texts.');
        }
    }
} // end unarchive()
