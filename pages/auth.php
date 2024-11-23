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
    $reg_username = $_POST['username'];
    $reg_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $reg_phone = $_POST['phone'];
    $reg_address = $_POST['address'];
    $reg_email = $_POST['email'];
    $reg_security_question = $_POST['sec_ques'];
    $reg_security_answer = password_hash($_POST['Ans'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO user (username, password, phone, address, email, sec_ques, Ans) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $reg_username, $reg_password, $reg_phone, $reg_address, $reg_email, $reg_security_question, $reg_security_answer);

    if ($stmt->execute()) {
        $success = "Registration successful! Please log in.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle login
if (isset($_POST['login'])) {
    $login_phone = $_POST['phone'];
    $login_password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE phone = ?");
    $stmt->bind_param("s", $login_phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $user_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($login_password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user_name;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Phone number not found.";
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
            width: 400px;
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
        input, select {
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
    <div class="container" id="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="toggleForm('register')">Register</a></p>
    </div>

    <div class="container" id="register-container" style="display: none;">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="sec_ques" required>
                <option value="" disabled selected>Select a Security Question</option>
                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                <option value="What was your first pet's name?">What was your first pet's name?</option>
                <option value="What is your favorite book?">What is your favorite book?</option>
            </select>
            <input type="text" name="Ans" placeholder="Security Answer" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="toggleForm('login')">Login</a></p>
    </div>

    <script>
        function toggleForm(formType) {
            document.getElementById('login-container').style.display = formType === 'login' ? 'block' : 'none';
            document.getElementById('register-container').style.display = formType === 'register' ? 'block' : 'none';
        }
    </script>
</body>
</html>
