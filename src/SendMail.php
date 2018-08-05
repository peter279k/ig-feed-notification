<?php

namespace IgFeedNotification;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail
{
    public function __construct() {}

    public static function sendMail($msg, $media)
    {
        // send email
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0; // Set the SMTP Debug mode it can set 0, 1, 2
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST');  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;        // Enable SMTP authentication
        $mail->Username = getenv('MAIL_USERNAME');  // SMTP username
        $mail->Password = getenv('MAIL_PASSWORD'); // SMTP password
        $mail->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
        $mail->Port = getenv('MAIL_PORT');
        //Recipients
        $mail->setFrom('admin@igclerks.com', 'New Instagram Post');
        $mail->addAddress('dsamsondeen@gmail.com', 'dsamsondeen');
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $media->getCaption();
        $mail->Body    = $msg;
        $mail->AltBody = $media->getLink();
        $mail->send();
    }
}
