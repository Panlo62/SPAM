<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/single_product.css">
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
      $pid = htmlspecialchars($_GET['id']);
      $conn = mysqli_connect("localhost", "root", "", "spam");
      $sql = "SELECT pid, name, description, category, price, discount, reviews, inventory FROM product WHERE pid = $pid";
      $result = mysqli_query($conn, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        $pid = $row["pid"];
        $name = $row["name"];
        $description = $row["description"];
        $category = $row["category"];
        $price = (int)$row["price"];
        $discount = (int)$row["discount"];
        $reviews = $row["reviews"];
        $inventory = $row["inventory"];
        $images = ["../images/prod{$pid}a.jpg", "../images/prod{$pid}b.jpg", "../images/prod{$pid}c.jpg", "../images/prod{$pid}d.jpg"];
        $finalPrice = (int)((100-$discount)/100 * $price);
      }
      mysqli_close($conn);
    ?>

    <main class = 'products'>
        <div class = 'figure'>
          <?php echo "<img src = '$images[0]', alt = 'Product image 1'>
          <img src = '$images[1]', alt = 'Product image 2'><br>
          <img src = '$images[2]', alt = 'Product image 3'>
          <img src = '$images[3]', alt = 'Product image 4'><br>";?>
        </div>
        <div class = 'fig-cap'> 
         <?php echo "<h1>$name</h1> Reviews: $reviews<br> $description <br>";
        if ($finalPrice == $price) {
          echo "<p><strong>Price:</strong> ₹$price</p>";
        }
        else {
          echo "<span class='discount'><b>-$discount% </b></span> <span class='final-price'>₹$finalPrice</span> 
          <br> MRP: <span class='original-price'>₹$price</span>";
        }?><br>
        Quantity: <input type = "number" value = 1 id = "quan"><br><br>
        <button type = "submit"> <img src="../images/shopping-cart.png" width="30" height="23" 
        alt = "Add to Cart">Add to Cart &nbsp;</button></div>
    </main>

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
    </script>
  </body>
</html>