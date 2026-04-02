<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
        
        if (!empty($google_id) || password_verify($password, $hashedPassword)) { // login successful, remember me
            $token = new Token($this->pdo);
            $token->add($user_id);
        } else { // wrong password
            throw new UserException('Username and password combination is incorrect. Please try again.');
        }
    } 
    
    /**
     * Logout user
     *
     * @param boolean $deleted_account
     * @return void
     */
    public function logout(bool $deleted_account): void
    {
        $domain = $_SERVER['HTTP_HOST'];

        if ($deleted_account || $this->isLoggedIn()) {
            setcookie('user_token', '', time() - 3600, "/", $domain, true); // delete user_token cookie
        }
    } 
        
    /**
     * Checks if user is logged
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        if (!isset($_COOKIE['user_token'])) {
            return false;
        }
    
        $token_cookie = $_COOKIE['user_token'];
        $token = new Token($this->pdo);
        $token->loadRecordByCookieString($token_cookie);
    
        if (!$token->user_id) {
            return false; // Token from cookie is not valid or not in DB
        }
    
        // Token is valid, now try to load the associated user
        $this->user->loadRecordById($token->user_id);
    
        if (!$this->user->id) {
            return false; // The user_id from the token does not correspond to a real user
        }
    
        // Both token and user are valid
        return true;
    } 
}
