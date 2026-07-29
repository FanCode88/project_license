<?php
//Start session and include configurations
require_once('auth.php');
require_once('connection/config.php');

//Connect to MySQL server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//Get member id from session
$memberId = isset($_SESSION['SESS_MEMBER_ID']) ? $_SESSION['SESS_MEMBER_ID'] : null;

//Retrieve number of items in cart using prepared statements
$num_items = 0;
if ($memberId) {
  $stmt_cart = mysqli_prepare($link, "SELECT COUNT(*) FROM cart_details WHERE member_id = ? AND flag = ?");
  if ($stmt_cart) {
    $flag_0 = 0;
    mysqli_stmt_bind_param($stmt_cart, "ii", $memberId, $flag_0);
    mysqli_stmt_execute($stmt_cart);
    mysqli_stmt_bind_result($stmt_cart, $num_items);
    mysqli_stmt_fetch($stmt_cart);
    mysqli_stmt_close($stmt_cart);
  }
}

//Retrieve all rows from the messages table securely
$messages_result = mysqli_query($link, "SELECT * FROM messages");
if (!$messages_result) {
  die("Something is wrong ... \n" . mysqli_error($link));
}
$num_messages = mysqli_num_rows($messages_result);
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Restaurant: Tables</title>
  <link href="stylesheets/user_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/user.js"></script>
</head>

<body>
  <div id="page">
    <div id="menu">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="foodzone.php">Food Zone</a></li>
        <li><a href="specialdeals.php">Special Deals</a></li>
        <li><a href="member-index.php">My Account</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </div>
    <div id="header">
      <div id="logo"> <a href="index.php" class="blockLink"></a></div>
      <div id="company_name">Albita Restaurant</div>
    </div>
    <div id="center">
      <h1>MESSAGES</h1>
      <div style="border:#bd6f2f solid 1px;padding:4px 6px 2px 6px">
        <a href="member-index.php">Home</a> | <a href="cart.php">Cart[
          <?php echo $num_items; ?>]
        </a> | <a href="inbox.php">Inbox[
          <?php echo $num_messages; ?>]
        </a> | <a href="tables.php">Tables</a> | <a href="partyhalls.php">Party-Halls</a> | <a href="ratings.php">Rate
          Us</a> | <a href="logout.php">Logout</a>
        <p>&nbsp;</p>
        <p>Here you can ... For more information <a href="contactus.php">Click Here</a> to contact us.</p>
        <hr>
        <table width="850" style="text-align:center;">
          <CAPTION>
            <h2>INBOX</h2>
          </CAPTION>
          <tr>
            <th>From</th>
            <th>Date Received</th>
            <th>Time Received</th>
            <th>Subject</th>
            <th>Text</th>
          </tr>

          <?php
          //Loop through all table rows safely using htmlspecialchars to prevent XSS
          while ($row = mysqli_fetch_assoc($messages_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['message_from']) . "</td>";
            echo "<td>" . htmlspecialchars($row['message_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['message_time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['message_subject']) . "</td>";
            echo "<td width='350' align='left'>" . htmlspecialchars($row['message_text']) . "</td>";
            echo "</tr>";
          }
          mysqli_free_result($messages_result);
          mysqli_close($link);
          ?>
        </table>
      </div>
    </div>
    <div id="footer">
      <div class="bottom_menu"><a href="index.php">Home Page</a> | <a href="aboutus.php">About Us</a> | <a
          href="specialdeals.php">Special Deals</a> | <a href="foodzone.php">Food Zone</a> | <a href="#">Affiliate
          Program</a> |<br>
        | <a href="admin/index.php" target="_blank">Administrator</a> |</div>

      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
