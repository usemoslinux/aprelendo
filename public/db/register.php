<?php 
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/users.php'); // loads User class

try {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
        $user = new User($con);
        $result = $user->register($_POST['username'], $_POST['email'], $_POST['password'], $_POST['native_lang'], $_POST['learning_lang']);
        if ($result) {
            $user->createRememberMeCookie($_POST['username'], $_POST['password']);
        } 
    } else {
        throw new Exception ('Either username, email or password were not provided. Please try again.');
    }    
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>