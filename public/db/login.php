<?php 
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/users.php'); // loads User class

try {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user = new User($con);
        $user->createRememberMeCookie($_POST['username'], $_POST['password']);
    } else {
        throw new Exception ('Either username, email or password were not provided. Please try again.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>