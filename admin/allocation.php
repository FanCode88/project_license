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

// selecting all records from the staff table
$query_staff = "SELECT * FROM staff";
$staff = mysqli_query($link, $query_staff) or die("There are no records to display ... \n" . mysqli_error($link));

// get order ids from the orders_details table based on flag=0
$flag_0 = 0;
$query_orders = "SELECT * FROM orders_details WHERE flag='$flag_0'";
$orders = mysqli_query($link, $query_orders) or die("There are no records to display ... \n" . mysqli_error($link));

// get reservation ids from the reservations_details table based on flag=0
$query_reservations = "SELECT * FROM reservations_details WHERE flag='$flag_0'";
$reservations = mysqli_query($link, $query_reservations) or die("There are no records to display ... \n" . mysqli_error($link));

// selecting records for form dropdowns (reusable results)
$staff_1 = mysqli_query($link, $query_staff) or die("There are no records to display ... \n" . mysqli_error($link));
$staff_2 = mysqli_query($link, $query_staff) or die("There are no records to display ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Staff Allocation</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Staff Allocation</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php" class="active">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">

      <!-- Tabelul cu lista de membri ai staff-ului -->
      <table class="modern-table">
        <caption>
          <h3>Staff List</h3>
        </caption>
        <tr>
          <th>Staff ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Street Address</th>
          <th>Action(s)</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($staff)) {
          echo "<tr>";
          echo "<td><span class=\"font-bold\">" . htmlspecialchars($row['StaffID']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
          echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
          echo "<td class=\"desc-cell\">" . htmlspecialchars($row['Street_Address']) . "</td>";
          echo '<td><a href="delete-staff.php?id=' . $row['StaffID'] . '" class="btn-remove">Remove Staff</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($staff);
        ?>
      </table>

      <hr />

      <!-- Zona formularelor de alocare redesenată modern -->
      <div class="forms-wrapper">

        <!-- Formular Alocare Comenzi -->
        <div class="allocation-card">
          <h3>Orders Allocation</h3>
          <p class="required-info"><span class="required-star">*</span> Required fields</p>

          <form id="ordersAllocationForm" name="ordersAllocationForm" method="post" action="orders-allocation.php" onsubmit="return ordersAllocationValidate(this)">
            <div class="form-group">
              <label for="orderid"><span class="required-star">*</span> Order ID</label>
              <select name="orderid" id="orderid">
                <option value="select">- select one option -</option>
                <?php
                while ($row = mysqli_fetch_assoc($orders)) {
                  echo "<option value=\"" . htmlspecialchars($row['order_id']) . "\">" . htmlspecialchars($row['order_id']) . "</option>";
                }
                mysqli_free_result($orders);
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="staffid"><span class="required-star">*</span> Staff ID</label>
              <select name="staffid" id="staffid">
                <option value="select">- select one option -</option>
                <?php
                while ($row = mysqli_fetch_assoc($staff_1)) {
                  echo "<option value=\"" . htmlspecialchars($row['StaffID']) . "\">" . htmlspecialchars($row['StaffID']) . "</option>";
                }
                mysqli_free_result($staff_1);
                ?>
              </select>
            </div>

            <div class="form-action">
              <input type="submit" name="Submit" value="Allocate Staff" class="btn-primary" />
            </div>
          </form>
        </div>

        <!-- Formular Alocare Rezervări -->
        <div class="allocation-card">
          <h3>Reservations Allocation</h3>
          <p class="required-info"><span class="required-star">*</span> Required fields</p>

          <form id="reservationsAllocationForm" name="reservationsAllocationForm" method="post" action="reservations-allocation.php" onsubmit="return reservationsAllocationValidate(this)">
            <div class="form-group">
              <label for="reservationid"><span class="required-star">*</span> Reservation ID</label>
              <select name="reservationid" id="reservationid">
                <option value="select">- select one option -</option>
                <?php
                while ($row = mysqli_fetch_assoc($reservations)) {
                  echo "<option value=\"" . htmlspecialchars($row['ReservationID']) . "\">" . htmlspecialchars($row['ReservationID']) . "</option>";
                }
                mysqli_free_result($reservations);
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="staffid_res"><span class="required-star">*</span> Staff ID</label>
              <select name="staffid" id="staffid_res">
                <option value="select">- select one option -</option>
                <?php
                while ($row = mysqli_fetch_assoc($staff_2)) {
                  echo "<option value=\"" . htmlspecialchars($row['StaffID']) . "\">" . htmlspecialchars($row['StaffID']) . "</option>";
                }
                mysqli_free_result($staff_2);
                ?>
              </select>
            </div>

            <div class="form-action">
              <input type="submit" name="Submit" value="Allocate Staff" class="btn-primary" />
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
