<?php
// Conectare la baza de date
require_once('connection/config.php');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
  die('Failed to connect to server: ' . mysql_error());
}

$db = mysql_select_db(DB_DATABASE);
if (!$db) {
  die("Unable to select database");
}

$questions = mysql_query("SELECT * FROM questions") or die("Error: " . mysql_error());

// Logica cookie "Remember Me"
if (isset($_POST['Submit'])) {
  if (isset($_POST['remember'])) {
    setcookie('remember_me', $_POST['login'], time() + 31536000);
  } else {
    setcookie('remember_me', 'gone', time() - 100);
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Food Plaza: Login</title>
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
      <div id="logo"><a href="index.php" class="blockLink"></a></div>
      <div id="company_name">Food Plaza Restaurant</div>
    </div>

    <div id="center">
      <h1>Login/Register</h1>
      <table align="center" width="700" border="0" cellpadding="10">
        <tr valign="top">
          <!-- Login Box -->
          <td width="300" style="text-align:center;">
            <div style="border:#bd6f2f solid 1px; padding:15px; border-radius:5px;">
              <h3 style="margin-top:0;">Login</h3>
              <form name="loginForm" method="post" action="login-exec.php" onsubmit="return loginValidate(this)">
                <table border="0" align="center" cellpadding="5">
                  <tr>
                    <td><b>Email</b></td>
                    <td><input name="login" type="text" style="width:130px;" /></td>
                  </tr>
                  <tr>
                    <td><b>Password</b></td>
                    <td><input name="password" type="password" style="width:130px;" /></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="font-size:11px;">
                      <input name="remember" type="checkbox" value="1" <?php if (isset($_COOKIE['remember_me'])) echo 'checked="checked"'; ?> /> Remember me
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2"><input type="submit" name="Submit" value="Login" /></td>
                  </tr>
                </table>
              </form>
            </div>
          </td>
          <!-- Register Box -->
          <td width="400" style="text-align:center;">
            <div style="border:#bd6f2f solid 1px; padding:15px; border-radius:5px;">
              <h3 style="margin-top:0;">Register</h3>
              <form name="registerForm" method="post" action="register-exec.php" onsubmit="return registerValidate(this)">
                <table border="0" align="center" cellpadding="3">
                  <tr>
                    <td>First Name</td>
                    <td><input name="fname" type="text" style="width:150px;" /></td>
                  </tr>
                  <tr>
                    <td>Last Name</td>
                    <td><input name="lname" type="text" style="width:150px;" /></td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td><input name="login" type="text" style="width:150px;" /></td>
                  </tr>
                  <tr>
                    <td>Password</td>
                    <td><input name="password" type="password" style="width:150px;" /></td>
                  </tr>
                  <tr>
                    <td>Question</td>
                    <td>
                      <select name="question" style="width:156px;">
                        <option value="select">- select -</option>
                        <?php
                        mysql_data_seek($questions, 0);
                        while ($row = mysql_fetch_array($questions)) {
                          echo "<option value='{$row['question_id']}'>{$row['question_text']}</option>";
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding-top:10px;"><input type="submit" name="Submit" value="Register" /></td>
                  </tr>
                </table>
              </form>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div id="footer">
      <div class="bottom_menu">
        <a href="home.htm">Home Page</a> | <a href="aboutus.htm">About Us</a> | <a href="foodzone.php">Food Zone</a> |
        <a href="admin/index.php" target="_blank">Administrator</a>
      </div>
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
