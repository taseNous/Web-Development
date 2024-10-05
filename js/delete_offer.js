document.addEventListener("DOMContentLoaded", function () {
    // Attach click event to cancel buttons
    document.querySelectorAll('.cancel-offer').forEach(function (button) {
        button.addEventListener('click', function () {
            const offerId = this.getAttribute('data-id');

            if (confirm("Are you sure you want to cancel this offer?")) {
                fetch('delete_offer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${offerId}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Offer canceled successfully.");
                            location.reload(); // Reload the page to update the offers list
                        } else {
                            alert("Failed to cancel offer: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("An error occurred while canceling the offer.");
                    });
            }
        });
    });
});