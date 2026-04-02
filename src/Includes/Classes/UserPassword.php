<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class UserPassword
{
    /**
     * Check if $password = $hashed_password
     * Meaning the password provided by user is equal to the one stored in the database
     *
     * @param string $password
     * @return boolean
     */
    public static function verify(string $password, string $hashed_password): bool
    {
        try {
            if (!password_verify($password, $hashed_password)) {
                throw new UserException('Username and password combination is incorrect. Please try again.');
            }

            return true;
        } catch (\Exception $e) {
            throw new UserException($e->getMessage());
        }
    } 

    /**
     * Creates hash for a given password
     *
     * @param string $password
     * @return string
     */
    public static function createHash(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }
}
