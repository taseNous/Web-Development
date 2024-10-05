$.getJSON('../json/proionta.json', function(jsonData) {
    var data = jsonData;
    var categorySelect = $('#productCategory');
    var productNameSelect = $('#productName');
    var categories = [];

    // Populate Category Dropdown
    $.each(data.items, function(i, item) {
        if($.inArray(item.category, categories) === -1) {
            categories.push(item.category);
            categorySelect.append($('<option>', { 
                value: item.category,
                text : item.category 
            }));
        }
    });

    // Populate Product Dropdown when Category changes
    categorySelect.change(function() {
        var selectedCategory = $(this).val();
        productNameSelect.empty(); // Clear the product dropdown
        $.each(data.items, function(i, item) {
            if (item.category === selectedCategory) {
                productNameSelect.append($('<option>', { 
                    value: item.name,
                    text : item.name 
                }));
            }
        });
    });

    // Trigger change to populate products for the first category by default
    categorySelect.change();
});


    // Handle Form Submission
    $('#addItemForm').submit(function(e) {
        e.preventDefault();
    
        // Get selected product and category
        var selectedCategory = $('#productCategory').val();
        var selectedProduct = $('#productName').val();
    
        // Send data via AJAX to the PHP script
        $.ajax({
            url: '../php/add_to_announcement.php',
            type: 'POST',
            data: { category: selectedCategory, product: selectedProduct },
            success: function(response) {
                console.log("Server response:", response); // Log the server response for debugging
                alert(response); // Display the response as an alert
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error); // Log any AJAX errors
                alert('An error occurred while making the announcement.');
            }
        });
    });