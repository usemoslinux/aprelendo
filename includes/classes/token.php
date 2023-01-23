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

class Token extends DBEntity
{
    
    private $id      = 0;
    private $token   = '';
    private $expires = '';
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        $this->pdo = $pdo;
        $this->user_id = $user_id;
        $this->table = 'auth_tokens';
    } // end __construct()

    /**
     * Deletes old tokens from db
     *
     * @return \PDO
     */
    private function deleteOld(): void
    {
        try {
            $this->pdo->query("DELETE FROM `{$this->table}` WHERE `expires` < NOW()");
        } catch (\Exception $e) {
            throw new \Exception('There was an unexpected error trying to delete old token records.');
        }
    } // end deleteOld()

    /**
     * Loads Record data in object properties (looks record in db by id)
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        try {
            $sql = "SELECT *
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `expires` >= NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id       = $row['id'];
                $this->user_id  = $row['user_id'];
                $this->token    = $row['token'];
                $this->expires  = $row['expires'];
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load token record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

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
     * @return void
     */
    public function add(): void
    {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : "";
        $user_id = $this->user_id;

        // first, remove old tokens from auth_tokens table
        $this->deleteOld();
        $this->loadRecord($user_id);
            
        // check if valid token is already in db
        if (!empty($this->token)) {
            // a valid token is already in the db (force use of https)
            if (!setcookie('user_token', $this->token, strtotime($this->expires), "/", $domain, true)) {
                throw new \Exception('There was an unexpected error trying to create token cookie.');
            }
        } else {
            // create new token, insert it in db & set cookie
            $token = $this->generate();
            $time_stamp = time() + 31536000; // 1 year
            $expires = date('Y-m-d H:i:s', $time_stamp);
            
            try {
                $sql = "INSERT INTO `{$this->table}` (`token`, `user_id`, `expires`) VALUES (?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$token, $user_id, $expires]);

                if (!setcookie('user_token', $token, $time_stamp, "/", $domain, true)) {
                    throw new \Exception('There was an unexpected error trying to create token cookie.');
                }
            } catch (\PDOException $e) {
                throw new \Exception('There was an unexpected error trying to add token record.');
            } finally {
                $stmt = null;
            }
        }
    } // end add()
}
