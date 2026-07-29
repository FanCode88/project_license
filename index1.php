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

//retrieve questions from the questions table
$questions = mysql_query("SELECT * FROM questions")
  or die("Something is wrong ... \n" . mysql_error());
?>
<?php
//setting-up a remember me cookie
if (isset($_POST['Submit'])) {
  //setting up a remember me cookie
  if ($_POST['remember']) {
    $year = time() + 31536000;
    setcookie('remember_me', $_POST['login'], $year);
  } else if (!$_POST['remember']) {
    if (isset($_COOKIE['remember_me'])) {
      $past = time() - 100;
      setcookie(remember_me, gone, $past);
    }
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Albita Restaurant:Home</title>

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


  <link href="stylesheets/styles.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" src="validation/user.js">
  </script>
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
    <div id="header" class="stretchX">
      <div id="logo"> <a href="index.php" class="blockLink"></a></div>
      <div id="company_name">Albita Restaurant</div>
    </div>
    <div id="center">
      <h1>
        <center>Welcome To Albita Restaurant Online Ordering System!</center>
      </h1>
      <div class="body_text">
        Order your food today from the Albita and it will be delivered at your door step. Jump in to our weekly special deals in the Special Deals menu. Register an account with us to enjoy fast ordering, delivery, and convenient payment of your food. Start now by logging in below or registering if you don't have an account with us:
      </div>
      <table align="center" width="100%">
        <tr align="center">
          <td style="text-align:center;">
            <div style="border:#bd6f2f solid 1px;padding:4px 6px 2px 6px">
              <form id="loginForm" name="loginForm" method="post" action="login-exec.php" onsubmit="return loginValidate(this)">
                <table width="290" border="0" align="center" cellpadding="2" cellspacing="0">
                  <tr>
                    <td colspan="2" style="text-align:center;">
                      <font color="#FF0000">* </font>Required fields
                    </td>
                  </tr>
                  <tr>
                    <td width="112"><b>Email</b></td>
                    <td width="188">
                      <font color="#FF0000">* </font><input name="login" type="text" class="textfield" id="login" />
                    </td>
                  </tr>
                  <tr>
                    <td><b>Password</b></td>
                    <td>
                      <font color="#FF0000">* </font><input name="password" type="password" class="textfield" id="password" />
                    </td>
                  </tr>
                  <tr>
                    <td><input name="remember" type="checkbox" class="" id="remember" value="1" onselect="cookie()" <?php if (isset($_COOKIE['remember_me'])) {
                                                                                                                      echo 'checked="checked"';
                                                                                                                    } else {
                                                                                                                      echo '';
                                                                                                                    }
                                                                                                                    ?> />Remember me</td>
                    <td><a href="JavaScript: resetPassword()">Forgot password?</a></td>
                  </tr>
                  <tr>
                    <td colspan="2"><input type="reset" value="Clear Fields" />
                      <input type="submit" name="Submit" value="Login" />
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </table>
              </form>
            </div>
          </td>
          <hr>
          <td style="text-align:center;">
            <div style="border:#bd6f2f solid 1px;padding:4px 6px 2px 6px;">
              <form id="loginForm" name="loginForm" method="post" action="register-exec.php" onsubmit="return registerValidate(this)">
                <table width="450" border="0" align="center" cellpadding="2" cellspacing="0">
                  <tr>
                    <td colspan="2" style="text-align:center;">
                      <font color="#FF0000">* </font>Required fields
                    </td>
                  </tr>
                  <tr>
                    <th>First Name </th>
                    <td>
                      <font color="#FF0000">* </font><input name="fname" type="text" class="textfield" id="fname" />
                    </td>
                  </tr>
                  <tr>
                    <th>Last Name </th>
                    <td>
                      <font color="#FF0000">* </font><input name="lname" type="text" class="textfield" id="lname" />
                    </td>
                  </tr>
                  <tr>
                    <th width="124">Email</th>
                    <td width="168">
                      <font color="#FF0000">* </font><input name="login" type="text" class="textfield" id="login" />
                    </td>
                  </tr>
                  <tr>
                    <th>Password</th>
                    <td>
                      <font color="#FF0000">* </font><input name="password" type="password" class="textfield" id="password" />
                    </td>
                  </tr>
                  <tr>
                    <th>Confirm Password </th>
                    <td>
                      <font color="#FF0000">* </font><input name="cpassword" type="password" class="textfield" id="cpassword" />
                    </td>
                  </tr>
                  <tr>
                    <th>Security Question </th>
                    <td>
                      <font color="#FF0000">* </font><select name="question" id="question">
                        <option value="select">- select question -
                          <?php
                          //loop through quantities table rows
                          while ($row = mysql_fetch_array($questions)) {
                            echo "<option value=$row[question_id]>$row[question_text]";
                          }
                          ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>Security Answer</th>
                    <td>
                      <font color="#FF0000">* </font><input name="answer" type="text" class="textfield" id="answer" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2"><input type="reset" value="Clear Fields" />
                      <input type="submit" name="Submit" value="Register" />
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </table>
              </form>
            </div>
          </td>
        </tr>
      </table>
      <hr>
    </div>
    <div id="footer">
      <div class="bottom_menu"><a href="index.php">Home Page</a> | <a href="aboutus.php">About Us</a> | <a href="specialdeals.php">Special Deals</a> | <a href="foodzone.php">Food Zone</a> | <a href="#">Affiliate Program</a> |<br>
        | <a href="admin/index.php" target="_blank">Administrator</a> |</div>

      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
