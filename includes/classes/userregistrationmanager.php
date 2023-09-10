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

use Aprelendo\Includes\Classes\Language;
use Aprelendo\Includes\Classes\UserPassword;
use Aprelendo\Includes\Classes\UserException;

class UserRegistrationManager extends DBEntity
{
    private User $user;

    public function __construct(User $user) {
        parent::__construct($user->pdo);
        $this->table = 'users';
        $this->user = $user;
    }

    /**
     * Creates new user & associated languages and reader preferences
     *
     * @param array $user_data
     * @return void
     */
    public function register(array $user_data): void
    {
        // $username, $email, $password, $native_lang = 'en', $lang = 'en', $send_email = false
        $this->user->name = $user_data['username'];
        $this->user->email = $user_data['email'];
        $password = $user_data['password'];
        $this->user->native_lang = isset($user_data['native_lang']) ? $user_data['native_lang'] : 'en';
        $this->user->lang = isset($user_data['lang']) ? $user_data['lang'] : 'en';
        $this->user->time_zone = isset($user_data['time_zone']) ? $user_data['time_zone'] : 'UTC';
        $send_email = isset($user_data['send_email']) ? $user_data['send_email'] : false;
        $this->user->is_active = false;

        // check if user already exists
        if ($this->user->existsByName($this->user->name)) {
            throw new UserException('Username already exists. Please try again.');
        }
        
        // check if email already exists
        if ($this->user->existsByEmail($this->user->email)) {
            throw new UserException('Email already exists. Did you <a class="alert-link" '
                . 'href="/forgotpassword">forget</a> you username or password?');
        }
        
        // create password hash
        $password_hash = UserPassword::createHash($password);

        // create account activation hash
        $activation_hash = $this->user->activation_hash = md5(rand(0, 1000));

        // save user data in db
        $user_active = !$send_email;
        $sql = "INSERT INTO `{$this->table}` (`name`, `password_hash`, `email`, `native_lang_iso`,
            `learning_lang_iso`, `time_zone`, `activation_hash`, `is_active`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $this->sqlExecute($sql, [
            [$this->user->name, $password_hash, $this->user->email, $this->user->native_lang,
            $this->user->lang, $this->user->time_zone, $activation_hash, (int)$user_active]
        ]);

        $user_id = $this->user->id = $this->pdo->lastInsertId();

        // create & save default language preferences for user
        $lang = new Language($this->pdo, $user_id);
        $lang->createInitialRecordsForUser($this->user->native_lang);

        $sql = "INSERT INTO `preferences` (`user_id`, `font_family`, `font_size`, `line_height`, `text_alignment`,
                `display_mode`, `assisted_learning`)
                VALUES (?, 'Helvetica', '12pt', '1.5', 'left', 'light', '1')";

        $this->sqlExecute($sql, [$user_id]);
        
        if ($send_email) {
            $this->sendActivationEmail($this->user->email, $this->user->name, $activation_hash);
        }
    } // end register()
    
    /**
     * Send activation email to user.
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
            $this->user->delete();
            throw new UserException('Oops! There was an unexpected error trying to send you an e-mail to activate '
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
        $sql = "SELECT COUNT(*)
                FROM `{$this->table}`
                WHERE `name`=? AND `activation_hash`=?";

        $num_rows = $this->sqlCount($sql, [$username, $hash]);
                    
        if ($num_rows == 0) {
            throw new UserException('User does not exist.');
        }

        $sql = "UPDATE `{$this->table}`
                SET `is_active`=true
                WHERE `name`=? AND `activation_hash`=?";
        
        $this->sqlExecute($sql, [$username, $hash]);
    } // end activate()
}
