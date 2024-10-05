// Fetch the admin positions from the server
fetch('../php/get_pos_admin.php')
.then(response => response.json())
.then(data => {
    data.forEach(function(position) {
        const lat = parseFloat(position.lat);
        const lon = parseFloat(position.lon);

        // Create the draggable marker
        var marker = L.marker([lat, lon],{ draggable: true,  icon: admin}).addTo(marker_admin);

        // Bind the initial popup with lat/lon
        marker.bindPopup('Base' + '<br>Latitude: ' + lat.toFixed(6) + '<br>Longitude: ' + lon.toFixed(6)).openPopup();

        // Store the original position in case the user cancels the drag
        var originalPosition = marker.getLatLng();

        // Listen for the dragend event
        marker.on('dragend', function(event) {
            var marker = event.target;
            var newPosition = marker.getLatLng();

            // Show a confirmation dialog
            if (confirm('Are you sure you want to update the marker position to:\nLatitude: ' + newPosition.lat.toFixed(6) + '\nLongitude: ' + newPosition.lng.toFixed(6) + '?')) {
                // If the user confirms, update the popup and send the updated position to the server
                marker.setPopupContent('Base' + '<br>Latitude: ' + newPosition.lat.toFixed(6) + '<br>Longitude: ' + newPosition.lng.toFixed(6)).openPopup();

                // Send the updated position to the server
                fetch('../php/update_pos_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: position.id, // Ensure the correct ID is sent
                        lat: newPosition.lat,
                        lon: newPosition.lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Position updated successfully!');
                    } else {
                        console.error('Error updating position:', data.error);
                    }
                })
                .catch(error => console.error('Fetch error:', error));
            } else {
                // If the user cancels, revert the marker to its original position
                marker.setLatLng(originalPosition);
                marker.setPopupContent('Latitude: ' + originalPosition.lat.toFixed(6) + '<br>Longitude: ' + originalPosition.lng.toFixed(6)).openPopup();
            }
        });
    });
    marker_admin.addTo(map);
})
.catch(error => console.error('Error fetching data:', error));