document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('structure-coordinate-map');
    const latitudeInput = document.getElementById('structure-latitude');
    const longitudeInput = document.getElementById('structure-longitude');

    if (!mapElement || !latitudeInput || !longitudeInput) {
        return;
    }

    const defaultLatitude = 53.72;
    const defaultLongitude = 91.44;

    const hasCoordinates =
        latitudeInput.value.trim() !== ''
        && longitudeInput.value.trim() !== ''
        && Number.isFinite(Number(latitudeInput.value))
        && Number.isFinite(Number(longitudeInput.value));

    const latitude = hasCoordinates
        ? Number(latitudeInput.value)
        : defaultLatitude;

    const longitude = hasCoordinates
        ? Number(longitudeInput.value)
        : defaultLongitude;

    const map = L.map(mapElement).setView(
        [latitude, longitude],
        hasCoordinates ? 16 : 13,
    );
    map.attributionControl.setPrefix(false);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    let marker = null;

    if (hasCoordinates) {
        marker = L.marker(
            [latitude, longitude],
            {
                draggable: true
            }
        ).addTo(map);
    }

    function createOrMoveMarker(lat, lng) {
        if (marker === null) {
            marker = L.marker(
                [lat, lng],
                {
                    draggable: true
                }
            ).addTo(map);

            marker.on('dragend', function () {
                const position = marker.getLatLng();

                updateCoordinates(
                    position.lat,
                    position.lng
                );
            });

            return;
        }

        marker.setLatLng([lat, lng]);
    }

    function updateCoordinates(lat, lng) {
        latitudeInput.value = lat.toFixed(6);
        longitudeInput.value = lng.toFixed(6);

        createOrMoveMarker(lat, lng);
    }

    map.on('click', function (event) {
        updateCoordinates(
            event.latlng.lat,
            event.latlng.lng
        );
    });

    if (marker !== null) {
        marker.on('dragend', function () {
            const position = marker.getLatLng();

            updateCoordinates(
                position.lat,
                position.lng
            );
        });
    }

    function updateMarkerFromInputs() {
        if (
            latitudeInput.value.trim() === ''
            || longitudeInput.value.trim() === ''
        ) {
            return;
        }

        const newLatitude = Number(latitudeInput.value);
        const newLongitude = Number(longitudeInput.value);

        if (
            !Number.isFinite(newLatitude)
            || !Number.isFinite(newLongitude)
            || newLatitude < -90
            || newLatitude > 90
            || newLongitude < -180
            || newLongitude > 180
        ) {
            return;
        }

        createOrMoveMarker(
            newLatitude,
            newLongitude
        );

        map.setView(
            [newLatitude, newLongitude],
            16
        );
    }

    latitudeInput.addEventListener(
        'change',
        updateMarkerFromInputs
    );

    longitudeInput.addEventListener(
        'change',
        updateMarkerFromInputs
    );
});