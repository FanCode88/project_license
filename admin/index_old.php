<?php
require_once('auth.php');
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//define default value for flag
$flag_1 = 1;

//defining global variables
$total_result = null;
$excellent_result = null;
$good_result = null;
$average_result = null;
$bad_result = null;
$worse_result = null;
$no_rate_result = null;

//count the number of records in the members, orders_details, and reservations_details tables using MySQLi
$members = mysqli_query($link, "SELECT * FROM members")
  or die("There are no records to count ... \n" . mysqli_error($link));

$orders_placed = mysqli_query($link, "SELECT * FROM orders_details")
  or die("There are no records to count ... \n" . mysqli_error($link));

$orders_processed = mysqli_query($link, "SELECT * FROM orders_details WHERE flag='$flag_1'")
  or die("There are no records to count ... \n" . mysqli_error($link));

$tables_reserved = mysqli_query($link, "SELECT * FROM reservations_details WHERE table_flag='$flag_1'")
  or die("There are no records to count ... \n" . mysqli_error($link));

$partyhalls_reserved = mysqli_query($link, "SELECT * FROM reservations_details WHERE partyhall_flag='$flag_1'")
  or die("There are no records to count ... \n" . mysqli_error($link));

$tables_allocated = mysqli_query($link, "SELECT * FROM reservations_details WHERE flag='$flag_1' AND table_flag='$flag_1'")
  or die("There are no records to count ... \n" . mysqli_error($link));

$partyhalls_allocated = mysqli_query($link, "SELECT * FROM reservations_details WHERE flag='$flag_1' AND partyhall_flag='$flag_1'")
  or die("There are no records to count ... \n" . mysqli_error($link));

//get food names and ids from food_details table
$foods = mysqli_query($link, "SELECT * FROM food_details")
  or die("Something is wrong ... \n" . mysqli_error($link));
?>
<?php
if (isset($_POST['Submit'])) {
  //Function to sanitize values received from the form. Prevents SQL injection
  function clean($link, $str)
  {
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
  }
  //get category id
  $id = clean($link, $_POST['food']);

  //get ratings ids using MySQLi
  $ratings = mysqli_query($link, "SELECT * FROM ratings")
    or die("Something is wrong ... \n" . mysqli_error($link));

  $row_1 = mysqli_fetch_array($ratings);
  $row_2 = mysqli_fetch_array($ratings);
  $row_3 = mysqli_fetch_array($ratings);
  $row_4 = mysqli_fetch_array($ratings);
  $row_5 = mysqli_fetch_array($ratings);

  $excellent = $row_1 ? $row_1['rate_id'] : 0;
  $good = $row_2 ? $row_2['rate_id'] : 0;
  $average = $row_3 ? $row_3['rate_id'] : 0;
  $bad = $row_4 ? $row_4['rate_id'] : 0;
  $worse = $row_5 ? $row_5['rate_id'] : 0;
  mysqli_free_result($ratings);

  //selecting all records using prepared statements to prevent SQL injection
  $stmt_total = mysqli_prepare($link, "SELECT fd.food_name FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ?");
  mysqli_stmt_bind_param($stmt_total, "i", $id);
  mysqli_stmt_execute($stmt_total);
  $total_result = mysqli_stmt_get_result($stmt_total);
  mysqli_stmt_close($stmt_total);

  $stmt_exc = mysqli_prepare($link, "SELECT * FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ? AND pd.rate_id = ?");
  mysqli_stmt_bind_param($stmt_exc, "ii", $id, $excellent);
  mysqli_stmt_execute($stmt_exc);
  $excellent_result = mysqli_stmt_get_result($stmt_exc);
  mysqli_stmt_close($stmt_exc);

  $stmt_good = mysqli_prepare($link, "SELECT * FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ? AND pd.rate_id = ?");
  mysqli_stmt_bind_param($stmt_good, "ii", $id, $good);
  mysqli_stmt_execute($stmt_good);
  $good_result = mysqli_stmt_get_result($stmt_good);
  mysqli_stmt_close($stmt_good);

  $stmt_avg = mysqli_prepare($link, "SELECT * FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ? AND pd.rate_id = ?");
  mysqli_stmt_bind_param($stmt_avg, "ii", $id, $average);
  mysqli_stmt_execute($stmt_avg);
  $average_result = mysqli_stmt_get_result($stmt_avg);
  mysqli_stmt_close($stmt_avg);

  $stmt_bad = mysqli_prepare($link, "SELECT * FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ? AND pd.rate_id = ?");
  mysqli_stmt_bind_param($stmt_bad, "ii", $id, $bad);
  mysqli_stmt_execute($stmt_bad);
  $bad_result = mysqli_stmt_get_result($stmt_bad);
  mysqli_stmt_close($stmt_bad);

  $stmt_worse = mysqli_prepare($link, "SELECT * FROM food_details fd JOIN polls_details pd ON fd.food_id = pd.food_id WHERE pd.food_id = ? AND pd.rate_id = ?");
  mysqli_stmt_bind_param($stmt_worse, "ii", $id, $worse);
  mysqli_stmt_execute($stmt_worse);
  $worse_result = mysqli_stmt_get_result($stmt_worse);
  mysqli_stmt_close($stmt_worse);

  $stmt_norate = mysqli_prepare($link, "SELECT * FROM food_details WHERE food_id = ?");
  mysqli_stmt_bind_param($stmt_norate, "i", $id);
  mysqli_stmt_execute($stmt_norate);
  $no_rate_result = mysqli_stmt_get_result($stmt_norate);
  mysqli_stmt_close($stmt_norate);
}
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Admin Index</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js">
  </script>
