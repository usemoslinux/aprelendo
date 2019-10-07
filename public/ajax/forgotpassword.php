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

require_once '../../includes/dbinit.php'; // connect to database

use Aprelendo\Includes\Classes\User;

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

try {
    if (isset($_POST['email'])) {
        $email = $con->escape_string($_POST['email']);
        
        // check if email exists in db
        $sql = "SELECT `name`, `password_hash` FROM `users` WHERE `email`=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
                
        if ($result->num_rows > 0) {
            // get username associated to that email address
            $row = $result->fetch_array();
            $username = $row['name'];
            $password_hash = $row['password_hash'];
            
            // create reset link & send email
            $reset_link = "https://www.aprelendo.com/forgotpassword.php?username=$username&reset=$password_hash";
            $to = $email;
            $subject = 'Aprelendo - Password reset';
            
            // get template
            $message = User::get_url_contents(APP_ROOT . 'templates/password_reset.html');

            // edit template
            $message = str_replace('{{action_url}}', $reset_link, $message);
            $message = str_replace('{{name}}', $username, $message);
            $message = str_replace('{{ip}}', $_SERVER['REMOTE_ADDR'], $message);
            $message = str_replace('{{device}}', $_SERVER['HTTP_USER_AGENT'], $message);
            $message = str_replace('{{current_year}}', date("Y"), $message);
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From:' . EMAIL_SENDER;
            
            $mail_sent = mail($to, $subject, $message, $headers, '-f ' . EMAIL_SENDER);
            if (!$mail_sent) {
                throw new \Exception ('There was an error trying to send you an e-mail with your new temporary password.');
            }
        } else { // if email address does not exist in db
            throw new \Exception ('No user registered with that email address. Please try again.');
        } 
    } else if(isset($_POST['username']) && isset($_POST['pass1']) && isset($_POST['pass2'])) {
        if ($_POST['pass1'] === $_POST['pass2']) {
            // create password hash
            $options = [
                'cost' => 11,
            ];
            $username = $_POST['username'];
            $password_hash = password_hash($_POST['pass1'], PASSWORD_BCRYPT, $options);

            $sql = "UPDATE `users` SET `password_hash`=? WHERE `name`=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $password_hash, $username);
            $result = $stmt->execute();
            $stmt->close();
            if (!$result) { // if password update is NOT successful
                throw new \Exception ('Oops! There was an unexpected error when trying to save your new password.');
            }
        } else {
            throw new \Exception ('The passwords you entered are not identical. Please try again.');
        }
    } else {
        throw new \Exception ('Oops! There was an unexpected error when trying to reset your password.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>