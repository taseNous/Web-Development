$(document).ready(function() {
    // Function to load announcements via AJAX
    function loadAnnouncements() {
        $.ajax({
            url: '../php/fetch_announcements.php', // URL to the PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var tableBody = $('#inventoryStatusAdmin tbody');
                    tableBody.empty(); // Clear the table body

                    if (response.announcements.length > 0) {
                        var i = 1;
                        $.each(response.announcements, function(index, announcement) {
                            // Create a new row and append to the table body
                            var row = '<tr>' +
                                        '<td>' + i++ + '</td>' +  // Number
                                        '<td>' + announcement.category + '</td>' +  // Category
                                        '<td>' + announcement.product + '</td>' +  // Product                            
                                    '</tr>';

                            tableBody.append(row);
                        });
                    } else {
                        // If no announcements found
                        tableBody.append('<tr><td colspan="5">No announcements found</td></tr>');
                    }
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