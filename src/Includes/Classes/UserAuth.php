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

class UserAuth extends DBEntity
{
    private User $user;

    public function __construct(User $user) {
        parent::__construct($user->pdo);
        $this->table = 'users';
        $this->user = $user;
    }

    /**
     * Logs user & creates "remember me" cookie
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login($username = "", $password = "", $google_id = ""): void
    {
        $sql = "SELECT *
            FROM `{$this->table}`
            WHERE `name`=?";

        $row = $this->sqlFetch($sql, [$username]);

        if (!$row) {
            throw new UserException('Username and password combination is incorrect. Please try again.');
        }

        $user_id = $row['id'];
        $hashedPassword = $row['password_hash'];

        // check if user account is active
        if (!$row['is_active']) {
            throw new UserException('You need to activate your account first. Check your email for the '
                . 'activation link.');
        }
        
        if (password_verify($password, $hashedPassword) || $google_id !== "") { // login successful, remember me
            $token = new Token($this->pdo);
            $token->add($user_id);
        } else { // wrong password
            throw new UserException('Username and password combination is incorrect. Please try again.');
        }
    } // end login()
    
    /**
     * Logout user
     *
     * @param boolean $deleted_account
     * @return void
     */
    public function logout(bool $deleted_account): void
    {
        $domain = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
        $domain = $domain ?? "";

        if ($deleted_account || $this->isLoggedIn()) {
            setcookie('user_token', '', time() - 3600, "/", $domain, true); // delete user_token cookie
        }
        
        header('Location:/index.php');
    } // end logout()
        
    /**
     * Checks if user is logged
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        $result = false;

        if (isset($_COOKIE['user_token'])) {
            $token_cookie = $_COOKIE['user_token'];
            $token = new Token($this->pdo);
            $token->loadRecordByCookieString($token_cookie);
            
            if (!$token->user_id) {
                $result = false;
            }

            // get username & other user data
            $this->user->loadRecordById($token->user_id);

            $result = true;
        }

        return $result;
    } // end isLoggedIn()
}