</head>

<body>
  <div id="page">
    <div id="header">
      <h1>Administrator Control Panel</h1>
      <a href="profile.php">Profile</a> | <a href="categories.php">Categories</a> | <a href="foods.php">Foods</a> | <a
        href="accounts.php">Accounts</a> | <a href="orders.php">Orders</a> | <a href="reservations.php">Reservations</a>
      | <a href="specials.php">Specials</a> | <a href="allocation.php">Staff</a> | <a href="messages.php">Messages</a> |
      <a href="options.php">Options</a> | <a href="logout.php">Logout</a>
    </div>
    <div id="container">
      <table width="1000" align="center" style="text-align:center">
        <caption>
          <h3>CURRENT STATUS</h3>
        </caption>
        <tr>
          <th>Members Registered</th>
          <th>Orders Placed</th>
          <th>Orders Processed</th>
          <th>Orders Pending</th>
          <th>Table(s) Reserved</th>
          <th>Table(s) Allocated</th>
          <th>Table(s) Pending</th>
          <th>PartyHall(s) Reserved</th>
          <th>PartyHall(s) Allocated</th>
          <th>PartyHall(s) Pending</th>
        </tr>

        <?php
        $result1 = mysqli_num_rows($members);
        $result2 = mysqli_num_rows($orders_placed);
        $result3 = mysqli_num_rows($orders_processed);
        $result4 = $result2 - $result3; //gets pending order(s)
        $result5 = mysqli_num_rows($tables_reserved);
        $result6 = mysqli_num_rows($tables_allocated);
        $result7 = $result5 - $result6; //gets pending table(s)
        $result8 = mysqli_num_rows($partyhalls_reserved);
        $result9 = mysqli_num_rows($partyhalls_allocated);
        $result10 = $result8 - $result9; //gets pending partyhall(s)
        
        mysqli_free_result($members);
        mysqli_free_result($orders_placed);
        mysqli_free_result($orders_processed);
        mysqli_free_result($tables_reserved);
        mysqli_free_result($tables_allocated);
        mysqli_free_result($partyhalls_reserved);
        mysqli_free_result($partyhalls_allocated);

        echo "<tr>";
        echo "<td>" . $result1 . "</td>";
        echo "<td>" . $result2 . "</td>";
        echo "<td>" . $result3 . "</td>";
        echo "<td>" . $result4 . "</td>";
        echo "<td>" . $result5 . "</td>";
        echo "<td>" . $result6 . "</td>";
        echo "<td>" . $result7 . "</td>";
        echo "<td>" . $result8 . "</td>";
        echo "<td>" . $result9 . "</td>";
        echo "<td>" . $result10 . "</td>";
        echo "</tr>";
        ?>
      </table>
      <hr>
      <form name="foodStatusForm" id="foodStatusForm" method="post" action="index.php"
        onsubmit="return statusValidate(this)">
        <table width="360" align="center">
          <caption>
            <h3>CUSTOMERS' RATINGS (100%)</h3>
          </caption>
          <tr>
            <td>Food</td>
            <td width="168"><select name="food" id="food">
                <option value="select">- select food -</option>
                <?php
                //loop through food_details table rows
                while ($row = mysqli_fetch_array($foods)) {
                  echo "<option value=\"" . $row['food_id'] . "\">" . htmlspecialchars($row['food_name']) . "</option>";
                }
                mysqli_free_result($foods);
                ?>
              </select></td>
            <td><input type="submit" name="Submit" value="Show Ratings" /></td>
          </tr>
        </table>
      </form>
      <table width="900" align="center">
        <tr>
          <th></th>
          <th>Excellent</th>
          <th>Good</th>
          <th>Average</th>
          <th>Bad</th>
          <th>Worse</th>
        </tr>

        <?php
        if (isset($_POST['Submit'])) {
          //actual values
          $excellent_value = mysqli_num_rows($excellent_result);
          $good_value = mysqli_num_rows($good_result);
          $average_value = mysqli_num_rows($average_result);
          $bad_value = mysqli_num_rows($bad_result);
          $worse_value = mysqli_num_rows($worse_result);

          //percentile rates
          $total_value = mysqli_num_rows($total_result);
          if ($total_value != 0) {
            $excellent_rate = round(($excellent_value / $total_value) * 100, 2);
            $good_rate = round(($good_value / $total_value) * 100, 2);
            $average_rate = round(($average_value / $total_value) * 100, 2);
            $bad_rate = round(($bad_value / $total_value) * 100, 2);
            $worse_rate = round(($worse_value / $total_value) * 100, 2);
          } else {
            $excellent_rate = 0;
            $good_rate = 0;
            $average_rate = 0;
            $bad_rate = 0;
            $worse_rate = 0;
          }

          //get food name
          if ($total_value > 0) {
            $row = mysqli_fetch_array($total_result);
            $food_name = $row['food_name'];
          } else {
            $row = mysqli_fetch_array($no_rate_result);
            $food_name = $row ? $row['food_name'] : 'Unknown Food';
          }

          if ($total_result)
            mysqli_free_result($total_result);
          if ($excellent_result)
            mysqli_free_result($excellent_result);
          if ($good_result)
            mysqli_free_result($good_result);
          if ($average_result)
            mysqli_free_result($average_result);
          if ($bad_result)
            mysqli_free_result($bad_result);
          if ($worse_result)
            mysqli_free_result($worse_result);
          if ($no_rate_result)
            mysqli_free_result($no_rate_result);

          echo "<tr>";
          echo "<th>" . htmlspecialchars($food_name) . "</th>";
          echo "<td>" . $excellent_value . "(" . $excellent_rate . "%)" . "</td>";
          echo "<td>" . $good_value . "(" . $good_rate . "%)" . "</td>";
          echo "<td>" . $average_value . "(" . $average_rate . "%)" . "</td>";
          echo "<td>" . $bad_value . "(" . $bad_rate . "%)" . "</td>";
          echo "<td>" . $worse_value . "(" . $worse_rate . "%)" . "</td>";
          echo "</tr>";
        }
        mysqli_close($link);
        ?>
      </table>
      <hr>
    </div>
    <div id="footer">
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
