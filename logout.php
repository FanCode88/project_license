<?php
// Start sesiune
session_start();

// Distrugem sesiunea complet
$_SESSION = array();
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}
session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Pizza-Inn: Logged Out</title>
  <link href="stylesheets/user_styles.css" rel="stylesheet" type="text/css" />
  <style>
    /* Stil modern pentru chenar */
    .modern-box {
      border: 1px solid #e0e0e0 !important;
      border-top: 4px solid #bd6f2f !important;
      /* Accent portocaliu sus */
      border-radius: 10px;
      padding: 30px !important;
      margin: 20px 0;
      background: #ffffff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .modern-box .error {
      font-size: 1.2em;
      color: #333;
      margin-bottom: 15px;
    }

    .modern-box a {
      color: #bd6f2f;
      text-decoration: none;
      font-weight: bold;
    }

    .modern-box a:hover {
      text-decoration: underline;
    }
  </style>
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
      <h1>Logged Out</h1>
      <!-- Chenar modernizat -->
      <div class="modern-box">
        <div class="error">You have been logged out.</div>
        <p>Sesiunea ta a fost închisă cu succes.</p>
        <p><a href="login-register.php">Click Here to Login again</a></p>
      </div>
    </div>

    <div id="footer">
      <div class="bottom_menu">
        <a href="index.php">Home Page</a> | <a href="aboutus.php">About Us</a> |
        <a href="specialdeals.php">Special Deals</a> | <a href="foodzone.php">Food Zone</a> |
        <a href="#">Affiliate Program</a> |<br>
        | <a href="admin/index.php" target="_blank">Administrator</a> |
      </div>
      <div class="bottom_addr">&copy; <?php echo date("Y"); ?> Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
