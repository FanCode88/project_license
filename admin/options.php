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

// retrieve categories from the categories table
$categories = mysqli_query($link, "SELECT * FROM categories")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve quantities from the quantities table
$quantities = mysqli_query($link, "SELECT * FROM quantities")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve currencies from the currencies table (deleting)
$currencies = mysqli_query($link, "SELECT * FROM currencies")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve currencies from the currencies table (updating)
$currencies_1 = mysqli_query($link, "SELECT * FROM currencies")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve polls from the ratings table
$ratings = mysqli_query($link, "SELECT * FROM ratings")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve timezones from the timezones table (deleting)
$timezones = mysqli_query($link, "SELECT * FROM timezones")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve timezones from the timezones table (updating)
$timezones_1 = mysqli_query($link, "SELECT * FROM timezones")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve tables from the tables table
$tables = mysqli_query($link, "SELECT * FROM tables")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve partyhalls from the partyhalls table
$partyhalls = mysqli_query($link, "SELECT * FROM partyhalls")
  or die("Something is wrong ... \n" . mysqli_error($link));

// retrieve questions from the questions table
$questions = mysqli_query($link, "SELECT * FROM questions")
  or die("Something is wrong ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Options</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Options Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php" class="active">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">

      <!-- Zona formularelor administrative grupate pe carduri -->
      <div class="forms-wrapper" style="gap: 25px; padding: 10px 0;">

        <!-- MANAGE CATEGORIES -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Categories</h3>

          <form name="categoryAddForm" action="categories-exec.php" method="post" onsubmit="return categoriesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add New Category</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="categoryDeleteForm" action="delete-category.php" method="post" onsubmit="return categoriesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Category</label>
                <select name="category" id="category" style="width: 100%;">
                  <option value="select">- select category -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($categories)) {
                    echo "<option value=\"" . htmlspecialchars($row['category_id']) . "\">" . htmlspecialchars($row['category_name']) . "</option>";
                  }
                  mysqli_free_result($categories);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

        <!-- MANAGE QUANTITIES -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Quantities</h3>

          <form name="quantityAddForm" action="quantities-exec.php" method="post" onsubmit="return quantitiesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add Quantity</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="quantityDeleteForm" action="delete-quantity.php" method="post" onsubmit="return quantitiesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Quantity</label>
                <select name="quantity" id="quantity" style="width: 100%;">
                  <option value="select">- select quantity -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($quantities)) {
                    echo "<option value=\"" . htmlspecialchars($row['quantity_id']) . "\">" . htmlspecialchars($row['quantity_value']) . "</option>";
                  }
                  mysqli_free_result($quantities);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

        <!-- MANAGE CURRENCIES -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Currencies</h3>

          <form name="currencyAddForm" action="currencies-exec.php" method="post" onsubmit="return currenciesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add Currency</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <div style="display: flex; gap: 10px; margin-top: 15px;">
            <form name="currencyDeleteForm" action="delete-currency.php" method="post" onsubmit="return currenciesValidate(this)" style="flex: 1;">
              <div class="form-group">
                <label>Remove</label>
                <select name="currency" id="currency" style="width: 100%;">
                  <option value="select">- select -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($currencies)) {
                    echo "<option value=\"" . htmlspecialchars($row['currency_id']) . "\">" . htmlspecialchars($row['currency_symbol']) . "</option>";
                  }
                  mysqli_free_result($currencies);
                  ?>
                </select>
                <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; width: 100%; margin-top: 5px; padding: 5px;" />
              </div>
            </form>

            <form name="currencyActivateForm" action="activate-currency.php" method="post" onsubmit="return currenciesValidate(this)" style="flex: 1;">
              <div class="form-group">
                <label>Activate</label>
                <select name="currency" id="currency_act" style="width: 100%;">
                  <option value="select">- select -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($currencies_1)) {
                    echo "<option value=\"" . htmlspecialchars($row['currency_id']) . "\">" . htmlspecialchars($row['currency_symbol']) . "</option>";
                  }
                  mysqli_free_result($currencies_1);
                  ?>
                </select>
                <input type="submit" name="Update" value="Activate" class="btn-primary" style="background: #28a745; border-color: #28a745; width: 100%; margin-top: 5px; padding: 5px;" />
              </div>
            </form>
          </div>
        </div>

        <!-- MANAGE RATINGS -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Ratings</h3>

          <form name="ratingAddForm" action="ratings-exec.php" method="post" onsubmit="return ratingsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add Rate Level</label>
                <input type="text" name="name" id="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="ratingDeleteForm" action="delete-rating.php" method="post" onsubmit="return ratingsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Level</label>
                <select name="rating" id="rating" style="width: 100%;">
                  <option value="select">- select level -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($ratings)) {
                    echo "<option value=\"" . htmlspecialchars($row['rate_id']) . "\">" . htmlspecialchars($row['rate_name']) . "</option>";
                  }
                  mysqli_free_result($ratings);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

        <!-- MANAGE TIMEZONES -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Timezones</h3>

          <form name="timezoneAddForm" action="timezone-exec.php" method="post" onsubmit="return timezonesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add Timezone</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <div style="display: flex; gap: 10px; margin-top: 15px;">
            <form name="timezoneDeleteForm" action="delete-timezone.php" method="post" onsubmit="return timezonesValidate(this)" style="flex: 1;">
              <div class="form-group">
                <label>Remove</label>
                <select name="timezone" id="timezone" style="width: 100%;">
                  <option value="select">- select -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($timezones)) {
                    echo "<option value=\"" . htmlspecialchars($row['timezone_id']) . "\">" . htmlspecialchars($row['timezone_reference']) . "</option>";
                  }
                  mysqli_free_result($timezones);
                  ?>
                </select>
                <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; width: 100%; margin-top: 5px; padding: 5px;" />
              </div>
            </form>

            <form name="timezoneActivateForm" action="activate-timezone.php" method="post" onsubmit="return timezonesValidate(this)" style="flex: 1;">
              <div class="form-group">
                <label>Activate</label>
                <select name="timezone" id="timezone_act" style="width: 100%;">
                  <option value="select">- select -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($timezones_1)) {
                    echo "<option value=\"" . htmlspecialchars($row['timezone_id']) . "\">" . htmlspecialchars($row['timezone_reference']) . "</option>";
                  }
                  mysqli_free_result($timezones_1);
                  ?>
                </select>
                <input type="submit" name="Update" value="Activate" class="btn-primary" style="background: #28a745; border-color: #28a745; width: 100%; margin-top: 5px; padding: 5px;" />
              </div>
            </form>
          </div>
        </div>

        <!-- MANAGE TABLES -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Tables</h3>

          <form name="tableAddForm" action="tables-exec.php" method="post" onsubmit="return tablesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Table Name/Number</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="tableDeleteForm" action="delete-table.php" method="post" onsubmit="return tablesValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Table</label>
                <select name="table" id="table" style="width: 100%;">
                  <option value="select">- select table -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($tables)) {
                    echo "<option value=\"" . htmlspecialchars($row['table_id']) . "\">" . htmlspecialchars($row['table_name']) . "</option>";
                  }
                  mysqli_free_result($tables);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

        <!-- MANAGE PARTY-HALLS -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Party-Halls</h3>

          <form name="partyhallAddForm" action="partyhalls-exec.php" method="post" onsubmit="return partyhallsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Hall Name/Number</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="partyhallDeleteForm" action="delete-partyhall.php" method="post" onsubmit="return partyhallsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Party-Hall</label>
                <select name="partyhall" id="partyhall" style="width: 100%;">
                  <option value="select">- select partyhall -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($partyhalls)) {
                    echo "<option value=\"" . htmlspecialchars($row['partyhall_id']) . "\">" . htmlspecialchars($row['partyhall_name']) . "</option>";
                  }
                  mysqli_free_result($partyhalls);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

        <!-- MANAGE QUESTIONS -->
        <div class="allocation-card" style="width: 100%; max-width: 430px; margin: 0;">
          <h3>Manage Questions</h3>

          <form name="questionAddForm" action="questions-exec.php" method="post" onsubmit="return questionsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
              <div style="flex: 1;">
                <label>Add Question</label>
                <input type="text" name="name" class="textfield" style="width: 100%; box-sizing: border-box;" />
              </div>
              <input type="submit" name="Insert" value="Add" class="btn-primary" style="padding: 7px 15px;" />
            </div>
          </form>

          <form name="questionDeleteForm" action="delete-question.php" method="post" onsubmit="return questionsValidate(this)">
            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end; margin-top: 15px;">
              <div style="flex: 1;">
                <label>Select Question</label>
                <select name="question" id="question" style="width: 100%;">
                  <option value="select">- select question -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($questions)) {
                    echo "<option value=\"" . htmlspecialchars($row['question_id']) . "\">" . htmlspecialchars($row['question_text']) . "</option>";
                  }
                  mysqli_free_result($questions);
                  ?>
                </select>
              </div>
              <input type="submit" name="Delete" value="Remove" class="btn-primary" style="background: #dc3545; border-color: #dc3545; padding: 7px 15px;" />
            </div>
          </form>
        </div>

      </div>

      <hr />
    </div>

    <div id="footer">
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>

  </div>
</body>
<?php mysqli_close($link); ?>

</html>
