<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    
    header("Location: ../index.php?showLogin=true");
    exit;
}

if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "citizen") {
    
    header("Location: ../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.1/dist/MarkerCluster.Default.css" />
</head>

<body>
    
    <header>
        <?php include '../includes/navbar.php'; ?>
    </header>

        <div class="mapp">
            <div id="map"></div>
        </div>

        

        <footer>
            <?php include '../includes/footer.php'; ?>
        </footer>

</body>

</html>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.1/dist/leaflet.markercluster-src.js"></script>

<script>

    var map = L.map('map').setView([38.2462420, 21.7350847], 16); //Initialize the map

    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { // Add OSM layer
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var CartoDB_DarkMatter = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    });

    var Stadia_StamenWatercolor = L.tileLayer('https://tiles.stadiamaps.com/tiles/stamen_watercolor/{z}/{x}/{y}.{ext}', {
        minZoom: 1,
        maxZoom: 16,
        attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://www.stamen.com/" target="_blank">Stamen Design</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ext: 'jpg'
    });

    var Stadia_AlidadeSatellite = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.{ext}', {
        minZoom: 0,
        maxZoom: 20,
        attribution: '&copy; CNES, Distribution Airbus DS, © Airbus DS, © PlanetObserver (Contains Copernicus Data) | &copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        ext: 'jpg'
    });

    var baseMaps = {
        "Open Street Map": osm,
        "Dark Map": CartoDB_DarkMatter,
        "Water Color Map": Stadia_StamenWatercolor,
        "Satelite Map": Stadia_AlidadeSatellite
    };

    L.control.layers(baseMaps).addTo(map);

    var offer = L.icon({
        iconUrl: '../img/offer.svg',
        iconSize: [40, 40],
    });

    var request = L.icon({
        iconUrl: '../img/request.svg',
        iconSize: [40, 40],
    });

    var admin = L.icon({
        iconUrl: '../img/red_marker.png',
        iconSize: [40, 40],
    });

    // Define layers for different data types
    let marker_admin = L.layerGroup();
    let marker_rescuer = L.layerGroup();
    let markers_request_citizen = L.layerGroup();
    let markers_offer_citizen = L.layerGroup();

    // This array will store the line layers, so we can manage them later if needed
    let lines = [];

    // Fetch both rescuer positions, offer positions, and request positions
    Promise.all([
        fetch('../php/get_pos_rescuer.php').then(response => response.json()),
        fetch('../php/get_offers_citizen.php').then(response => response.json()),
        fetch('../php/get_request_citizen.php').then(response => response.json())
    ]).then(([rescuerData, offerData, requestData]) => {
        // Store markers in objects with IDs as keys for easy access
        const rescuerMarkers = {};
        const offerMarkers = {};
        const requestMarkers = {}; // New: Store request markers

        // Place rescuer markers and store references
        rescuerData.forEach(function(position) {
            const lat = parseFloat(position.lat);
            const lon = parseFloat(position.lon);

            // Create the rescuer marker
            var marker = L.marker([lat, lon]).setOpacity(1);
            
            marker.addTo(marker_rescuer);

            // Store marker by rescuer ID
            rescuerMarkers[position.id] = marker;

        });

    // Place offer markers and store references
    offerData.forEach(function(position) {
        const lat = parseFloat(position.latitude);
        const lon = parseFloat(position.longitude);

        // Create the offer marker
        var marker = L.marker([lat, lon], {
            offerId: position.id}).setOpacity(0);
        marker.addTo(markers_offer_citizen);

        // Store marker by offer ID
        offerMarkers[position.id] = marker;

        // Check if a rescuer is assigned to this offer
        if (position.rescuer_id) {
            const rescuerMarker = rescuerMarkers[position.rescuer_id];
            if (rescuerMarker) {
                // Draw a line between the rescuer and the offer
                const line = L.polyline([rescuerMarker.getLatLng(), marker.getLatLng()], {
                    color: 'red',
                    weight: 2,
                    opacity: 0.7
                }).addTo(map);

                // Store the line for future reference if needed
                lines.push(line);
            }
        }

        // Existing code for popup content...
    });

    // Place request markers and store references
    requestData.forEach(function(position) {
        const lat = parseFloat(position.latitude);
        const lon = parseFloat(position.longitude);

        // Create the request marker
        var marker = L.marker([lat, lon]).setOpacity(0);
        marker.addTo(markers_request_citizen); // Assuming you have a layer group for request markers

        // Store marker by request ID
        requestMarkers[position.id] = marker;

        // Check if a rescuer is assigned to this request
        if (position.rescuer_id) {
            const rescuerMarker = rescuerMarkers[position.rescuer_id];
            if (rescuerMarker) {
                // Draw a line between the rescuer and the request
                const line = L.polyline([rescuerMarker.getLatLng(), marker.getLatLng()], {
                    color: 'green', // Different color for rescuer-request lines
                    weight: 2,
                    opacity: 0.7
                }).addTo(map);

                // Store the line for future reference if needed
                lines.push(line);
            }
        }

        // Existing code for popup content...
    });

    }).catch(error => console.error('Error fetching data:', error));

    // Add filter control inside the map
    var filterControl = L.control({ position: 'topright' });

    filterControl.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'leaflet-control-layers leaflet-control-layers-toggle-filters');
        div.innerHTML = `
            <label><input type="checkbox" id="baseToggle" checked> Base</label><br>
            <label><input type="checkbox" id="rescuerToggle" checked> Rescuers</label><br>
            <label><input type="checkbox" id="requestToggle" checked> Requests</label><br>
            <label><input type="checkbox" id="offerToggle" checked> Offers</label>
        `;
        L.DomEvent.disableClickPropagation(div);
        return div;
    };

    filterControl.addTo(map);

    // Toggle marker visibility based on checkboxes
    document.getElementById('baseToggle').addEventListener('change', function() {
        this.checked ? map.addLayer(marker_admin) : map.removeLayer(marker_admin);
    });

    document.getElementById('rescuerToggle').addEventListener('change', function() {
        this.checked ? map.addLayer(marker_rescuer) : map.removeLayer(marker_rescuer);
    });

    document.getElementById('requestToggle').addEventListener('change', function() {
        this.checked ? map.addLayer(markers_request_citizen) : map.removeLayer(markers_request_citizen);
    });

    document.getElementById('offerToggle').addEventListener('change', function() {
        this.checked ? map.addLayer(markers_offer_citizen) : map.removeLayer(markers_offer_citizen);
    });

    



