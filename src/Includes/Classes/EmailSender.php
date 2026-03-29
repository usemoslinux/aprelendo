<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailSender
{
    public $mail;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mail = new PHPMailer(true); // passing true enables exceptions
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mail->isSMTP();
        $this->mail->Host       = EMAIL_HOST;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = EMAIL_SENDER_USERNAME;
        $this->mail->Password   = EMAIL_SENDER_PASSWORD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;

        $this->mail->setFrom(EMAIL_SENDER, 'Aprelendo');
    } // end __construct()
}
