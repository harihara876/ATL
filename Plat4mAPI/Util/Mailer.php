<?php

namespace Plat4mAPI\Util;

use Plat4mAPI\Util\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer
{
    /**
     * Send email.
     * @param string $to To address.
     * @param string $subject Subject.
     * @param string $body Body.
     * @return bool Status.
     */
    public function send($recipientEmail, $recipientName, $subject, $body,$attachment='',$filename='')
    {
        $mail = new PHPMailer(TRUE);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = EMAIL_SMTP_HOST;
        $mail->SMTPAuth = TRUE;
        $mail->Username = EMAIL_SENDER;
        $mail->Password = EMAIL_SENDER_PASSWORD;
        $mail->SMTPSecure = EMAIL_SMTP_SECURITY;
        $mail->Port = EMAIL_SMTP_PORT;
        $mail->From = EMAIL_SENDER;
        $mail->FromName = EMAIL_SENDER_NAME;
        $mail->addAddress($recipientEmail, $recipientName);
        $mail->addStringAttachment($attachment,$filename);
        $mail->isHTML(FALSE);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        try {
            return $mail->send();
        } catch (PHPMailerException $ex) {
            Logger::errExcept($ex);
            return FALSE;
        }
    }
}
