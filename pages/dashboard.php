<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/dashboard.css">
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
      //Check if user is logged in
      session_start();
      if (!isset($_SESSION['uid'])) {
        header("Location: ../pages/auth.php");
        exit();
      }
    ?>

    <main>
      <?php
        $uid = $_SESSION['uid'];
        $sql = "SELECT username, phone, address, email FROM user WHERE uid=$uid";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          $username = $row["username"];
          $phone = $row["phone"];
          $address = $row["address"];
          $email = $row["email"];
        }
      ?>
      <div class="dashboard-container">
        <div class="user-info-container">
          <div class="user-info-left">
            <h2 class="user-name"><?php echo $username?></h2>
            <p class="user-address"><?php echo $address?></p>
            <p class="user-phone"><strong>Phone:</strong> <?php echo $phone?></p>
            <p class="user-email"><strong>Email:</strong> <?php echo $email?></p>
          </div>
          <div class="user-info-right">
            <img src="../images/user.png" alt="User Photo" class="user-photo">
            <button class="view-cart-button" onclick="window.location.href='../pages/cart.php'">View Cart</button>
          </div>
        </div>
        <div class="purchasing-history">
          <h3>Purchasing History</h3>
          <ol>
            <?php
            $sql = "SELECT oi.pid, p.inventory, p.name, p.price, p.discount FROM user u, orders o, order_items oi, product p WHERE o.oid=oi.oid AND u.uid = o.uid AND p.pid=oi.pid AND u.uid=$uid";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
              $pid = $row["pid"];
              $name = $row["name"];
              $price = $row["price"];
              $discount = $row["discount"];
              $finalPrice = (int)((100 - $row['discount'])/100 * $row['price']);
              echo "<li class='cart-item'>";
              echo "<p class='item-name'>$name</p>";
              echo "<p class='item-price'>₹$finalPrice</p>";
              echo "<button id='id$pid' class='add-to-cart-btn' onclick='showControls($pid)'>Add to Cart</button>";
              echo "<div id='id$pid' class='quantity-controls' style='display:none;'>";
              echo "<button onclick='updateQuantity(\"decrement\")'>-</button>";
              echo "<input id='quantityInput' type='number' value='1' min='1' style='font-size: 14px;' readonly/>";
              echo "<button onclick='updateQuantity(\"increment\")'>+</button>";
              echo "<button class='confirm-btn' onclick=\"confirmAddToCart()\">Confirm</button>";
              echo "</div>";
              echo "</li>";
            }
            mysqli_close($conn);
            ?>
          </ol>
        </div>
      </div>
    </main>

    <script>
      let inventory;
      function showControls(pid) {
        document.querySelector(`#id${pid}.quantity-controls`).style.display = 'block';
        document.querySelector(`#id${pid}`).style.display = 'none';
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
    </script>

    <script>
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