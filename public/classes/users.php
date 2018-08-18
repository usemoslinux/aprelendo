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
    private $lg_iso_codes = array(  
        'en' => 'english',
        'es' => 'spanish',
        'pt' => 'portuguese',
        'fr' => 'french',
        'it' => 'italian',
        'de' => 'german');
    
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
        
        // check if user already exists
        $result = $this->con->query("SELECT userName FROM users WHERE userName='$username'");
        if ($result->num_rows > 0) {
            throw new Exception ('Username already exists. Please try again.');
        }
        
        // check if email already exists
        $result = $this->con->query("SELECT userEmail FROM users WHERE userEmail='$email'");
        if ($result->num_rows > 0) {
            throw new Exception ('Email already exists. Did you forget you username or password?');
        }
        
        // create hash
        $options = ['cost' => 11];
        $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
        // save user data in db
        $result = $this->con->query("INSERT INTO users (userName, userPasswordHash, userEmail, userNativeLang, userLearningLang) 
            VALUES ('$username', '$password_hash', '$email', '$native_lang', '$learning_lang')"); 
        if ($result) {
            $user_id = $this->id = $this->con->insert_id;
            
            // save default language preferences for user
            foreach ($this->lg_iso_codes as $key => $value) {
                $translator_uri = mysqli_escape_string($this->con,'https://translate.google.com/m?hl=' . $value . '&sl=' . $this->lg_iso_codes[$native_lang] . '&&ie=UTF-8&q=%s');
                $dict_uri = $this->con->escape_string('https://www.linguee.com/' . $value . '-' . $this->lg_iso_codes[$native_lang] . '/search?source=auto&query=%s');
                
                $result = $this->con->query("INSERT INTO languages (LgUserId, LgName, LgDict1URI, LgTranslatorURI) 
                    VALUES ('$user_id', '$key', '$dict_uri', '$translator_uri')");
            }

            if ($result) {
                $result = $this->con->query("INSERT INTO preferences (prefUserId, prefFontFamily, prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning) 
                    VALUES ('$user_id', 'Helvetica', '12pt', '1.5', 'left', 'light', '1')");
                return true; // full registration process was successful
                
                if (!$result) {
                    throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
                }
            } else {
                throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
            }
        } else {
            throw new Exception ('There was an unexpected error trying to create your user profile. Please try again later.');
        }
        return true;
    } // end register
    
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
    } // end login
    
    /**
     * Logout user
     *
     * @return void
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            setcookie('user_token', '', time() - 3600, "/", false, 0); // delete user_token cookie
        } 
        
        header('Location:index.php');
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
                if ($result = $this->con->query("SELECT userName, userEmail, userNativeLang, userLearningLang FROM users WHERE userId='$user_id'")) {
                    $row = $result->fetch_assoc();
                    $this->name = $username = $row['userName'];
                    $this->email = $row['userEmail'];
                    $this->native_lang = $row['userNativeLang'];
                    $this->learning_lang = $learning_lang = $row['userLearningLang'];
                    
                    // get active language id (learning_lang_id)
                    if ($result = $this->con->query("SELECT LgId FROM languages WHERE LgUserId='$user_id' AND LgName='$learning_lang'")) {
                        $row = $result->fetch_assoc();
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
     * Converts full language names to 639-1 iso codes (ie. 'English' => 'en')
     *
     * @param string $iso_code
     * @return string
     */
    public function getLanguageName($iso_code) {
        return $this->lg_iso_codes[$iso_code];
    }
    
    /**
     * Gives index of 639-1 iso codes in $this->lg_iso_codes array
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