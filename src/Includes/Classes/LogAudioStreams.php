<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class LogAudioStreams extends Log
{
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo, $user_id);
        $this->table = 'log_audio_streams';
    } 
}
