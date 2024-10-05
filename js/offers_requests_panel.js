// Fetch usertype from the server
fetch('../php/get_usertype_rescuer.php')
    .then(response => response.json())
    .then(data => {
        var userType = data.usertype;

        // Check if the user is a "rescuer"
        if (userType === "rescuer") {
        console.log("User is a rescuer");
        // Create the buttons
var buttonsControl = L.control({ position: 'bottomleft' });

buttonsControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-control-buttons');
    div.innerHTML = `
        <button id="showOffersBtn" class="btn btn-primary btn-sm">Offers</button>
        <button id="showRequestsBtn" class="btn btn-secondary btn-sm">Requests</button>
    `;
    return div;
};

buttonsControl.addTo(map);

// Offer/Request Panel Control
var offerRequestPanelControl = L.control({ position: 'bottomleft' });

offerRequestPanelControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-control-offer-request-panel');
    div.innerHTML = `
        <h4 id="panelTitle"></h4>
        <div id="panelContent">
            <!-- Panel content will be dynamically updated -->
        </div>
        <button id="closeOfferRequestPanelBtn" class="btn btn-danger btn-sm">Close</button>
    `;
    return div;
};

offerRequestPanelControl.addTo(map);

// Function to toggle the panel and update content
function togglePanel(show, title = "Offers", content = "") {
    var panel = document.querySelector('.leaflet-control-offer-request-panel');
    var panelTitle = document.getElementById('panelTitle');
    var panelContent = document.getElementById('panelContent');

    panelTitle.textContent = title;
    panelContent.innerHTML = content;
    panel.style.display = show ? 'block' : 'none';
}

// Event listener for the "Offers" button
document.getElementById('showOffersBtn').addEventListener('click', function() {
    // Fetch offers data from the backend
    fetch('../php/getOffers.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch offers');
            }
            return response.json();
        })
        .then(data => {
            let content = `
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Citizen</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((offer, index) => {
                content += `
                    <tr data-offer-id="${offer.offer_id}">
                        <td>${index + 1}</td>
                        <td>${offer.citizen}</td>
                        <td>${offer.phone}</td>
                        <td>${offer.category}</td>
                        <td>${offer.product}</td>
                        <td>${offer.quantity}</td>
                        <td>${new Date(offer.when_accepted).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-success btn-sm cancel-offer-btn">Cancel</button>
                            <button class="btn btn-warning btn-sm finish-offer-btn"data-offer-id="${offer.offer_id}">Finish</button>
                        </td>
                    </tr>
                `;
            });
            
            content += `</tbody></table>`;

            togglePanel(true, "Offers", content);
        })
        .catch(error => {
            console.error('Error fetching offers:', error);
            togglePanel(true, "Offers", "<p>Error loading offers.</p>");
        });
});

// Event listener for the "Requests" button
document.getElementById('showRequestsBtn').addEventListener('click', function() {
    // Fetch requests data from the backend
    fetch('../php/getRequests.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch requests');
            }
            return response.json();
        })
        .then(data => {
            let content = `
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Citizen</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((request, index) => {
                content += `
                    <tr data-request-id="${request.request_id}">
                        <td>${index + 1}</td>
                        <td>${request.citizen}</td>
                        <td>${request.phone}</td>
                        <td>${request.category}</td>
                        <td>${request.product}</td>
                        <td>${request.quantity}</td>
                        <td>${new Date(request.when_accepted).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-success btn-sm cancel-request-btn">Cancel</button>
                            <button class="btn btn-warning btn-sm finish-request-btn"data-request-id="${request.request_id}">Finish</button>
                        </td>
                    </tr>
                `;
            });
            
            content += `</tbody></table>`;

            togglePanel(true, "Requests", content);
        })
        .catch(error => {
            console.error('Error fetching requests:', error);
            togglePanel(true, "Requests", "<p>Error loading requests.</p>");
        });
});

// Event listener to close the panel
document.getElementById('closeOfferRequestPanelBtn').addEventListener('click', function() {
    togglePanel(false);
});

// Event listener for canceling an offer or request
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('cancel-offer-btn')) {
        // Get the offer ID from the row's data attribute
        const offerId = event.target.closest('tr').dataset.offerId;

        fetch('../php/cancelOffer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ offer_id: offerId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Offer has been canceled successfully.');
                // Optionally, remove the row or update the UI
                event.target.closest('tr').remove();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }

});

// Event listener for canceling a request
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('cancel-request-btn')) {
        // Get the request ID from the row's data attribute
        const requestId = event.target.closest('tr').dataset.requestId;

        fetch('../php/cancelRequest.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ request_id: requestId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Request has been canceled successfully.');
                // Optionally, remove the row or update the UI
                event.target.closest('tr').remove();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

// Add the event listener for the "Finish" button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('finish-offer-btn')) {
        // Retrieve the offer ID
        const offerId = event.target.getAttribute('data-offer-id');

        if (!offerId) {
            console.error('Error: Offer ID is missing in the button attribute.');
            return;
        }

        // Send the POST request to finish the offer
        fetch('../php/finishOffer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: offerId }), // Send the offer ID
        })
        .then(response => response.text())  // Get the raw response as text
        .then(text => {
            console.log('Raw Response:', text);  // Log the raw response for debugging

            let data;
            try {
                data = JSON.parse(text);  // Attempt to parse the text as JSON
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('An error occurred while processing the offer. Please try again later.');
                return;
            }

            if (data.success) {
                alert('Offer has been successfully finished.');
                // Optionally, remove the corresponding marker from the map or update the UI
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An unexpected error occurred. Please try again later.');
        });
    }
});

// Add the event listener for the "Finish" button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('finish-request-btn')) {
        // Retrieve the request ID
        const requestId = event.target.getAttribute('data-request-id');

        if (!requestId) {
            console.error('Error: Request ID is missing in the button attribute.');
            return;
        }

        // Send the POST request to finish the request
        fetch('../php/finishRequest.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: requestId }), // Send the request ID
        })
        .then(response => response.text())  // Get the raw response as text
        .then(text => {
            console.log('Raw Response:', text);  // Log the raw response for debugging

            let data;
            try {
                data = JSON.parse(text);  // Attempt to parse the text as JSON
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('The request finished successfully!');
                return;
            }

            if (data.success) {
                alert('Request has been successfully finished.');
                // Optionally, remove the corresponding marker from the map or update the UI
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An unexpected error occurred. Please try again later.');
        });
    }
});
 
}
    })
