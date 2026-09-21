<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class MailService
{
    public function __construct(
        private array $config
    ) {
    }

    public function sendContactRequest(
        string $name,
        string $phone,
        string $email,
        string $message
    ): bool {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['port'];

            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                $this->config['from'],
                $this->config['from_name']
            );

            $mail->addAddress($this->config['to']);

            if ($email !== '') {
                $mail->addReplyTo($email, $name);
            }

            $mail->Subject = 'Новая заявка с сайта «Джем»';

            $mail->Body =
                "Имя: {$name}\n" .
                "Телефон: {$phone}\n" .
                "Email: {$email}\n\n" .
                "Сообщение:\n{$message}";

            $mail->send();

            return true;
        } catch (Exception $exception) {
            error_log(
                'Contact mail error: ' . $exception->getMessage()
            );

            return false;
        }
    }
}