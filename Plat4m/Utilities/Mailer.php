<?php

namespace Plat4m\Utilities;

use Exception;

use Plat4m\Utilities\Logger;
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
     * @throws Exception
     */
    public function send($recipientEmail, $recipientName, $subject, $body)
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
        $mail->isHTML(FALSE);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        try {
            return $mail->send();
        } catch (PHPMailerException $ex) {
            Logger::errExcept($ex);
            throw new Exception($mail->ErrorInfo, 500);
        }
    }
}
