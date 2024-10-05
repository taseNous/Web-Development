fetch('../php/get_request_citizen.php')
    .then(response => response.json())
    .then(data => {
        console.log("Data fetched from server:", data);

        data.forEach(function (position) {
            const lat = parseFloat(position.latitude);
            const lon = parseFloat(position.longitude);

            // Determine the icon based on whether rescuer_id is null
            var icon;
            if (position.rescuer_id) {
                // If rescuer_id is not null, use a different icon
                icon = L.icon({
                    iconUrl: '../img/request_occupied.svg', // Path to the custom icon for assigned offers
                    iconSize: [40, 40]
                });
            } else {
                // Default icon for offers without an assigned rescuer
                icon = L.icon({
                    iconUrl: '../img/request.svg', // Path to the default icon
                    iconSize: [40, 40]
                });
            }


            var marker = L.marker([lat, lon], { icon: icon }).addTo(markers_request_citizen);

            var popupContent = `
                <b>Request</b> <br>
                <b>Full Name:</b> ${position.first_name} ${position.last_name} <br>
                <b>Phone:</b> ${position.phone} <br>
                <b>Product:</b> ${position.product} <br>
                <b>People:</b> ${position.people} <br>
                <b>Rescuer ID:</b> ${position.rescuer_id} <br>
                <b>Latitude:</b> ${lat.toFixed(6)} <br>
                <b>Longitude:</b> ${lon.toFixed(6)} <br><br>
                <button class="take-on-btn btn btn-primary btn-sm" data-request-id="${position.id}">Take on</button>
            `;

            marker.bindPopup(popupContent).openPopup();
        });

        markers_request_citizen.addTo(map);
    })
    .catch(error => console.error('Error fetching data:', error));

// Add the event listener for the "Take on" button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('take-on-btn')) {
        const requestId = event.target.getAttribute('data-request-id');

        fetch('../php/take_on_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: requestId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('You have successfully taken on the request.');
                // Optionally, you can remove the marker or update it to show it’s assigned.
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});