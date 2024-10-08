<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนบัญชี</title>
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
        <h1>ลงทะเบียนบัญชี</h1>
        <form id="registerForm" action="add-member.php" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="fullname">Full name:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" required>
            </div>
            <div id="error-message" class="error"></div>
            <input type="submit" value="ลงทะเบียน">
        </form>
        <a href="login.php">
            <button>กลับสู้หน้า login</button>
        </a>
    </div>

    <script>
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const form = document.getElementById('registerForm');
        const errorMessage = document.getElementById('error-message');

        // Check if passwords match and meet length requirement before form submission
        form.addEventListener('submit', function (e) {
            if (passwordField.value.length < 6) {
                e.preventDefault();  // Prevent form submission
                errorMessage.textContent = 'Password must be at least 6 characters long.';
            } else if (passwordField.value !== confirmPasswordField.value) {
                e.preventDefault();  // Prevent form submission
                errorMessage.textContent = 'Passwords do not match. Please try again.';
            } else {
                errorMessage.textContent = '';  // Clear error message if validation passes
            }
        });
    </script>
</body>

</html>
