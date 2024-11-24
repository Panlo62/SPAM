<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/auth.css">
  </head>
  <body><header>
      <nav>
        <ul id="left">
          <?php
            $conn = mysqli_connect("localhost", "root", "", "spam");
            $sql = "SELECT category, count(pid) AS num_pid FROM product GROUP BY category ORDER BY num_pid DESC LIMIT 3";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)) {
              $category = $row["category"];
              $encodedCategory = rawurlencode($category);
              echo "<li><a href='../pages/product.php?category=$encodedCategory'>$category</a></li>";
            }
          ?>
          <li>
            <a href="../pages/product.php">More</a>
          </li>
        </ul>
        <div class="main">
          <img src="../images/spam.png" alt="SPAM logo">
        </div>
        <ul id="right">
          <li style="display: flex;">
            <input type="search" name="search" placeholder="Search item">
            <img id="search" class="icon" src="../images/search.png" alt="Search icon">
          </li>
          <li>
            <a href="../pages/dashboard.php"><img class="icon" src="../images/user.png" alt="User icon"></a>
          </li>
          <li>
            <a href="../pages/cart.php"><img class="icon" src="../images/shopping-cart.png" alt="Cart icon"></a>
          </li>
        </ul>
      </nav>
    </header>
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
            <input type="text" name="phone" placeholder="Phone Number" pattern="^\d{10}$" required>
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
    $reg_password = $_POST['password'];
    $reg_phone = $_POST['phone'];
    $reg_address = $_POST['address'];
    $reg_email = $_POST['email'];
    $reg_security_question = $_POST['sec_ques'];
    $reg_security_answer = $_POST['Ans'];
    $result = $conn->query("SELECT MAX(Uid) AS uid FROM user");
    $row = $result->fetch_assoc();
    $uid = (int)$row['uid'] + 1;
    $stmt = $conn->query("INSERT INTO user VALUES ($uid, '$reg_username', '$reg_password', '$reg_phone', '$reg_address', '$reg_email', '$reg_security_question', '$reg_security_answer')");

    if ($stmt == TRUE) {
        $success = "Registration successful! Please log in.";
    } else {
        echo "Some error occured!";
    }

}

// Handle login
if (isset($_POST['login'])) {
    $login_phone = $_POST['phone'];
    $login_password = $_POST['password'];

   $query = "SELECT password FROM user WHERE phone = $login_phone "; 

// FETCHING DATA FROM DATABASE 
$result = $conn->query($query);
if($result->num_rows > 0)
{
    $row = $result->fetch_assoc();
    if ($login_password==$row['password']){
        header("location: ../pages/product.php");
    }
    else{
        echo "Invalid password! ";
    }
}
else{
    echo "Invalid phone number! ";
}

$conn->close();
}
?>

    <script>
        function toggleForm(formType) {
            document.getElementById('login-container').style.display = formType === 'login' ? 'block' : 'none';
            document.getElementById('register-container').style.display = formType === 'register' ? 'block' : 'none';
        }
    </script>
</body>
</html>
