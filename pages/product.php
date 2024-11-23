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

    <?php
      // Retrieve parameters from the URL
      $category = "";
      $price = "";
      $rating = "";
      $searchInput = "";
      $query = "";
      if (isset($_GET['category'])) {
        $query .= " WHERE ";
        $category = $_GET['category'];
        $query .= "category = '{$category}'";
      }
      if (isset($_GET['price'])) {
        if ($query) {
          $query .= " AND ";
        }
        else {
          $query .= " WHERE ";
        }
        $price = htmlspecialchars($_GET['price']);
        $minPrice = $price - 10000;
        $query .= "price - (price * discount / 100) BETWEEN $minPrice AND $price";
      }
      if (isset($_GET['rating'])) {
        if ($query) {
          $query .= " AND ";
        }
        else {
          $query .= " WHERE ";
        }
        $rating = htmlspecialchars($_GET['rating']);
        $query .= "reviews >= $rating";
      }
      if (isset($_GET['search'])) {
        $query .= " WHERE ";
        $searchInput = htmlspecialchars($_GET['search']);
        $query .= "name LIKE '%{$searchInput}%' OR description LIKE '%{$searchInput}%'";
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
                $Category = $row["category"];
                echo "<label><input type='radio' name='category' value='$Category'>$Category</label><br>";
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
            <button type="" class="reset-filters" onclick="remove(event)">Reset</button>
          </div>
        </form>
      </aside>
      <main class="products">
        <div class="second-header">
          <form onsubmit="filter(event)">
            <input type="text" placeholder="Search Item" name="searchInput">
            <input type="image" id="searchBarIcon" src="../images/search.png" height="50px">
          </form>
        </div>
        <div class="product-grid">
          <?php
          $sql = "SELECT pid, name, description, price, discount, reviews FROM product" . $query;
          $result = mysqli_query($conn, $sql);
          if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
              $pid = $row["pid"];
              $name = $row["name"];
              $description = $row["description"];
              $Price = (int)$row["price"];
              $discount = $row["discount"];
              $reviews = $row["reviews"];
              $name = $row['name'];
              $image = "../images/prod{$pid}a.jpg";
              $finalPrice = (int)((100 - $row['discount'])/100 * $row['price']);
              echo "<a href='../pages/single_product.php?id=$pid'><div class='product-tab' style='background: url($image), #f2f2f2; background-size: cover; background-position: center;' title='$description'>";
              echo "<div class='product-content'><h2>$name</h2>";
              if ($finalPrice == $Price) {
                echo "<p><strong>Price:</strong> ₹$Price</p>";
              }
              else {
                echo "<p><strong>Price:</strong> <span class='final-price'>₹$finalPrice</span> <span class='original-price'>₹$Price</span> <span class='discount'>($discount% off)</span></p>";
              }
              echo "<p><strong>Reviews:</strong> $reviews</p>";
              echo "</div></div></a>";
            }
          } else {
            echo '<div class="no_prod"><div>';
            echo '<img src="../images/noprod.png" alt="No products icon" style="width: 100px; margin-bottom: 10px;">';
            echo '<p>No products available for the selected filters.</p><br>';
            echo '<a href="../pages/product.php" style="color: #007BFF; text-decoration: none; font-weight: bold;">Explore Other Categories</a>';
            echo '</div></div';
          }
          mysqli_close($conn);
          ?>
        </div>
      </main>
    </div>

    <script>
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");
      const searchBar = document.querySelector('input[name="searchInput"]');
      const searchBarIcon = document.getElementById("searchBarIcon");

      if ("<?php echo $searchInput ?>") {
        searchBar.value = "<?php echo $searchInput ?>";
      }
      const searchStart= searchBar.value;

      if ("<?php echo $category?>") {
        document.querySelector('input[name="category"][value="<?php echo $category?>"]').checked = true;
      }
      if ("<?php echo $price?>") {
        document.querySelector('input[name="price"][value="<?php echo $price?>"]').checked = true;
      }
      if ("<?php echo $rating?>") {
        document.querySelector('input[name="rating"][value="<?php echo $rating?>"]').checked = true;
      } 

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
      searchBarIcon.addEventListener("click", () => {
        filter(event);
      })

      //Open product.php page when pressed enter
      searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
          openPage(searchInput.value.trim());
        }
      })

      const filter = event => {
        event.preventDefault();
        const category = document.querySelector('input[name="category"]:checked');
        const price = document.querySelector('input[name="price"]:checked');
        const rating = document.querySelector('input[name="rating"]:checked');
        let query = '';
        if (searchBar.value.trim() !== searchStart) {
          window.location.href = `../pages/product.php?search=${searchBar.value.trim()}`;
        }
        else {
          if (category) {
            query += "category="+encodeURIComponent(category.value);
          }
          if (price) {
            if (query) {
              query += "&";
            }
            query += "price="+price.value;
          }
          if (rating) {
            if (query) {
              query += "&";
            }
            query += "rating="+rating.value;
          }
          if (query) {
            window.location.href = `../pages/product.php?`+query;
          }
        }
      }

      const remove = event => {
        event.preventDefault();
        window.location.href = `../pages/product.php`;
      }
    </script>
  </body>
</html>