<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/home.css">
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
              echo "<li><a href='pages/product.php?category=$encodedCategory'>$category</a></li>";
            }
            mysqli_close($conn);
          ?>
          <li>
            <a href="pages/product.php">More</a>
          </li>
        </ul>
        <div class="main">
          <img src="images/spam.png" alt="SPAM logo">
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
      <div class="left">
        <h1>One <i>Place</i> for All Your <i>Needs</i></h1>
        <p>Discover everything you’re looking for, from essentials to indulgences.</p>
        <button><a href="pages/product.php">Shop New Arrivals</a></button>
      </div>
      <div class="right">
        <img src="images/home_img.webp" alt="Image with lots of items belonging to different categories">
      </div>
    </main>

    <script>
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");

      //Open the product.php page
      const openPage = input => {
        if (input !== "") {
          window.location.href = `pages/product.php?search=${encodeURIComponent(input)}`;
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