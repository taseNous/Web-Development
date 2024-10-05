<?php

session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    
    header("Location: ../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charts</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <?php include '../includes/navbar.php'; ?>
    </header>

    <div style="width: 80%; height: 80%; margin: auto; padding-top: 120px;">
        <canvas id="offersRequestsChart"></canvas>
    </div>

    <footer>
        <?php include '../includes/footer.php'; ?> 
    </footer>

</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

<script>
    // Fetch the data from your PHP endpoint
    fetch('../php/fetch_offer_request_data.php') // Replace with your PHP file path
        .then(response => response.json())
        .then(data => {
            // Process the data to create datasets for offers and requests
            const offerData = [];
            const requestData = [];
            const labels = [];

            data.forEach(entry => {
                if (entry.type === 'offer') {
                    offerData.push({ x: entry.date, y: entry.total });
                } else if (entry.type === 'request') {
                    requestData.push({ x: entry.date, y: entry.total });
                }

                // Add dates to labels (if not already present)
                if (!labels.includes(entry.date)) {
                    labels.push(entry.date);
                }
            });

            // Create the chart
            const ctx = document.getElementById('offersRequestsChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line', // You can change this to 'bar' if you prefer
                data: {
                    datasets: [
                        {
                            label: 'Offers',
                            data: offerData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true
                        },
                        {
                            label: 'Requests',
                            data: requestData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            type: 'time', // Use a time scale for the x-axis
                            time: {
                                unit: 'day', // Display units by day
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Offers/Requests'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
</script>