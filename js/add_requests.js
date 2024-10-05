$(document).ready(function() {
    // Function to load request history from the server
    function loadRequestHistory() {
        $.ajax({
            url: '../php/fetch_requests.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var tableBody = $('#userdata tbody');
                tableBody.empty(); // Clear any existing rows

                if (data.error) {
                    console.error('Error fetching data:', data.error);
                } else {
                    // Loop through the returned data and create table rows
                    $.each(data, function(index, request) {
                        var row = "<tr>" +
                            "<td>" + (index + 1) + "</td>" + // Auto-increment number
                            "<td>" + request.category + "</td>" +
                            "<td>" + request.product + "</td>" +
                            "<td>" + request.people + "</td>" +
                            "</tr>";

                        tableBody.append(row);
                    });
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to handle category and product dropdowns
    $.getJSON('../json/proionta.json', function(jsonData) {
        var data = jsonData;
        var categorySelect = $('#productCategory');
        var categories = [];

        // Populate Category Dropdown
        $.each(data.items, function(i, item) {
            if ($.inArray(item.category, categories) === -1) {
                categories.push(item.category);
                categorySelect.append($('<option>', {
                    value: item.category,
                    text: item.category
                }));
            }
        });

        // Sort products alphabetically by name before populating the dropdown
        var productNameSelect = $('#productName');
        var sortedProducts = data.items.sort((a, b) => a.name.localeCompare(b.name));

        // Populate the "Search for a Product" dropdown alphabetically
        $.each(sortedProducts, function(i, item) {
            productNameSelect.append($('<option>', {
                value: item.name,
                text: item.name
            }));
        });

        // Update the product dropdown when a category is selected
        categorySelect.change(function() {
            var selectedCategory = $(this).val();
            productNameSelect.empty(); // Clear the product dropdown

            var filteredProducts = sortedProducts.filter(item => item.category === selectedCategory);

            // Populate the product dropdown based on the selected category
            $.each(filteredProducts, function(i, item) {
                productNameSelect.append($('<option>', {
                    value: item.name,
                    text: item.name
                }));
            });
        });

        // Trigger change to populate products for the first category by default
        categorySelect.change();

        // Handle Form Submission (no changes here)
        $('#addItemForm').submit(function(e) {
            e.preventDefault();

            // Get the value from the autocomplete input field
            var inputName = $('#fname').val().trim();
            // Get the value from the dropdown
            var selectedName = $('#productName').val();

            // Determine which value to use: prefer the input field if it's not empty
            var productName = inputName || selectedName;

            // Get the quantity from the input field
            var quantity = parseInt($('#productQuantity').val());

            // Find the selected item from the sorted data
            var selectedItem = sortedProducts.find(item => item.name === productName);

            if (selectedItem) {
                var category = selectedItem.category;

                // Send the data to the PHP script using AJAX
                $.ajax({
                    url: '../php/add_request.php', // Replace with your PHP file path
                    method: 'POST',
                    data: {
                        category: category,
                        product: productName,
                        people: quantity,
                        is_accepted: 1,
                        when_accepted: new Date().toISOString(),
                        when_completed: null
                    },
                    success: function(response) {
                        console.log(response); // Handle the response from the PHP script

                        // Update the frontend table
                        var existingRow = $('#userdata tbody tr').filter(function() {
                            return $(this).find('td:eq(2)').text() === productName;
                        });

                        if (existingRow.length) {
                            // If the product exists, update the quantity
                            var currentQuantity = parseInt(existingRow.find('td:eq(4)').text().match(/For # people: (\d+)/)[1]);
                            var newQuantity = currentQuantity + quantity;
                            existingRow.find('td:eq(4)').html("For # people: " + newQuantity);
                        } else {
                            // If the product does not exist, add a new row
                            var rowCount = $('#userdata tbody tr').length + 1;

                            var tblRow = "<tr>" +
                                "<td>" + rowCount + "</td>" +
                                "<td>" + selectedItem.category + "</td>" +
                                "<td>" + selectedItem.name + "</td>" +
                                "<td>" + selectedItem.people + "</td>" +
                                
                                
                                "</tr>";

                            $(tblRow).appendTo("#userdata tbody");
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error); // Handle errors if any
                    }
                });
            }
        });

        
    });

    // Load the request history when the page loads
    loadRequestHistory();
});