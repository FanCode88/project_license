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

$result = mysqli_query($link, "SELECT * FROM specials")
  or die("There are no records to display ... \n" . mysqli_error($link));

$flag_1 = 1;
$currencies = mysqli_query($link, "SELECT * FROM currencies WHERE flag='$flag_1'")
  or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours. " . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Specials</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Specials Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php" class="active">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">

      <table class="modern-table">
        <caption>
          <h3>Add New Promotion</h3>
        </caption>
        <form name="specialsForm" id="specialsForm" action="specials-exec.php" method="post" enctype="multipart/form-data" onsubmit="return specialsValidate(this)">
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Photo</th>
            <th>Action</th>
          </tr>
          <tr>
            <td><input type="text" name="name" id="name" class="textfield" placeholder="e.g. Summer Offer" /></td>
            <td><textarea name="description" id="description" class="textfield" rows="2" cols="15" placeholder="Details..."></textarea></td>
            <td><input type="text" name="price" id="price" class="textfield" placeholder="0.00" /></td>
            <td><input type="date" name="start_date" id="start_date" class="textfield" /></td>
            <td><input type="date" name="end_date" id="end_date" class="textfield" /></td>
            <td><input type="file" name="photo" id="photo" class="file-input" /></td>
            <td><input type="submit" name="Submit" value="Add Promo" class="btn-submit" /></td>
          </tr>
        </form>
      </table>

      <hr />

      <table class="modern-table">
        <caption>
          <h3>Active Promotions</h3>
        </caption>
        <tr>
          <th>Promo Photo</th>
          <th>Promo Name</th>
          <th>Promo Description</th>
          <th>Promo Price</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Action(s)</th>
        </tr>

        <?php
        $symbol = mysqli_fetch_assoc($currencies);
        while ($row = mysqli_fetch_array($result)) {
          echo "<tr>";
          echo '<td><img src="../images/' . $row['special_photo'] . '" class="promo-img" width="80" height="70"></td>';
          echo '<td class="font-bold">' . htmlspecialchars($row['special_name']) . '</td>';
          echo '<td class="desc-cell">' . htmlspecialchars($row['special_description']) . '</td>';
          echo '<td><span class="price-cell">' . $symbol['currency_symbol'] . $row['special_price'] . '</span></td>';
          echo '<td>' . $row['special_start_date'] . '</td>';
          echo '<td>' . $row['special_end_date'] . '</td>';
          echo '<td><a href="delete-special.php?id=' . $row['special_id'] . '" class="btn-remove">Remove</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($result);
        mysqli_close($link);
        ?>
      </table>

    </div>

    <div id="footer">
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>

  </div>
</body>

</html>