////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Wait for the DOM to fully load, including scripts like request_offer_panel.js
    window.addEventListener('load', function() {
        // Once the DOM and all scripts are loaded, run the proximity check
        setTimeout(checkProximityAndEnableButtonss, 500); // Adjust the timeout if needed
    });


    // Function to check distance and enable buttons if a rescuer is within 100 meters of a base
    function checkProximityAndEnableButtonss() {
        // Loop through all rescuer markers
        marker_rescuer.eachLayer(function(rescuerMarker) {
            // Loop through all offer markers
            markers_offer_citizen.eachLayer(function(offerMarker) {
                
                // Calculate distance between the rescuer and offer markers
                var distance = rescuerMarker.getLatLng().distanceTo(offerMarker.getLatLng());

                // Log distance for debugging
                console.log(`Distance between offer and rescuer: ${distance.toFixed(2)} meters`);

                // Assuming each offerMarker has a unique offerId in its options
                var offerId = offerMarker.options.offerId;

                // Now, we attempt to find the Finish button associated with this offerId
                var finishButton = document.querySelector(`.finish-offer-btn[data-offer-id="${offerId}"]`);

                console.log(`Looking for button with offerId: ${offerId}`);
                console.log(finishButton); // Check if this is null or if it finds the correct button


                // If the button exists, we handle enabling/disabling it
                if (finishButton) {
                    if (distance <= 100) {
                        finishButton.disabled = false; // Enable the button if within 50 meters
                    } else {
                        finishButton.disabled = true; // Disable the button if not within 50 meters
                    }
                } else {
                    console.log(`No button found for offerId: ${offerId}`);
                }
            });
        });
    }

    ///////////////////  RESCUER LOGGED IN ////////////////////////////////


    
    // Fetch usertype from the server
