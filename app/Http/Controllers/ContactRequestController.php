<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MailService;

final class ContactRequestController
{
    public function __invoke(array $app): void
    {
        $csrfToken = $_POST['_csrf'] ?? null;

        if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
            $_SESSION['contact_errors'] = [
                'Сессия формы устарела. Обновите страницу и попробуйте ещё раз.',
            ];

            header('Location: /#contact-form');
            exit;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $privacyAccepted = isset($_POST['privacy']);
        $website = trim((string) ($_POST['website'] ?? ''));

        if ($website !== '') {
            http_response_code(400);
            return;
        }

        $lastSubmitAt = $_SESSION['contact_last_submit_at'] ?? 0;

        if (
            is_int($lastSubmitAt) &&
            (time() - $lastSubmitAt) < 30
        ) {
            $_SESSION['contact_errors'] = [
                'Слишком частая отправка. Попробуйте ещё раз через несколько секунд.',
            ];

            header('Location: /#contact-form');
            exit;
        }

        $errors = [];

        if ($name === '') {
            $errors[] = 'Укажите имя.';
        }

        if ($phone === '') {
            $errors[] = 'Укажите телефон.';
        }

        if ($phone !== '' && mb_strlen($phone) < 5) {
            $errors[] = 'Укажите корректный телефон.';
        }

        if ($message === '') {
            $errors[] = 'Опишите задачу.';
        }

        if ($message !== '' && mb_strlen($message) < 5) {
            $errors[] = 'Сообщение слишком короткое.';
        }

        if (!$privacyAccepted) {
            $errors[] = 'Необходимо согласие на обработку персональных данных.';
        }

        if (mb_strlen($name) > 100) {
            $errors[] = 'Имя слишком длинное.';
        }

        if (mb_strlen($phone) > 50) {
            $errors[] = 'Телефон слишком длинный.';
        }

        if (
            $email !== '' &&
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Укажите корректный email.';
        }

        if (mb_strlen($message) > 3000) {
            $errors[] = 'Сообщение слишком длинное.';
        }

        if ($errors !== []) {

            $_SESSION['contact_old'] = [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'message' => $message,
                'privacy' => $privacyAccepted,
            ];

            $_SESSION['contact_errors'] = $errors;

            header('Location: /#contact-form');
            exit;
        }

        $mailService = new MailService(
            $app['config']['mail']
        );

        $sent = $mailService->sendContactRequest(
            $name,
            $phone,
            $email,
            $message
        );

        if (!$sent) {
            $_SESSION['contact_errors'] = [
                'Не удалось отправить заявку. Попробуйте ещё раз позже.',
            ];

            $_SESSION['contact_old'] = [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'message' => $message,
                'privacy' => $privacyAccepted,
            ];

            header('Location: /#contact-form');
            exit;
        }

        $_SESSION['contact_last_submit_at'] = time();
        $_SESSION['contact_success'] = 'Заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.';

       header('Location: /#contact-form');
        exit;
    }
}