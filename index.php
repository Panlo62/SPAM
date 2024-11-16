<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/header.css">
  </head>
  <body>
    <header>
      <nav>
        <ul id="left">
          <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "SPAM";
            $conn = mysqli_connect($servername, $username, $password, $database);
            $sql = "SELECT category, count(pid) AS num_pid FROM product GROUP BY category ORDER BY num_pid DESC LIMIT 3";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)) {
              $category = $row["category"];
              echo "<li>$category</li>";
            }
            mysqli_close($conn);
          ?>
          <li>
            More categories
          </li>
        </ul>
        <div class="main">
          <b>Spam</b><img src="images/spam.png" alt="SPAM logo">
        </div>
        <ul id="right">
					<li>
            <input type="search" name="search" placeholder="Search item" style="display: none">
            <img class="icon" src="images/search.png" alt="Search icon">
          </li>
          <li>
            <a href="pages/dashboard.php"><img class="icon" src="images/user.png" alt="User icon"></a>
          </li>
          <li>
        		<a href="pages/cart.php"><img class="icon" src="images/shopping-cart.png" alt="Cart icon"></a>
          </li>
        </ul>
      </nav>
    </header>
    <!--
    <main>
      <h1><img src="images/spam.png" alt="SPAM logo">SPAM</h1>
      <p>One place for all your needs</p>
      <button>Shop Now</button>
    </main>
    -->
  </body>
</html>
