<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('auth.php');

// checking connection and connecting to a database
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Interogare securizată cu mysqli
$query = "SELECT members.member_id, members.firstname, members.lastname, billing_details.Street_Address, billing_details.Mobile_No, orders_details.*, food_details.*, cart_details.*, quantities.*
          FROM members, billing_details, orders_details, quantities, food_details, cart_details
          WHERE members.member_id=orders_details.member_id
          AND billing_details.billing_id=orders_details.billing_id
          AND orders_details.cart_id=cart_details.cart_id
          AND cart_details.food_id=food_details.food_id
          AND cart_details.quantity_id=quantities.quantity_id";

$result = mysqli_query($link, $query) or die("There are no records to display ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Orders</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="page">
    <div id="header">
      <h1>Orders Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php" class="active">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">
      <table class="modern-table">
        <caption>
          <h3>Orders List</h3>
        </caption>
        <tr>
          <th>Order ID</th>
          <th>Customer Names</th>
          <th>Food Name</th>
          <th>Food Price</th>
          <th>Quantity</th>
          <th>Total Cost</th>
          <th>Delivery Date</th>
          <th>Delivery Address</th>
          <th>Mobile No</th>
          <th>Action(s)</th>
        </tr>

        <?php
        // Parcurgem rândurile bazei de date
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td><span class=\"font-bold\">" . htmlspecialchars($row['order_id']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</td>";
          echo "<td class=\"font-bold\">" . htmlspecialchars($row['food_name']) . "</td>";
          echo "<td>" . htmlspecialchars($row['food_price']) . "</td>";
          echo "<td>" . htmlspecialchars($row['quantity_value']) . "</td>";
          echo "<td><span class=\"price-cell\">" . htmlspecialchars($row['total']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['delivery_date']) . "</td>";
          echo "<td class=\"desc-cell\">" . htmlspecialchars($row['Street_Address']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Mobile_No']) . "</td>";
          echo '<td><a href="delete-order.php?id=' . $row['order_id'] . '" class="btn-remove">Remove Order</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($result);
        mysqli_close($link);
        ?>
      </table>
      <hr />
    </div>

    <div id="footer">
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
