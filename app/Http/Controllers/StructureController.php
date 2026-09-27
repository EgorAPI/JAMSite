<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Json\StructureRepository;
use App\Services\StructureService;

final class StructureController
{
    public function __invoke(array $app): void
    {
        $repository = new StructureRepository(
            $app['config']['paths']['data'] . '/structures.json'
        );

        $service = new StructureService(
            $repository,
            $app['config']['structures']
        );

        $structures = $service->all(true);

        $structureTypes = $app['config']['structures']['types'];
        $structureStatuses = $app['config']['structures']['statuses'];

        require dirname(__DIR__, 3) . '/templates/pages/structures.php';
    }
}