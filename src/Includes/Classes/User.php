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
    public int    $id = 0;
    public string $name = '';
    public string $password_hash = '';
    public string $email = '';
    public string $lang = '';
    public int    $lang_id = 0;
    public string $native_lang = '';
    public string $time_zone = 'UTC';
    public string $activation_hash = '';
    public bool   $is_active = false;
    public string $google_id = '';
    public string $hf_token = '';
    public ?string $reset_token = null;
    public ?string $reset_token_expires = null;
    public string $date_created = '';
    public string $error_msg = '';
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->table = 'users';
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
            $this->id                  = $record['id'];
            $this->name                = $record['name'];
            $this->password_hash       = $record['password_hash'];
            $this->email               = $record['email'];
            $this->native_lang         = $record['native_lang_iso'];
            $this->lang                = $record['learning_lang_iso'];
            $this->time_zone           = $record['time_zone'];
            $this->activation_hash     = $record['activation_hash'];
            $this->is_active           = $record['is_active'];
            $this->google_id           = $record['google_id'];
            $this->hf_token            = $record['hf_token'];
            $this->reset_token         = $record['reset_token'];
            $this->reset_token_expires = $record['reset_token_expires'];
            $this->date_created        = $record['date_created'];

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
     * Loads user record data by name
     *
     * @param string $name
     * @return void
     */
    public function loadRecordByName(string $name): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `name`=?";
        $this->loadRecord($this->sqlFetch($sql, [$name]));
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
     * Updates user profile in db
     *
     * @param array $user_data
     * @return void
     */
    public function updateProfile(array $user_data): void
    {
        // Extract variables from user data
        $new_username = $user_data['new_username'];
        $new_email = $user_data['new_email'];
        $password = $user_data['password'];
        $new_password = $user_data['new_password'] ?? null; // Default to null if not set
        $new_native_lang = $user_data['new_native_lang'];
        $new_lang = $user_data['new_lang'];
        $hf_token = $user_data['hf_token'];

        // Verify the user's password
        if (empty($this->google_id) && !UserPassword::verify($password, $this->password_hash)) {
            throw new UserException('Invalid password. Please try again.');
        }

        $user_id = $this->id;

        // Check for duplicate username
        if ($this->name !== $new_username && $this->existsByName($new_username)) {
            throw new UserException('Username already exists. Please try again.');
        }

        // Check for duplicate email
        if ($this->email !== $new_email && $this->existsByEmail($new_email)) {
            throw new UserException('Email already exists. Please try using another one.');
        }

        // Prepare the parameters and SQL statement
        $params = [$new_username, $new_email, $new_native_lang, $new_lang, $hf_token];
        $sql = "UPDATE `{$this->table}` SET `name`=?, `email`=?, `native_lang_iso`=?, `learning_lang_iso`=?, `hf_token`=?";

        // Add password hash if a new password is provided
        if (!empty($new_password)) {
            $new_password_hash = UserPassword::createHash($new_password);
            $sql .= ", `password_hash`=?";
            $params[] = $new_password_hash;
        }

        $sql .= " WHERE `id`=?";
        $params[] = $user_id;
        $this->sqlExecute($sql, $params);

        // if new password was set, then update remember-me cookie
        if (!empty($new_password)) {
            $user_auth = new UserAuth($this);
            $user_auth->login($new_username, $new_password);
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
     * Generates and saves a password reset token for the user.
     * The plaintext token is returned so it can be emailed to the user.
     * The hash of the token is stored in the database.
     *
     * @return string The plaintext reset token.
     */
    public function setResetToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = new \DateTime('+15 minutes');

        $sql = "UPDATE `{$this->table}` SET `reset_token`=?, `reset_token_expires`=? WHERE `id`=?";
        $this->sqlExecute($sql, [hash('sha256', $token), $expires->format('Y-m-d H:i:s'), $this->id]);
        
        return $token; // Return plaintext token to be emailed
    }

    /**
     * Finds a user by a valid, non-expired password reset token.
     *
     * @param string $token The plaintext token from the user.
     * @return boolean True if a user was found and loaded, false otherwise.
     */
    public function loadUserByValidResetToken(string $token): bool
    {
        $hashed_token = hash('sha256', $token);
        $sql = "SELECT * FROM `{$this->table}` WHERE `reset_token`=? AND `reset_token_expires` >= NOW()";
        $record = $this->sqlFetch($sql, [$hashed_token]);
        
        if ($record) {
            $this->loadRecord($record);
            return true;
        }
        return false;
    }

    /**
     * Clears the password reset token and expiry for the user.
     *
     * @return void
     */
    public function clearResetToken(): void
    {
        $sql = "UPDATE `{$this->table}` SET `reset_token`=NULL, `reset_token_expires`=NULL WHERE `id`=?";
        $this->sqlExecute($sql, [$this->id]);
    }

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
        $this->deleteFiles();
        
        $sql = "DELETE FROM `{$this->table}` WHERE `id`=?";
        $this->sqlExecute($sql, [$this->id]);

        // Rebuild popular sources as some sources could have been removed
        // and counts need to be updated
        $popular_sources = new PopularSources($this->pdo);
        $popular_sources->rebuild();
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
