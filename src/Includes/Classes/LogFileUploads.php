<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class LogFileUploads extends Log
{
    public const MAX_UPLOAD_LIMIT = 3; // max 3 uploads per day

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo, $user_id);
        $this->table = 'log_file_uploads';
    } // end __construct()
}
