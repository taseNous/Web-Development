// Sample data in the same format as the provided JSON
const data = {
    "items": [
        {"id": "16", "name": "Water", "category": "Beverages", "details": [{"detail_name": "Volume", "detail_value": "1.5L"}, {"detail_name": "Pack Size", "detail_value": "6"}]},
        {"id": "17", "name": "Orange Juice", "category": "Beverages", "details": [{"detail_name": "Volume", "detail_value": "250ml"}, {"detail_name": "Pack Size", "detail_value": "12"}]},
        {"id": "18", "name": "Sardines", "category": "Food", "details": [{"detail_name": "Weight", "detail_value": "200g"}]},
        {"id": "22", "name": "Men Sneakers", "category": "Clothing", "details": [{"detail_name": "Size", "detail_value": "44"}]},
        // Add the rest of your items here...
    ]
};

// Function to populate the category dropdown
function populateCategories() {
    const categories = [...new Set(data.items.map(item => item.category))]; // Get unique categories
    const dropdown = document.getElementById('productCategory');
    
    dropdown.innerHTML = '<option value="">All Categories</option>'; // Default option to show all items
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = category;
        dropdown.appendChild(option);
    });
}

// Function to populate the table with filtered data
function populateTable(categoryFilter = '') {
    const tableBody = document.querySelector('#inventoryTable tbody');
    tableBody.innerHTML = ''; // Clear existing rows

    let filteredItems = data.items;

    // Filter items based on the selected category
    if (categoryFilter) {
        filteredItems = filteredItems.filter(item => item.category === categoryFilter);
    }

    // Loop through the filtered items and create table rows
    filteredItems.forEach((item, index) => {
        const row = document.createElement('tr');
        
        // Create the table cells for each column
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.category}</td>
            <td>${item.name}</td>
            <td>${getDetailValue(item.details, 'Volume')}</td>
            <td>${getDetailValue(item.details, 'Weight')}</td>
            <td>${getDetailValue(item.details, 'Pack Size')}</td>
            <td>${getDetailValue(item.details, 'Type')}</td>
            <td>${getDetailValue(item.details, 'Size')}</td>
            <td><input type="number" min="1" value="1"></td>
        `;
        tableBody.appendChild(row);
    });
}

// Helper function to get the detail value by name
function getDetailValue(details, detailName) {
    const detail = details.find(d => d.detail_name === detailName);
    return detail ? detail.detail_value : ''; // Return the detail value or empty string
}

// Event listener for the category dropdown
document.getElementById('productCategory').addEventListener('change', function() {
    populateTable(this.value); // Filter the table based on the selected category
});

// Initialize the page by populating the categories and table
window.onload = function() {
    populateCategories(); // Populate the dropdown
    populateTable(); // Initially populate the table with all items
};