fetch('../php/get_usertype_rescuer.php')
    .then(response => response.json())
    .then(data => {
        var userType = data.usertype;

        // Check if the user is a "rescuer"
        if (userType === "rescuer") {
        console.log("User is a rescuer");
    

        var customPanelControl = L.control({ position: 'topright' });

    customPanelControl.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'leaflet-control-custom-panel');
        div.innerHTML = `
            <button id="loadDataBtn" class="btn btn-primary" disabled>Load</button> <br>
            <button id="unloadDataBtn" class="btn btn-danger" disabled>Unload</button>
        `;
        return div;
    };

    customPanelControl.addTo(map);

    // Create a custom control for the inventory panel
    var inventoryPanelControl = L.control({ position: 'topright' });

    inventoryPanelControl.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'leaflet-control-inventory-panel');
        div.innerHTML = `
            <h4>Available Products</h4>
            <div id="productList"></div>
            <button id="closeInventoryBtn" class="btn btn-danger btn-sm mt-2">Close</button>
        `;
        div.style.display = 'none'; // Initially hidden

        setTimeout(function() {
            // Event listener for Load button
            document.getElementById('loadDataBtn').addEventListener('click', function () {
                toggleInventoryPanel(true);

                fetch('../php/inventory_panel.php')
                    .then(response => response.json())
                    .then(data => {
                        const productList = document.getElementById('productList');
                        productList.innerHTML = '';

                        data.forEach(product => {
                            const productItem = document.createElement('div');
                            productItem.classList.add('product-item');
                            productItem.innerHTML = `
                                <input type="checkbox" id="product_${product.id}" name="product_${product.id}">
                                <label for="product_${product.id}"><strong>${product.product}</strong> - Quantity: ${product.quantity}</label>
                                <input type="number" id="quantity_${product.id}" name="quantity_${product.id}" min="1" max="${product.quantity}" value="1">
                            `;
                            productList.appendChild(productItem);
                        });

                        // Add submit button
                        if (!document.getElementById('submitInventoryBtn')) {
                            const submitButton = document.createElement('button');
                            submitButton.id = 'submitInventoryBtn';
                            submitButton.className = 'btn btn-success btn-sm mt-2';
                            submitButton.textContent = 'Submit';
                            document.getElementById('productList').appendChild(submitButton);

                            // Handle the submit button click event
                            submitButton.addEventListener('click', function () {
                                const selectedProducts = [];

                                document.querySelectorAll('.product-item input[type="checkbox"]').forEach(checkbox => {
                                    if (checkbox.checked) {
                                        const productId = checkbox.id.split('_')[1];
                                        const quantity = document.getElementById(`quantity_${productId}`).value;

                                        selectedProducts.push({ id: productId, quantity: quantity });
                                    }
                                });

                                if (selectedProducts.length > 0) {
                                    // Send selected products to the server via AJAX
                                    fetch('../php/load.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({ products: selectedProducts })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Products loaded successfully!');
                                            toggleInventoryPanel(false);
                                        } else {
                                            alert('Error loading products: ' + data.message);
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                                } else {
                                    alert('Please select at least one product and enter a valid quantity.');
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching inventory data:', error));
            });

        document.getElementById('unloadDataBtn').addEventListener('click', function () {

        // Logic to handle unloading data
        fetch('../php/unload.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Products unloaded successfully!');
                } else {
                    alert('Error unloading products: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });


            // Event listeners for close and unload buttons
            document.getElementById('unloadDataBtn').addEventListener('click', function () {
                toggleInventoryPanel(false);
            });

            document.getElementById('closeInventoryBtn').addEventListener('click', function() {
                toggleInventoryPanel(false);
            });
        }, 0); 

        return div;
    };

    inventoryPanelControl.addTo(map);

    // Function to check distance and enable buttons if a rescuer is within 100 meters of a base
    function checkProximityAndEnableButtons() {
        var buttonsEnabled = false;

        // Loop through all base markers
        marker_admin.eachLayer(function(baseMarker) {
            // Loop through all rescuer markers
            marker_rescuer.eachLayer(function(rescuerMarker) {
                // Calculate distance between the base and rescuer markers
                var distance = baseMarker.getLatLng().distanceTo(rescuerMarker.getLatLng());

                // Log distance for debugging
                console.log(`Distance between base and rescuer: ${distance.toFixed(2)} meters`);

                // If the distance is less than or equal to 100 meters, enable the buttons
                if (distance <= 100) {
                    buttonsEnabled = true;
                }
            });
        });

        // Enable or disable the buttons based on the proximity check
        document.getElementById('loadDataBtn').disabled = !buttonsEnabled;
        document.getElementById('unloadDataBtn').disabled = !buttonsEnabled;
    }

    // Ensure the check runs only after all markers are added
    setTimeout(checkProximityAndEnableButtons, 500); // Adjust the timeout if needed

    function toggleInventoryPanel(show) {
        var panel = document.querySelector('.leaflet-control-inventory-panel');
        panel.style.display = show ? 'block' : 'none';
    }


   ///////////////////////////////// OPEN LAOD///////////////////////////////////////////

   var toggleButtonControl = L.control({ position: 'bottomright' });

    toggleButtonControl.onAdd = function(map) {
        var button = L.DomUtil.create('button', 'leaflet-control-button');
        button.innerHTML = 'Open Load';
        button.onclick = function() {
            var panel = document.querySelector('.leaflet-control-panel');
            panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
            
            // Load data when panel is opened
            if (panel.style.display === 'block') {
                loadTableData();
            }
        };
        return button;
    };

    toggleButtonControl.addTo(map);

    // Create the panel as a Leaflet control
    var panelControl = L.control({ position: 'bottomright' });

    panelControl.onAdd = function(map) {
        var panelDiv = L.DomUtil.create('div', 'leaflet-control-panel');

        // Create the table structure
        panelDiv.innerHTML = `
            <h5>Inventory</h5>
            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        `;
        return panelDiv;
    };

    panelControl.addTo(map);


    /////////////////////// RESCUER LOAD  ///////////////////////////////////////////

    function loadTableData() {
        $.ajax({
            url: '../php/rescuer_load.php', // Path to your PHP file
            method: 'GET', // 'GET' is fine for retrieving data
            dataType: 'json', // Expecting JSON data
            success: function(data) {
                var tableBody = $('#inventoryTable tbody');
                tableBody.empty(); // Clear the table before adding new data

                if (data && Array.isArray(data)) {
                    // Populate the table with fetched data
                    data.forEach(function(item, index) {
                        var row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.category}</td>
                                <td>${item.product}</td>
                                <td>${item.quantity}</td>
                            </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    console.error('Unexpected data format:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
 

    
}
    })

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/get_request_citizen1.js"></script> 
<script src="../js/get_offer_citizen1.js"></script> 
<script src="../js/get_pos_rescuer.js"></script> 
<script src="../js/get_pos_admin1.js"></script> 
<script src="../js/offers_requests_panel.js"></script>