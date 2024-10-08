<?php
session_start(); // Start session to track login attempts

// Initialize the error variable
$error = null;

// Initialize the failed login attempts session variable
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

include("server.php"); // Include your database connection

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    // Query to check if username or email exists and password matches
    $sql = "SELECT * FROM user WHERE (username = ? OR email = ?) AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username_email, $username_email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Successful login
        $_SESSION['login_attempts'] = 0; // Reset login attempts on success
        header("Location: index.php");
        exit();
    } else {
        // Check if username or email exists but password is incorrect
        $sql_check_user = "SELECT * FROM user WHERE username = ? OR email = ?";
        $stmt_check_user = $conn->prepare($sql_check_user);
        $stmt_check_user->bind_param("ss", $username_email, $username_email);
        $stmt_check_user->execute();
        $result_check_user = $stmt_check_user->get_result();

        if ($result_check_user->num_rows > 0) {
            $error = "Incorrect password";
        } else {
            $error = "No user found with that username or email";
        }

        // Increment failed login attempts
        $_SESSION['login_attempts']++;
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    // If the page is refreshed or visited, reset the login attempts
    $_SESSION['login_attempts'] = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container_login">
        <h1>เข้าสู่ระบบ</h1>
        <form action="login.php" method="POST">
            <div>
                <label for="username_email">Username or Email:</label>
                <input type="text" id="username_email" name="username_email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Display error message if it exists -->
            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <input type="submit" value="เข้าสู่ระบบ">
        </form>

        <a href="register.php">
            <button>สมัครสมาชิก</button>
        </a>

        <div>
            <!-- Show "ลืมรหัสผ่าน" button only if failed login attempts reach 3 -->
            <?php if ($_SESSION['login_attempts'] >= 3) { ?>
                <a href="forgot-password.php">
                    <button>ลืมรหัสผ่าน</button>
                </a>
            <?php } ?>
        </div>

    </div>
</body>

</html>
