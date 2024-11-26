Video link => https://youtu.be/60tVrvSAIZ8





----------------------------------------------------------------------------------------------------------------





Directory structure of project =>
1) images => Folder for all images and icons used in the project
2) index.php => Main starting page of website
3) css => Folder for all css files
4) pages => Folder for all helper php pages to be navigated through links in index.php page
5) .extra => 2 files - spamDB.txt (mysql commands for database), spam.png (initial project thought image)

Files in pages =>
1) product.php => Main product page
2) single_product.php => Page for single product
3) cart.php => Display cart if user is logged in
4) dashboard.php => Show dashboard if user is logged in
5) auth.php => Handle login, register and reset password
6) logout.php => Helper file for user logout
7) check_session.php => Helper file to check if user is in session
8) updateCart.php => Helper file to update quantity change of item in cart table
9) removeCart.php => Helper to remove a product from cart table in database
10) updateProduct.php => Helper file to update tables orders, order_items, product table once an item is purchase
11) get_cart_quantity.php => Helper file to get the initial quantity of an item in single product page in case the user has already added it to cart





----------------------------------------------------------------------------------------------------------------





Database schema =>
5 tables- user, product, orders, order_items, cart

TABLE User => Uid, Username, Password, Phone, Address, Email, Sec_ques, Ans
Uid is PK, Phone is unique, Email can only be null

TABLE Product => Pid, Name, Description, Category, Price, Discount, Reviews Decimal(2,1), Inventory
Pid is PK

TABLE CART => Pid, Uid, Quantity
Pid, Uid are PK and are FK to User and Product TABLE respectively

TABLE Orders => Oid, Uid, Payment, Shipping
Oid, Uid are Pk and Uid is FK to User TABLE

TABLE order_items => Oid, Pid, Quantity
Oid, Pid are PK and are PK to Orders and Product TABLE respectively
