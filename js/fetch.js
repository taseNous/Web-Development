$(document).ready(function() {
    // Function to load announcements via AJAX
    function loadAnnouncements() {
        $.ajax({
            url: '../php/fetch_announcements.php', // URL to the PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var tableBody = $('#inventoryStatus tbody');
                    tableBody.empty(); // Clear the table body

                    if (response.announcements.length > 0) {
                        var i = 1;
                        $.each(response.announcements, function(index, announcement) {
                            // Create a new row with input field and offer button
                            var row = '<tr>' +
                                        '<td>' + i++ + '</td>' +  // Number
                                        '<td>' + announcement.category + '</td>' +  // Category
                                        '<td>' + announcement.product + '</td>' +  // Product
                                        // Ensure the quantity input has a unique ID based on index
                                        '<td><input type="number" min="1" class="quantity-input" id="quantity_' + index + '" /></td>' +  
                                        // Button knows the index to find the correct input
                                        '<td><button class="offer-btn" data-index="' + index + '" data-id="' + announcement.id + '" data-category="' + announcement.category + '" data-product="' + announcement.product + '">Offer</button></td>' + 
                                    '</tr>';

                            tableBody.append(row);
                        });
                    } else {
                        // If no announcements found
                        tableBody.append('<tr><td colspan="5">No announcements found</td></tr>');
                    }

                    // Attach click event listener to each "Offer" button
                    $('.offer-btn').click(function() {
                        var index = $(this).data('index');  // Get the index of the row
                        var category = $(this).data('category');
                        var product = $(this).data('product');
                        var quantity = $('#quantity_' + index).val();  // Find the correct input by index

                        if (!quantity) {
                            alert('Please enter a quantity');
                            return;
                        }

                        // AJAX to post the data to offer.php
                        $.ajax({
                            url: '../php/offer.php',
                            method: 'POST',
                            data: {
                                category: category,
                                product: product,
                                quantity: quantity,
                                announcement_id: $(this).data('id')  // Send the announcement_id
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log('Response from server:', response); // Log the response
                                if (response.status === 'success') {
                                    alert('Offer successfully made!');
                                } else {
                                    alert('Error: ' + response.error);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                                console.log('Response text:', xhr.responseText); // Log raw response
                            }
                        });
                    });
                } else {
                    alert('Failed to fetch announcements');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching announcements:', error);
            }
        });
    }

    // Load announcements when the page loads
    loadAnnouncements();
});
