<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
