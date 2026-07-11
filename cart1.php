<?php
require_once('qrlib.php');
?>
<?php
require_once('auth.php');
?>
<?php
//checking connection and connecting to a database
require_once('connection/config.php');
//Connect to mysql server
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
  die('Failed to connect to server: ' . mysql_error());
}

//Select database
$db = mysql_select_db(DB_DATABASE);
if (!$db) {
  die("Unable to select database");
}

//define default values for flag_0
$flag_0 = 0;

//get member_id from session
$member_id = $_SESSION['SESS_MEMBER_ID'];

//selecting particular records from the food_details and cart_details tables. Return an error if there are no records in the tables
$result = mysql_query("SELECT food_name,food_description,food_price,food_photo,cart_id,quantity_value,total,flag,category_name FROM food_details,cart_details,categories,quantities WHERE cart_details.member_id='$member_id' AND cart_details.flag='$flag_0' AND cart_details.food_id=food_details.food_id AND food_details.food_category=categories.category_id AND cart_details.quantity_id=quantities.quantity_id")
  or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");
?>
<?php
if (isset($_POST['Submit'])) {
  //Function to sanitize values received from the form. Prevents SQL injection
  function clean($str)
  {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
      $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
  }
  //get category id
  $id = clean($_POST['category']);

  //selecting all records from the food_details table based on category id. Return an error if there are no records in the table
  $result = mysql_query("SELECT * FROM food_details WHERE food_category='$id'")
    or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");
}
?>
<?php
//retrieving quantities from the quantities table
$quantities = mysql_query("SELECT * FROM quantities")
  or die("Something is wrong ... \n" . mysql_error());
?>
<?php
//retrieving cart ids from the cart_details table
//define a default value for flag_0
$flag_0 = 0;
$items = mysql_query("SELECT * FROM cart_details WHERE member_id='$member_id' AND flag='$flag_0'")
  or die("Something is wrong ... \n" . mysql_error());
?>
<?php
//retrive a currency from the currencies table
//define a default value for flag_1
$flag_1 = 1;
$currencies = mysql_query("SELECT * FROM currencies WHERE flag='$flag_1'")
  or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>My Account </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,600,600i,700,700i|Satisfy|Comic+Neue:300,300i,400,400i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Delicious - v4.1.0
  * Template URL: https://bootstrapmade.com/delicious-free-restaurant-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Top Bar ======= -->
  <section id="topbar" class="d-flex align-items-center fixed-top ">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-center justify-content-lg-start">
      <i class="bi bi-phone d-flex align-items-center"><span>+0738740300</span></i>
      <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
    </div>
  </section>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center ">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <div class="logo me-auto">
        <h1><a href="index.html">Deluxe restaurant</a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
      </div>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="index.php#login">Login</a></li>
          <li><a class="nav-link scrollto" href="index.php#menu">Our Food</a></li>
          <li><a class="nav-link scrollto" href="index.php">Specials</a></li>
          <li><a class="nav-link scrollto" href="cont.php">Account</a></li>
          <li><a class="nav-link scrollto" href="index.php#chefs">Chefs</a></li>
          <li><a class="nav-link scrollto" href="index.php#gallery">Gallery</a></li>
          <li class="dropdown"><a href="#"><span>Drop Down</span> <i class="bi bi-chevron-down"></i></a>

          </li>
          <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

      <a href="#book-a-table" class="book-a-table-btn scrollto">Book a table</a>

    </div>
  </header><!-- End Header -->

  <main id="main">

    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
      <div class="container">



      </div>
    </section><!-- End Breadcrumbs Section -->

    <section class="inner-page">
      <div class="container">



        <div id="center">
          <h1>MY SHOPPING CART</h1>
          <hr>
          <h3><a href="foodzone.php">Continue Shopping!</a></h3>
          <form name="quantityForm" id="quantityForm" method="post" action="update-quantity.php" onsubmit="return updateQuantity(this)">
            <table width="560" align="center">
              <tr>
                <td>Item ID</td>
                <td><select name="item" id="item">
                    <option value="select">- select -
                      <?php
                      //loop through cart_details table rows
                      while ($row = mysql_fetch_array($items)) {
                        echo "<option value=$row[cart_id]>$row[cart_id]";
                      }
                      ?>
                  </select>
                </td>
                <td>Quantity</td>
                <td><select name="quantity" id="quantity">
                    <option value="select">- select -
                      <?php
                      //loop through quantities table rows
                      while ($row = mysql_fetch_assoc($quantities)) {
                        echo "<option value=$row[quantity_id]>$row[quantity_value]";
                      }
                      ?>
                  </select>
                </td>
                <td><input type="submit" name="Submit" value="Change Quantity" /></td>
              </tr>
            </table>
          </form>
          <div style="border:#bd6f2f solid 1px;padding:4px 6px 2px 6px">
            <table width="650" height="auto" style="text-align:center;">
              <tr>
                <th>Item ID</th>
                <th>Food Photo</th>
                <th>Food Name</th>
                <th>Food Description</th>
                <th>Food Category</th>
                <th>Food Price</th>
                <th>Quantity</th>
                <th>Total Cost</th>
                <th>Action(s)</th>
              </tr>

              <?php
              //loop through all table rows
              $symbol = mysql_fetch_assoc($currencies); //gets active currency
              while ($row = mysql_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['cart_id'] . "</td>";
                echo '<td><a href=images/' . $row['food_photo'] . ' alt="click to view full image" target="_blank"><img src=images/' . $row['food_photo'] . ' width="80" height="70"></a></td>';
                echo "<td>" . $row['food_name'] . "</td>";
                echo "<td>" . $row['food_description'] . "</td>";
                echo "<td>" . $row['category_name'] . "</td>";
                echo "<td>" . $symbol['currency_symbol'] . "" . $row['food_price'] . "</td>";
                echo "<td>" . $row['quantity_value'] . "</td>";
                echo "<td>" . $symbol['currency_symbol'] . "" . $row['total'] . "</td>";
                /*
                    echo "<form>";
                    echo '<td><select name="quantity" id="quantity" onchange="getQuantity(this.value)">
                    <option value="select">- select quantity -
                    <?php
                    while ($row=mysql_fetch_assoc($quantities)){
                    echo "<option value=$row[quantity_id]>$row[quantity_value]";
                    //$_SESSION[SESS_CART_ID] = $row[cart_id];
                }
                ?>
                </select></td>';
                echo "</form>";
                */
                /*
                echo "<form>";
                    echo "<td><select name='quantity' id='quantity' onclick='getQuantity(this.value)'>
                    <option value='1'>select
                    <option value='2'>1
                    <option value='3'>2
                    <option value='4'>3



                </select></td>";
                echo "</form>";
                */
                echo '<td><a href="order-exec.php?id=' . $row['cart_id'] . '">Place Order</a></td>';
                echo "</tr>";
              }
              mysql_free_result($result);
              mysql_close($link);
              ?>
            </table>
          </div>
        </div>
        <div id="footer">
          <div class="bottom_menu"><a href="index.php">Home Page</a> |
            | <a href="admin/index.php" target="_blank">Administrator</a> |</div>

          <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
        </div>
      </div>


</body>

</html>
