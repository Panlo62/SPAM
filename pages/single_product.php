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
      $pid = htmlspecialchars($_GET['id']);
      $conn = mysqli_connect("localhost", "root", "", "spam");
      $sql = "SELECT pid, name, description, category, price, discount, reviews, inventory FROM product WHERE pid = $pid";
      $result = mysqli_query($conn, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        $pid = $row["pid"];
        $name = $row["name"];
        $description = $row["description"];
        $price = (int)$row["price"];
        $discount = (int)$row["discount"];
        $reviews = $row["reviews"];
        $inventory = $row["inventory"];
        $images = ["../images/prod{$pid}a.jpg", "../images/prod{$pid}b.jpg", "../images/prod{$pid}c.jpg", "../images/prod{$pid}d.jpg"];
        $finalPrice = (int)((100-$discount)/100 * $price);
      }
      mysqli_close($conn);
    ?>

    <main>
      <div class="product-images">
        <div class="main-image">
          <img src=<?php echo $images[0] ?> alt="Main Product Image" id="main-image">
        </div>
        <div class="image-thumbnails">
          <?php foreach ($images as $img) {
              echo "<img src=$img alt='$name image' onclick='changeImage(\"{$img}\")'>";
            }
          ?>
        </div>
      </div>
      <div class="product-details">
        <h1 id="product-name"><?php echo $name ?></h1>
        <p id="product-description"><?php echo $description ?></p>
        <div class="ratings">
          <?php $i = 1;
          for (; $i < $reviews; $i++) {
            echo "<span class='star'>&#x2605;</span>";
          }
          if ($reviews < $i && $reviews > $i-1) {
            $fill = ($reviews - $i + 1)*100;
            echo "<span class='star' style='background: linear-gradient(90deg, black $fill%, #ddd $fill%); background-clip: text; color: transparent;'>&#x2605;</span>";
          }
          for (; $i < 5; $i++) {
            echo "<span class='star' style='color: #ddd;'>&#x2605;</span>";
          }
          ?>
          <span class="rating-number">(<?php echo $reviews ?>/5)</span>
        </div>
        <div class="product-pricing">
          <p class="original-price">₹<span id="original-price"><?php echo $price ?></span></p>
          <p class="discount-price">₹<span id="discount-price"><?php echo $finalPrice ?></span></p>
          <p class="discount-percentage">(<?php echo $discount ?>% off)</p>
        </div>
        <button id="<?php echo $pid; ?>" class="add-to-cart-btn" onclick="checkSession()">Add to Cart</button>
        <div id="quantityControls" class="quantity-controls" style="display:none;">
          <button onclick="updateQuantity('decrement')">-</button>
          <input id="quantityInput" type="number" value="1" min="1" style="font-size: 20px;" readonly/>
          <button onclick="updateQuantity('increment')">+</button>
          <button onclick="confirmAddToCart()">Confirm</button>
        </div>
      </div>
    </main>

    <script>
      function checkSession() {
        fetch('check_session.php')
          .then(response => response.json())
          .then(data => {
            if (data.status === 'not_logged_in') {
              window.location.href = '../pages/auth.php';
            } else if (data.status === 'logged_in') {
              showControls();
            }
        })
      }

      let inventory;
      function showControls() {
        document.getElementById('quantityControls').style.display = 'block';
        document.querySelector('.add-to-cart-btn').style.display = 'none';
        const pid = <?php echo $pid ?>;
        fetch('get_cart_quantity.php?pid=' + pid)
        .then(response => response.json())
        .then(data => {
          const quantity = data.quantity;
          inventory = data.inventory;
          document.getElementById('quantityInput').value = quantity;
        })
      }

      // Function to update quantity (increase or decrease)
      function updateQuantity(action) {
        let quantityInput = document.getElementById('quantityInput');
        let currentQuantity = parseInt(quantityInput.value);
        if (action === 'increment') {
          if (inventory == currentQuantity) {
            alert("Maximum limit reached");
          }
          else {
            currentQuantity++;
          }
        } else if (action === 'decrement' && currentQuantity > 1) {
          currentQuantity--;
        }
        quantityInput.value = currentQuantity;
      }

      function confirmAddToCart() {
        const qty = document.getElementById('quantityInput').value;
        const id = <?php echo $pid ?>;
        fetch('updateCart.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json',},
          body: JSON.stringify({id, qty}),
        });
        alert("Product added to Cart");
      }

      function changeImage(imageUrl) {
        mainImage = document.getElementById('main-image').src = imageUrl;
      }

      const search = document.getElementById("search");
      const searchInput = document.querySelector("input[type='search']");

      //Open the product.php page
      const openPage = input => {
        if (input !== "") {
          window.location.href = `../pages/product.php?search=${encodeURIComponent(input)}`;
        }
      }

      //First click displays input box, second click for search
      search.addEventListener("click", () => {
        if (searchInput.style.display === "") {
          searchInput.style.display = "inline";
        }
        else {
          if (searchInput.value.trim() !== "") {
            openPage(searchInput.value.trim());
          }
        }
      })

      //Search product when pressed enter
      searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
          if (searchInput.value.trim() !== "") {
            openPage(searchInput.value.trim());
          }
        }
      })
    </script>
  </body>
</html>