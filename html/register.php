<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('db', 'user', 'userpassword', 'webapp');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $message = "อีเมลนี้ถูกใช้ไปแล้ว";
    } else {
        $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $message = "สมัครสมาชิกสำเร็จ! <a href='index.php'>เข้าสู่ระบบ</a>";
        } else {
            $message = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;700&family=Kanit:wght@400;700&family=Prompt:wght@400;700&display=swap"
        rel="stylesheet">
</head>

<body>

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
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php">หน้าแรก</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Register Box -->
        <div class="container d-flex justify-content-center align-items-center">
            <!-- Header -->
            <div class="card shadow-lg p-4" style="width: 700px;">
                <h2 class="text-center mb-6 highlight">สมัครสมาชิกเพื่อเข้าใช้งานระบบ</h2>

                <!-- ฟอร์มเข้าสู่ระบบ -->
                <div class="container d-flex justify-content-center align-items-center">
                    <div class="card shadow-lg p-4 " style="width: 300px;">
                        <div class="login-container">
                            <div>
                                <p>*กรุณากรอกข้อมูลให้ครบทุกช่อง</p>
                            </div>

                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="อีเมล"
                                        required>
                                    <div id="emailError" class="error-message"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="รหัสผ่าน" required>
                                    <div id="passwordError" class="error-message"></div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn_login">สมัครสมาชิก</button>
                                </div>
                            </form>
                            <?php if ($message): ?>
                            <p class="message"><?= htmlspecialchars($message) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</html>