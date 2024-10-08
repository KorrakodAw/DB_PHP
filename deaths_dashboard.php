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
        <h1>กราฟแสดงจำนวนผู้เสียชีวิตและผู้ติดเชื้อ</h1>

        <!-- เลือกประเทศ -->
        <label for="country">เลือกประเทศ:</label>
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
        <!-- กราฟ -->
        <canvas id="countryChart" width="400" height="200"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const countrySelect = document.getElementById('country');
            const monthSelect = document.getElementById('month');
            const chartCanvas = document.getElementById('countryChart').getContext('2d');
            const noDataMessage = document.getElementById('noDataMessage');
            let countryChart;

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
                            if (countryChart) {
                                countryChart.destroy(); // ลบกราฟเดิมออก
                            }
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
                        updateChart(dates, deaths, cases);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            // ฟังก์ชันอัปเดตกราฟ (แบบ Bar Chart)
            function updateChart(dates, deaths, cases) {
                if (countryChart) {
                    countryChart.destroy(); // ลบกราฟเดิมก่อนสร้างใหม่
                }

                countryChart = new Chart(chartCanvas, {
                    type: 'bar', // กำหนดให้เป็น Bar Chart
                    data: {
                        labels: dates, // แกน X (วันที่)
                        datasets: [{
                            label: 'จำนวนผู้เสียชีวิต',
                            data: deaths, // ข้อมูลจำนวนผู้เสียชีวิต
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'จำนวนผู้ติดเชื้อ',
                            data: cases, // ข้อมูลจำนวนผู้ติดเชื้อ
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true // ให้แกน Y เริ่มจากศูนย์
                            }
                        }
                    }
                });
            }

            // โหลดรายชื่อประเทศจากฐานข้อมูล (AJAX)
            fetch('deaths_gengraph.php?getCountries=1')
                .then(response => response.json())
                .then(countries => {
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
            countrySelect.addEventListener('change', function () {
                fetchData(this.value, monthSelect.value);
            });

            // เปลี่ยนเดือนอัตโนมัติ
            monthSelect.addEventListener('change', function () {
                fetchData(countrySelect.value, this.value);
            });
        });
    </script>

</body>

</html>
