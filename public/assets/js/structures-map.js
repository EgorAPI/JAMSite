document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('structures-map');

    if (!mapElement || !window.structuresData) {
        return;
    }

    const structures = window.structuresData;

    const structureTypes = window.structureTypes ?? {};

    const structureStatuses = window.structureStatuses ?? {};

    const legend = document.getElementById(
        'structures-legend'
    );

    legend.innerHTML = '';

    const typeLabels = {
        billboard: 'Б',
        prismatron: 'П',
        cityboard: 'СБ',
        cityscroller: 'СС',
        lightbox: 'СК',
        brandmauer: 'БМ',
        pillar: 'ПЛ'
    };

    const typeColors = {
        billboard: '#dc3545',
        prismatron: '#fd7e14',
        cityboard: '#0d6efd',
        cityscroller: '#6f42c1',
        lightbox: '#198754',
        brandmauer: '#7e8286',
        pillar: '#d4be5e'
    };

    Object.entries(structureTypes).forEach(function ([typeKey, typeConfig]) {
        const item = document.createElement('div');

        item.className = 'structure-legend-item';
        item.dataset.type = typeKey;

        const marker = document.createElement('span');
        marker.className = 'structure-legend-marker';
        marker.textContent = typeLabels[typeKey] ?? '?';
        marker.style.backgroundColor =
            typeColors[typeKey] ?? '#212529';

        const name = document.createElement('span');
        name.textContent = typeConfig.name;

        item.appendChild(marker);
        item.appendChild(name);

        legend.appendChild(item);
    });

    function createStructureIcon(type) {

        const label = typeLabels[type] ?? '?';
        const color = typeColors[type] ?? '#212529';

        return L.divIcon({
            className: 'structure-marker',
            html:
                '<span style="background-color: '
                + color
                + ';">'
                + label
                + '</span>',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });
    }

    const map = L.map('structures-map', {
        attributionControl: false
    }).setView(
        [53.7212, 91.4424],
        13
    );

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    const markers = [];


    const panelContent = document.getElementById(
        'structure-panel-content'
    );


    structures.forEach(function (structure) {
        const marker = L.marker(
            [
                structure.latitude,
                structure.longitude
            ],
            {
                icon: createStructureIcon(structure.type)
            }
        ).addTo(map);

        marker.on('click', function () {
            const typeName =
                structureTypes[structure.type]?.name
                ?? structure.type;

            panelContent.innerHTML = '';

            const title = document.createElement('h3');
            title.className = 'h5 mb-2';
            title.textContent =
                typeName + ' № ' + structure.number;

            const description = document.createElement('p');
            description.className = 'mb-3';
            description.textContent =
                structure.location_description;

            panelContent.appendChild(title);
            panelContent.appendChild(description);

            (structure.surfaces ?? []).forEach(function (surface) {
                const surfaceElement = document.createElement('div');

                surfaceElement.className = 'structure-surface';

                if (surface.kind === 'dynamic') {
                    surfaceElement.textContent =
                        surface.name + ' — Сменяющаяся конструкция';
                } else {
                    const statusName =
                        structureStatuses[surface.status]
                        ?? surface.status;

                    surfaceElement.textContent =
                        surface.name + ' — ' + statusName;
                }

                panelContent.appendChild(surfaceElement);

                if (surface.image) {
                    const image = document.createElement('img');

                    image.src = surface.image;
                    image.alt =
                        typeName
                        + ' № '
                        + structure.number
                        + ' — '
                        + surface.name;

                    image.className = 'structure-surface-image';

                    surfaceElement.appendChild(image);
                }
            });
        });

        markers.push({
            structure: structure,
            marker: marker
        });
    });

        if (markers.length > 0) {
            const bounds = L.latLngBounds(
                markers.map(function (item) {
                    return item.marker.getLatLng();
                })
            );

            map.fitBounds(bounds, {
                padding: [40, 40],
                maxZoom: 15
            });
        }

        const typeFilter = document.getElementById(
            'structures-type-filter'
        );

        const searchInput = document.getElementById(
            'structures-search'
        );

        const availabilityFilter = document.getElementById(
            'structures-availability-filter'
        );

        function applyFilters() {
            const selectedType = typeFilter.value;
            const selectedAvailability = availabilityFilter.value;

            const query = searchInput.value
                .trim()
                .toLowerCase();

            markers.forEach(function (item) {
                const structure = item.structure;

                const number = String(
                    structure.number ?? ''
                ).toLowerCase();

                const description = String(
                    structure.location_description ?? ''
                ).toLowerCase();

                const matchesType =
                    selectedType === ''
                    || structure.type === selectedType;

                const matchesSearch =
                    query === ''
                    || number.includes(query)
                    || description.includes(query);

                const hasFreeSurface = (structure.surfaces ?? []).some(
                    function (surface) {
                        return surface.kind === 'standard'
                            && surface.status === 'free';
                    }
                );

                const matchesAvailability =
                    selectedAvailability === ''
                    || (
                        selectedAvailability === 'free'
                        && hasFreeSurface
                );

                const shouldShow =
                    matchesType
                    && matchesSearch
                    && matchesAvailability;

                if (shouldShow) {
                    item.marker.addTo(map);
                } else {
                    map.removeLayer(item.marker);
                }
            });
        }

        typeFilter.addEventListener(
            'change',
            applyFilters
        );

        searchInput.addEventListener(
            'input',
            applyFilters
        );

        availabilityFilter.addEventListener(
            'change',
            applyFilters
        );
    });
