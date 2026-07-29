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

// selecting all records from the reservations_details table based on table ids
$query_tables = "SELECT members.firstname, members.lastname, reservations_details.ReservationID, reservations_details.table_id, reservations_details.Reserve_Date, reservations_details.Reserve_Time, tables.table_id, tables.table_name
                 FROM members, reservations_details, tables
                 WHERE members.member_id = reservations_details.member_id AND tables.table_id=reservations_details.table_id";
$tables = mysqli_query($link, $query_tables) or die("There are no records to display ... \n" . mysqli_error($link));

// selecting all records from the reservations_details table based on partyhall ids
$query_halls = "SELECT members.firstname, members.lastname, reservations_details.ReservationID, reservations_details.partyhall_id, reservations_details.Reserve_Date, reservations_details.Reserve_Time, partyhalls.partyhall_id, partyhalls.partyhall_name
                FROM members, reservations_details, partyhalls
                WHERE members.member_id = reservations_details.member_id AND partyhalls.partyhall_id=reservations_details.partyhall_id";
$partyhalls = mysqli_query($link, $query_halls) or die("There are no records to display ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Reservations</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Reservations Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php" class="active">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">

      <!-- Primul Tabel: Mese Rezervate -->
      <table class="modern-table">
        <caption>
          <h3>Tables Reserved</h3>
        </caption>
        <tr>
          <th>Reservation ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Table Name</th>
          <th>Reserved Date</th>
          <th>Reserved Time</th>
          <th>Action(s)</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_array($tables)) {
          echo "<tr>";
          echo "<td><span class=\"font-bold\">" . htmlspecialchars($row['ReservationID']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
          echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
          echo "<td class=\"font-bold\">" . htmlspecialchars($row['table_name']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Reserve_Date']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Reserve_Time']) . "</td>";
          echo '<td><a href="delete-reservation.php?id=' . $row['ReservationID'] . '" class="btn-remove">Delete Reservation</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($tables);
        ?>
      </table>

      <hr />

      <!-- Al doilea Tabel: Săli Rezervate -->
      <table class="modern-table">
        <caption>
          <h3>Party-Halls Reserved</h3>
        </caption>
        <tr>
          <th>Reservation ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>PartyHall Name</th>
          <th>Reserved Date</th>
          <th>Reserved Time</th>
          <th>Action(s)</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_array($partyhalls)) {
          echo "<tr>";
          echo "<td><span class=\"font-bold\">" . htmlspecialchars($row['ReservationID']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
          echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
          echo "<td class=\"font-bold\">" . htmlspecialchars($row['partyhall_name']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Reserve_Date']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Reserve_Time']) . "</td>";
          echo '<td><a href="delete-reservation.php?id=' . $row['ReservationID'] . '" class="btn-remove">Delete Reservation</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($partyhalls);
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
