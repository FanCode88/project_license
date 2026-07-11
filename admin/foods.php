<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('auth.php');
require_once('connection/config.php');

// Conectare la baza de date
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Interogări
$result = mysqli_query($link, "SELECT * FROM food_details, categories WHERE food_details.food_category=categories.category_id")
  or die("Eroare la interogarea alimentelor: " . mysqli_error($link));

$categories = mysqli_query($link, "SELECT * FROM categories")
  or die("Eroare la interogarea categoriilor: " . mysqli_error($link));

$flag_1 = 1;
$currency_result = mysqli_query($link, "SELECT * FROM currencies WHERE flag='$flag_1'");
$symbol = mysqli_fetch_assoc($currency_result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Manage Foods</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">
    <div id="header">
      <h1>Foods Management</h1>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php" class="active">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">
      <!-- Formular Adăugare Food -->
      <div class="allocation-card" style="margin-bottom: 30px;">
        <h3>Add a New Food Item</h3>
        <form name="foodsForm" id="foodsForm" action="foods-exec.php" method="post" enctype="multipart/form-data" onsubmit="return foodsValidate(this)">
          <table width="100%" border="0" cellpadding="5" cellspacing="0">
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Price</th>
              <th>Category</th>
              <th>Photo</th>
              <th>Action</th>
            </tr>
            <tr>
              <td><input type="text" name="name" id="name" class="textfield" /></td>
              <td><textarea name="description" id="description" class="textfield" rows="2" cols="20"></textarea></td>
              <td><input type="text" name="price" id="price" class="textfield" style="width: 80px;" /></td>
              <td>
                <select name="category" id="category">
                  <option value="select">- select -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($categories)) {
                    echo "<option value=\"" . $row['category_id'] . "\">" . htmlspecialchars($row['category_name']) . "</option>";
                  }
                  ?>
                </select>
              </td>
              <td><input type="file" name="photo" id="photo" /></td>
              <td><input type="submit" name="Submit" value="Add Food" class="btn-primary" /></td>
            </tr>
          </table>
        </form>
      </div>

      <hr />

      <!-- Tabel cu alimente disponibile -->
      <table class="modern-table" width="100%">
        <caption>
          <h3>Available Foods</h3>
        </caption>
        <tr>
          <th>Photo</th>
          <th>Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Category</th>
          <th>Action</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td><img src='../images/" . htmlspecialchars($row['food_photo']) . "' width='60' height='60' style='border-radius: 4px;'></td>";
          echo "<td><strong>" . htmlspecialchars($row['food_name']) . "</strong></td>";
          echo "<td>" . htmlspecialchars($row['food_description']) . "</td>";
          echo "<td>" . ($symbol ? htmlspecialchars($symbol['currency_symbol']) : "$") . "" . htmlspecialchars($row['food_price']) . "</td>";
          echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
          echo '<td><a href="delete-food.php?id=' . $row['food_id'] . '" class="btn-remove">Remove</a></td>';
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
