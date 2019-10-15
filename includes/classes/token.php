<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

class Token extends DBEntity {
    public function __construct($con, $user_id) {
        $this->con = $con;
        $this->user_id = $user_id;
        $this->table = 'auth_tokens';
    }

    private function deleteOld() {
        return $this->con->query("DELETE FROM `auth_tokens` WHERE `expires` < NOW()");
    }

    private function alreadyExists($user_id) {
        $sql = "SELECT `token`, `expires`
                FROM `auth_tokens`
                WHERE `user_id`=? AND `expires` >= NOW()
                LIMIT 1";

        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                
        return $result;
    } // end alreadyExists()

    /**
     * Generate token to store in cookie
     *
     * @param integer $length
     * @return string
     */
    private function generateToken($length = 20)
    {
        return bin2hex(random_bytes($length));
    } // end generateToken()

    /**
     * Add token to the auth_token table
     */
    public function add() {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : "";
        $user_id = $this->user_id;

        // first, remove old tokens from auth_tokens table
        $this->deleteOld();
            
        // check if valid token is already in db
        $result = $this->alreadyExists($user_id);
        if ($result !== false) {
            // a valid token is already in the db
            $token = $result['token'];
            $time_stamp = $result['expires'];
            return setcookie('user_token', $token, strtotime($time_stamp), "/", $domain, true);
        } else {
            // create new token, insert it in db & set cookie
            $token = $this->generateToken();
            $time_stamp = time() + 31536000; // 1 year
            $expires = date('Y-m-d H:i:s', $time_stamp);
            
            try {
                $sql = "INSERT INTO `auth_tokens` (`token`, `user_id`, `expires`) VALUES (?, ?, ?)";
                $stmt = $this->con->prepare($sql);
                $result = $stmt->execute([$token, $user_id, $expires]);

                if ($result) {
                    return setcookie('user_token', $token, $time_stamp, "/", $domain, true);
                } 
            } catch (\Exception $e) {
                return false;
            } finally {
                $stmt = null;
            }
        }
    } // end add()
}

?>