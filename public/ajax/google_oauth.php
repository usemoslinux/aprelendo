<?php
require_once '../../includes/dbinit.php'; // connect to database

$user_id = $_POST["id"];
$user_name = $_POST["name"];
$user_email = $_POST["email"];

$sql = "SELECT * FROM users WHERE userEmail='$user_email'";

$result = $con->query($sql);

if($result && !empty($result->fetch_assoc())){
    $sql2 = "UPDATE users SET userGoogleId='$user_id' WHERE userEmail='$user_email'";
} else {
    $sql2 = "INSERT INTO users (userName, userEmail, userActive, userGoogleId) VALUES ('$user_name', '$user_email', '1', '$user_id')";

    // CREATE COOKIE
}

$result = $con->query($sql2);

if ($result) {
    echo "Updated Successful";
} else {
    echo "Error: " . $con->error;
}

?>