$(document).ready(function() {
    // Function to load offers via AJAX
    function loadOffers() {
        $.ajax({
            url: '../php/fetch_offers.php', // URL of the PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var offersTableBody = $('#userOffersTable tbody');
                    offersTableBody.empty(); // Clear the table body

                    if (response.offers.length > 0) {
                        var i = 1;
                        $.each(response.offers, function(index, offer) {
                            // Create a new row and append to the table body
                            var row = '<tr>' +
                                        '<td>' + i++ + '</td>' +  // Number
                                        '<td>' + offer.category + '</td>' +  // Category
                                        '<td>' + offer.product + '</td>' +  // Product
                                        '<td>' + offer.quantity + '</td>' +  // Quantity
                                        '<td>' +
                                            '<form action="../php/delete_offer.php" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this offer?\');">' +
                                                '<input type="hidden" name="offer_id" value="' + offer.id + '">' +
                                                '<button type="submit" class="btn btn-danger">Delete</button>' +
                                            '</form>' +
                                        '</td>' +
                                    '</tr>';

                            // Append the new row to the table body
                            offersTableBody.append(row);
                        });
                    } else {
                        // If no offers, display a message
                        offersTableBody.append('<tr><td colspan="5">You haven\'t made any offers yet.</td></tr>');
                    }
                } else {
                    alert('Failed to fetch offers: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching offers:', error);
            }
        });
    }

    // Load offers when the page loads
    loadOffers();
});
