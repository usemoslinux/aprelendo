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

require_once '../../includes/dbinit.php'; // connect to database

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\User;

try {
    if (isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['message']) && !empty($_POST['message']) ) {
        
        $name = $_POST['name'];
        $to = $_POST['email'];
        $message = $_POST['message'];

        // check if email is valid
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('The email address you entered is invalid. Please try again.');
        } 

        // check if fields have the allowed length
        if (strlen($name) > 100 || strlen($to) > 100 || strlen($message) > 5000) {
            throw new \Exception('You have exceeded the allowed length for one or more of the fields. Correct this and try again.');
        }

        // create & send email

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . EMAIL_SENDER;

        $subject = 'Support request - ' . $name;

        $message .= "\r\n\r\nE-mail: " . $to;
        $message .= "\r\n\r\nIP: " . $_SERVER['REMOTE_ADDR'];
        $message .= "\r\n\r\nDevice: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n\r\n";
        // it's important to include that last double \r\n in $message, otherwise email won't be well formed. This is true especially because Content-Type:text/plain
        
        $mail_sent = mail(SUPPORT_EMAIL, $subject, $message, $headers, '-f ' . EMAIL_SENDER);
        if (!$mail_sent) {
            throw new \Exception('There was an unexpected error trying to send your query. Please try again later.');
        } 
    } else {
        throw new \Exception('You need to complete all form fields in order to send your query. Please try again.');
    }    
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>