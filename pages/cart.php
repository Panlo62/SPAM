<!doctype html>
<html lang=en>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAM</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/header.css">
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

    <main class="cart-container">
      <h1 class="cart-header">Your Shopping Cart</h1>
      <?php
        $uid = $_SESSION['uid'];
        $sql = "SELECT cart.pid, quantity, name, price, discount, inventory FROM cart, product WHERE product.pid=cart.pid AND uid = $uid";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
          echo "<div class='cart-items'>";
          while ($row = $result->fetch_assoc()) {
            $pid = $row["pid"];
            $quantity = $row["quantity"];
            $name = $row["name"];
            $price = (int)$row["price"];
            $discount = $row["discount"];
            $inventory = $row["inventory"];
            $image = "../images/prod{$pid}a.jpg";
            $finalPrice = (int)((100 - $row['discount'])/100 * $row['price']);
            echo "<div class='cart-item'>";
            echo "<a href='../pages/single_product.php?id=$pid'><img src='$image' alt='$name'></a>";
            echo "<p class='item-name'>$name</p>";
            echo "<p class='item-price'>₹$finalPrice</p>";
            echo "<div class='quantity-control'>";
            echo "<button class='decrease-qty'>-</button>";
            echo "<input id='$pid' type='text' value='$quantity' max='$inventory' readonly>";
            echo "<button class='increase-qty'>+</button>";
            echo "</div>";
            echo "<button class='remove'>Remove</button>";
            echo "</div>";
          }
          echo "</div>";
          echo "<div class='purchase-container'>";
          echo "<button class='purchase'>Purchase All Items</button>";
          echo "</div>";
        }
        else {
          echo "<div class='no-products'><p>No products added to the cart.</p></div>";
          echo "<div class='purchase-container'>";
          echo "<button onclick='window.location.href = \"../pages/product.php\"'>Shop Items</button>";
          echo "</div>";
        }
        mysqli_close($conn);
      ?>
    </main>

    <script>
      //Update quantity in cart
      const updateDb = (id, qty) => {
        fetch('updateCart.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json',},
          body: JSON.stringify({ id, qty }),
        });
      };

      //Remove product from cart
      const removeDb = (id = 0) => {
        fetch('removeCart.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json',},
          body: JSON.stringify({ id }),
        });
      };

      //Reduce inventory for the purchased product
      const updateProduct = (cartData) => {
        fetch('updateProduct.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json',},
          body: JSON.stringify(cartData),
        });
      };

      // Handle quantity increase and decrease
      document.querySelectorAll('.increase-qty').forEach(button => {
        button.addEventListener('click', event => {
            const input = event.target.parentElement.querySelector('input');
            if (input.value != input.max) {
              input.value = parseInt(input.value) + 1;
              updateDb(input.id, input.value);
            }
            else {
              alert("No more item available.");
            }
        });
      });

      document.querySelectorAll('.decrease-qty').forEach(button => {
        button.addEventListener('click', event => {
            const input = event.target.parentElement.querySelector('input');
            if (parseInt(input.value) > 1) {
              input.value = parseInt(input.value) - 1;
              updateDb(input.id, input.value);
            }
            else {
              const cartItem = event.target.closest('.cart-item');
              cartItem.remove();
              removeDb(input.id);
              if (document.querySelector(".cart-items").innerHTML === "") {
                window.location.href = "../pages/cart.php";
              }
            }
        });
      });

      // Handle "Remove" button
      document.querySelectorAll('.remove').forEach(button => {
        button.addEventListener('click', event => {
            const cartItem = event.target.closest('.cart-item');
            cartItem.remove();
            removeDb(cartItem.querySelector('input').id);
            if (document.querySelector(".cart-items").innerHTML === "") {
              window.location.href = "../pages/cart.php";
            }
        });
      });

      document.querySelector('.purchase').addEventListener('click', () => {
        const items = document.querySelectorAll('.cart-item');
        const cartData = Array.from(items).map(item => {
            const id = item.querySelector('input').id;
            const quantity = item.querySelector('input').value;
            return {id, quantity };
        });

        alert('Purchase successful!');
        updateProduct(cartData);
        removeDb();
        window.location.href = "../pages/cart.php";
      });
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