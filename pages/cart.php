

<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="../css/cart.css">
    

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

    <main class="products">
        <p>cart items</p>
        <div class="product-grid">
        <?php
          $uid = 2;
          $sql = "SELECT product.pid, product.name, product.price,product.reviews,product.discount,product.category,product.description, cart.quantity
                  FROM cart
                  JOIN product ON cart.pid = product.pid
                  WHERE cart.Uid = ?";
          
          $stmt = mysqli_prepare($conn, $sql);

      
          mysqli_stmt_bind_param($stmt, "i", $uid);

        
          mysqli_stmt_execute($stmt);

          
          $result = mysqli_stmt_get_result($stmt);

      
          if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pid = $row["pid"];
                    $name = $row["name"];
                    $category = $row["category"];
                    $description = $row["description"];
                    $price = (int)$row["price"];
                    $discount = $row["discount"];
                    $reviews = $row["reviews"];
                    $image = "../images/prod{$pid}a.jpg";
                    $finalPrice = (int)((100 - $discount) / 100 * $price);
                    $qty = $row["quantity"];
        
                    echo "<a href='../pages/single_product.php?id=$pid'>
                        <div class='product-tab' style='background: url($image), #f2f2f2; background-size: cover; background-position: center;' title='$description'>
                            <div class='product-content'>
                                <h2>$name</h2>";
                    if ($finalPrice == $price) {
                        echo "<p><strong>Price:</strong> ₹$price</p>";
                    } else {
                        echo "<p><strong>Price:</strong> <span class='final-price'>₹$finalPrice</span> 
                              <span class='original-price'>₹$price</span> 
                              <span class='discount'>($discount% off)</span></p>";
                    }
                    echo "<p><strong>Reviews:</strong> $reviews</p>";
                    echo "<p><strong>Qty: $qty</strong> </p>";
                    echo "</div></div></a>";
                }
          } else {
              echo "<p>No products available in your cart.</p>";
          }

          
          mysqli_stmt_close($stmt);
          mysqli_close($conn);


       
        
      
        ?>
        </div>
      </main>
  

    <script>
      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");

    
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