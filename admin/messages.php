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

// selecting all records from the messages table
$query = "SELECT * FROM messages";
$result = mysqli_query($link, $query) or die("There are no records to display ... \n" . mysqli_error($link));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Messages</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="validation/admin.js"></script>
</head>

<body>
  <div id="page">

    <div id="header">
      <h1>Messages Management</h1>
      <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="categories.php">Categories</a>
        <a href="foods.php">Foods</a>
        <a href="accounts.php">Accounts</a>
        <a href="orders.php">Orders</a>
        <a href="reservations.php">Reservations</a>
        <a href="specials.php">Specials</a>
        <a href="allocation.php">Staff</a>
        <a href="messages.php" class="active">Messages</a>
        <a href="options.php">Options</a>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>

    <div id="container">

      <!-- Zona formularului redesenată modern -->
      <div class="forms-wrapper" style="justify-content: center; margin-bottom: 20px;">
        <div class="allocation-card" style="width: 100%; max-width: 600px;">
          <h3>Send a Message</h3>

          <form id="messageForm" name="messageForm" method="post" action="message-exec.php" onsubmit="return messageValidate(this)">
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" name="subject" id="subject" class="textfield" style="width: 100%; padding: 8px; box-sizing: border-box;" />
            </div>

            <div class="form-group">
              <label for="txtmessage">Message Box</label>
              <textarea name="txtmessage" id="txtmessage" class="textfield" rows="5" style="width: 100%; padding: 8px; box-sizing: border-box; font-family: inherit;"></textarea>
            </div>

            <div class="form-action" style="text-align: center; gap: 10px; display: flex; justify-content: center;">
              <input type="submit" name="Submit" value="Send Message" class="btn-primary" />
              <input type="reset" name="Reset" value="Clear Field" class="btn-primary" style="background: #6c757d; border-color: #5a6268;" />
            </div>
          </form>
        </div>
      </div>

      <hr />

      <!-- Tabelul cu mesaje primite/trimise -->
      <table class="modern-table">
        <caption>
          <h3>Sent Messages</h3>
        </caption>
        <tr>
          <th>Message ID</th>
          <th>Date Sent</th>
          <th>Time Sent</th>
          <th>Message Subject</th>
          <th>Message Text</th>
          <th>Action(s)</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td><span class=\"font-bold\">" . htmlspecialchars($row['message_id']) . "</span></td>";
          echo "<td>" . htmlspecialchars($row['message_date']) . "</td>";
          echo "<td>" . htmlspecialchars($row['message_time']) . "</td>";
          echo "<td class=\"font-bold\">" . htmlspecialchars($row['message_subject']) . "</td>";
          echo "<td class=\"desc-cell\" style=\"text-align: left; max-width: 400px;\">" . htmlspecialchars($row['message_text']) . "</td>";
          echo '<td><a href="delete-message.php?id=' . $row['message_id'] . '" class="btn-remove">Remove Message</a></td>';
          echo "</tr>";
        }
        mysqli_free_result($result);
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
