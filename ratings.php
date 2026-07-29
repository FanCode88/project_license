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

//Selecting all records from the food_details table
$foods_result = mysqli_query($link, "SELECT * FROM food_details");
if (!$foods_result) {
  die("A problem has occured ... \nOur team is working on it at the moment ... \nPlease check back after few hours.");
}

//Selecting all records from the ratings table
$ratings_result = mysqli_query($link, "SELECT * FROM ratings");
if (!$ratings_result) {
  die("A problem has occured ... \nOur team is working on it at the moment ... \nPlease check back after few hours.");
}

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

//Retrieve number of messages
$messages_result = mysqli_query($link, "SELECT * FROM messages");
$num_messages = $messages_result ? mysqli_num_rows($messages_result) : 0;
if ($messages_result) {
  mysqli_free_result($messages_result);
}
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Food Plaza: Rating</title>
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
      <div id="company_name">Food Plaza Restaurant</div>
    </div>
    <div id="center">
      <h1>RATE US</h1>
      <div style="border:#bd6f2f solid 1px;padding:4px 6px 2px 6px">
        <a href="member-index.php">Home</a> | <a href="cart.php">Cart[<?php echo $num_items; ?>]</a> | <a
          href="inbox.php">Inbox[<?php echo $num_messages; ?>]</a> | <a href="tables.php">Tables</a> | <a
          href="partyhalls.php">Party-Halls</a> | <a href="ratings.php">Rate Us</a> | <a href="logout.php">Logout</a>
        <p>&nbsp;</p>
        <p>Here you can ... For more information <a href="contactus.php">Click Here</a> to contact us.</p>
        <hr>
        <form name="ratingForm" id="ratingForm" method="post"
          action="ratings-exec.php?id=<?php echo htmlspecialchars($memberId); ?>" onsubmit="return ratingValidate(this)"
          style="text-align:center;">
          <table align="center" width="300">
            <CAPTION>
              <h2>RATE OUR FOODS</h2>
            </CAPTION>
            <tr>
              <td>Food</td>
              <td><select name="food" id="food">
                  <option value="select">- select food -</option>
                  <?php
                  //Loop through food_details table rows safely
                  while ($row = mysqli_fetch_assoc($foods_result)) {
                    echo "<option value=\"" . htmlspecialchars($row['food_id']) . "\">" . htmlspecialchars($row['food_name']) . "</option>";
                  }
                  mysqli_free_result($foods_result);
                  ?>
                </select></td>
            </tr>
            <tr>
              <td>Scale</td>
              <td><select name="scale" id="scale">
                  <option value="select">- select scale -</option>
                  <?php
                  //Loop through ratings table rows safely
                  while ($row = mysqli_fetch_assoc($ratings_result)) {
                    echo "<option value=\"" . htmlspecialchars($row['rate_id']) . "\">" . htmlspecialchars($row['rate_name']) . "</option>";
                  }
                  mysqli_free_result($ratings_result);
                  mysqli_close($link);
                  ?>
                </select></td>
            </tr>
            <tr>
              <td colspan="2"><input type="submit" name="Submit" value="Rate" /></td>
            </tr>
          </table>
        </form>
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
