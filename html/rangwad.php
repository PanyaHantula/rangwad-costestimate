<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Land Surveyors Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;700&family=Kanit:wght@400;700&family=Prompt:wght@400;700&display=swap"
        rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkEMUIP4XY0t24lWB-gXjXzkuB4LPe4t4&libraries=places">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>

<body class="bg-light">

    <!-- Logo Header and Navbar -->
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <!-- Logo Show -->
            <img src="img/logo.png" alt="RANGWAD Logo" class="logo">

            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg custom-navbar">
                <div class="container-fluid p-0">
                    <div class="collapse navbar-collapse show">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 flex-row">
                            <li class="nav-item me-3">
                                <a class="nav-link active" href="index.php">หน้าแรก</a>
                            </li>
                            <!-- แสดงชื่อผู้ใช้ -->
                            <li class="nav-item">
                                <span class="nav-link" id="usernameDisplay">ผู้ใช้งาน:
                                    <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                            </li>
                            <!-- ปุ่มออกจากระบบ -->
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="logout.php" class="logout-link">ออกจากระบบ</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Box -->
    <div class="container d-flex justify-content-center align-items-center">
        <div class="card shadow-lg p-4" style="width: 1000px;">

            <!-- Header -->
            <h2 class="text-center mb-6 highlight">ระบบประมาณการต้นทุนค่าใช้จ่ายงานรังวัดที่ดิน</h2>

            <!-- customer detail -->
            <div class="service-info-box">
                <div>
                    <h5>ข้อมูลผู้ขอรับบริการ</h5>
                    <p>*กรุณากรอกข้อมูลให้ครบทุกช่อง</p>
                </div>

                <!-- ข้อมูลผู้ขอรับบริการ -->
                <div class="row">
                    <div class="col-12 col-sm-2 mb-3">
                        <select name="firstname" class="form-control" required>
                            <option value="นาย">นาย</option>
                            <option value="นางสาว">นางสาว</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-5 mb-3">
                        <input type="text" name="name" class="form-control" placeholder="ชื่อ" required>
                    </div>
                    <div class="col-12 col-sm-5 mb-3">
                        <input type="text" name="lastname" class="form-control" placeholder="นามสกุล" required>
                    </div>
                </div>

                <!-- ข้อมูลพื้นที่ -->
                <div class="row mb-2">
                    <div class="col-md-10">
                        <label class="form-label">พื้นที่ดำเนินการ</label>
                        <input type="text" class="form-control" placeholder="ที่อยู่">
                    </div>
                </div>
                <div class="service-sub-info-box">
                    <div class="input-container">
                        <label for="start">จุดเริ่มต้น:</label>
                        <input id="start" type="text" placeholder="กรอกสถานที่เริ่มต้น" onclick="setLocation('start')">
                        <button onclick="useMyLocation()">📍 ใช้ตำแหน่งของฉัน</button>
                    </div>                  
                    <div class="input-container">
                        <label for="end">จุดหมาย:</label>
                        <input id="end" type="text" placeholder="กรอกสถานที่ปลายทาง" onclick="setLocation('end')">
                    </div>
                    <div class="button-container">
                        <button onclick="calculateRoute()">คำนวณระยะทาง</button>
                    </div>
                    <div id="map"></div>
                    <div id="output">
                        <p><strong>ระยะทาง:</strong> <span id="distance"></span></p>
                        <p><strong>เวลาเดินทาง:</strong> <span id="duration"></span></p>
                    </div>
                </div>

                <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkEMUIP4XY0t24lWB-gXjXzkuB4LPe4t4&libraries=places&callback=initMap">
                </script>

                <!-- ข้อมูลการทำงาน -->
                <div class="row mb-12">
                    <div class="col-md-3">
                        <label class="form-label">ระยะทางการเดินทาง</label>
                        <input type="text" class="form-control" id="TotalDistanceInput" placeholder="กิโลเมตร">
                    </div>
                    <div class="col-md-6 d-flex">
                        <div class="w-50">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="d-flex align-items-end mx-2">
                            <span class="form-label">ถึง</span>
                        </div>
                        <div class="w-50">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>
                </div>

                <!-- แถวสุดท้ายที่ต้องการกรอบ -->
                <div class="row mb-6 mt-2">
                    <div class="col-md-2">
                        <label class="form-label">เนื้อที่โดยประมาณ</label>
                        <input type="text" class="form-control" placeholder="ไร่">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">จำนวนวันทำงาน</label>
                        <input type="text" class="form-control" id="TotalDateInput" placeholder="วัน" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">จำนวนช่างรางวัด</label>
                        <input type="number" class="form-control" id="staffHumanInput" placeholder="คน" value="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label ">จำนวนคนงาน</label>
                        <input type="number" class="form-control" id="workerHumanInput" placeholder="คน" value="1">
                    </div>
                </div>

                <!-- JavaScript คำนวณระยะเวลาอัตโนมัติ -->
                <script>
                let map;
                let selectedInput = null;
                let markers = {};

                function initMap() {
                    map = new google.maps.Map(document.getElementById("map"), {
                        center: {
                            lat: 13.736717,
                            lng: 100.523186
                        }, // กรุงเทพฯ
                        zoom: 12,
                    });

                    new google.maps.places.Autocomplete(document.getElementById("start"));
                    new google.maps.places.Autocomplete(document.getElementById("end"));

                    map.addListener("click", (event) => {
                        if (selectedInput) {
                            let latLng = event.latLng;
                            document.getElementById(selectedInput).value = `${latLng.lat()},${latLng.lng()}`;
                            placeMarker(latLng, selectedInput);
                            selectedInput = null;
                        }
                    });
                }

                function useMyLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            const latLngStr = `${lat},${lng}`;
                            document.getElementById("start").value = latLngStr;
                            map.setCenter({
                                lat,
                                lng
                            });
                            new google.maps.Marker({
                                position: {
                                    lat,
                                    lng
                                },
                                map,
                                title: "ตำแหน่งของคุณ",
                            });
                        }, () => {
                            alert("ไม่สามารถเข้าถึงตำแหน่งของคุณได้");
                        });
                    } else {
                        alert("เบราว์เซอร์นี้ไม่รองรับ Geolocation");
                    }
                }

                function setLocation(inputId) {
                    selectedInput = inputId;
                    alert("คลิกบนแผนที่เพื่อเลือกตำแหน่ง");
                }

                function placeMarker(location, inputId) {
                    if (markers[inputId]) {
                        markers[inputId].setMap(null);
                    }
                    markers[inputId] = new google.maps.Marker({
                        position: location,
                        map: map,
                    });
                }

                async function calculateRoute() {
                    let start = document.getElementById("start").value;
                    let end = document.getElementById("end").value;

                    if (!start || !end) {
                        alert("โปรดกรอกสถานที่เริ่มต้นและปลายทาง");
                        return;
                    }

                    const apiKey = "AIzaSyAkEMUIP4XY0t24lWB-gXjXzkuB4LPe4t4";
                    const routeApiUrl = `https://routes.googleapis.com/directions/v2:computeRoutes?key=${apiKey}`;

                    const requestData = {
                        origin: {
                            location: {
                                latLng: {
                                    latitude: parseFloat(start.split(',')[0]),
                                    longitude: parseFloat(start.split(',')[1])
                                }
                            }
                        },
                        destination: {
                            location: {
                                latLng: {
                                    latitude: parseFloat(end.split(',')[0]),
                                    longitude: parseFloat(end.split(',')[1])
                                }
                            }
                        },
                        travelMode: "DRIVE",
                        computeAlternativeRoutes: false,
                        routingPreference: "TRAFFIC_AWARE"
                    };

                    try {
                        let response = await fetch(routeApiUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-Goog-Api-Key": apiKey,
                                "X-Goog-FieldMask": "routes.distanceMeters,routes.duration"
                            },
                            body: JSON.stringify(requestData)
                        });

                        let data = await response.json();

                        if (data.routes && data.routes.length > 0) {
                            let route = data.routes[0];
                            let distance = (route.distanceMeters / 1000).toFixed(2) + " km";
                            let duration = (route.duration.replace("s", "") / 60).toFixed(2) + " นาที";

                            document.getElementById("distance").innerText = distance;
                            document.getElementById("duration").innerText = duration;
                            document.getElementById("TotalDistanceInput").value = distance; // อัปเดตค่า input
                            document.getElementById("distance-15").value = distance; // อัปเดตค่า input
                            document.getElementById("distance-16").value = distance; // อัปเดตค่า input

                            calculateOilCost();
                            calculateVehicleLossCost();
                            calculateTotalExpense();

                        } else {
                            alert("ไม่สามารถคำนวณเส้นทางได้");
                        }
                    } catch (error) {
                        alert("เกิดข้อผิดพลาดในการดึงข้อมูล: " + error);
                    }
                }

                // อัฟเดตจำนวนวันทำงาน
                document.addEventListener("DOMContentLoaded", function() {
                    let dataInput_17 = document.getElementById("worker-date-17");
                    let dataInput_18 = document.getElementById("worker-date-18");
                    let dataInput_20 = document.getElementById("worker-date-20");
                    let dataInput_21 = document.getElementById("worker-date-21");

                    const startDateInput = document.getElementById("startDate");
                    const endDateInput = document.getElementById("endDate");
                    const totalDateInput = document.getElementById("TotalDateInput");

                    function calculateDateDifference() {
                        const startDate = new Date(startDateInput.value);
                        const endDate = new Date(endDateInput.value);

                        if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                            let timeDiff = endDate - startDate;
                            let dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                            totalDateInput.value = dayDiff > 0 ? dayDiff : 0;

                            dataInput_17.value = TotalDateInput.value;
                            dataInput_18.value = TotalDateInput.value;
                            dataInput_20.value = TotalDateInput.value;
                            dataInput_21.value = TotalDateInput.value;
                            calculateFoodOtherExpense();
                            calculateAccommodationCost();
                            calculateSurveyorAllowance();
                            calculateWorkerCost();
                            calculateTotalExpense();

                        } else {
                            totalDateInput.value = "";
                        }
                    }

                    startDateInput.addEventListener("change", calculateDateDifference);
                    endDateInput.addEventListener("change", calculateDateDifference);
                });

                // update จำนวนคนงาน
                document.addEventListener("DOMContentLoaded", function() {
                    // ดึงช่อง input ทั้งสองช่องมาใช้งาน
                    let staffHumanInput = document.getElementById("staffHumanInput");
                    let staffCountInput_18 = document.getElementById("staffCountInput-18");
                    let staffCountInput_19 = document.getElementById("staffCountInput-19");
                    let staffCountInput_20 = document.getElementById("staffCountInput-20");
                    let staffCountInput_21 = document.getElementById("staffCountInput-21");

                    // เมื่อเปลี่ยนค่าจำนวนคนงาน ให้เปลี่ยนค่าหัวข้อ 17 อัตโนมัติ
                    staffHumanInput.addEventListener("input", function() {
                        staffCountInput_18.value = staffHumanInput.value;
                        staffCountInput_19.value = staffHumanInput.value;
                        staffCountInput_20.value = staffHumanInput.value;
                        staffCountInput_21.value = staffHumanInput.value;
                        calculateSurveyorAllowance();
                        calculateSurveyorSalary();
                        calculateAccommodationCost();
                        calculateFoodOtherExpense();
                        calculateTotalExpense();

                    });

                    staffHumanInput.addEventListener("change", function() {
                        staffCountInput_18.value = staffHumanInput.value;
                        staffCountInput_19.value = staffHumanInput.value;
                        staffCountInput_20.value = staffHumanInput.value;
                        staffCountInput_21.value = staffHumanInput.value;
                        calculateSurveyorAllowance();
                        calculateSurveyorSalary();
                        calculateAccommodationCost();
                        calculateFoodOtherExpense();
                        calculateTotalExpense();
                    });
                });

                // update จำนวนคนช่างรางวัด
                document.addEventListener("DOMContentLoaded", function() {
                    // ดึงช่อง input ทั้งสองช่องมาใช้งาน
                    let workerHumanInput = document.getElementById("workerHumanInput");
                    let workerCountInput = document.getElementById("workerCountInput");

                    // เมื่อเปลี่ยนค่าจำนวนคนงาน ให้เปลี่ยนค่าหัวข้อ 17 อัตโนมัติ
                    workerHumanInput.addEventListener("input", function() {
                        workerCountInput.value = workerHumanInput.value;
                        calculateWorkerCost();
                        calculateTotalExpense();
                    });

                    workerHumanInput.addEventListener("change", function() {
                        workerCountInput.value = workerHumanInput.value;
                        calculateWorkerCost();
                        calculateTotalExpense();
                    });
                });
                </script>
            </div>

            <!-- ตารางแสดงผลการประมาณการค่าใช้จ่าย -->
            <div class="service-info-box">
                <div class="row mb-6 text-center highlight">
                    <div class="col-md-9">
                        <label class="form-label fs-3">รายการ</label>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-3">ค่าใช้จ่าย (บาท)</label>
                    </div>
                </div>

                <!-- งานทะเบียน -->
                <div class="row mb-3 service-sub-info-box mt-2">
                    <div class="row mb-9">
                        <label class="form-label fs-4 fw-bold text-decoration-underline">
                            งานทะเบียน
                        </label>
                    </div>
                    <!-- 1. ค่าอากรสแตมป์ติดใบมอบอำนาจที่ดิน -->
                    <div class="row mb-3 stamp-duty-row">
                        <div class="col-md-7">
                            <label class="form-label">1. ค่าอากรสแตมป์ติดใบมอบอำนาจที่ดิน (30 บาทต่อใบ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end stamp-duty-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">ใบ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 stamp-duty-cost">30.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateStampDuty() {
                        let row = document.querySelector(".stamp-duty-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".stamp-duty-count").value) || 0;
                        let costPerStamp = 30; // ค่าคงที่ 30 บาทต่อใบ
                        let totalCost = Math.max(0, count * costPerStamp); // ป้องกันค่าติดลบ

                        row.querySelector(".stamp-duty-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".stamp-duty-count");

                        input.addEventListener("input", calculateStampDuty);
                        input.addEventListener("change", calculateStampDuty);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateStampDuty();
                    });
                    </script>

                    <!-- 2.ค่าอากรสแตมป์ติดใบสัญญาจ้าง -->
                    <div class="row mb-3 stamp-contract-row">
                        <div class="col-md-7">
                            <label class="form-label">2. ค่าอากรสแตมป์ติดใบสัญญาจ้าง (30 บาทต่อใบ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end stamp-contract-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">ใบ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 stamp-contract-cost">30.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateStampContractCost() {
                        let row = document.querySelector(".stamp-contract-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".stamp-contract-count").value) || 0;
                        let costPerStamp = 30; // ค่าคงที่ 30 บาทต่อใบ
                        let totalCost = Math.max(0, count * costPerStamp); // ป้องกันค่าติดลบ

                        row.querySelector(".stamp-contract-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".stamp-contract-count");

                        input.addEventListener("input", calculateStampContractCost);
                        input.addEventListener("change", calculateStampContractCost);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateStampContractCost();
                    });
                    </script>

                    <!-- 3.ค่าอากรสแตมป์ติดใปมอบติดตามงานรังวัด -->
                    <div class="row mb-3 stamp-survey-row">
                        <div class="col-md-7">
                            <label class="form-label">3. ค่าอากรสแตมป์ติดใบมอบติดตามงานรังวัด (10 บาทต่อใบ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end stamp-survey-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">ใบ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 stamp-survey-cost">10.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateStampSurveyCost() {
                        let row = document.querySelector(".stamp-survey-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".stamp-survey-count").value) || 0;
                        let costPerStamp = 10; // ค่าคงที่ 10 บาทต่อใบ
                        let totalCost = Math.max(0, count * costPerStamp); // ป้องกันค่าติดลบ

                        row.querySelector(".stamp-survey-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".stamp-survey-count");

                        input.addEventListener("input", calculateStampSurveyCost);
                        input.addEventListener("change", calculateStampSurveyCost);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateStampSurveyCost();
                    });
                    </script>

                    <!-- 4.ค่าอากรสแตมป์ติดใปมอบสำนักงานทำสัญญาแทน -->
                    <div class="row mb-3 stamp-office-row">
                        <div class="col-md-7">
                            <label class="form-label">4. ค่าอากรสแตมป์ติดใบมอบสำนักงานทำสัญญาแทน (10
                                บาทต่อใบ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end stamp-office-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">ใบ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 stamp-office-cost">10.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateStampOfficeCost() {
                        let row = document.querySelector(".stamp-office-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".stamp-office-count").value) || 0;
                        let costPerStamp = 10; // ค่าคงที่ 10 บาทต่อใบ
                        let totalCost = Math.max(0, count * costPerStamp); // ป้องกันค่าติดลบ

                        row.querySelector(".stamp-office-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".stamp-office-count");

                        input.addEventListener("input", calculateStampOfficeCost);
                        input.addEventListener("change", calculateStampOfficeCost);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateStampOfficeCost();
                    });
                    </script>

                    <!-- 5.ค่าคำขอ -->
                    <div class="row mb-3 request-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">5. ค่าคำขอ (5 บาทต่อคำขอ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end request-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">คำขอ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 request-fee-cost">5.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateRequestFee() {
                        let row = document.querySelector(".request-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".request-fee-count").value) || 0;
                        let costPerRequest = 5; // ค่าคงที่ 5 บาทต่อคำขอ
                        let totalCost = Math.max(0, count * costPerRequest); // ป้องกันค่าติดลบ

                        row.querySelector(".request-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".request-fee-count");

                        input.addEventListener("input", calculateRequestFee);
                        input.addEventListener("change", calculateRequestFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateRequestFee();
                    });
                    </script>

                    <!-- 6.ค่าคำพยาน -->
                    <div class="row mb-3 witness-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">6. ค่าคำพยาน (20 บาทต่อคำพยาน)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end witness-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">คำขอ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 witness-fee-cost">20.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateWitnessFee() {
                        let row = document.querySelector(".witness-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".witness-fee-count").value) || 0;
                        let costPerWitness = 20; // ค่าคงที่ 20 บาทต่อคำพยาน
                        let totalCost = Math.max(0, count * costPerWitness); // ป้องกันค่าติดลบ

                        row.querySelector(".witness-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".witness-fee-count");

                        input.addEventListener("input", calculateWitnessFee);
                        input.addEventListener("change", calculateWitnessFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateWitnessFee();
                    });
                    </script>

                </div>

                <!-- งานฝ่ายรังวัด -->
                <div class="row mb-3 service-sub-info-box mt-2">
                    <div class="row mb-12">
                        <label class="form-label fs-4 fw-bold text-decoration-underline">
                            งานฝ่ายรังวัด
                        </label>
                    </div>
                    <!-- 7. ค่าหลักเขต/หลัก-->
                    <div class="row mb-3 boundary-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">7. ค่าหลักเขต (15 บาทต่อหลัก)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end boundary-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">หลัก</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 boundary-fee-cost">20.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateBoundaryFee() {
                        let row = document.querySelector(".boundary-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".boundary-fee-count").value) || 0;
                        let costPerBoundary = 15; // ค่าคงที่ 20 บาทต่อหลักเขต
                        let totalCost = Math.max(0, count * costPerBoundary); // ป้องกันค่าติดลบ

                        row.querySelector(".boundary-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".boundary-fee-count");

                        input.addEventListener("input", calculateBoundaryFee);
                        input.addEventListener("change", calculateBoundaryFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateBoundaryFee();
                    });
                    </script>

                    <!-- 8. ค่าแบ่งแยก/แปลง -->
                    <div class="row mb-3 separation-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">8. ค่าแบ่งแยก (40 บาทต่อแปลง)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end separation-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">แปลง</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 separation-fee-cost">40.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateSeparationFee() {
                        let row = document.querySelector(".separation-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".separation-fee-count").value) || 0;
                        let costPerPlot = 40; // ค่าคงที่ 40 บาทต่อแปลง
                        let totalCost = Math.max(0, count * costPerPlot); // ป้องกันค่าติดลบ

                        row.querySelector(".separation-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".separation-fee-count");

                        input.addEventListener("input", calculateSeparationFee);
                        input.addEventListener("change", calculateSeparationFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateSeparationFee();
                    });
                    </script>

                    <!-- 9. ค่าแบ่งจำกัดเนื้อที่/แปลง -->
                    <div class="row mb-3 area-limit-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">9. ค่าแบ่งจำกัดเนื้อที่ (30 บาทต่อแปลง)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end area-limit-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">แปลง</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 area-limit-fee-cost">30.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateAreaLimitFee() {
                        let row = document.querySelector(".area-limit-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".area-limit-fee-count").value) || 0;
                        let costPerPlot = 30; // ค่าคงที่ 30 บาทต่อแปลง
                        let totalCost = Math.max(0, count * costPerPlot); // ป้องกันค่าติดลบ

                        row.querySelector(".area-limit-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".area-limit-fee-count");

                        input.addEventListener("input", calculateAreaLimitFee);
                        input.addEventListener("change", calculateAreaLimitFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateAreaLimitFee();
                    });
                    </script>

                    <!-- 10.ค่าส่งหมายข้างเคียง/คน -->
                    <div class="row mb-3 adjacent-notice-row">
                        <div class="col-md-7">
                            <label class="form-label">10. ค่าส่งหมายข้างเคียง (15 บาทต่อคน)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end adjacent-notice-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">คน</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 adjacent-notice-cost">15.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateAdjacentNoticeFee() {
                        document.querySelectorAll(".adjacent-notice-row").forEach(row => {
                            let countInput = row.querySelector(".adjacent-notice-count");
                            let count = parseInt(countInput.value) || 0;
                            let costPerPerson = 15; // ค่าคงที่ 5 บาทต่อคน
                            let totalCost = Math.max(0, count * costPerPerson); // ป้องกันค่าติดลบ

                            row.querySelector(".adjacent-notice-cost").textContent =
                                totalCost // อัปเดตค่าผลลัพธ์ที่ label
                        });
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        document.querySelectorAll(".adjacent-notice-count").forEach(input => {
                            input.addEventListener("input", calculateAdjacentNoticeFee);
                            input.addEventListener("change", calculateAdjacentNoticeFee);
                        });

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateAdjacentNoticeFee();
                    });
                    </script>
                </div>

                <!-- งานจดทะเบียน -->
                <div class="row mb-3 service-sub-info-box mt-2">
                    <div class="row mb-9">
                        <label class="form-label fs-4 fw-bold text-decoration-underline">
                            งานจดทะเบียน
                        </label>
                    </div>
                    <!-- 11.ค่าคำของานจดทะเบียน -->
                    <div class="row mb-3 registration-request-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">11. ค่าคำของานจดทะเบียน (5 บาทต่อคำขอ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end registration-request-count" value="1"
                                min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">คำขอ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 registration-request-fee">5.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateRegistrationRequestFee() {
                        let row = document.querySelector(".registration-request-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".registration-request-count").value) || 0;
                        let costPerRequest = 5; // ค่าคงที่ 5 บาทต่อคำขอ
                        let totalCost = Math.max(0, count * costPerRequest); // ป้องกันค่าติดลบ

                        row.querySelector(".registration-request-fee").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".registration-request-count");

                        input.addEventListener("input", calculateRegistrationRequestFee);
                        input.addEventListener("change", calculateRegistrationRequestFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateRegistrationRequestFee();
                    });
                    </script>

                    <!-- 12.ค่าธรรมเนียม -->
                    <div class="row mb-3 fee-row">
                        <div class="col-md-7">
                            <label class="form-label">12. ค่าธรรมเนียม (50 บาทต่อรายการ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">รายการ</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 fee-cost">50.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateFee() {
                        let row = document.querySelector(".fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".fee-count").value) || 0;
                        let costPerItem = 50; // ค่าคงที่ 50 บาทต่อรายการ
                        let totalCost = Math.max(0, count * costPerItem); // ป้องกันค่าติดลบ

                        row.querySelector(".fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".fee-count");

                        input.addEventListener("input", calculateFee);
                        input.addEventListener("change", calculateFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateFee();
                    });
                    </script>

                    <!-- 13.ค่าโฉนด/แปลง -->
                    <div class="row mb-3 deed-fee-row">
                        <div class="col-md-7">
                            <label class="form-label">13. ค่าโฉนด (50 บาทต่อแปลง)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end deed-fee-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">แปลง</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 deed-fee-cost">50.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateDeedFee() {
                        let row = document.querySelector(".deed-fee-row");
                        if (!row) return;

                        let count = parseInt(row.querySelector(".deed-fee-count").value) || 0;
                        let costPerDeed = 50; // ค่าคงที่ 50 บาทต่อแปลง
                        let totalCost = Math.max(0, count * costPerDeed); // ป้องกันค่าติดลบ

                        row.querySelector(".deed-fee-cost").textContent = totalCost // แสดงผลลัพธ์ที่ label
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let input = document.querySelector(".deed-fee-count");

                        input.addEventListener("input", calculateDeedFee);
                        input.addEventListener("change", calculateDeedFee);

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateDeedFee();
                    });
                    </script>

                    <!-- 14.ค่าพยาน -->
                    <div class="row mb-3 witness-expense-row">
                        <div class="col-md-7">
                            <label class="form-label">14. ค่าพยาน (20 บาทต่อคน)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end witness-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">คน</label>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label text-end fs-4 total-witness-expense">20.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateWitnessExpense() {
                        document.querySelectorAll(".witness-expense-row").forEach(row => {
                            let countInput = row.querySelector(".witness-count");
                            let count = parseInt(countInput.value) || 0;
                            let costPerWitness = 20; // ค่าคงที่ 20 บาทต่อพยาน
                            let totalCost = Math.max(0, count * costPerWitness); // ป้องกันค่าติดลบ

                            row.querySelector(".total-witness-expense").textContent =
                                totalCost // อัปเดตค่าผลลัพธ์ที่ label
                        });
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        document.querySelectorAll(".witness-count").forEach(input => {
                            input.addEventListener("input", calculateWitnessExpense);
                            input.addEventListener("change", calculateWitnessExpense);
                        });

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateWitnessExpense();
                    });
                    </script>
                </div>

                <!-- งานช่างรางวัด -->
                <div class="row mb-3 service-sub-info-box mt-2">
                    <div class="row mb-12">
                        <label class="form-label fs-4 fw-bold text-decoration-underline">
                            งานช่างรางวัด
                        </label>
                    </div>

                    <!-- 15.ค่าน้ำมัน -->
                    <div class="row mb-2 oil-row">
                        <div class="col-md-3">
                            <label class="form-label">15. ค่าน้ำมัน </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end distance" value="1" min="0"
                                id="distance-15">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> กม. x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end cost-per-distance" value="7" min="0">

                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end round" value="2" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">เที่ยว</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 oil-cost">0.00</label>
                        </div>
                        <script>
                        function calculateOilCost() {
                            let row = document.querySelector(".oil-row"); // เลือกเฉพาะแถวที่ต้องคำนวณ
                            if (!row) return;

                            let distance = parseFloat(row.querySelector(".distance").value) || 0;
                            let costPerDistance = parseFloat(row.querySelector(".cost-per-distance").value) || 0;
                            let roundTrip = parseFloat(row.querySelector(".round").value) || 0;

                            let totalCost = Math.max(0, distance * costPerDistance * roundTrip); // ป้องกันค่าติดลบ

                            row.querySelector(".oil-cost").textContent = totalCost.toLocaleString('en-US');
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(".distance, .cost-per-distance, .round");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateOilCost);
                                input.addEventListener("change", calculateOilCost);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateOilCost();
                        });
                        </script>
                    </div>

                    <!-- 16.ค่าเสือมยานพนะ ต่อ กิโลเมตร -->
                    <div class="row mb-2 vehicle-loss-row">
                        <div class="col-md-3">
                            <label class="form-label">16. ค่าเสื่อมยานพาหนะ </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end vehicle-loss-distance" value="1"
                                id="distance-16" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> กม. x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end vehicle-loss-cost-per-distance" value="1"
                                min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end vehicle-loss-round" value="2" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">เที่ยว</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 vehicle-loss-cost">0.00</label>
                        </div>
                        <script>
                        function calculateVehicleLossCost() {
                            let row = document.querySelector(".vehicle-loss-row"); // เลือกเฉพาะแถวที่ต้องคำนวณ
                            if (!row) return;

                            let distance = parseFloat(row.querySelector(".vehicle-loss-distance").value) || 0;
                            let costPerDistance = parseFloat(row.querySelector(".vehicle-loss-cost-per-distance")
                                .value) || 0;
                            let roundTrip = parseFloat(row.querySelector(".vehicle-loss-round").value) || 0;

                            let totalCost = Math.max(0, distance * costPerDistance * roundTrip); // ป้องกันค่าติดลบ

                            row.querySelector(".vehicle-loss-cost").textContent = totalCost.toLocaleString('en-US');
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(
                                ".vehicle-loss-distance, .vehicle-loss-cost-per-distance, .vehicle-loss-round"
                            );

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateVehicleLossCost);
                                input.addEventListener("change", calculateVehicleLossCost);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateVehicleLossCost();
                        });
                        </script>
                    </div>

                    <!-- 17.ค่าคนงาน -->
                    <div class="row mb-2 worker-row">
                        <div class="col-md-4">
                            <label class="form-label">17. ค่าคนงาน </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end worker-count" id="workerCountInput"
                                value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> คน x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end wage-per-day" value="500" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end work-days" value="1" min="0"
                                id="worker-date-17">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">วัน</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 worker-cost">0.00</label>
                        </div>
                        <script>
                        function calculateWorkerCost() {
                            let row = document.querySelector(".worker-row");
                            if (!row) return;

                            let workerCount = parseFloat(row.querySelector(".worker-count").value) || 0;
                            let wagePerDay = parseFloat(row.querySelector(".wage-per-day").value) || 0;
                            let workDays = parseFloat(row.querySelector(".work-days").value) || 0;

                            let totalCost = Math.max(0, workerCount * wagePerDay * workDays); // ป้องกันค่าติดลบ

                            row.querySelector(".worker-cost").textContent = totalCost.toLocaleString('en-US');
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(".worker-count, .wage-per-day, .work-days");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateWorkerCost);
                                input.addEventListener("change", calculateWorkerCost);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateWorkerCost();
                        });
                        </script>
                    </div>


                    <!-- 18.ค่าเบี้ยเลี้ยงช่างรังวัด -->
                    <div class="row mb-2 surveyor-row">
                        <div class="col-md-4">
                            <label class="form-label">18. ค่าเบี้ยเลี้ยงช่างรังวัด </label>
                        </div>
                        <div class="col-md-1">
                            <input type="type" class="form-control text-end surveyor-count" value="1" min="0"
                                id="staffCountInput-18">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> คน x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end allowance-per-day" value="500" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end work-days" value="1" min="0"
                                id="worker-date-18">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">วัน</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 total-allowance">0.00</label>
                        </div>
                        <script>
                        function calculateSurveyorAllowance() {
                            let row = document.querySelector(".surveyor-row");
                            if (!row) return;

                            let surveyorCount = parseFloat(row.querySelector(".surveyor-count").value) || 0;
                            let allowancePerDay = parseFloat(row.querySelector(".allowance-per-day").value) || 0;
                            let workDays = parseFloat(row.querySelector(".work-days").value) || 0;

                            let totalCost = Math.max(0, surveyorCount * allowancePerDay * workDays); // ป้องกันค่าติดลบ

                            row.querySelector(".total-allowance").textContent = totalCost.toLocaleString('en-US', {
                                maximumFractionDigits: 0
                            });

                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(
                                ".surveyor-count, .allowance-per-day, .work-days");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateSurveyorAllowance);
                                input.addEventListener("change", calculateSurveyorAllowance);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateSurveyorAllowance();
                        });
                        </script>
                    </div>


                    <!-- 19.เงินเดือนช่างรังวัด -->
                    <div class="row mb-2 salary-row">
                        <div class="col-md-6">
                            <label class="form-label">19. เงินเดือนช่างรังวัด </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end surveyor-count" value="1" min="0"
                                id="staffCountInput-19">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> คน x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end salary-per-person" value="1,875"
                                oninput="formatNumber(this)">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท </label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 total-salary">0.00</label>
                        </div>
                        <script>
                        function formatNumber(input) {
                            // ลบ "," ออกจากค่าก่อนนำไปคำนวณ
                            let value = input.value.replace(/,/g, '');
                            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // เพิ่ม "," ทุกหลักพัน
                        }

                        function calculateSurveyorSalary() {
                            let row = document.querySelector(".salary-row");
                            if (!row) return;

                            let surveyorCount = parseFloat(row.querySelector(".surveyor-count").value) || 0;
                            let salaryPerPerson = parseFloat(row.querySelector(".salary-per-person").value.replace(/,/g,
                                '')) || 0;

                            let totalCost = Math.max(0, surveyorCount * salaryPerPerson); // ป้องกันค่าติดลบ

                            row.querySelector(".total-salary").textContent = totalCost.toLocaleString('en-US', {
                                maximumFractionDigits: 0
                            }); // แสดงผลแบบมีเครื่องหมาย , คั่นหลัก
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(".surveyor-count, .salary-per-person");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", function() {
                                    formatNumber(input);
                                    calculateSurveyorSalary();
                                });
                                input.addEventListener("change", calculateSurveyorSalary);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateSurveyorSalary();
                        });
                        </script>
                    </div>


                    <!-- 20.คาที่พักวันในการดำเนินการส่งเรื่อง -->
                    <div class="row mb-2 accommodation-row">
                        <div class="col-md-4">
                            <label class="form-label">20. ค่าที่พัก </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end person-count" value="1" min="0"
                                id="staffCountInput-20">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> คน x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end cost-per-night" value="500" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end nights-count" value="1" min="0"
                                id="worker-date-20">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">วัน</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 total-accommodation">0.00</label>
                        </div>
                        <script>
                        function calculateAccommodationCost() {
                            let row = document.querySelector(".accommodation-row");
                            if (!row) return;

                            let personCount = parseFloat(row.querySelector(".person-count").value) || 0;
                            let costPerNight = parseFloat(row.querySelector(".cost-per-night").value) || 0;
                            let nightsCount = parseFloat(row.querySelector(".nights-count").value) || 0;

                            let totalCost = Math.max(0, personCount * costPerNight * nightsCount); // ป้องกันค่าติดลบ

                            row.querySelector(".total-accommodation").textContent = totalCost.toLocaleString('en-US', {
                                maximumFractionDigits: 0
                            }); // แสดงผลเป็นทศนิยม 2 ตำแหน่ง
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(
                                ".person-count, .cost-per-night, .nights-count");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateAccommodationCost);
                                input.addEventListener("change", calculateAccommodationCost);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateAccommodationCost();
                        });
                        </script>
                    </div>


                    <!-- 21.ค่าอาหารและค่าใช้จ่ายอื่นๆ -->
                    <div class="row mb-2 food-other-expense-row">
                        <div class="col-md-4">
                            <label class="form-label">21. ค่าอาหารและค่าใช้จ่ายอื่นๆ </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end food-person-count" value="1" min="0"
                                id="staffCountInput-21">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> คน x </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end food-cost-per-day" value="500" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label"> บาท x </label>
                        </div>
                        <div class="col-md-1">
                            <input type="text" class="form-control text-end food-days-count" value="1" min="0"
                                id="worker-date-21">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">วัน</label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-end fs-4 total-food-other-expense">0.00</label>
                        </div>
                        <script>
                        function calculateFoodOtherExpense() {
                            let row = document.querySelector(".food-other-expense-row");
                            if (!row) return;

                            let foodPersonCount = parseFloat(row.querySelector(".food-person-count").value) || 0;
                            let foodCostPerDay = parseFloat(row.querySelector(".food-cost-per-day").value) || 0;
                            let foodDaysCount = parseFloat(row.querySelector(".food-days-count").value) || 0;

                            let totalCost = Math.max(0, foodPersonCount * foodCostPerDay *
                                foodDaysCount); // ป้องกันค่าติดลบ

                            row.querySelector(".total-food-other-expense").textContent = totalCost.toLocaleString(
                                'en-US', {
                                    maximumFractionDigits: 0
                                }); // แสดงผลเป็นทศนิยม 2 ตำแหน่ง
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            let inputs = document.querySelectorAll(
                                ".food-person-count, .food-cost-per-day, .food-days-count");

                            // เพิ่ม event listener ให้ทุก input
                            inputs.forEach(function(input) {
                                input.addEventListener("input", calculateFoodOtherExpense);
                                input.addEventListener("change", calculateFoodOtherExpense);
                            });

                            // คำนวณค่าเริ่มต้นตอนโหลดหน้า
                            calculateFoodOtherExpense();
                        });
                        </script>
                    </div>


                    <!-- 22.ค่าดำเนินส่วนสำนักงาน (เงินเดือน+ค่าเช่า+เครื่องมือ) -->
                    <div class="row mb-3 manage-row">
                        <div class="col-md-7">
                            <label class="form-label">
                                22. ค่าดำเนินส่วนสำนักงาน (เงินเดือน+ค่าเช่า+เครื่องมือ)
                            </label>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">จำนวน</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control text-end manage-count" value="750" min="0"
                                data-cost="1">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">บาท</label>
                        </div>
                        <div class="col-md-1 text-end">
                            <label class="form-label text-end fs-4 manage-cost">750.00</label>
                        </div>
                        <script>
                        function calculateManageCost() {
                            document.querySelectorAll(".manage-row").forEach(function(row) {
                                let manageCount = parseInt(row.querySelector(".manage-count").value) || 0;
                                let costPerManage = parseFloat(row.querySelector(".manage-count").getAttribute(
                                    "data-cost")) || 750;
                                let totalCost = Math.max(0, manageCount * costPerManage); // ป้องกันค่าติดลบ

                                row.querySelector(".manage-cost").textContent =
                                    totalCost // แสดงผลลัพธ์ที่ label
                            });
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelectorAll(".manage-count").forEach(function(input) {
                                input.addEventListener("input", calculateManageCost);
                                input.addEventListener("change", calculateManageCost);
                            });

                            // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                            calculateManageCost();
                        });
                        </script>
                    </div>
                    <!-- 23.ค่าลงระวางของสำนักงาน -->
                    <div class="row mb-3 office-survey-fee-row">
                        <div class="col-md-8">
                            <label class="form-label">23. ค่าลงระวางของสำนักงาน (500 บาทต่อรายการ)</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control text-end office-survey-count" value="1" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">รายการ</label>
                        </div>
                        <div class="col-md-1 text-end">
                            <label class="form-label text-end fs-4 total-office-survey-fee">500.00</label>
                        </div>
                    </div>

                    <script>
                    function calculateOfficeSurveyFee() {
                        document.querySelectorAll(".office-survey-fee-row").forEach(row => {
                            let countInput = row.querySelector(".office-survey-count");
                            let count = parseInt(countInput.value) || 0;
                            let costPerItem = 500; // ค่าคงที่ 750 บาทต่อรายการ
                            let totalCost = Math.max(0, count * costPerItem); // ป้องกันค่าติดลบ

                            row.querySelector(".total-office-survey-fee").textContent =
                                totalCost // แสดงผลลัพธ์ที่ label
                        });
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        document.querySelectorAll(".office-survey-count").forEach(input => {
                            input.addEventListener("input", calculateOfficeSurveyFee);
                            input.addEventListener("change", calculateOfficeSurveyFee);
                        });

                        // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                        calculateOfficeSurveyFee();
                    });
                    </script>

                </div>

                <!-- รวมค่าใช้จ่าย -->
                <div class="row mb-4">
                    <div class="col-md-12 total-expense-wrapper">
                        <div class="total-expense-box">
                            <span class="total-expense-label">รวมค่าใช้จ่าย</span>
                            <span class="total-expense">0.00</span>
                            <span class="total-expense-unit">บาท</span>
                        </div>
                    </div>
                </div>

                <!-- java script การคำนวณค่าใช้จ่าย -->
                <script>
                function calculateTotalExpense() {
                    let total = 0;

                    // ดึงค่าทุกพารามิเตอร์จากข้อที่ 1-23 (เรียงลำดับให้ตรงกับแบบฟอร์ม)
                    let costSelectors = [
                        ".stamp-duty-cost", // 1. ค่าอากรสแตมป์ติดใบมอบอำนาจที่ดิน
                        ".stamp-contract-cost", // 2. ค่าอากรสแตมป์ติดใบสัญญาจ้าง
                        ".stamp-survey-cost", // 3. ค่าอากรสแตมป์ติดใบมอบติดตามงานรังวัด
                        ".stamp-office-cost", // 4. ค่าอากรสแตมป์ติดใบมอบสำนักงานทำสัญญาแทน
                        ".request-fee-cost", // 5. ค่าคำขอ
                        ".total-witness-expense", // 6. ค่าพยาน
                        ".boundary-fee-cost", // 7. ค่าหลักเขต
                        ".separation-fee-cost", // 8. ค่าแบ่งแยก
                        ".area-limit-fee-cost", // 9. ค่าแบ่งจำกัดเนื้อที่
                        ".adjacent-notice-cost", // 10. ค่าส่งหมายข้างเคียง
                        ".registration-request-fee", // 11. ค่าคำของานจดทะเบียน
                        ".fee-cost", // 12. ค่าธรรมเนียม
                        ".deed-fee-cost", // 13. ค่าโฉนด
                        ".total-witness-expense", // 14. ค่าพยาน
                        ".oil-cost", // 15. ค่าน้ำมัน
                        ".vehicle-loss-cost", // 16. ค่าเสื่อมยานพาหนะ
                        ".worker-cost", // 17. ค่าคนงาน
                        ".total-allowance", // 18. ค่าเบี้ยเลี้ยงช่างรังวัด
                        ".total-salary", // 19. เงินเดือนช่างรังวัด
                        ".total-accommodation", // 20. ค่าที่พัก
                        ".total-food-other-expense", // 21. ค่าอาหารและค่าใช้จ่ายอื่นๆ
                        ".manage-cost", // 22. ค่าดำเนินส่วนสำนักงาน
                        ".total-office-survey-fee" // 23. ค่าลงระวางของสำนักงาน
                    ];

                    // บวกค่าทั้งหมดเข้าด้วยกัน
                    costSelectors.forEach(selector => {
                        document.querySelectorAll(selector).forEach(label => {
                            total += parseFloat(label.textContent.replace(/,/g, '')) || 0;
                        });
                    });

                    // แสดงผลลัพธ์
                    document.querySelector(".total-expense").textContent = total.toLocaleString('en-US');
                }

                document.addEventListener("DOMContentLoaded", function() {
                    let inputs = document.querySelectorAll("input");

                    // เรียกใช้ฟังก์ชันทุกครั้งที่มีการเปลี่ยนค่า
                    inputs.forEach(input => {
                        input.addEventListener("input", calculateTotalExpense);
                        input.addEventListener("change", calculateTotalExpense);
                    });

                    // คำนวณค่าเริ่มต้นเมื่อโหลดหน้า
                    calculateTotalExpense();
                });
                </script>
            </div>

            <!--- button Export PDF -->
            <div>
                <button id="exportPdfBtn" class="btn btn-danger mt-3">Export to PDF</button>

                <script>
                document.getElementById("exportPdfBtn").addEventListener("click", function() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    let pdf = new jsPDF("p", "mm", "a4"); // ตั้งค่า PDF เป็น A4 แนวตั้ง

                    let element = document.body; // ดึงเนื้อหาทั้งหน้าเว็บ

                    html2canvas(element, {
                        scale: 2, // เพิ่มความละเอียด
                        useCORS: true, // ป้องกันปัญหาภาพจาก URL ภายนอกไม่โหลด
                        scrollX: 0,
                        scrollY: 0,
                        windowWidth: document.documentElement.scrollWidth, // ขนาดจริงของเว็บ
                        windowHeight: document.documentElement.scrollHeight
                    }).then((canvas) => {
                        let imgData = canvas.toDataURL("image/png"); // แปลงเป็นรูป PNG

                        let imgWidth = 210; // ความกว้างของ A4 (210mm)
                        let pageHeight = 297; // ความสูงของ A4 (297mm)
                        let imgHeight = (canvas.height * imgWidth) / canvas
                            .width; // คำนวณสัดส่วนความสูงของภาพ

                        let heightLeft = imgHeight; // ใช้เก็บความสูงของเนื้อหาที่ยังเหลือ
                        let position = 0;

                        // เพิ่มรูปเข้า PDF หน้าแรก
                        pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;

                        // ถ้ามีข้อมูลเกิน 1 หน้า A4 → เพิ่มหน้าใหม่แล้ววางรูปต่อ
                        while (heightLeft > 0) {
                            position -= pageHeight; // ขยับตำแหน่งภาพลง
                            pdf.addPage(); // เพิ่มหน้าใหม่
                            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }

                        pdf.save("exported_survey.pdf"); // บันทึกไฟล์ PDF
                    });
                });
                </script>
            </div>

        </div>
    </div>
</body>

</html>

<!--
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยินดีต้อนรับ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="welcome-text">
        <h1>ยินดีต้อนรับ <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        <a href="logout.php" class="logout-link">ออกจากระบบ</a>
    </div>
</body>
</html>