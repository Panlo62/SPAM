<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/product.css">
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
              echo "<li>$category</li>";  //Call product page with category search
            }
            mysqli_close($conn);
          ?>
          <li>
            <a href="pages/product.php">More</a>
          </li>
        </ul>
        <div class="main">
          <b>Spam</b><img src="images/spam.png" alt="SPAM logo">
        </div>
        <ul id="right">
          <li style="display: flex;">
            <input type="search" name="search" placeholder="Search item">
            <img id="search" class="icon" src="images/search.png" alt="Search icon">
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

    <main>
    </main>

    <script>
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");
      search.addEventListener("click", () => {
        if (searchInput.style.display === "") {
          searchInput.style.display = "inline";
        }
        else {
          //Call product page with search
        }
      })
      searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
          //Call product page with search
        }
      })
    </script>
  </body>
</html>