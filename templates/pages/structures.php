<?php

declare(strict_types=1);

$pageTitle = 'Карта рекламных конструкций';

ob_start();
?>

<section class="container py-5">
    <h1>Карта рекламных конструкций</h1>

    <div class="structures-controls mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <input
                    type="search"
                    id="structures-search"
                    class="form-control"
                    placeholder="Поиск по номеру или местоположению"
                >
            </div>

            <div class="col-md-3">
                <select
                    id="structures-type-filter"
                    class="form-select"
                >
                    <option value="">Все типы</option>

                    <?php foreach ($structureTypes as $typeKey => $typeConfig): ?>
                        <option value="<?= htmlspecialchars($typeKey) ?>">
                            <?= htmlspecialchars($typeConfig['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">

            </div>
        </div>
    </div>

    <div class="alert alert-info py-2 mb-3" role="note">
        Актуальную информацию по скроллерам и призматронам, а также дригих конструкций уточняйте у менеджера.
    </div>

    <div class="row g-3">
        <div class="col-lg-9">
            <div
                id="structures-map"
                style="width: 100%; height: 600px;"
            ></div>
        </div>

        <div class="col-lg-3">
            <aside
                id="structure-panel"
                class="structure-panel h-100"
            >
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h5">
                            Условные обозначения
                        </h2>

                        <div id="structures-legend">
                            Здесь будут обозначения типов конструкций.
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="h5">
                            Информация о конструкции
                        </h2>

                        <div id="structure-panel-content">
                            Выберите конструкцию на карте.
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
    window.structuresData = <?= json_encode(
        $structures,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) ?>;

    window.structureTypes = <?= json_encode(
        $structureTypes,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) ?>;

    </script>

    <script src="/assets/js/structures-map.js"></script>

</section>

<?php

$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/main.php';