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

use Aprelendo\Includes\Classes\InternalException;
use Aprelendo\Includes\Classes\UserException;

class Token extends DBEntity
{
    public int $id      = 0;
    public int $user_id = 0;
    public string $token   = '';
    public string $expires = '';
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->table = 'auth_tokens';
    } // end __construct()

    /**
     * Deletes old tokens from db
     *
     * @return \PDO
     */
    private function deleteOld(): void
    {
        $this->pdo->query("DELETE FROM `{$this->table}` WHERE `expires` < NOW()");
    } // end deleteOld()

    /**
     * Loads Record data in object properties (looks record in db by id)
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `user_id`=? AND `expires` >= NOW()";
        $row = $this->sqlFetch($sql, [$id]);
        
        $this->loadObject($row);
    } // end loadRecord()

    /**
     * Loads Record data in object properties (looks record in db by token)
     *
     * @param string $token_cookie
     * @return void
     */
    public function loadRecordByCookieString(string $token_cookie): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `token`=?";
        $row = $this->sqlFetch($sql, [$token_cookie]);
        
        $this->loadObject($row);
    } // end loadRecordByCookieString()

    /**
     * Loads Record data in object properties
     *
     * @param array $record
     * @return void
     */
    private function loadObject(array $record): void
    {
        if ($record) {
            $this->id       = $record['id'];
            $this->user_id  = $record['user_id'];
            $this->token    = $record['token'];
            $this->expires  = $record['expires'];
        }
    } // end loadObject()

    /**
     * Generate token to store in cookie
     *
     * @param int $length
     * @return string
     */
    private function generate($length = 20)
    {
        return bin2hex(random_bytes($length));
    } // end generate()

    /**
     * Adds token to db
     *
     * @param int $user_id
     * @return void
     */
    public function add(int $user_id): void
    {
        $domain = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
        $domain = $domain ?? "";

        // first, remove old tokens from auth_tokens table
        $this->deleteOld();
        $this->loadRecord($user_id);
            
        // check if valid token is already in db
        if (!empty($this->token)) {
            // a valid token is already in the db (force use of https)
            if (!setcookie('user_token', $this->token, strtotime($this->expires), "/", $domain, true)) {
                throw new UserException('Error creating token cookie.');
            }
        } else {
            // create new token, insert it in db & set cookie
            $token = $this->generate();
            $time_stamp = time() + 31536000; // 1 year
            $expires = date('Y-m-d H:i:s', $time_stamp);
            
            $sql = "INSERT INTO `{$this->table}` (`token`, `user_id`, `expires`) VALUES (?, ?, ?)";
            $this->sqlExecute($sql, [$token, $user_id, $expires]);

            if (!setcookie('user_token', $token, $time_stamp, "/", $domain, true)) {
                throw new UserException('Error creating token cookie.');
            }
        }
    } // end add()
}
