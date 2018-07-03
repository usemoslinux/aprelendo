<?php 

class User 
{
    public $id;
    public $name;
    public $email;
    public $learning_lang;
    public $learning_lang_id;
    public $native_lang;
    public $error_msg;
    
    private $con;
    private $token;
    private $token_expire_date;
    private $lg_iso_codes = array(  'en' => 'english',
    'es' => 'spanish',
    'pr' => 'portuguese',
    'fr' => 'french',
    'it' => 'italian',
    'de' => 'german');
    
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
     * @return bool
     */
    public function register($username, $email, $password, $native_lang = 'en', $learning_lang) {
        $username = $this->name = mysqli_escape_string($this->con, $username);
        $email = $this->email = mysqli_escape_string($this->con, $email);
        $password = mysqli_escape_string($this->con, $password);
        $native_lang = $this->native_lang = mysqli_escape_string($this->con, $native_lang);
        $learning_lang = $this->learning_lang = mysqli_escape_string($this->con, $learning_lang);
        
        // check if user already exists
        $result = mysqli_query($this->con, "SELECT userName FROM users WHERE userName='$username'");
        if (mysqli_num_rows($result) > 0) {
            $this->error_msg = 'Username already exists. Please try again.';
            return false;
        }
        
        // check if email already exists
        $result = mysqli_query($this->con, "SELECT userEmail FROM users WHERE userEmail='$email'");
        if (mysqli_num_rows($result) > 0) {
            $this->error_msg = 'Email already exists. Did you forget you username or password?';
            return false;
        }
        
        // create hash
        $options = ['cost' => 11];
        $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
        // save user data in db
        $result = mysqli_query($this->con, "INSERT INTO users (userName, userPasswordHash, userEmail, userNativeLang, userLearningLang) 
            VALUES ('$username', '$password_hash', '$email', '$native_lang', '$learning_lang')"); 
        if ($result) {
            $user_id = $this->id = mysqli_insert_id($this->con);
            
            // save default language preferences for user
            foreach ($this->lg_iso_codes as $key => $value) {
                $translator_uri = mysqli_escape_string($this->con,'https://translate.google.com/m?hl=' . $value . '&sl=' . $this->lg_iso_codes[$native_lang] . '&&ie=UTF-8&q=%s');
                $dict_uri = mysqli_escape_string($this->con, 'https://www.linguee.com/' . $value . '-' . $this->lg_iso_codes[$native_lang] . '/search?source=auto&query=%s');
                
                $result = mysqli_query($this->con, "INSERT INTO languages (LgUserId, LgName, LgDict1URI, LgTranslatorURI) 
                    VALUES ('$user_id', '$key', '$dict_uri', '$translator_uri')");
            }

            if ($result) {
                $result = mysqli_query($this->con, "INSERT INTO preferences (prefUserId, prefFontFamily, prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning) 
                    VALUES ('$user_id', 'Helvetica', '12pt', '1.5', 'left', 'light', '1')");
                return true; // full registration process was successful
                
                if (!$result) {
                    $this->error_msg = 'There was an unexpected error trying to create your user profile. Please try again later.';
                    return false;
                }
            } else {
                $this->error_msg = 'There was an unexpected error trying to create your user profile. Please try again later.';
                return false;
            }
        } else {
            $this->error_msg = 'There was an unexpected error trying to create your user profile. Please try again later.';
            return false;
        }
    } // end register
    
    /**
     * Creates "remember me" cookie
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function createRememberMeCookie($username, $password) {
        $username = mysqli_escape_string($this->con, $username);
        $password = mysqli_escape_string($this->con, $password);
        
        $result = mysqli_query($this->con, "SELECT * FROM users WHERE userName='$username'");
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['userPasswordHash'];
        if (mysqli_num_rows($result) == 0) {
            $this->error_msg = 'Username and password combination is incorrect. Please try again.';
            return false;
        }
        
        if (password_verify($password, $hashedPassword)) { // login successful, remember me
            // first, clean auth_tokens table
            $result = mysqli_query($this->con, "DELETE FROM auth_tokens WHERE expires < NOW()");
            
            // then, save new token in auth_tokens table
            $token = $this->token = $this->generateToken();
            $user_id = $this->user_id = $row['userId'];
            $time_stamp = time() + 3600; // 1 hour
            $expires = $this->token_expire_date = date('Y-m-d H:i:s', $time_stamp);
            
            $result = mysqli_query($this->con, "INSERT INTO auth_tokens (token, userid, expires) VALUES ('$token', $user_id, '$expires')");
            if ($result) {
                setcookie('user_token', $token, $time_stamp, "/", false, 0);
                return true;
            } else {
                $this->error_msg = 'There was a problem trying to create the authentication cookie. Please try again.';
                return false;
            }
        } else {
            $this->error_msg = 'Username and password combination is incorrect. Please try again.';
            return false;
        }
    } // end login
    
    /**
     * Logout user
     *
     * @return void
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            setcookie('user_token', $token, time() - 3600, "/", false, 0); // delete user_token cookie
        } 
        
        header('Location:index.php');
    } // end logout
    
    /**
     * Shows error in JSON format
     *
     * @param string $error_msg
     * @return json
     */
    public function showJSONError($error_msg = null) {
        $error = isset($error_msg) ? $error_msg : $this->error_msg;
        $error_array = array('error_msg' => $error);
        
        header('Content-Type: application/json');
        return json_encode($error_array);
    } // end showJSONError
    
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
            if ($result = mysqli_query($this->con, "SELECT userid FROM auth_tokens WHERE token='$token'")) {
                $row = mysqli_fetch_assoc($result);
                $this->id = $user_id = $row['userid'];
                
                // get username & other user data
                if ($result = mysqli_query($this->con, "SELECT userName, userEmail, userNativeLang, userLearningLang FROM users WHERE userId='$user_id'")) {
                    $row = mysqli_fetch_assoc($result);
                    $this->name = $username = $row['userName'];
                    $this->email = $row['userEmail'];
                    $this->native_lang = $row['userNativeLang'];
                    $this->learning_lang = $learning_lang = $row['userLearningLang'];
                    
                    // get active language id (learning_lang_id)
                    if ($result = mysqli_query($this->con, "SELECT LgId FROM languages WHERE LgUserId='$user_id' AND LgName='$learning_lang'")) {
                        $row = mysqli_fetch_assoc($result);
                        $this->learning_lang_id = $row['LgId'];
                        $is_logged = true;
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
     * @return bool
     */
    public function updateUserProfile($new_username, $new_email, $password, $new_password, $new_native_lang, $new_learning_lang) {
        // check if $password is correct, without it user would not have the right priviliges to update his profile
        $authorized = $this->checkPassword($password);

        if ($authorized) {
            $user_id = mysqli_escape_string($this->con, $this->id);
            $new_username = mysqli_escape_string($this->con, $new_username);
            $new_email = mysqli_escape_string($this->con, $new_email);
            $new_password = mysqli_escape_string($this->con, $new_password);
            $new_native_lang = mysqli_escape_string($this->con, $new_native_lang);
            $new_learning_lang = mysqli_escape_string($this->con, $new_learning_lang);
            
            // check if user already exists
            if ($this->name != $new_username) {
                $result = mysqli_query($this->con, "SELECT userName FROM users WHERE userName='$new_username'");
                if (mysqli_num_rows($result) > 0) {
                    // $this->error_msg = 'Username already exists. Please try again.';
                    // return false;
                    throw new Exception ('Username already exists. Please try again.');
                }
            }
            
            // check if email already exists
            if ($this->email != $new_email) {
                $result = mysqli_query($this->con, "SELECT userEmail FROM users WHERE userEmail='$new_email'");
                if (mysqli_num_rows($result) > 0) {
                    $this->error_msg = 'Email already exists. Please try using another one.';
                    return false;
                }
            }
            
            // was a new password given? In that case, save new password and replace the old one
            if (empty($new_password)) {
                $result = mysqli_query($this->con, "UPDATE users SET userName='$new_username', 
                userEmail='$new_email', userNativeLang='$new_native_lang', userLearningLang='$new_learning_lang' 
                WHERE userId='$user_id'");
            } else {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 11]);

                $result = mysqli_query($this->con, "UPDATE users SET userName='$new_username', userPasswordHash='$new_password_hash', 
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
                $this->error_msg = 'There was an unknown problem trying to update your profile. Please try again later.';
                return false;
            }
        }
    }
    
    /**
     * Converts full language names to 639-1 iso codes (ie. 'English' => 'en')
     *
     * @param string $iso_code
     * @return string
     */
    public function getLanguageName($iso_code) {
        return $this->lg_iso_codes[$iso_code];
    }
    
    /**
     * Converts 639-1 iso codes to full language names (ie. 'en' => 'English')
     *
     * @param string $lang_name
     * @return string
     */
    public function getLanguageIndex($lang_name) {
        $keys = array_keys($this->lg_iso_codes);
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
     * @return bool
     */
    public function setActiveLang($lang_id) {
        $result = mysqli_query($this->con, "SELECT LgName FROM languages WHERE LgId = '$lang_id'");
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $lang_name = mysqli_escape_string($this->con, $row['LgName']);
            $user_id = $this->id;
            
            $result = mysqli_query($this->con, "UPDATE users SET userLearningLang = '$lang_name' WHERE userId='$user_id'");
            
            if ($result) {
                $this->learning_lang_id = $lang_id;
            }
        }
        
        return $result;  
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
     * @return bool
     */
    private function checkPassword($password) {
        $user_id = $this->id;

        $result = mysqli_query($this->con, "SELECT userPasswordHash FROM users WHERE userId='$user_id'");
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $hashedPassword = $row['userPasswordHash'];
                if (password_verify($password, $hashedPassword)) {
                    return true;
                } else {
                    $this->error_msg = 'Incorrect password. Please try again.';
                    return false;
                }
            }
    }
}


?>