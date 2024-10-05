let data = {};

    // Function to populate the category dropdown
    function populateCategories() {
        const categories = [...new Set(data.items.map(item => item.category))]; // Get unique categories
        const dropdown = document.getElementById('productCategory1');
        
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

    // Fetch the JSON data from the external file
    function fetchData() {
        fetch('../json/proionta.json')  // URL of the JSON file
            .then(response => response.json())
            .then(jsonData => {
                data = jsonData;  // Store the fetched data
                populateCategories();  // Populate the dropdown
                populateTable();  // Initially populate the table with all items
            })
            .catch(error => {
                console.error('Error fetching the JSON data:', error);
            });
    }

    // Event listener for the category dropdown
    document.getElementById('productCategory').addEventListener('change', function() {
        populateTable(this.value); // Filter the table based on the selected category
    });

    // Initialize the page by fetching data and populating the categories and table
    window.onload = function() {
        fetchData(); // Fetch data from the external JSON file
    };