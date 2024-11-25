<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/auth.css">
  </head>
  <body>
    <header>
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
            mysqli_close($conn);
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

    <?php
      // Database connection
      $conn = mysqli_connect("localhost", "root", "", "spam");
      
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
        $reg_security_answer = $_POST['ans'];
        $result = mysqli_query($conn, "SELECT MAX(Uid) AS uid FROM user");
        $row = mysqli_fetch_assoc($result);
        $uid = (int)$row['uid'] + 1;

        $sql = "INSERT INTO user VALUES ($uid, '$reg_username', '$reg_password', '$reg_phone', '$reg_address', '$reg_email', '$reg_security_question', '$reg_security_answer')";
        
        if (mysqli_query($conn, $sql)) {
          $success = "Registration successful! Please log in.";
        }
        else {
          $error = "Error! Only one user can register with one phone number.";
        }
      }
      
      // Handle login
      if (isset($_POST['login'])) {
        $login_phone = $_POST['phone'];
        $login_password = $_POST['password'];
        $sql = "SELECT password, uid FROM user WHERE phone = $login_phone "; 

        // FETCHING DATA FROM DATABASE
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          if ($login_password == $row['password']) {
            $_SESSION['uid'] = $row['uid'];
            header("location: ../pages/dashboard.php");
          }
          else {
            $error = "Invalid password!";
          }
        }
        else{
          $error = "Invalid number! No user registered for this phone number.";
        }
      }

      // Handle reset password
      if (isset($_POST['resetPassword'])) {
        $reset_security_question = $_POST['sec_ques'];
        $reset_security_answer = $_POST['ans'];
        $reset_phone = $_POST['phone'];
        $reset_password = $_POST['password'];
        $reset_confirm_password = $_POST['confirm_password'];

        if ($reset_password != $reset_confirm_password) {
          $error = "Invalid! Password and Confirm password do not match.";
        }
        else {
          $sql = "SELECT uid, sec_ques, ans FROM user WHERE phone = $reset_phone";

          // FETCHING DATA FROM DATABASE
          $result = mysqli_query($conn, $sql);
          if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $uid = $row['uid'];
            if ($reset_security_answer == $row['ans'] && $reset_security_question == $row['sec_ques']) {
              //Update password
              $sql = "UPDATE user SET password = '$reset_password' WHERE uid = $uid";
              mysqli_query($conn, $sql);
              $success = "Password changed successfully!";
            }
            else {
              $error = "Invalid security question / answer!";
            }
          }
          else{
            $error = "Invalid number! No user registered for this phone number.";
          }
        }
      }

      mysqli_close($conn);
    ?>

    <main>
      <div class="container" id="login-container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
          <input type="text" name="phone" placeholder="Phone Number" required>
          <input type="password" name="password" placeholder="Password" required>
          <a href="#" onclick="toggleForm('resetPassword')" style="margin-bottom: 10px;">Forgot password?</a>
          <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="toggleForm('register')">Register</a></p>
      </div>

      <div class="container" id="resetPassword-container" style="display: none;">
        <h2>Reset Password</h2>
        <form method="POST">
          <input type="text" name="phone" placeholder="Phone Number" required>
          <select name="sec_ques" required>
            <option value="" disabled selected>Select your Security Question</option>
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="What is your birthplace?">What is your birthplace?</option>
            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
            <option value="What is your childhood nickname?">What is your childhood nickname?</option>
            <option value="What was your first school name?">What was your first school name?</option>
            <option value="What is your father's middle name?">What is your father's middle name?</option>
            <option value="What is the name of your best friend?">What is the name of your best friend?</option>
            <option value="What is your favorite book?">What is your favorite book?</option>
          </select>
          <input type="text" name="ans" placeholder="Security Answer" required>
          <input type="password" name="password" placeholder="Password" pattern="^[a-zA-Z0-9]{8}$" title="Password must be 8 alphanumeric characters." required>
          <input type="password" name="confirm_password" placeholder="Confirm password" pattern="^[a-zA-Z0-9]{8}$" title="Password must be 8 alphanumeric characters." required>
          <button type="submit" name="resetPassword">Reset password</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="toggleForm('register')">Register</a></p>
      </div>

      <div class="container" id="register-container" style="display: none;">
        <h2>Register</h2>
        <form method="POST">
          <input type="text" name="username" placeholder="Username" required>
          <input type="password" name="password" placeholder="Password" pattern="^[a-zA-Z0-9]{8}$" title="Password must be 8 alphanumeric characters." required>
          <input type="text" name="phone" placeholder="Phone Number" pattern="^\d{10}$" title="Write a ten digit phone number" required>
          <input type="text" name="address" placeholder="Address" required>
          <input type="email" name="email" placeholder="Email">
          <select name="sec_ques" required>
            <option value="" disabled selected>Select a Security Question</option>
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="What is your birthplace?">What is your birthplace?</option>
            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
            <option value="What is your childhood nickname?">What is your childhood nickname?</option>
            <option value="What was your first school name?">What was your first school name?</option>
            <option value="What is your father's middle name?">What is your father's middle name?</option>
            <option value="What is the name of your best friend?">What is the name of your best friend?</option>
            <option value="What is your favorite book?">What is your favorite book?</option>
          </select>
          <input type="text" name="ans" placeholder="Security Answer" required>
          <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="toggleForm('login')">Login</a></p>
      </div>
    </main>

    <script>
      function toggleForm(formType) {
        document.getElementById('login-container').style.display = formType === 'login' ? 'block' : 'none';
        document.getElementById('resetPassword-container').style.display = formType === 'resetPassword' ? 'block' : 'none';
        document.getElementById('register-container').style.display = formType === 'register' ? 'block' : 'none';
      }
      
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");
      
      //Open the product.php page
      const openPage = input => {
        if (input !== "") {
          window.location.href = `../pages/product.php?search=${encodeURIComponent(input)}`;
        }
      }

      //First click displays input box, second click opens product.php page
      search.addEventListener("click", () => {
        if (searchInput.style.display === "") {
          searchInput.style.display = "inline";
        }
        else {
          openPage(searchInput.value.trim());
        }
      })

      //Open product.php page when pressed enter
      searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
          openPage(searchInput.value.trim());
        }
      })
    </script>
  </body>
</html>