<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('auth.php');
require_once('connection/config.php');

// Conectare la baza de date folosind mysqli
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Preluare categorii
$query = "SELECT * FROM categories";
$result = mysqli_query($link, $query) or die("There are no records to display ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Categories Management</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Categories Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php" class="active">Categories</a>
        <a href="foods.php">Foods</a>
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

      <!-- Card pentru adăugare categorie -->
      <div class="allocation-card" style="max-width: 400px; margin: 0 auto 20px auto;">
        <h3>Add New Category</h3>
        <form id="categoryForm" name="categoryForm" method="post" action="categories-exec.php" onsubmit="return categoriesValidate(this)">
          <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
          </div>
          <div class="form-action" style="text-align: right; margin-top: 10px;">
            <input type="submit" name="Submit" value="Add Category" class="btn-primary" />
          </div>
        </form>
      </div>

      <hr />

      <!-- Tabel categorii existente -->
      <table class="modern-table" style="max-width: 500px; margin: 0 auto;">
        <caption>
          <h3>Available Categories</h3>
        </caption>
        <tr>
          <th>Category Name</th>
          <th>Action(s)</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
          echo '<td><a href="delete-category.php?id=' . $row['category_id'] . '" class="btn-remove">Remove</a></td>';
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
