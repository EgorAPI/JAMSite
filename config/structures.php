<?php

declare(strict_types=1);

return [
    'types' => [
        'billboard' => [
            'name' => 'Билборд',
            'surfaces' => [
                [
                    'name' => 'Сторона A',
                    'kind' => 'standard',
                ],
                [
                    'name' => 'Сторона B',
                    'kind' => 'standard',
                ],
            ],
        ],

        'prismatron' => [
            'name' => 'Призматрон',
            'surfaces' => [
                [
                    'name' => 'Баннерная сторона',
                    'kind' => 'standard',
                ],
                [
                    'name' => 'Сменяющаяся сторона',
                    'kind' => 'dynamic',
                ],
            ],
        ],

        'cityboard' => [
            'name' => 'Ситиборд',
            'surfaces' => [
                [
                    'name' => 'Сторона A',
                    'kind' => 'standard',
                ],
                [
                    'name' => 'Сторона B',
                    'kind' => 'standard',
                ],
            ],
        ],

        'cityscroller' => [
            'name' => 'Ситискроллер',
            'surfaces' => [
                [
                    'name' => 'Сторона A',
                    'kind' => 'dynamic',
                ],
                [
                    'name' => 'Сторона B',
                    'kind' => 'dynamic',
                ],
            ],
        ],

        'lightbox' => [
            'name' => 'Световой короб',
            'surfaces' => [
                [
                    'name' => 'Рекламная поверхность',
                    'kind' => 'standard',
                ],
            ],
        ],

        'brandmauer' => [
            'name' => 'Брендмауэр',
            'surfaces' => [
                [
                    'name' => 'Рекламная поверхность',
                    'kind' => 'standard',
                ],
            ],
        ],

        'pillar' => [
            'name' => 'Пилар',
            'surfaces' => [
                ['name' => 'Сторона A', 'kind' => 'standard'],
                ['name' => 'Сторона B', 'kind' => 'standard'],
                ['name' => 'Сторона C', 'kind' => 'standard'],
            ],
        ],
    ],

    'statuses' => [
        'free' => 'Свободна',
        'occupied' => 'Занята',
        'unavailable' => 'Недоступна',
    ],

    'default_status' => 'unavailable',
];