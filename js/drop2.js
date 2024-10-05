$(document).ready(function() {
    // JSON data containing categories
    const jsonData = {
        "items": [
            {"category": "Beverages"},
            {"category": "Food"},
            {"category": "Clothing"},
            {"category": "Personal Hygiene"},
            {"category": "Kitchen Supplies"},
            {"category": "Medical Supplies"}
        ]
    };

    // Function to populate categories from JSON into the dropdown
    function loadCategories() {
        var dropdown = $('#productCategory1');
        dropdown.empty(); // Clear existing options
        dropdown.append('<option value="">All Categories</option>'); // Default option

        // Populate the dropdown from JSON data
        $.each(jsonData.items, function(index, item) {
            dropdown.append('<option value="' + item.category + '">' + item.category + '</option>');
        });
    }

    // Function to load inventory data via AJAX based on the selected category
    function loadInventory(category = '') {
        $.ajax({
            url: '../php/fetch_inventory.php', // PHP script to fetch data from the SQL table
            method: 'GET',
            data: { category: category }, // Pass the selected category
            dataType: 'json',
            success: function(response) {
                var tableBody = $('#inventoryTable tbody');
                tableBody.empty(); // Clear the table body

                if (response.success && response.inventory.length > 0) {
                    var i = 1;
                    $.each(response.inventory, function(index, item) {
                        var row = '<tr>' +
                                    '<td>' + i++ + '</td>' +
                                    '<td>' + item.category + '</td>' +
                                    '<td>' + item.product + '</td>' +
                                    '<td>' + (item.volume || '') + '</td>' +
                                    '<td>' + (item.weight || '') + '</td>' +
                                    '<td>' + (item.pack_size || '') + '</td>' +
                                    '<td>' + (item.type || '') + '</td>' +
                                    '<td>' + (item.size || '') + '</td>' +
                                    '<td>' + item.quantity + '</td>' +
                                  '</tr>';
                        tableBody.append(row);
                    });
                } else {
                    // If no inventory found
                    tableBody.append('<tr><td colspan="9">No inventory found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching inventory:', error);
            }
        });
    }

    // Event listener for category dropdown change
    $('#productCategor1').on('change', function() {
        var selectedCategory = $(this).val();
        loadInventory(selectedCategory); // Reload the inventory table based on the selected category
    });

    // Initialize: load categories and inventory
    loadCategories();  // Load categories from the JSON file
    loadInventory();   // Initially load all inventory
});
