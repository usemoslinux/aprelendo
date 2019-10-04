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

use Aprelendo\Includes\Classes\File;
use Aprelendo\Includes\Classes\Languages;

class User 
{
    use Curl;

    public $id;
    public $name;
    public $email;
    public $learning_lang;
    public $learning_lang_id;
    public $native_lang;
    public $premium_until;
    public $activation_hash;
    public $active;
    public $error_msg;
    
    private $con;   
    
    /**
     * Constructor
     * 
     * @param mysqli_connect $con
     */
    public function __construct ($con) {
        $this->con = $con;
    } // end construct
    
    /**
     * Creates new user & associated languages and reader preferences
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $native_lang
     * @param string $learning_lang
     * @return boolean
     */
    public function register($username, $email, $password, $native_lang = 'en', $learning_lang = 'en', $send_email = false) {
        $username = $this->name = $this->con->escape_string($username);
        $email = $this->email = $this->con->escape_string($email);
        $password = $this->con->escape_string($password);
        $native_lang = $this->native_lang = $this->con->escape_string($native_lang);
        $learning_lang = $this->learning_lang = $this->con->escape_string($learning_lang);
        $this->active = false;

        // check if user already exists
        $result = $this->con->query("SELECT `name` FROM `users` WHERE `name`='$username'");
        if ($result->num_rows > 0) {
            throw new \Exception ('Username already exists. Please try again.');
        }
        
        // check if email already exists
        $result = $this->con->query("SELECT `email` FROM `users` WHERE `email`='$email'");
        if ($result->num_rows > 0) {
            throw new \Exception ('Email already exists. Did you <a href="forgotpassword.php">forget</a> you username or password?');
        }
        
        // create password hash
        $options = ['cost' => 11];
        $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

        // create account activation hash
        $activation_hash = $this->activation_hash = md5(rand(0,1000));

        // save user data in db
        $user_active = !$send_email;
        $result = $this->con->query("INSERT INTO `users` (`name`, `password_hash`, `email`, `native_lang_iso`, `learning_lang_iso`, `activation_hash`, `is_active`) 
            VALUES ('$username', '$password_hash', '$email', '$native_lang', '$learning_lang', '$activation_hash', '$user_active')"); 
        if ($result) {
            $user_id = $this->id = $this->con->insert_id;
            
            // create & save default language preferences for user
            foreach (Language::$lg_iso_codes as $key => $value) {
                $translator_uri = $this->con->escape_string('https://translate.google.com/m?hl=' . $value . '&sl=' . Language::$lg_iso_codes[$native_lang] . '&&ie=UTF-8&q=%s');
                $dict_uri = $this->con->escape_string('https://www.linguee.com/' . $value . '-' . Language::$lg_iso_codes[$native_lang] . '/search?source=auto&query=%s');
                
                $result = $this->con->query("INSERT INTO `languages` (`user_id`, `name`, `dict1_uri`, `translator_uri`) 
                    VALUES ('$user_id', '$key', '$dict_uri', '$translator_uri')");
            }

            if ($result) {
                $result = $this->con->query("INSERT INTO `preferences` (`user_id`, `font_family`, `font_size`, `line_height`, `text_alignment`, `learning_mode`, `assisted_learning`) 
                    VALUES ('$user_id', 'Helvetica', '12pt', '1.5', 'left', 'light', '1')");
                
                return $send_email ? $this->sendActivationEmail($email, $username, $activation_hash) : true;
                
                if (!$result) {
                    throw new \Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
                }
            } else {
                throw new \Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
            }
        } else {
            throw new \Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
        }
    } // end register
    
    /**
     * Send activation email to user.
     * Without completing this step, the account should be considered inactive.
     *
     * @param string $email
     * @param string $username
     * @param string $hash
     * @return boolean
     */
    public function sendActivationEmail($email, $username, $hash)
    {
        // create activation link
        $reset_link = "https://www.aprelendo.com/accountactivation.php?username=$username&hash=$hash";

        // create email html
        $to = $email;
        $subject = 'Aprelendo - Account activation';
        
        // get template
        $message = $this->get_url_contents(APP_ROOT . 'templates/welcome.html');
        
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
            throw new \Exception ('Oops! There was an unexpected error trying to send you an e-mail to activate your account. Please try again later.');
        }
        return true;
    } // end sendActivationEmail

    /**
     * Activates user
     *
     * @param string $username
     * @param string $hash
     * @return void
     */
    public function activate($username, $hash)
    {
        $username = $this->con->escape_string($username);
        $hash = $this->con->escape_string($hash);

        // check if user name & hash exist in db
        $result = $this->con->query("SELECT `is_active` FROM `users` WHERE `name`='$username' AND `activation_hash`='$hash'");
        
        if ($result->num_rows > 0) {
            $yesterday = date("Y-m-d", time() - 60 * 60 * 24);
            $result = $this->con->query("UPDATE `users` SET `is_active`=true, `premium_until`='$yesterday' WHERE `name`='$username' AND `activation_hash`='$hash'");

            if (!$result) {
                throw new \Exception ('Oops! There was an unexpected error when trying to activate your account.');
            }            
        } else { // if no user is registered with that name & hash
            throw new \Exception ('The activation link seems to be malformed. Please try again using the one provided in the email we\'ve sent you.');
        } 
        return true;
    }

    /**
     * Creates "remember me" cookie
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username = "", $password = "", $google_id = "") {
        $username = $this->con->escape_string($username);
        $password = $this->con->escape_string($password);
        
        $result = $this->con->query("SELECT * FROM `users` WHERE `name`='$username'");
        
        // check if username exists
        if ($result->num_rows == 0) { // wrong username
            throw new \Exception ('Username and password combination is incorrect. Please try again.');
        }

        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $hashedPassword = $row['password_hash'];

        // check if user account is active
        if ($row['is_active'] == false) {
            throw new \Exception ('You need to activate your account first. Check your email for the activation link.');
        }
        
        if (password_verify($password, $hashedPassword) || $google_id !== "") { // login successful, remember me
            $token = new Token($this->con, $user_id);
            if (!$token->add()) {
                throw new \Exception ('There was a problem trying to create the authentication cookie. Please try again.');
            }
        } else { // wrong password
            throw new \Exception ('Username and password combination is incorrect. Please try again.');
        }
        return true;
    } // end login
    
    /**
     * Logout user
     *
     * @param boolean $deleted_account
     * @return void
     */
    public function logout($deleted_account) {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

        if ($deleted_account || $this->isLoggedIn()) {
            setcookie('user_token', '', time() - 3600, "/", $domain, true); // delete user_token cookie
        } 
        
        header('Location:/index.php');
        exit;
    } // end logout
        
    /**
     * Checks if user is logged
     *
     * @return boolean
     */
    public function isLoggedIn() {
        $is_logged = false;
        if (isset($_COOKIE['user_token'])) {
            $token = $_COOKIE['user_token'];
            
            // get user id
            if ($result = $this->con->query("SELECT `user_id` FROM `auth_tokens` WHERE `token`='$token'")) {
                $row = $result->fetch_assoc();
                $this->id = $user_id = $row['user_id'];
                
                // get username & other user data
                if ($result = $this->con->query("SELECT `name`, `email`, `native_lang_iso`, `learning_lang_iso`, `premium_until` FROM `users` WHERE `id`='$user_id'")) {
                    $row = $result->fetch_assoc();
                    $this->name = $row['name'];
                    $this->email = $row['email'];
                    $this->native_lang = $row['native_lang_iso'];
                    $this->learning_lang = $learning_lang = $row['learning_lang_iso'];
                    $this->premium_until = $row['premium_until'];
                    
                    // get active language id (learning_lang_id)
                    if ($result = $this->con->query("SELECT `id` FROM `languages` WHERE `user_id`='$user_id' AND `name`='$learning_lang'")) {
                        $row = $result->fetch_assoc();
                        $this->learning_lang_id = $row['id'];
                        $is_logged = true;
                    }
                }
            }
        }
        return $is_logged;
    } // end isLoggedIn
    
    /**
     * Checks if user has access to premium features
     *
     * @return boolean
     */
    public function isPremium() {
        return $this->premium_until > date('Y-m-d');
    } // end isPremium

    /**
     * Updates user profile in db
     *
     * @param string $new_username
     * @param string $new_email
     * @param string $password
     * @param string $new_password
     * @param string $new_native_lang
     * @param string $new_learning_lang
     * @return boolean
     */
    public function updateUserProfile($new_username, $new_email, $password, $new_password, $new_native_lang, $new_learning_lang) {
        // check if $password is correct, without it user would not have the right priviliges to update his profile
        $authorized = $this->checkPassword($password);

        if ($authorized) {
            $user_id = $this->con->escape_string($this->id);
            $new_username = $this->con->escape_string($new_username);
            $new_email = $this->con->escape_string($new_email);
            $new_password = $this->con->escape_string($new_password);
            $new_native_lang = $this->con->escape_string($new_native_lang);
            $new_learning_lang = $this->con->escape_string($new_learning_lang);
            
            // check if user already exists
            if ($this->name != $new_username) {
                $result = $this->con->query("SELECT `name` FROM `users` WHERE `name`='$new_username'");
                if ($result->num_rows > 0) {
                    throw new \Exception ('Username already exists. Please try again.');
                }
            }
            
            // check if email already exists
            if ($this->email != $new_email) {
                $result = $this->con->query("SELECT `email` FROM `users` WHERE `email`='$new_email'");
                if ($result->num_rows > 0) {
                    throw new \Exception ('Email already exists. Please try using another one.');
                }
            }
            
            // was a new password given? In that case, save new password and replace the old one
            if (empty($new_password)) {
                $result = $this->con->query("UPDATE `users` SET `name`='$new_username', 
                `email`='$new_email', `native_lang_iso`='$new_native_lang', `learning_lang_iso`='$new_learning_lang' 
                WHERE `id`='$user_id'");
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 11]);

                $result = $this->con->query("UPDATE `users` SET `name`='$new_username', `password_hash`='$new_password_hash', 
                `email`='$new_email', `native_lang_iso`='$new_native_lang', `learning_lang_iso`='$new_learning_lang' 
                WHERE `id`='$user_id'");
            }
            
            if ($result) {
                $this->name = $new_username;
                $this->email = $new_email;
                $this->native_lang = $new_native_lang;
                $this->learning_lang = $new_learning_lang;
                
                // if new password was set, then create new rememberme cookie
                if (empty($new_password)) {
                    return true;
                } else {
                    if ($this->login($new_username, $new_password)) {
                        return true;
                    }
                }
            } else {
                throw new \Exception ('There was an unknown problem trying to update your profile. Please try again later.');
            }
        }
        return true;
    }

    /**
     * Delete user account
     *
     * @return void
     */
    public function delete() {
        if ($this->isLoggedIn()) {
            $this->logout(true);
        }

        // delete files uploaded by user
        $table_names = array('texts', 'archivedtexts');
        
        foreach ($table_names as $table) {
            $user_id_col_name = $table == 'texts' ? 'user_id' : 'user_id';
            $source_uri_col_name = $table == 'texts' ? 'source_uri' : 'source_uri';
            $result = $this->con->query("SELECT $source_uri_col_name FROM $table WHERE $user_id_col_name='{$this->id}'");
            
            if ($result->num_rows > 0) {
                $file = new File();
                $filename = '';
                $file_extensions = array('.epub', '.mp3', '.ogg');

                while ($row = $result->fetch_assoc()) {
                    $filename = $row[$source_uri_col_name];
                    if (in_array(substr($filename, -5), $file_extensions)) {
                        $file->delete($filename);
                    }
                }
            }
        }
        
        // delete user from db
        $result = $this->con->query("DELETE FROM `users` WHERE `id`='{$this->id}'");
        if (!$result) {
            throw new \Exception('Oops! There was an unexpected problem trying to delete your account. Please try again later.');
        }
    } // end logout
    
    /**
     * Gives index of 639-1 iso codes in Language::$lg_iso_codes array
     *
     * @param string $lang_name
     * @return string
     */
    public function getLanguageIndex($lang_name) {
        $keys = array_keys(Language::$lg_iso_codes);
        for ($i=0; $i < count($keys)-1; $i++) { 
            if ($keys[$i] == $lang_name) {
                return $i;
            }
        }
    }
    
    /**
     * Update active language in db
     *
     * @param integer $lang_id
     * @return boolean
     */
    public function setActiveLang($lang_id) {
        $result = $this->con->query("SELECT `name` FROM `languages` WHERE `id` = '$lang_id'");
        
        if ($result) {
            $row = $result->fetch_assoc();
            $lang_name = $this->con->escape_string($row['name']);
            $user_id = $this->id;
            
            $result = $this->con->query("UPDATE `users` SET `learning_lang_iso` = '$lang_name' WHERE `id`='$user_id'");
            
            if ($result) {
                $this->learning_lang_id = $lang_id;
                $this->learning_lang = $lang_name;
            }
        }
        
        return $result;  
    }

    public function isAllowedToAccessElement($table, $id)
    {
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

        $result = $this->con->query("SELECT * FROM $table WHERE $id_col_name = '$id' AND $user_id_col_name = {$this->id}");
        
        if ($result && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Check if $password = user password
     *
     * @param string $password
     * @return boolean
     */
    private function checkPassword($password) {
        $user_id = $this->id;

        $result = $this->con->query("SELECT `password_hash` FROM `users` WHERE `id`='$user_id'");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['password_hash'];
                if (password_verify($password, $hashedPassword)) {
                    return true;
                } else {
                    throw new \Exception ('Username and password combination are incorrect. Please try again.');
                }
            }
    }
}


?>