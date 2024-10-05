document.getElementById('unloadDataBtn').addEventListener('click', function () {
    fetch('unload.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Data successfully unloaded and moved back to inventory.');
            var inventoryPanel = document.querySelector('.leaflet-control-inventory-panel');
            inventoryPanel.style.display = 'none'; // Hide the panel after unloading
        } else {
            alert('Error unloading data: ' + data.message);
        }
    })
    .catch(error => console.error('Error unloading data:', error));
});
