// Populate category dropdown and handle product list population

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
                    value: item.id, // Use ID here instead of name
                    text : item.name 
                }));
                
            }
        });
    });

    // Trigger change to populate products for the first category by default
    categorySelect.change();
});

// Handle form submission
$('#addItemForm').submit(function (event) {
    event.preventDefault(); // Prevent default form submission

    const category = $('#productCategory').val();
    const productId = $('#productName').val(); // Now this should be the ID
    const quantity = $('#productQuantity').val();

    // Find the selected product details from the JSON
    let selectedProduct = null;
    $.getJSON('../json/proionta.json', function (data) {
        selectedProduct = data.items.find(item => item.id == productId);
        if (!selectedProduct || !category || quantity === '' || quantity <= 0) {
            alert("Please select a category, product, and enter a valid quantity.");
            return;
        }
        

        // Prepare the product details
        const productDetails = {};
        selectedProduct.details.forEach(detail => {
            productDetails[detail.detail_name.toLowerCase().replace(" ", "_")] = detail.detail_value;
        });

        // Send data to the PHP script (add_to_inventory.php) via POST
        $.ajax({
            type: 'POST',
            url: '../php/add_to_inventory.php',
            data: {
                category: category,
                product: selectedProduct.name,
                quantity: quantity,
                volume: productDetails.volume || '',
                weight: productDetails.weight || '',
                pack_size: productDetails.pack_size || '',
                type: productDetails.type || '',
                size: productDetails.size || ''
            },
            success: function (response) {
                alert('Product added to inventory!');
                // Optionally, refresh the table or take further actions here
            },
            error: function (xhr, status, error) {
                console.error('Error adding product:', error);
            }
        });
    });
});
