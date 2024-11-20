<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/product.css">
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
              echo "<li><a href='../pages/product.php?category=$category'>$category</a></li>";
            }
          ?>
          <li>
            <a href="../pages/product.php">More</a>
          </li>
        </ul>
        <div class="main">
          <b>Spam</b><img src="../images/spam.png" alt="SPAM logo">
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
      // Retrieve parameters from the URL
      if (isset($_GET['category'])) {
        $catgeory = htmlspecialchars($_GET['category']);
        echo $category;
      }
      if (isset($_GET['search'])) {
        $input = htmlspecialchars($_GET['search']);
        echo $input;
      }
    ?>

    <div class="content">
      <aside class="product-filter">
        <h2>Filter products</h2>
        <form onsubmit="filter(event)">
          <div class="filter-section">
            <h3>Category:</h3>
            <?php
              $sql = "SELECT DISTINCT category FROM product";
              $result = mysqli_query($conn, $sql);
              while($row = mysqli_fetch_assoc($result)) {
                $category = $row["category"];
                echo "<label><input type='radio' name='category' value='$category'>$category</label><br>";
              }
            ?>
          </div>

          <div class="filter-section">
            <h3>Price:</h3>
            <label><input type='radio' name='price' value='10000'>0 - 10,000</label><br>
            <label><input type='radio' name='price' value='20000'>10,000 - 20,000</label><br>
            <label><input type='radio' name='price' value='30000'>20,000 - 30,000</label><br>
            <label><input type='radio' name='price' value='40000'>30,000 - 40,000</label><br>
            <label><input type='radio' name='price' value='50000'>40,000 - 50,000</label><br>
            <label><input type='radio' name='price' value='50000+'>50,000+</label>
          </div>

          <div class="filter-section">
            <h3>Ratings:</h3>
            <label><input type='radio' id='4.5' name='rating' value='4.5'>4.5+</label><br>
            <label><input type='radio' id='4' name='rating' value='4'>4+</label><br>
            <label><input type='radio' id='3.5' name='rating' value='3.5'>3.5+</label><br>
            <label><input type='radio' id='3' name='rating' value='3'>3+</label><br>
            <label><input type='radio' id='2' name='rating' value='2'>2+</label>
          </div>

          <div class="filter-buttons">
            <button type="submit" class="apply-filters">Apply Filters</button>
            <button type="reset" class="reset-filters">Reset</button>
          </div>
        </form>
      </aside>
      <main class="products">
        <div class="second-header">
          <form onsubmit="filter(event)">
            <input type="text" placeholder="Search Item" name="searchQuery">
            <input type="image" src="../images/search.png" height="50px">
          </form>
        </div>
        <div class="product-grid">
          <?php
          $sql = "SELECT pid, name, description, category, price, discount, reviews FROM product";
          $result = mysqli_query($conn, $sql);
          if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
              $pid = $row["pid"];
              $name = $row["name"];
              $category = $row["category"];
              $description = $row["description"];
              $price = (int)$row["price"];
              $discount = $row["discount"];
              $reviews = $row["reviews"];
              $name = $row['name'];
              $image = "../images/prod{$pid}a.jpg";
              $finalPrice = (int)((100 - $row['discount'])/100 * $row['price']);
              echo "<a href='../single_product.php?id=$pid'><div class='product-tab' style='background: url($image), #f2f2f2; background-size: cover; background-position: center;' title='$description'>";
              echo "<div class='product-content'><h2>$name</h2>";
              if ($finalPrice == $price) {
                echo "<p><strong>Price:</strong> ₹$price</p>";
              }
              else {
                echo "<p><strong>Price:</strong> <span class='final-price'>₹$finalPrice</span> <span class='original-price'>₹$price</span> <span class='discount'>($discount% off)</span></p>";
              }
              echo "<p><strong>Reviews:</strong> $reviews</p>";
              echo "</div></div></a>";
            }
          } else {
            echo "<p>No products available.</p>";
          }
          mysqli_close($conn);
          ?>
        </div>
      </main>
    </div>

    <script>
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");

      //First click displays input box, second click for search
      search.addEventListener("click", () => {
        if (searchInput.style.display === "") {
          searchInput.style.display = "inline";
        }
        else {
          if (searchInput.value.trim() !== "") {
            $input = searchInput.value.trim();   //Php input to this value and change search
          }
        }
      })

      //Search product when pressed enter
      searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
          if (searchInput.value.trim() !== "") {
            $input = searchInput.value.trim();   //Php input to this value and change search
          }
        }
      })

      const filter = event => {
        event.preventDefault();
        console.log(event);
      }
    </script>
  </body>
</html>