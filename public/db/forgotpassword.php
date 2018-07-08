<?php

try {
    if (isset($_POST['email'])) {
        require_once('dbinit.php'); // connect to database
        
        $email = $con->escape_string($_POST['email']);
        
        // check if email exists in db
        $result = $con->query("SELECT userEmail, userName FROM users WHERE userEmail='$email'");
        
        if ($result->num_rows > 0) {
            // get username associated to that email address
            $row = $result->fetch_array();
            $username = $row['userName'];
            
            // create password hash
            $options = [
                'cost' => 11,
            ];
            $password = time();
            $password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
            
            // replace user's password with new hash
            $result = $con->query("UPDATE users SET userPasswordHash='$password_hash' WHERE userName='$username'");
            if ($result) { // if password update is successful
                // create reset link & send email
                $reset_link = "https://localhost/forgotpassword.php?username=$username&reset=$password_hash";
                $to = $email;
                $subject = 'LangX - Password reset';
                $message = 'We received a request to reset the password for ' . $username . ' on LangX. ' .
                'If you submitted this request, you can use the following link to reset your password: ' . 
                $reset_link;
                
                $headers = 'From: langx@langx.com' . "\r\n" .
                'Reply-To: langx@langx.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                
                $mail_sent = mail($to, $subject, $message); // send email to reset password (requires 'sendmail' package in Debian/Ubuntu)
                if (!$mail_sent) {
                    throw new Exception ('There was an error trying to send you an e-mail with your new temporary password.');
                }
            } else { // if password update not successful
                throw new Exception ('There was an error trying to create your new temporary password.');
            }
        } // end if 
    } else {
        throw new Exception ('Oops! There was an unexpected error when trying to reset your password.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>