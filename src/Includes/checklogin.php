<?php
// SPDX-License-Identifier: GPL-3.0-or-later

use Aprelendo\User;
use Aprelendo\UserAuth;

if (!isset($user)) {
    require_once 'dbinit.php'; // connect to database

    $user = new User($pdo);
    $user_auth = new UserAuth($user);
    $user_is_logged = $user_auth->isLoggedIn();
    $pdo->exec("SET time_zone='{$user->time_zone}';"); // use user local time zone

    if (!$user_is_logged && !isset($no_redirect)) {
        header('Location:/login.php');
        exit;
    }
}
