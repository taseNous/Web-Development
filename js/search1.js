$(document).ready(function() {
    // Fetch the JSON data
    $.ajax({
        url: '../json/proionta.json',  // Adjust path if necessary
        dataType: 'json',
        success: function(data) {
            // Extract product names
            var productNames = [];
            $.each(data.items, function(index, item) {
                productNames.push(item.name);
            });

            // Initialize autocomplete
            $("#fname").autocomplete({
                source: productNames,
                minLength: 0,  // Show dropdown even if no text is typed
                autoFocus: true // Automatically focus on the first item
            }).focus(function() {
                $(this).autocomplete("search", "");  // Trigger the dropdown on focus
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error loading JSON: " + textStatus);
        }
    });
});