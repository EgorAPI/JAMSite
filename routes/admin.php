<?php

declare(strict_types=1);

use App\Repositories\Json\PortfolioRepository;
use App\Repositories\Json\CategoryRepository;

return [
    'GET' => [
        '/' => static function (array $app): void {
            admin_require_auth($app);

            render(
                'admin/dashboard',
                ['title' => 'Джем Admin'],
                'admin'
            );
        },
        '/works' => static function (array $app): void {
            admin_require_auth($app);

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );
            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            render(
                'admin/works',
                [
                    'title' => 'Работы — Джем Admin',
                    'works' => $portfolio->all(false),
                    'categories' => $categories->all(false),
                ],
                'admin'
            );
        },
        '/works/create' => static function (array $app): void {
            admin_require_auth($app);

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            render(
                'admin/work-create',
                [
                    'title' => 'Добавить работу — Джем Admin',
                    'categories' => $categories->all(true),
                ],
                'admin'
            );
        },
        '/works/edit' => static function (array $app): void {
            admin_require_auth($app);

            $id = trim((string) ($_GET['id'] ?? ''));

            if ($id === '') {
                http_response_code(400);

                echo 'Не указан ID работы.';
                return;
            }

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );

            $work = $portfolio->find($id);

            if ($work === null) {
                http_response_code(404);

                echo 'Работа не найдена.';
                return;
            }

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            render(
                'admin/work-edit',
                [
                    'title' => 'Редактировать работу — Джем Admin',
                    'work' => $work,
                    'categories' => $categories->all(true),
                ],
                'admin'
            );
        },
        '/categories' => static function (array $app): void {
            admin_require_auth($app);

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            render(
                'admin/categories',
                [
                    'title' => 'Категории — Джем Admin',
                    'categories' => $categories->all(false),
                ],
                'admin'
            );
        },
        '/categories/create' => static function (array $app): void {
            admin_require_auth($app);

            render(
                'admin/category-create',
                [
                    'title' => 'Добавить категорию — Джем Admin',
                ],
                'admin'
            );
        },

        '/login' => static function (array $app): void {
            if (admin_is_authenticated()) {
                header('Location: /admin/');
                exit;
            }

            render(
                'admin/login',
                ['title' => 'Вход — Джем Admin'],
                'admin'
            );
        },
        '/slides' => static function (array $app): void {
            admin_require_auth($app);

            $slides = new \App\Repositories\Json\SlideRepository(
                $app['config']['paths']['data'] . '/slides.json'
            );

            render(
                'admin/slides',
                [
                    'title' => 'Слайды — Джем Admin',
                    'slides' => $slides->all(false),
                ],
                'admin'
            );
        },
        '/settings' => static function (array $app): void {
            admin_require_auth($app);

            $settingsRepository = new \App\Repositories\Json\SettingsRepository(
                $app['config']['paths']['data'] . '/settings.json'
            );

            render(
                'admin/settings',
                [
                    'title' => 'Настройки — Джем Admin',
                    'settings' => $settingsRepository->get(),
                ],
                'admin'
            );
        },
    ],

    'POST' => [
        '/login' => static function (array $app): void {
            
            if (admin_is_authenticated()) {
                header('Location: /admin/');
                exit;
            }
            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);
                echo 'Недействительный CSRF-токен.';
                return;
            }

            if (admin_login_is_locked($app)) {
                http_response_code(429);

                render(
                    'admin/login',
                    [
                        'title' => 'Вход — Джем Admin',
                        'error' => 'Слишком много неудачных попыток. Попробуйте войти позже.',
                    ],
                    'admin'
                );

                return;
            }

            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $adminConfig = $app['config']['security']['admin'] ?? [];

            $validUsername = (string) ($adminConfig['username'] ?? '');
            $passwordHash = (string) ($adminConfig['password_hash'] ?? '');

            if ($validUsername === '' || $passwordHash === '') {
                http_response_code(500);

                render(
                    'admin/login',
                    [
                        'title' => 'Вход — Джем Admin',
                        'error' => 'Администратор не настроен. Проверьте конфигурацию окружения.',
                    ],
                    'admin'
                );

                return;
            }

            if (
                $username === $validUsername &&
                $passwordHash !== '' &&
                password_verify($password, $passwordHash)
            ) {
                admin_clear_login_attempts();

                session_regenerate_id(true);

                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_last_activity'] = time();

                unset($_SESSION['_csrf']);

                header('Location: /admin/');
                exit;
            }

            admin_register_failed_login();

            http_response_code(422);


            render(
                'admin/login',
                [
                    'title' => 'Вход — Джем Admin',
                    'error' => 'Неверный логин или пароль.',
                ],
                'admin'
            );
        },

        '/logout' => static function (array $app): void {
            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);
                echo 'Недействительный CSRF-токен.';
                return;
            }

            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            header('Location: /admin/login');
            exit;
        },
        '/works' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $title = trim((string) ($_POST['title'] ?? ''));
            $category = trim((string) ($_POST['category'] ?? ''));
            $sort = (int) ($_POST['sort'] ?? 0);

            if ($title === '' || $category === '') {
                http_response_code(422);

                echo 'Название и категория обязательны.';
                return;
            }

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );

            if (
                !isset($_FILES['image'])
                || !is_array($_FILES['image'])
                || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
            ) {
                http_response_code(422);

                echo 'Изображение обязательно.';
                return;
            }

            if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                http_response_code(422);

                echo 'Ошибка загрузки изображения.';
                return;
            }
            $maxFileSize = 10 * 1024 * 1024;

            if (($_FILES['image']['size'] ?? 0) > $maxFileSize) {
                http_response_code(422);

                echo 'Размер изображения не должен превышать 10 МБ.';
                return;
            }
            $imagePath = '';

                if (
                    isset($_FILES['image'])
                    && is_array($_FILES['image'])
                    && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
                ) {
                    $tmpName = (string) $_FILES['image']['tmp_name'];
                    $imageInfo = getimagesize($tmpName);

                    if ($imageInfo === false) {
                        http_response_code(422);

                        echo 'Не удалось определить параметры изображения.';
                        return;
                    }

                    [$width, $height] = $imageInfo;

                    if ($width > 6000 || $height > 6000) {
                        http_response_code(422);

                        echo 'Размер изображения не должен превышать 6000×6000 пикселей.';
                        return;
                    }
                    $originalName = (string) $_FILES['image']['name'];

                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($tmpName);

                    $allowedTypes = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                    ];

                    if (!isset($allowedTypes[$mimeType])) {
                        http_response_code(422);

                        echo 'Допустимы только JPG, PNG и WebP.';
                        return;
                    }

                    $extension = $allowedTypes[$mimeType];

                    $fileName = bin2hex(random_bytes(8)) . '.webp';

                    $uploadDir = $app['config']['paths']['uploads'] . '/portfolio';
                    $destination = $uploadDir . '/' . $fileName;

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    switch ($mimeType) {
                        case 'image/jpeg':
                            $sourceImage = imagecreatefromjpeg($tmpName);
                            break;

                        case 'image/png':
                            $sourceImage = imagecreatefrompng($tmpName);
                            break;

                        case 'image/webp':
                            $sourceImage = imagecreatefromwebp($tmpName);
                            break;

                        default:
                            $sourceImage = false;
                            break;
                    }

                    if ($sourceImage === false) {
                        http_response_code(422);

                        echo 'Не удалось обработать изображение.';
                        return;
                    }

                    $maxWidth = 2000;
                    $maxHeight = 2000;

                    $sourceWidth = imagesx($sourceImage);
                    $sourceHeight = imagesy($sourceImage);

                    $targetWidth = $sourceWidth;
                    $targetHeight = $sourceHeight;

                    if ($sourceWidth > $maxWidth || $sourceHeight > $maxHeight) {
                        $scale = min(
                            $maxWidth / $sourceWidth,
                            $maxHeight / $sourceHeight
                        );

                        $targetWidth = (int) round($sourceWidth * $scale);
                        $targetHeight = (int) round($sourceHeight * $scale);

                        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
                        imagealphablending($resizedImage, false);
                        imagesavealpha($resizedImage, true);

                        $transparent = imagecolorallocatealpha(
                            $resizedImage,
                            0,
                            0,
                            0,
                            127
                        );

                        imagefilledrectangle(
                            $resizedImage,
                            0,
                            0,
                            $targetWidth,
                            $targetHeight,
                            $transparent
                        );

                        imagecopyresampled(
                            $resizedImage,
                            $sourceImage,
                            0,
                            0,
                            0,
                            0,
                            $targetWidth,
                            $targetHeight,
                            $sourceWidth,
                            $sourceHeight
                        );

                        imagedestroy($sourceImage);

                        $sourceImage = $resizedImage;
                    }
                    if (!imagewebp($sourceImage, $destination, 82)) {
                        imagedestroy($sourceImage);

                        http_response_code(500);

                        echo 'Не удалось сохранить изображение.';
                        return;
                    }

                    imagedestroy($sourceImage);

                    $imagePath = '/uploads/portfolio/' . $fileName;
                    $oldImagePath = (string) ($work['image'] ?? '');

                    if ($oldImagePath !== '') {
                        $oldImageFile = $app['config']['paths']['uploads']
                            . str_replace('/uploads', '', $oldImagePath);

                        if (is_file($oldImageFile)) {
                            unlink($oldImageFile);
                        }
                    }
                }

            $portfolio->create([
                'id' => bin2hex(random_bytes(8)),
                'title' => $title,
                'category' => $category,
                'image' => $imagePath,
                'active' => isset($_POST['active']),
                'featured' => isset($_POST['featured']),
                'sort' => $sort,
            ]);

            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Работа успешно добавлена.',
            ];
            header('Location: /admin/works');
            exit;
        },
        '/works/update' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));
            $title = trim((string) ($_POST['title'] ?? ''));
            $category = trim((string) ($_POST['category'] ?? ''));
            $sort = (int) ($_POST['sort'] ?? 0);

            if ($id === '' || $title === '' || $category === '') {
                http_response_code(422);

                echo 'ID, название и категория обязательны.';
                return;
            }

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );

            $work = $portfolio->find($id);

            if ($work === null) {
                http_response_code(404);

                echo 'Работа не найдена.';
                return;
            }

            $imagePath = $work['image'] ?? '';
            if (
                isset($_FILES['image'])
                && is_array($_FILES['image'])
                && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            ) {
                if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    http_response_code(422);

                    echo 'Ошибка загрузки изображения.';
                    return;
                }

                $maxFileSize = 10 * 1024 * 1024;

                if (($_FILES['image']['size'] ?? 0) > $maxFileSize) {
                    http_response_code(422);

                    echo 'Размер изображения не должен превышать 10 МБ.';
                    return;
                }

                $tmpName = (string) $_FILES['image']['tmp_name'];

                $imageInfo = getimagesize($tmpName);

                if ($imageInfo === false) {
                    http_response_code(422);

                    echo 'Не удалось определить параметры изображения.';
                    return;
                }

                [$width, $height] = $imageInfo;

                if ($width > 6000 || $height > 6000) {
                    http_response_code(422);

                    echo 'Размер изображения не должен превышать 6000×6000 пикселей.';
                    return;
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($tmpName);

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                ];

                if (!isset($allowedTypes[$mimeType])) {
                    http_response_code(422);

                    echo 'Допустимы только JPG, PNG и WebP.';
                    return;
                }

                $fileName = bin2hex(random_bytes(8)) . '.webp';

                $uploadDir = $app['config']['paths']['uploads'] . '/portfolio';
                $destination = $uploadDir . '/' . $fileName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                switch ($mimeType) {
                    case 'image/jpeg':
                        $sourceImage = imagecreatefromjpeg($tmpName);
                        break;

                    case 'image/png':
                        $sourceImage = imagecreatefrompng($tmpName);
                        break;

                    case 'image/webp':
                        $sourceImage = imagecreatefromwebp($tmpName);
                        break;

                    default:
                        $sourceImage = false;
                        break;
                }

                if ($sourceImage === false) {
                    http_response_code(422);

                    echo 'Не удалось обработать изображение.';
                    return;
                }

                $maxWidth = 2000;
                $maxHeight = 2000;

                $sourceWidth = imagesx($sourceImage);
                $sourceHeight = imagesy($sourceImage);

                if ($sourceWidth > $maxWidth || $sourceHeight > $maxHeight) {
                    $scale = min(
                        $maxWidth / $sourceWidth,
                        $maxHeight / $sourceHeight
                    );

                    $targetWidth = (int) round($sourceWidth * $scale);
                    $targetHeight = (int) round($sourceHeight * $scale);

                    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);

                    $transparent = imagecolorallocatealpha(
                        $resizedImage,
                        0,
                        0,
                        0,
                        127
                    );

                    imagefilledrectangle(
                        $resizedImage,
                        0,
                        0,
                        $targetWidth,
                        $targetHeight,
                        $transparent
                    );

                    imagecopyresampled(
                        $resizedImage,
                        $sourceImage,
                        0,
                        0,
                        0,
                        0,
                        $targetWidth,
                        $targetHeight,
                        $sourceWidth,
                        $sourceHeight
                    );

                    imagedestroy($sourceImage);

                    $sourceImage = $resizedImage;
                }

                if (!imagewebp($sourceImage, $destination, 82)) {
                    imagedestroy($sourceImage);

                    http_response_code(500);

                    echo 'Не удалось сохранить изображение.';
                    return;
                }

                imagedestroy($sourceImage);

                $imagePath = '/uploads/portfolio/' . $fileName;
                $oldImagePath = (string) ($work['image'] ?? '');

                if ($oldImagePath !== '') {
                    $oldImageFile = $app['config']['paths']['uploads']
                        . str_replace('/uploads', '', $oldImagePath);

                    if (is_file($oldImageFile)) {
                        unlink($oldImageFile);
                    }
                }

            }

            $portfolio->update($id, [
                'title' => $title,
                'category' => $category,
                'image' => $imagePath,
                'active' => isset($_POST['active']),
                'featured' => isset($_POST['featured']),
                'sort' => $sort,
            ]);

            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Работа успешно обновлена.',
            ];
            header('Location: /admin/works');
            exit;
        },
        '/works/delete' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID работы.';
                return;
            }

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );

            $work = $portfolio->find($id);

            if ($work === null) {
                http_response_code(404);

                echo 'Работа не найдена.';
                return;
            }

            $imagePath = (string) ($work['image'] ?? '');

            if ($imagePath !== '') {
                $imageFile = $app['config']['paths']['uploads']
                    . str_replace('/uploads', '', $imagePath);

                if (is_file($imageFile)) {
                    unlink($imageFile);
                }
            }

            $portfolio->delete($id);

            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Работа успешно удалена.',
            ];
            header('Location: /admin/works');
            exit;
        },
        '/categories' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $title = trim((string) ($_POST['title'] ?? ''));

            if ($title === '') {
                http_response_code(422);

                echo 'Название категории обязательно.';
                return;
            }

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            $categories->create([
                'id' => bin2hex(random_bytes(6)),
                'title' => $title,
                'active' => true,
                'sort' => 0,
            ]);

            header('Location: /admin/categories');
            exit;
        },
        '/categories/update' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));
            $title = trim((string) ($_POST['title'] ?? ''));

            if ($id === '' || $title === '') {
                http_response_code(422);

                echo 'ID и название категории обязательны.';
                return;
            }

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            $category = $categories->update($id, [
                'title' => $title,
            ]);

            if ($category === null) {
                http_response_code(404);

                echo 'Категория не найдена.';
                return;
            }

            header('Location: /admin/categories');
            exit;
        },
        '/categories/toggle' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID категории.';
                return;
            }

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            $category = $categories->find($id);

            if ($category === null) {
                http_response_code(404);

                echo 'Категория не найдена.';
                return;
            }

            $categories->update($id, [
                'active' => !empty($category['active']) ? false : true,
            ]);

            header('Location: /admin/categories');
            exit;
        },
        '/categories/delete' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID категории.';
                return;
            }

            $categories = new CategoryRepository(
                $app['config']['paths']['data'] . '/categories.json'
            );

            $portfolio = new PortfolioRepository(
                $app['config']['paths']['data'] . '/portfolio.json'
            );

            $category = $categories->find($id);

            if ($category === null) {
                http_response_code(404);

                echo 'Категория не найдена.';
                return;
            }

            foreach ($portfolio->all(false) as $work) {
                if (($work['category'] ?? null) === $id) {
                    $_SESSION['admin_flash'] = [
                        'type' => 'danger',
                        'message' => 'Нельзя удалить категорию, пока она используется в работах.',
                    ];

                    header('Location: /admin/categories');
                    exit;
                }
            }

            $categories->delete($id);

            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Категория успешно удалена.',
            ];

            header('Location: /admin/categories');
            exit;
        },
        '/slides' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            if (
                !isset($_FILES['image'])
                || !is_array($_FILES['image'])
                || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
            ) {
                http_response_code(422);

                echo 'Изображение обязательно.';
                return;
            }

            if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                http_response_code(422);

                echo 'Ошибка загрузки изображения.';
                return;
            }

            $tmpName = (string) $_FILES['image']['tmp_name'];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tmpName);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($allowedTypes[$mimeType])) {
                http_response_code(422);

                echo 'Допустимы только JPG, PNG и WebP.';
                return;
            }
            $maxFileSize = 10 * 1024 * 1024;

            if (($_FILES['image']['size'] ?? 0) > $maxFileSize) {
                http_response_code(422);

                echo 'Размер изображения не должен превышать 10 МБ.';
                return;
            }

            $imageInfo = getimagesize($tmpName);

            if ($imageInfo === false) {
                http_response_code(422);

                echo 'Не удалось определить параметры изображения.';
                return;
            }

            [$width, $height] = $imageInfo;

            if ($width > 6000 || $height > 6000) {
                http_response_code(422);

                echo 'Размер изображения не должен превышать 6000×6000 пикселей.';
                return;
            }

            $fileName = bin2hex(random_bytes(8)) . '.webp';

            $uploadDir = $app['config']['paths']['uploads'] . '/slides';
            $destination = $uploadDir . '/' . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($tmpName);
                    break;

                case 'image/png':
                    $sourceImage = imagecreatefrompng($tmpName);
                    break;

                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($tmpName);
                    break;

                default:
                    $sourceImage = false;
                    break;
            }

            if ($sourceImage === false) {
                http_response_code(422);

                echo 'Не удалось обработать изображение.';
                return;
            }

            $targetWidth = 1600;
            $targetHeight = 1280;

            $sourceWidth = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);

            $targetRatio = $targetWidth / $targetHeight;
            $sourceRatio = $sourceWidth / $sourceHeight;

            $cropX = 0;
            $cropY = 0;
            $cropWidth = $sourceWidth;
            $cropHeight = $sourceHeight;

            if ($sourceRatio > $targetRatio) {
                // Изображение слишком широкое — обрезаем по бокам.
                $cropWidth = (int) round($sourceHeight * $targetRatio);
                $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
            } elseif ($sourceRatio < $targetRatio) {
                // Изображение слишком высокое — обрезаем сверху и снизу.
                $cropHeight = (int) round($sourceWidth / $targetRatio);
                $cropY = (int) round(($sourceHeight - $cropHeight) / 2);
            }

            $resizedImage = imagecreatetruecolor(
                $targetWidth,
                $targetHeight
            );

            /* Сохраняем прозрачность PNG/WebP */
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);

            $transparent = imagecolorallocatealpha(
                $resizedImage,
                0,
                0,
                0,
                127
            );

            imagefilledrectangle(
                $resizedImage,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $transparent
            );

            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0,
                0,
                $cropX,
                $cropY,
                $targetWidth,
                $targetHeight,
                $cropWidth,
                $cropHeight
            );

            imagedestroy($sourceImage);

            if (!imagewebp($resizedImage, $destination, 82)) {
                imagedestroy($resizedImage);

                http_response_code(500);

                echo 'Не удалось сохранить изображение.';
                return;
            }

            imagedestroy($resizedImage);

            $imagePath = '/uploads/slides/' . $fileName;

            $slides = new \App\Repositories\Json\SlideRepository(
                $app['config']['paths']['data'] . '/slides.json'
            );

            $slides->create([
                'id' => bin2hex(random_bytes(8)),
                'image' => $imagePath,
                'active' => true,
                'sort_order' => 0,
            ]);
            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Слайд успешно добавлен.',
            ];
            header('Location: /admin/slides');
            exit;
        },
        '/slides/toggle' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID слайда.';
                return;
            }

            $slides = new \App\Repositories\Json\SlideRepository(
                $app['config']['paths']['data'] . '/slides.json'
            );

            $slide = $slides->find($id);

            if ($slide === null) {
                http_response_code(404);

                echo 'Слайд не найден.';
                return;
            }

            $slides->update($id, [
                'active' => !empty($slide['active']) ? false : true,
            ]);

            header('Location: /admin/slides');
            exit;
        },
        '/slides/sort' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID слайда.';
                return;
            }

            $slides = new \App\Repositories\Json\SlideRepository(
                $app['config']['paths']['data'] . '/slides.json'
            );

            $slide = $slides->update($id, [
                'sort_order' => $sortOrder,
            ]);

            if ($slide === null) {
                http_response_code(404);

                echo 'Слайд не найден.';
                return;
            }

            header('Location: /admin/slides');
            exit;
        },
        '/slides/delete' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $id = trim((string) ($_POST['id'] ?? ''));

            if ($id === '') {
                http_response_code(422);

                echo 'Не указан ID слайда.';
                return;
            }

            $slides = new \App\Repositories\Json\SlideRepository(
                $app['config']['paths']['data'] . '/slides.json'
            );

            $slide = $slides->find($id);

            if ($slide === null) {
                http_response_code(404);

                echo 'Слайд не найден.';
                return;
            }

            $imagePath = (string) ($slide['image'] ?? '');

            if ($imagePath !== '') {
                $imageFile = $app['config']['paths']['uploads']
                    . str_replace('/uploads', '', $imagePath);

                if (is_file($imageFile)) {
                    unlink($imageFile);
                }
            }

            $slides->delete($id);
            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Слайд успешно удалён.',
            ];

            header('Location: /admin/slides');
            exit;
        },
        '/settings' => static function (array $app): void {
            admin_require_auth($app);

            $csrfToken = $_POST['_csrf'] ?? null;

            if (!csrf_validate(is_string($csrfToken) ? $csrfToken : null)) {
                http_response_code(419);

                echo 'Недействительный CSRF-токен.';
                return;
            }

            $settingsRepository = new \App\Repositories\Json\SettingsRepository(
                $app['config']['paths']['data'] . '/settings.json'
            );

            $settingsRepository->update([
                'company_name' => trim((string) ($_POST['company_name'] ?? '')),
                'phone_1' => trim((string) ($_POST['phone_1'] ?? '')),
                'phone_2' => trim((string) ($_POST['phone_2'] ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'office_address' => trim((string) ($_POST['office_address'] ?? '')),
                'workshop_address' => trim((string) ($_POST['workshop_address'] ?? '')),
                'working_hours' => trim((string) ($_POST['working_hours'] ?? '')),
            ]);

            $_SESSION['admin_flash'] = [
                'type' => 'success',
                'message' => 'Настройки успешно сохранены.',
            ];

            header('Location: /admin/settings');
            exit;
        },
    ],
];