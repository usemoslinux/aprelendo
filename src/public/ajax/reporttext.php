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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\ReportedTexts;
use Aprelendo\SharedTexts;
use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if ($_POST['text_id'] && $_POST['reason']) {
        $report_text = new ReportedTexts($pdo, $_POST['text_id'], $user->id);
        $report_text->add($_POST['reason']);

        $shared_text = new SharedTexts($pdo, $user->id, $user->lang_id);
        $shared_text->loadRecord($_POST['text_id']);

        // create & send email
        $subject = 'Reported text - ID ' . $shared_text->id;

        $message = "\r\n\r\nText title: " . $shared_text->title;
        $message .= "\r\n\r\nReport reason: " . $_POST['reason'];
        $message .= "\r\n\r\nUser who reported: " . $user->name . "\r\n\r\n";
        
        $email_sender = new EmailSender();

        $email_sender->mail->setFrom(SUPPORT_EMAIL, 'Aprelendo - Reported Text');
        $email_sender->mail->addAddress(SUPPORT_EMAIL);
        $email_sender->mail->Subject = $subject;
        $email_sender->mail->Body = $message;
        $email_sender->mail->isHTML(false);

        $email_sender->mail->send();

        $response = ['success' => true];
    }

    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
