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

use Aprelendo\Includes\Classes\UserException;

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
                throw new UserException('Username and password combination are incorrect. Please try again.');
            }

            return true;
        } catch (\Exception $e) {
            throw new UserException($e->getMessage());
        }
    } // end verify()

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
