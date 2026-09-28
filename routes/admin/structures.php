<?php

declare(strict_types=1);

use App\Http\Controllers\AdminStructureController;

return [
    'GET' => [
        '/structures' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->index($app);
        },

        '/structures/create' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->create($app);
        },

        '/structures/edit' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->edit($app);
        },

        '/structures/import' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->importForm($app);
        },

        '/structures/export' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->export($app);
        },
    ],

    'POST' => [

        '/structures/import' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->import($app);
        },

        '/structures/store' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->store($app);
        },

        '/structures/update' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->update($app);
        },

        '/structures/surface-status' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->updateSurfaceStatus($app);
        },

        '/structures/visibility' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->updateVisibility($app);
        },

        '/structures/delete' => static function (array $app): void {
            admin_require_auth($app);
            (new AdminStructureController())->delete($app);
        },
    ],
];