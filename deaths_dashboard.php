<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Data Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php' ?>
    <div class="container">
        <h1>กราฟแสดงจำนวนผู้เสียชีวิตและผู้ติดเชื้อใน US</h1>

        <!-- เลือกประเทศ -->
        <label for="country">เลือกรัฐ:</label>
        <select name="country" id="country" required>
            <!-- รายชื่อประเทศจะถูกดึงจากฐานข้อมูล -->
        </select>

        <!-- เลือกเดือน -->
        <label for="month">เลือกเดือน:</label>
        <select name="month" id="month">
            <option value="">ทั้งหมด</option> <!-- ค่านี้หมายถึงไม่กรองตามเดือน -->
            <option value="1">มกราคม</option>
            <option value="2">กุมภาพันธ์</option>
            <option value="3">มีนาคม</option>
            <option value="4">เมษายน</option>
            <option value="5">พฤษภาคม</option>
            <option value="6">มิถุนายน</option>
            <option value="7">กรกฎาคม</option>
            <option value="8">สิงหาคม</option>
            <option value="9">กันยายน</option>
            <option value="10">ตุลาคม</option>
            <option value="11">พฤศจิกายน</option>
            <option value="12">ธันวาคม</option>
        </select>

        <div id="noDataMessage" style="color:red; display:none;">ยังไม่มีข้อมูลในเดือนนี้</div>

        <!-- กราฟผู้เสียชีวิต -->
        <canvas id="deathsChart" width="400" height="200"></canvas>

        <!-- กราฟผู้ติดเชื้อ -->
        <canvas id="casesChart" width="400" height="200"></canvas>
    </div>
    <div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const countrySelect = document.getElementById('country');
                const monthSelect = document.getElementById('month');
                const deathsChartCanvas = document.getElementById('deathsChart').getContext('2d');
                const casesChartCanvas = document.getElementById('casesChart').getContext('2d');
                const noDataMessage = document.getElementById('noDataMessage');
                let deathsChart;
                let casesChart;

                // ฟังก์ชันดึงข้อมูลจากฐานข้อมูล
                function fetchData(country, month) {
                    const url = `deaths_gengraph.php?country=${country}&month=${month}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            console.log(data); // ตรวจสอบข้อมูลที่ได้
                            if (data.error) {
                                noDataMessage.textContent = data.error;
                                noDataMessage.style.display = 'block'; // แสดงข้อความเตือน
                                if (deathsChart) deathsChart.destroy(); // ลบกราฟเดิมออก
                                if (casesChart) casesChart.destroy();
                                return;
                            }

                            noDataMessage.style.display = 'none';

                            const dates = data.map(item => item.date);
                            const deaths = data.map(item => item.total_deaths);
                            const cases = data.map(item => item.total_cases);

                            console.log('Dates:', dates);
                            console.log('Deaths:', deaths);
                            console.log('Cases:', cases);

                            // อัปเดตกราฟ
                            updateCharts(dates, deaths, cases);
                        })
                        .catch(error => console.error('Error fetching data:', error));
                }

                // ฟังก์ชันอัปเดตกราฟแยกกัน
                function updateCharts(dates, deaths, cases) {
                    // ลบกราฟเดิมก่อนสร้างใหม่
                    if (deathsChart) deathsChart.destroy();
                    if (casesChart) casesChart.destroy();

                    // หาค่าต่ำสุดและสูงสุดของข้อมูลผู้เสียชีวิตและผู้ติดเชื้อ
                    const minDeaths = Math.min(...deaths);
                    const maxDeaths = Math.max(...deaths);
                    const minCases = Math.min(...cases);
                    const maxCases = Math.max(...cases);

                    // สร้างกราฟผู้เสียชีวิต
                    deathsChart = new Chart(deathsChartCanvas, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'จำนวนผู้เสียชีวิต',
                                data: deaths,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    display: false // ซ่อนแกน X
                                },
                                y: {
                                    min: minDeaths - (minDeaths * 0.1), // กำหนดค่าต่ำสุดให้ต่ำกว่าค่าน้อยสุดเล็กน้อย
                                    max: maxDeaths + (maxDeaths * 0.1), // กำหนดค่าสูงสุดให้สูงกว่าค่าสูงสุดเล็กน้อย
                                    beginAtZero: false // ปิดการเริ่มจาก 0 เพื่อให้กราฟโฟกัสที่ช่วงที่มีข้อมูล
                                }
                            }
                        }
                    });

                    // สร้างกราฟผู้ติดเชื้อ
                    casesChart = new Chart(casesChartCanvas, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'จำนวนผู้ติดเชื้อ',
                                data: cases,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    display: false // ซ่อนแกน X
                                },
                                y: {
                                    min: minCases - (minCases * 0.1), // กำหนดค่าต่ำสุดให้ต่ำกว่าค่าน้อยสุดเล็กน้อย
                                    max: maxCases + (maxCases * 0.1), // กำหนดค่าสูงสุดให้สูงกว่าค่าสูงสุดเล็กน้อย
                                    beginAtZero: false // ปิดการเริ่มจาก 0 เพื่อให้กราฟโฟกัสที่ช่วงที่มีข้อมูล
                                }
                            }
                        }
                    });
                }



                // โหลดรายชื่อประเทศจากฐานข้อมูล (AJAX)
fetch('deaths_gengraph.php?getCountries=1')
    .then(response => response.json())
    .then(countries => {
        // เรียงลำดับชื่อประเทศตามตัวอักษร
        countries.sort((a, b) => a.localeCompare(b));

        countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            countrySelect.appendChild(option);
        });

        // เลือกประเทศแรกเป็นค่าเริ่มต้น
        const firstCountry = countrySelect.value;
        const firstMonth = monthSelect.value; // ค่าเริ่มต้นคือไม่เลือกเดือน
        fetchData(firstCountry, firstMonth);
    });


                // เปลี่ยนประเทศอัตโนมัติ
                countrySelect.addEventListener('change', function() {
                    fetchData(this.value, monthSelect.value);
                });

                // เปลี่ยนเดือนอัตโนมัติ
                monthSelect.addEventListener('change', function() {
                    fetchData(countrySelect.value, this.value);
                });
            });
        </script>
    </div>


</body>

</html>