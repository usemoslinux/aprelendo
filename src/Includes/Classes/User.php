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

class User extends DBEntity
{
    public int    $id;
    public string $name;
    public string $password_hash;
    public string $email;
    public string $lang;
    public int    $lang_id;
    public string $native_lang;
    public string $time_zone;
    public string $activation_hash;
    public bool   $is_active;
    public string $google_id;

    public string $error_msg;
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->table = 'users';

        $this->id              = 0;
        $this->name            = '';
        $this->password_hash   = '';
        $this->email           = '';
        $this->lang            = '';
        $this->lang_id         = 0;
        $this->native_lang     = '';
        $this->time_zone       = 'UTC';
        $this->activation_hash = '';
        $this->is_active       = false;
        $this->google_id       = '';
    
        $this->error_msg       = '';
    } // end __construct()

    /**
     * Loads user record data in current object
     *
     * @param array $record
     * @return void
     * @throws UserException
     */
    private function loadRecord(array $record): void
    {
        if ($record) {
            $this->id              = $record['id'];
            $this->name            = $record['name'];
            $this->password_hash   = $record['password_hash'];
            $this->email           = $record['email'];
            $this->native_lang     = $record['native_lang_iso'];
            $this->lang            = $record['learning_lang_iso'];
            $this->time_zone       = $record['time_zone'];
            $this->activation_hash = $record['activation_hash'];
            $this->is_active       = $record['is_active'];
            $this->google_id       = $record['google_id'];

            // get active language id (lang_id)
            $lang = new Language($this->pdo, $this->id);
            $lang->loadRecordByName($this->lang);
            $this->lang_id = $lang->id;
        }
    }

    /**
     * Loads user record data by id
     *
     * @param int $id
     * @return void
     */
    public function loadRecordById(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id`=?";
        $this->loadRecord($this->sqlFetch($sql, [$id]));
    }

    /**
     * Loads user record data by email
     *
     * @param string $email
     * @return void
     */
    public function loadRecordByEmail(string $email): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `email`=?";
        $this->loadRecord($this->sqlFetch($sql, [$email]));
    }

    /**
     * Checks if user exists by name
     *
     * @param string $name
     * @return bool
     */
    public function existsByName(string $name): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `name`=?";
        return $this->sqlCount($sql, [$name]) > 0;
    }

    /**
     * Checks if email exists
     *
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `email`=?";
        return $this->sqlCount($sql, [$email]) > 0;
    }

    /**
     * Checks if user exists by email and password hash
     *
     * @param string $email
     * @param string $password_hash
     * @return bool
     */
    public function existsByEmailAndPasswordHash(string $email, string $password_hash): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `email`=? AND `password_hash`=?";
        return $this->sqlCount($sql, [$email, $password_hash]) > 0;
    }
    
    /**
     * Updates user profile in db
     *
     * @param array $user_data
     * @return void
     */
    public function updateProfile(array $user_data): void
    {
        $new_username = $user_data['new_username'];
        $new_email = $user_data['new_email'];
        $password = $user_data['password'];
        $new_password = $user_data['new_password'];
        $new_native_lang = $user_data['new_native_lang'];
        $new_lang = $user_data['new_lang'];

        // check if $password is correct, without it user would not have the right priviliges to update his profile
        $hashed_password = $this->password_hash;
        $authorized = UserPassword::verify($password, $hashed_password);

        if ($authorized) {
            $user_id = $this->id;
            
            // check if user already exists
            if ($this->name != $new_username && $this->existsByName($new_username)) {
                throw new UserException('Username already exists. Please try again.');
            }
            
            // check if email already exists
            if ($this->email != $new_email && $this->existsByEmail($new_email)) {
                throw new UserException('Email already exists. Please try using another one.');
            }
            
            // was a new password given? In that case, save new password and replace the old one
            if (empty($new_password)) {
                $sql = "UPDATE `{$this->table}`
                        SET `name`=?, `email`=?, `native_lang_iso`=?, `learning_lang_iso`=?
                        WHERE `id`=?";
                $this->sqlExecute($sql, [$new_username, $new_email, $new_native_lang, $new_lang, $user_id]);
            } else {
                $new_password_hash = UserPassword::createHash($new_password);

                $sql = "UPDATE `{$this->table}`
                        SET `name`=?, `password_hash`=?, `email`=?, `native_lang_iso`=?, `learning_lang_iso`=?
                        WHERE `id`=?";
                $this->sqlExecute($sql, [
                    $new_username, $new_password_hash, $new_email, $new_native_lang,
                    $new_lang, $user_id
                ]);
            }
            
            // TODO: remove this lines?
            $this->name = $new_username;
            $this->email = $new_email;
            $this->native_lang = $new_native_lang;
            $this->lang = $new_lang;
            
            // if new password was set, then create new rememberme cookie
            if (!empty($new_password)) {
                $user_auth = new UserAuth($this);
                $user_auth->login($new_username, $new_password);
            }
        }
    } // updateProfile()

    /**
     * Updates password hash in db
     *
     * @param string $password_hash
     * @param string $name
     * @return void
     */
    public function updatePasswordHash(string $password_hash, string $email): void
    {
        $sql = "UPDATE `users` SET `password_hash`=? WHERE `email`=?";
        $this->sqlExecute($sql, [$password_hash, $email]);
    } // end updatePasswordHash()

    /**
     * Updates User's Google Id
     *
     * @param string $google_id
     * @param string $google_email
     * @return void
     */
    public function updateGoogleId(string $google_id, string $google_email): void
    {
        $sql = "UPDATE `users` SET `google_id`=?, `is_active`=true WHERE `email`=?";
        $this->sqlExecute($sql, [$google_id, $google_email]);
    } // end updateGoogleId()

    /**
     * Delete user account
     *
     * @return void
     */
    public function delete(): void
    {
        $user = new UserAuth($this);
        
        if ($user->isLoggedIn()) {
            $user->logout(true);
        }

        $this->deleteFiles();
        
        $sql = "DELETE FROM `{$this->table}` WHERE `id`=?";
        $this->sqlExecute($sql, [$this->id]);
    } // end delete()

    /**
     * Delete user files
     *
     * @return void
     */
    private function deleteFiles(): void
    {
        $sql = "SELECT `source_uri` FROM `texts` WHERE `user_id`=?
                UNION ALL
                SELECT `source_uri` FROM `archived_texts` WHERE `user_id`=?";
        $rows = $this->sqlFetchAll($sql, [$this->id, $this->id]);
                    
        $filename = '';
        $file_extensions = ['.epub', '.mp3', '.ogg'];

        foreach ($rows as $row) {
            $filename = $row['source_uri'];
            if (in_array(substr($filename, -5), $file_extensions)) {
                $file = new File($filename);
                $file->delete();
            }
        }
    } // end deleteFiles()
    
    /**
     * Update active language in db
     *
     * @param int $lang_id
     * @return void
     */
    public function setActiveLang(int $lang_id): void
    {
        $lang = new Language($this->pdo, $this->id);
        $lang->loadRecordById($lang_id);
        $lang_name = $lang->name;

        $sql = "UPDATE `{$this->table}` SET `learning_lang_iso`=? WHERE `id`=?";
        $this->sqlExecute($sql, [$lang_name, $this->id]);

        $this->lang_id = $lang_id;
        $this->lang = $lang_name;
    } // end setActiveLang()

    /**
     * Checks if user is allowed to access element in db
     * Either it owns it or it is a shared element
     *
     * @param string $table
     * @param int $id
     * @return boolean
     */
    public function isAllowedToAccessElement(string $table, int $id): bool
    {
        $result = false;

        switch ($table) {
            case 'texts':
                $sql = "SELECT `id` FROM `texts` WHERE `id` = ? AND `user_id` = ?
                        UNION ALL
                        SELECT `id` FROM `archived_texts` WHERE `id` = ? AND `user_id` = ?";
                $result = $this->sqlCount($sql, [$id, $this->id, $id, $this->id]) > 0;
                break;
            case 'shared_texts':
                $sql = "SELECT `id` FROM `$table` WHERE `id`=?";
                $result = $this->sqlCount($sql, [$id]) > 0;
                break;
            case 'words':
                $sql = "SELECT `id` FROM `$table` WHERE `id`=? AND `user_id`=?";
                $result = $this->sqlCount($sql, [$id, $this->id]) > 0;
                break;
            default:
                break;
        }

        return $result;
    } // end isAllowedToAccessElement()
}
