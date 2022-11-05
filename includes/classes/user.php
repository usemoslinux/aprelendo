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

use Aprelendo\Includes\Classes\File;
use Aprelendo\Includes\Classes\Language;

class User
{
    private $id              = 0;
    private $name            = '';
    private $password_hash   = '';
    private $email           = '';
    private $lang            = '';
    private $lang_id         = 0;
    private $native_lang     = '';
    private $activation_hash = '';
    private $active          = false;
    private $google_id       = '';

    private $time_fmt = 'Y-m-d H:i:s';
    
    private $error_msg = '';
    
    private $pdo;
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->table = 'users';
    } // end __construct()

    /**
     * Loads user record data
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
           
            if ($row) {
                $this->id              = $row['id'];
                $this->name            = $row['name'];
                $this->password_hash   = $row['password_hash'];
                $this->email           = $row['email'];
                $this->native_lang     = $row['native_lang_iso'];
                $this->lang            = $row['learning_lang_iso'];
                $this->activation_hash = $row['activation_hash'];
                $this->is_active       = $row['is_active'];
                $this->google_id       = $row['google_id'];

                // get active language id (lang_id)
                $lang = new Language($this->pdo, $this->id);
                $lang->loadRecordByName($this->lang);
                $this->lang_id = $lang->getId();
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load user record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Loads user record data by email
     *
     * @param string $email
     * @return void
     */
    public function loadRecordByEmail(string $email): void
    {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `email`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                $this->id              = $row['id'];
                $this->name            = $row['name'];
                $this->password_hash   = $row['password_hash'];
                $this->email           = $row['email'];
                $this->native_lang     = $row['native_lang_iso'];
                $this->lang            = $row['learning_lang_iso'];
                $this->activation_hash = $row['activation_hash'];
                $this->is_active       = $row['is_active'];
                $this->google_id       = $row['google_id'];
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load user record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecordByEmail()

    /**
     * Checks if user exists
     *
     * @param string $name
     * @return bool
     */
    public function existsByName(string $name): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `name`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name]);
            $num_rows = $stmt->fetchColumn();
           
            return ($num_rows) && ($num_rows > 0) ? true : false;
        } catch (\PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end existsByName()

    /**
     * Checks if email exists
     *
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `email`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            $num_rows = $stmt->fetchColumn();
           
            return ($num_rows) && ($num_rows > 0) ? true : false;
        } catch (\PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end existsByEmail()

    /**
     * Checks if user exists, by name and password
     *
     * @param string $name
     * @param string $password_hash
     * @return bool
     */
    public function existsByEmailAndPasswordHash(string $email, string $password_hash): bool
    {
        try {
            $sql = "SELECT COUNT(*) AS `exists` FROM `users` WHERE `email`=? AND `password_hash`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $password_hash]);
            $num_rows = $stmt->fetchColumn();
           
            return ($num_rows) && ($num_rows > 0) ? true : false;
        } catch (\PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end existsByEmailAndPasswordHash()
    
    /**
     * Creates new user & associated languages and reader preferences
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $native_lang
     * @param string $lang
     * @return void
     */
    public function register($username, $email, $password, $native_lang = 'en', $lang = 'en', $send_email = false): void
    {
        $this->name = $username;
        $this->email = $email;
        $this->native_lang = $native_lang;
        $this->lang = $lang;
        $this->active = false;

        try {
            // check if user already exists
            if ($this->existsByName($username)) {
                throw new \Exception('Username already exists. Please try again.');
            }
            
            // check if email already exists
            if ($this->existsByEmail($email)) {
                throw new \Exception('Email already exists. Did you <a class="alert-link" href="/forgotpassword">'
                    . 'forget</a> you username or password?');
            }
            
            // create password hash
            $options = ['cost' => 11];
            $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

            // create account activation hash
            $activation_hash = $this->activation_hash = md5(rand(0, 1000));

            // save user data in db
            $user_active = !$send_email;
            $sql = "INSERT INTO `{$this->table}` (`name`, `password_hash`, `email`, `native_lang_iso`,
                `learning_lang_iso`, `activation_hash`, `is_active`)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $password_hash, $email, $native_lang, $lang, $activation_hash,
                (int)$user_active]);

            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to create user record.');
            }

            $user_id = $this->id = $this->pdo->lastInsertId();

            // create & save default language preferences for user
            $lang = new Language($this->pdo, $user_id);
            $lang->createInitialRecordsForUser($native_lang);

            $sql = "INSERT INTO `preferences` (`user_id`, `font_family`, `font_size`, `line_height`, `text_alignment`,
                    `display_mode`, `assisted_learning`)
                    VALUES (?, 'Helvetica', '12pt', '1.5', 'left', 'light', '1')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to create default preferences for user.');
            }

            if ($send_email) {
                $this->sendActivationEmail($email, $username, $activation_hash);
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to register user.');
        } finally {
            $stmt = null;
        }
    } // end register()
    
    /**
     * Send activation email to user.
     * Without completing this step, the account should be considered inactive.
     *
     * @param string $email
     * @param string $username
     * @param string $hash
     * @return void
     */
    public function sendActivationEmail(string $email, string $username, string $hash): void
    {
        // create activation link
        $reset_link = "https://www.aprelendo.com/accountactivation?username=$username&hash=$hash";

        // create email html
        $to = $email;
        $subject = 'Aprelendo - Account activation';
        
        // get template
        $message = file_get_contents(APP_ROOT . 'templates/welcome.html');
        
        // edit template
        $message = str_replace('{{action_url}}', $reset_link, $message);
        $message = str_replace('{{name}}', $username, $message);
        $message = str_replace('{{current_year}}', date("Y"), $message);
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Aprelendo <' . EMAIL_SENDER . ">\r\n";
        
        // send email
        $mail_sent = mail($to, $subject, $message, $headers, '-f ' . EMAIL_SENDER);
        if (!$mail_sent) {
            $this->delete();
            throw new \Exception('Oops! There was an unexpected error trying to send you an e-mail to activate '
                . 'your account. Please try again later.');
        }
    } // end sendActivationEmail()

    /**
     * Activates user
     *
     * @param string $username
     * @param string $hash
     * @return void
     */
    public function activate(string $username, string $hash): void
    {
        try {
            // check if user name & hash exist in db
            $sql = "SELECT COUNT(*)
                    FROM `{$this->table}`
                    WHERE `name`=? AND `activation_hash`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $hash]);
            $num_rows = $stmt->fetchColumn();
            
            if ($num_rows == 0) {
                throw new \Exception('User does not exist.');
            }

            $sql = "UPDATE `{$this->table}`
                    SET `is_active`=true
                    WHERE `name`=? AND `activation_hash`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $hash]);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to activate user.');
        } finally {
            $stmt = null;
        }
    } // activate()

    /**
     * Creates "remember me" cookie
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login($username = "", $password = "", $google_id = ""): void
    {
        try {
            $sql = "SELECT *
                    FROM `{$this->table}`
                    WHERE `name`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $exists = !empty($row);
                
            // check if username exists
            if (!$exists) { // wrong username
                throw new \Exception('Username and password combination is incorrect. Please try again.');
            }

            $user_id = $row['id'];
            $hashedPassword = $row['password_hash'];

            // check if user account is active
            if ($row['is_active'] === false) {
                throw new \Exception('You need to activate your account first. Check your email for the '
                    . 'activation link.');
            }
            
            if (password_verify($password, $hashedPassword) || $google_id !== "") { // login successful, remember me
                $token = new Token($this->pdo, $user_id);
                $token->add();
            } else { // wrong password
                throw new \Exception('Username and password combination is incorrect. Please try again.');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // end login()
    
    /**
     * Logout user
     *
     * @param boolean $deleted_account
     * @return void
     */
    public function logout($deleted_account): void
    {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

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

        try {
            if (isset($_COOKIE['user_token'])) {
                $token = $_COOKIE['user_token'];
                
                // get user id
                $sql = "SELECT `user_id`
                        FROM `auth_tokens`
                        WHERE `token`=?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$token]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if (!$row) {
                    $result = false;
                }

                // get username & other user data
                $this->loadRecord($row['user_id']);

                $result = true;
            }
        } catch (\PDOException $e) {
            $result = false;
        } finally {
            $stmt = null;
        }

        return $result;
    } // end isLoggedIn()

    /**
     * Updates user profile in db
     *
     * @param string $new_username
     * @param string $new_email
     * @param string $password
     * @param string $new_password
     * @param string $new_native_lang
     * @param string $new_lang
     * @return void
     */
    public function updateUserProfile(
        string $new_username,
        string $new_email,
        string $password,
        string $new_password,
        string $new_native_lang,
        string $new_lang
        ): void
    {
        try {
            // check if $password is correct, without it user would not have the right priviliges to update his profile
            $authorized = $this->checkPassword($password);

            if ($authorized) {
                $user_id = $this->id;
                
                // check if user already exists
                if ($this->name != $new_username && $this->existsByName($new_username)) {
                    throw new \Exception('Username already exists. Please try again.');
                }
                
                // check if email already exists
                if ($this->email != $new_email && $this->existsByEmail($new_email)) {
                    throw new \Exception('Email already exists. Please try using another one.');
                }
                
                // was a new password given? In that case, save new password and replace the old one
                if (empty($new_password)) {
                    $sql = "UPDATE `{$this->table}`
                            SET `name`=?, `email`=?, `native_lang_iso`=?, `learning_lang_iso`=?
                            WHERE `id`=?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$new_username, $new_email, $new_native_lang, $new_lang, $user_id]);
                } else {
                    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 11]);
                    $sql = "UPDATE `{$this->table}`
                            SET `name`=?, `password_hash`=?, `email`=?, `native_lang_iso`=?, `learning_lang_iso`=?
                            WHERE `id`=?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$new_username, $new_password_hash, $new_email, $new_native_lang, $new_lang,
                        $user_id]);
                }
                
                $this->name = $new_username;
                $this->email = $new_email;
                $this->native_lang = $new_native_lang;
                $this->lang = $new_lang;
                
                // if new password was set, then create new rememberme cookie
                if (!empty($new_password)) {
                    $this->login($new_username, $new_password);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // updateUserProfile()

    /**
     * Updates password hash in db
     *
     * @param string $password_hash
     * @param string $name
     * @return void
     */
    public function updatePasswordHash(string $password_hash, string $email): void
    {
        try {
            $sql = "UPDATE `users` SET `password_hash`=? WHERE `email`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$password_hash, $email]);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to update user record.');
        } finally {
            $stmt = null;
        }
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
        try {
            $sql = "UPDATE `users`
                    SET `google_id`=?
                    WHERE `email`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$google_id, $google_email]);
        } catch (\Exception $e) {
            throw new \Exception('There was an unexpected error trying to update user record.');
        } finally {
            $stmt = null;
        }
    } // end updateGoogleId()

    /**
     * Delete user account
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->isLoggedIn()) {
            $this->logout(true);
        }

        try {
            // delete files uploaded by user
            $table_names = array('texts', 'archived_texts');
            
            // delete epub or audio files uploaded by user
            foreach ($table_names as $table) {
                $user_id_col_name = 'user_id';
                $source_uri_col_name = 'source_uri';
                
                $sql = "SELECT $source_uri_col_name
                        FROM $table
                        WHERE $user_id_col_name=?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->id]);
                            
                $filename = '';
                $file_extensions = array('.epub', '.mp3', '.ogg');

                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $filename = $row[$source_uri_col_name];
                    if (in_array(substr($filename, -5), $file_extensions)) {
                        $file = new File($filename);
                        $file->delete();
                    }
                }
            }
            
            // delete user from db
            $sql = "DELETE FROM `{$this->table}`
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->id]);

            if ($stmt->rowCount() == 0) {
                throw new \Exception('Oops! There was an unexpected problem trying to delete your account. '
                . 'Please try again later.');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // end delete()
    
    /**
     * Update active language in db
     *
     * @param int $lang_id
     * @return void
     */
    public function setActiveLang(int $lang_id): void
    {
        try {
            $sql = "SELECT `name`
                    FROM `languages`
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$lang_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $lang_name = $row['name'];
            $user_id = $this->id;
            
            $sql = "UPDATE `{$this->table}`
                    SET `learning_lang_iso`=?
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$lang_name, $user_id]);

            $this->lang_id = $lang_id;
            $this->lang = $lang_name;
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to set active language for user.');
        } finally {
            $stmt = null;
        }
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
        $id_col_name = '';
        $user_id_col_name = '';
        
        if ($table == 'shared_texts') {
            return true;
        }
        
        $col_names = array(
            array('texts', 'id', 'user_id'),
            array('archived_texts', 'id', 'user_id'),
            array('shared_texts', 'id', 'user_id'),
            array('words', 'id', 'user_id')
        );

        foreach ($col_names as $col_name) {
            if ($col_name[0] == $table) {
                $id_col_name = $col_name[1];
                $user_id_col_name = $col_name[2];
                break;
            }
        }

        try {
            $sql = "SELECT COUNT(*)
                    FROM `$table`
                    WHERE `$id_col_name`=? AND `$user_id_col_name`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $this->id]);
            $num_rows = $stmt->fetchColumn();
            
            return (int)$num_rows > 0;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end isAllowedToAccessElement()
    
    /**
     * Check if $password = user password
     *
     * @param string $password
     * @return boolean
     */
    private function checkPassword(string $password): bool
    {
        try {
            $user_id = $this->id;

            $sql = "SELECT `password_hash`
                    FROM `{$this->table}`
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hashedPassword = $row['password_hash'];
            if (password_verify($password, $hashedPassword)) {
                return true;
            } else {
                throw new \Exception('Username and password combination are incorrect. Please try again.');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $stmt = null;
        }
    } // end checkPassword()


    /**
     * Get the value of id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of password_hash
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the value of email
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the value of lang
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * Get the value of lang_id
     * @return int
     */
    public function getLangId(): int
    {
        return $this->lang_id;
    }

    /**
     * Get the value of native_lang
     * @return string
     */
    public function getNativeLang(): string
    {
        return $this->native_lang;
    }

    /**
     * Get the value of activation_hash
     * @return string
     */
    public function getActivationHash(): string
    {
        return $this->activation_hash;
    }

    /**
     * Get the value of active
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * Get the value of google_id
     * @return string
     */
    public function getGoogleId(): string
    {
        return $this->google_id;
    }

    /**
     * Get the value of error_msg
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->error_msg;
    }

}
