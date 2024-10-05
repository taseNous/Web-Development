$(document).ready(function() {
    // Function to load inventory data via AJAX
    function loadInventory() {
        $.ajax({
            url: '../php/fetch_inventory.php', // URL to the PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var tableBody = $('#inventoryTable tbody');
                    tableBody.empty(); // Clear the table body

                    if (response.inventory.length > 0) {
                        var i = 1;
                        $.each(response.inventory, function(index, item) {
                            // Create a new row and append to the table body
                            var row = '<tr>' +
                                        '<td>' + i++ + '</td>' +  // Number
                                        '<td>' + item.category + '</td>' +  // Category
                                        '<td>' + item.product + '</td>' +  // Product
                                        '<td>' + item.volume + '</td>' +  // Volume
                                        '<td>' + item.weight + '</td>' +  // Weight
                                        '<td>' + item.pack_size + '</td>' +  // Pack Size
                                        '<td>' + item.type + '</td>' +  // Type
                                        '<td>' + item.size + '</td>' +  // Size
                                        '<td>' + item.quantity + '</td>' +  // Quantity
                                    '</tr>';
                            tableBody.append(row);
                        });
                    } else {
                        // If no inventory found
                        tableBody.append('<tr><td colspan="8">No inventory found</td></tr>');
                    }
                } else {
                    alert('Failed to fetch inventory');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching inventory:', error);
            }
        });
    }

    // Load inventory when the page loads
    loadInventory();
});