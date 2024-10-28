<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'navbar.php' ?>
    <?php include 'server.php' ?>
    <div class="container">
        <h1>Dashboard</h1>
        <h1>สถิติการใส่แมสของแต่ละประเทศ</h1>
        <!-- Dropdown for country selection -->
        <div class="graph-contain">
            <?php
            // Fetch unique COUNTYFP values for the dropdown
            $sql = "SELECT DISTINCT COUNTYFP FROM `mask-use-by-country`";
            $result = $conn->query($sql);

            // Check if data is available
            if ($result->num_rows > 0) {
                echo '<select id="countrySelect" onchange="updateChart()">';
                echo '<option value="">เลือกประเทศ</option>';
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['COUNTYFP'] . '">ประเทศ ' . $row['COUNTYFP'] . '</option>';
                }
                echo '</select>';
            } else {
                echo 'No data available';
            }
            ?>
            <canvas id="maskUseChart" width="100" height="50"></canvas>

            <script>
                let chart;

                // Function to fetch data from PHP backend
                async function fetchMaskData(country = "") {
                    let url = 'show_graph.php';
                    if (country) {
                        url += `?country=${country}`;
                    }

                    const response = await fetch(url);
                    const data = await response.json();
                    return data;
                }

                // Function to plot or update the chart
                async function plotChart(country = "") {
                    const maskData = await fetchMaskData(country);

                    const labels = maskData.map(item => item.COUNTYFP);
                    const neverData = maskData.map(item => parseFloat(item.NEVER));
                    const rarelyData = maskData.map(item => parseFloat(item.RARELY));
                    const sometimesData = maskData.map(item => parseFloat(item.SOMETIMES));
                    const frequentlyData = maskData.map(item => parseFloat(item.FREQUENTLY));
                    const alwaysData = maskData.map(item => parseFloat(item.ALWAYS));

                    const ctx = document.getElementById('maskUseChart').getContext('2d');

                    // If chart exists, destroy it before creating a new one
                    if (chart) {
                        chart.destroy();
                    }

                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Never',
                                    data: neverData,
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Rarely',
                                    data: rarelyData,
                                    backgroundColor: 'rgba(230, 100, 255, 0.2)',
                                    borderColor: 'rgba(230, 100, 255, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Sometimes',
                                    data: sometimesData,
                                    backgroundColor: 'rgba(112, 112, 255, 0.2)',
                                    borderColor: 'rgba(112, 112, 255, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Frequently',
                                    data: frequentlyData,
                                    backgroundColor: 'rgba(246, 255, 100, 0.2)',
                                    borderColor: 'rgba(246, 255, 100, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Always',
                                    data: alwaysData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Function to update chart based on country selection
                function updateChart() {
                    const selectedCountry = document.getElementById('countrySelect').value;
                    plotChart(selectedCountry);
                }

                // Initial chart plotting for the first country when the page loads
                document.addEventListener("DOMContentLoaded", async function() {
                    const countrySelect = document.getElementById('countrySelect');
                    if (countrySelect.options.length > 1) {
                        const firstCountry = countrySelect.options[1].value; // Get the first non-placeholder country
                        countrySelect.value = firstCountry;
                        plotChart(firstCountry); // Plot with the first country as default
                    }
                });
            </script>


            // Function to update chart based on country selection
            function updateChart() {
            const selectedCountry = document.getElementById('countrySelect').value;
            plotChart(selectedCountry);
            }

            // Initial chart plotting for all countries when the page loads
            document.addEventListener("DOMContentLoaded", function() {
            plotChart(); // Plot with all data initially
            });
            </script>

        </div>
    </div>

</body>

</html>