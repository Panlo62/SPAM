<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "spam";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize session
session_start();

// Handle registration
if (isset($_POST['register'])) {
    $reg_username = $_POST['reg_username'];
    $reg_email = $_POST['reg_email'];
    $reg_phone_no = $_POST['reg_phone_no.'];
    $reg_address = $_POST['reg_address'];
    $reg_password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email,phone_no ,address, password) VALUES (?, ?, ?,?,?)");
    $stmt->bind_param("sss", $reg_username, $reg_email, $reg_password);

    if ($stmt->execute()) {
        $success = "Registration successful! Please log in.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle login
if (isset($_POST['login'])) {
    $login_username = $_POST['login_phone_no'];
    $login_password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $login_phone_no);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($login_password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $login_phone_no;
            header("Location: pages/dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f4f4f4;
        }
        .container {
            width: 300px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        p {
            text-align: center;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error, .success {
            text-align: center;
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="tel" name="login_phone_no" placeholder="Phone no" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="toggleForm('register')">Register</a></p>
    </div>

    <div class="container" style="display: none;" id="register-container">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="reg_username" placeholder="Username" required>
            <input type="email" name="reg_email" placeholder="Email" required>
            <input type="tel" name="reg_phone_no." placeholder="phone no." required>
            <input type="text" name="reg_address" placeholder="address" required>
            <input type="password" name="reg_password" placeholder="Password" required>
            <input type="password" name="reg_con_password" placeholder="Confirm Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="toggleForm('login')">Login</a></p>
    </div>

    <script>
        function toggleForm(formType) {
            document.querySelector('.container').style.display = formType === 'login' ? 'block' : 'none';
            document.getElementById('register-container').style.display = formType === 'register' ? 'block' : 'none';
        }
    </script>
</body>
</html>
