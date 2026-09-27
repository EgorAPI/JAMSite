<?php

declare(strict_types=1);

use App\Http\Controllers\AdminStructureController;

return [
    'GET' => [
        '/structures' => static function (array $app): void {
            (new AdminStructureController())->index($app);
        },

        '/structures/create' => static function (array $app): void {
            (new AdminStructureController())->create($app);
        },

        '/structures/edit' => static function (array $app): void {
            (new AdminStructureController())->edit($app);
        },

        '/structures/import' => static function (array $app): void {
            (new AdminStructureController())->importForm($app);
        },

        '/structures/export' => static function (array $app): void {
            (new AdminStructureController())->export($app);
        },
    ],

    'POST' => [

        '/structures/import' => static function (array $app): void {
            (new AdminStructureController())->import($app);
        },

        '/structures/store' => static function (array $app): void {
            (new AdminStructureController())->store($app);
        },

        '/structures/update' => static function (array $app): void {
            (new AdminStructureController())->update($app);
        },

        '/structures/surface-status' => static function (array $app): void {
            (new AdminStructureController())->updateSurfaceStatus($app);
        },

        '/structures/visibility' => static function (array $app): void {
            (new AdminStructureController())->updateVisibility($app);
        },

        '/structures/delete' => static function (array $app): void {
            (new AdminStructureController())->delete($app);
        },
    ],
];