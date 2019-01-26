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

use Aprelendo\Includes\Classes\Languages;

class User 
{
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
    private $token;
    private $token_expire_date;
    
    
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
    public function register($username, $email, $password, $native_lang = 'en', $learning_lang) {
        $username = $this->name = $this->con->escape_string($username);
        $email = $this->email = $this->con->escape_string($email);
        $password = $this->con->escape_string($password);
        $native_lang = $this->native_lang = $this->con->escape_string($native_lang);
        $learning_lang = $this->learning_lang = $this->con->escape_string($learning_lang);
        $this->active = false;

        // check if user already exists
        $result = $this->con->query("SELECT userName FROM users WHERE userName='$username'");
        if ($result->num_rows > 0) {
            throw new Exception ('Username already exists. Please try again.');
        }
        
        // check if email already exists
        $result = $this->con->query("SELECT userEmail FROM users WHERE userEmail='$email'");
        if ($result->num_rows > 0) {
            throw new Exception ('Email already exists. Did you <a href="forgotpassword.php">forget</a> you username or password?');
        }
        
        // create password hash
        $options = ['cost' => 11];
        $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

        // create account activation hash
        $activation_hash = $this->activation_hash = md5(rand(0,1000));

        // save user data in db
        $result = $this->con->query("INSERT INTO users (userName, userPasswordHash, userEmail, userNativeLang, userLearningLang, userActivationHash, userActive) 
            VALUES ('$username', '$password_hash', '$email', '$native_lang', '$learning_lang', '$activation_hash', false)"); 
        if ($result) {
            $user_id = $this->id = $this->con->insert_id;
            
            // create & save default language preferences for user
            foreach (Language::$lg_iso_codes as $key => $value) {
                $translator_uri = mysqli_escape_string($this->con,'https://translate.google.com/m?hl=' . $value . '&sl=' . Language::$lg_iso_codes[$native_lang] . '&&ie=UTF-8&q=%s');
                $dict_uri = $this->con->escape_string('https://www.linguee.com/' . $value . '-' . Language::$lg_iso_codes[$native_lang] . '/search?source=auto&query=%s');
                
                $result = $this->con->query("INSERT INTO languages (LgUserId, LgName, LgDict1URI, LgTranslatorURI) 
                    VALUES ('$user_id', '$key', '$dict_uri', '$translator_uri')");
            }

            if ($result) {
                $result = $this->con->query("INSERT INTO preferences (prefUserId, prefFontFamily, prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning) 
                    VALUES ('$user_id', 'Helvetica', '12pt', '1.5', 'left', 'light', '1')");
                
                return $this->sendActivationEmail($email, $username, $activation_hash);
                
                if (!$result) {
                    throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
                }
            } else {
                throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
            }
        } else {
            throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
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
        
        $message = file_get_contents(APP_ROOT . 'templates/welcome.html');
        $message = str_replace('{{action_url}}', $reset_link, $message);
        $message = str_replace('{{name}}', $username, $message);
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . EMAIL_SENDER;
        
        // send email
        $mail_sent = @mail($to, $subject, $message, $headers); // send email to reset password (requires 'sendmail' package in Debian/Ubuntu)
        if (!$mail_sent) {
            throw new Exception ('There was an error trying to send you an e-mail to activate your account.');
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
        $result = $this->con->query("SELECT userActive FROM users WHERE userName='$username' AND userActivationHash='$hash'");
        
        if ($result->num_rows > 0) {
            $result = $this->con->query("UPDATE users SET userActive=true WHERE userName='$username' AND userActivationHash='$hash'");
            if (!$result) {
                throw new Exception ('Oops! There was an unexpected error when trying to activate your account.');
            }
        } else { // if no user is registered with that name & hash
            throw new Exception ('The activation link seems to be malformed. Please try again using the one provided in the email we\'ve sent you.');
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
    public function createRememberMeCookie($username, $password) {
        $username = $this->con->escape_string($username);
        $password = $this->con->escape_string($password);
        
        $result = $this->con->query("SELECT * FROM users WHERE userName='$username'");
        $row = $result->fetch_assoc();
        $hashedPassword = $row['userPasswordHash'];
        if ($result->num_rows == 0) { // wrong username
            throw new Exception ('Username and password combination is incorrect. Please try again.');
        }

        if ($row['userActive'] == false) {
            throw new Exception ('You need to activate your account first. Check your email for the activation link.');
        }
        
        if (password_verify($password, $hashedPassword)) { // login successful, remember me
            // first, clean auth_tokens table
            $result = $this->con->query("DELETE FROM auth_tokens WHERE expires < NOW()");
            
            // then, save new token in auth_tokens table
            $token = $this->token = $this->generateToken();
            $user_id = $this->user_id = $row['userId'];
            $time_stamp = time() + 31536000; // 1 year
            $expires = $this->token_expire_date = date('Y-m-d H:i:s', $time_stamp);
            
            $result = $this->con->query("INSERT INTO auth_tokens (token, userid, expires) VALUES ('$token', $user_id, '$expires')");
            if ($result) {
                setcookie('user_token', $token, $time_stamp, "/", false, 0);
            } else {
                throw new Exception ('There was a problem trying to create the authentication cookie. Please try again.');
            }
        } else { // wrong password
            throw new Exception ('Username and password combination is incorrect. Please try again.');
        }
        return true;
    } // end createRememberMeCookie
    
    /**
     * Logout user
     *
     * @param boolean $deleted_account
     * @return void
     */
    public function logout($deleted_account) {
        if ($deleted_account || $this->isLoggedIn()) {
            setcookie('user_token', '', time() - 3600, "/", false, 0); // delete user_token cookie
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
            if ($result = $this->con->query("SELECT userid FROM auth_tokens WHERE token='$token'")) {
                $row = $result->fetch_assoc();
                $this->id = $user_id = $row['userid'];
                
                // get username & other user data
                if ($result = $this->con->query("SELECT userName, userEmail, userNativeLang, userLearningLang, userPremiumUntil FROM users WHERE userId='$user_id'")) {
                    $row = $result->fetch_assoc();
                    $this->name = $row['userName'];
                    $this->email = $row['userEmail'];
                    $this->native_lang = $row['userNativeLang'];
                    $this->learning_lang = $learning_lang = $row['userLearningLang'];
                    $this->premium_until = $row['userPremiumUntil'];
                    
                    // get active language id (learning_lang_id)
                    if ($result = $this->con->query("SELECT LgId FROM languages WHERE LgUserId='$user_id' AND LgName='$learning_lang'")) {
                        $row = $result->fetch_assoc();
                        $this->learning_lang_id = $row['LgId'];
                        $is_logged = true;
                    }
                    
                    // restart premium status in case premium_until date expired
                    if ($this->premium_until !== NULL && $this->premium_until < date('Y-m-d')) {
                        if ($result = $this->con->query("UPDATE users SET userPremiumUntil=NULL WHERE userId='$user_id'")) {
                            $this->premium_until = NULL;
                        }
                    }
                }
            }
        }
        return $is_logged;
    } // end isLoggedIn
    
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
                $result = $this->con->query("SELECT userName FROM users WHERE userName='$new_username'");
                if ($result->num_rows > 0) {
                    throw new Exception ('Username already exists. Please try again.');
                }
            }
            
            // check if email already exists
            if ($this->email != $new_email) {
                $result = $this->con->query("SELECT userEmail FROM users WHERE userEmail='$new_email'");
                if ($result->num_rows > 0) {
                    throw new Exception ('Email already exists. Please try using another one.');
                }
            }
            
            // was a new password given? In that case, save new password and replace the old one
            if (empty($new_password)) {
                $result = $this->con->query("UPDATE users SET userName='$new_username', 
                userEmail='$new_email', userNativeLang='$new_native_lang', userLearningLang='$new_learning_lang' 
                WHERE userId='$user_id'");
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 11]);

                $result = $this->con->query("UPDATE users SET userName='$new_username', userPasswordHash='$new_password_hash', 
                userEmail='$new_email', userNativeLang='$new_native_lang', userLearningLang='$new_learning_lang' 
                WHERE userId='$user_id'");
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
                    if ($this->createRememberMeCookie($new_username, $new_password)) {
                        return true;
                    }
                }
            } else {
                throw new Exception ('There was an unknown problem trying to update your profile. Please try again later.');
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
            require_once('files.php');

            // delete files uploaded by user
            $table_names = array('texts', 'archivedtexts');
            
            foreach ($table_names as $table) {
                $user_id_col_name = $table == 'texts' ? 'textUserId' : 'atextUserId';
                $source_uri_col_name = $table == 'texts' ? 'textSourceURI' : 'atextSourceURI';
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
            $result = $this->con->query("DELETE FROM users WHERE userId='{$this->id}'");
            if (!$result) {
                throw new Exception('Oops! There was an unexpected problem trying to delete your account. Please try again later.');
            }
            $this->logout(true);
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
        $result = $this->con->query("SELECT LgName FROM languages WHERE LgId = '$lang_id'");
        
        if ($result) {
            $row = $result->fetch_assoc();
            $lang_name = $this->con->escape_string($row['LgName']);
            $user_id = $this->id;
            
            $result = $this->con->query("UPDATE users SET userLearningLang = '$lang_name' WHERE userId='$user_id'");
            
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
            array('texts', 'textID', 'textUserId'),
            array('archivedtexts', 'atextID', 'atextUserId'),
            array('sharedtexts', 'stextID', 'stextUserId'),
            array('words', 'wordID', 'wordUserId')
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
     * Generate token to store in cookie
     *
     * @param integer $length
     * @return string
     */
    private function generateToken($length = 20)
    {
        return bin2hex(random_bytes($length));
    } // end generateToken
    
    /**
     * Check if $password = user password
     *
     * @param string $password
     * @return boolean
     */
    private function checkPassword($password) {
        $user_id = $this->id;

        $result = $this->con->query("SELECT userPasswordHash FROM users WHERE userId='$user_id'");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['userPasswordHash'];
                if (password_verify($password, $hashedPassword)) {
                    return true;
                } else {
                    throw new Exception ('Username and password combination are incorrect. Please try again.');
                }
            }
    }
}


?>