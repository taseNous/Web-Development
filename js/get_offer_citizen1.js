// Fetch the citizen positions with requests from the server
fetch('../php/get_offers_citizen.php')
    .then(response => response.json())
    .then(data => {
        console.log("Data fetched from server:", data); // Debugging line

        // Loop through the positions and add markers for those with requests
        data.forEach(function(position) {
            // Convert lat and lon to floating-point numbers
            const lat = parseFloat(position.latitude);
            const lon = parseFloat(position.longitude);

            // Determine the icon based on whether rescuer_id is null
            var icon;
            if (position.rescuer_id) {
                // If rescuer_id is not null, use a different icon
                icon = L.icon({
                    iconUrl: '../img/offer_occupied.svg', // Path to the custom icon for assigned offers
                    iconSize: [40, 40]
                });
            } else {
                // Default icon for offers without an assigned rescuer
                icon = L.icon({
                    iconUrl: '../img/offer.svg', // Path to the default icon
                    iconSize: [40, 40]
                });
            }

            var marker = L.marker([lat, lon], { icon: icon }, {
                offerId: position.id}).addTo(markers_offer_citizen);

            var popupContent = `
                <b>Offer</b> <br>
                <b>Full Name:</b> ${position.first_name} ${position.last_name} <br>
                <b>Phone:</b> ${position.phone} <br>
                <b>Product:</b> ${position.product} <br>
                <b>Quantity:</b> ${position.quantity} <br>
                <b>Rescuer ID:</b> ${position.rescuer_id ? position.rescuer_id : 'Not Assigned'} <br>
                <b>Latitude:</b> ${lat.toFixed(6)} <br>
                <b>Longitude:</b> ${lon.toFixed(6)} <br><br>
                <button class="take-on-btn1 btn btn-primary btn-sm" data-offer-id="${position.id}">Take on</button>
            `;

            marker.bindPopup(popupContent);

            // Store the marker with a reference to update it later
            markers_offer_citizen.addLayer(marker);
        });

        markers_offer_citizen.addTo(map);
    })
    .catch(error => console.error('Error fetching data:', error));

// Add the event listener for the "Take on" button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('take-on-btn1')) {
        const offerId = event.target.getAttribute('data-offer-id');

        fetch('../php/take_on_offer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: offerId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('You have successfully taken on the offer.');

                    // Find the corresponding marker and update its icon
                    markers_offer_citizen.eachLayer(function(marker) {
                        if (marker.getPopup().getContent().includes(`data-offer-id="${offerId}"`)) {
                            // Update the marker icon to indicate it's occupied
                            marker.setIcon(L.icon({
                                iconUrl: 'img/offer_occupied.svg', // Path to the custom icon for assigned offers
                                iconSize: [40, 40]
                            }));

                            // Optionally, close the popup after taking the offer
                            marker.closePopup();
                        }
                    });
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
