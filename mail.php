<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function email_send($email,$text){
    try {
        //Server settings
        $mail = new PHPMailer(true);
        $mail->charSet = "UTF-8";
        $mail->isSMTP();
        $mail->Host = '***';
        $mail->SMTPAuth = true;
        $mail->Username = '***';
        $mail->Password = '*****';
        //$mail->SMTPSecure = 'tls';
        $mail->Port = 587;


        //Recipients
        $mail->setFrom('****', '***');
        $mail->addReplyTo('*****', '*****');
        $mail->addAddress($email, 'User');

        //Content
        $mail->Subject = 'Raspi Bildirim';
        $mail->isHTML(true);
        $mailContent = $text;
        $mail->Body = $mailContent;
        
        $mail->send();
    } catch (Exception $e) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}