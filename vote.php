<?php
// Pornim sesiunea și forțăm afișarea erorilor pentru depanare
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectare la baza de date folosind MySQLi modern și setări corespunzătoare
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'polling');
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Setează setul de caractere UTF-8
mysqli_set_charset($link, "utf8");

// Dacă sesiunea nu este validă, redirecționează către ecranul de autentificare
if (empty($_SESSION['member_id'])) {
    mysqli_close($link);
    header("location: access-denied.php");
    exit();
}

// Preluarea pozițiilor folosind MySQLi
$positions = mysqli_query($link, "SELECT * FROM tbPositions");
if (!$positions) {
    die("There are no records to display ... \n" . mysqli_error($link));
}

// Verificarea trimiterii formularului și interogare securizată cu prepared statements
$result = null;
if (isset($_POST['Submit']) && isset($_POST['position'])) {
    $position = trim($_POST['position']);

    $stmt = mysqli_prepare($link, "SELECT * FROM tbCandidates WHERE candidate_position = ?");
    mysqli_stmt_bind_param($stmt, "s", $position);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Simple PHP Polling System: Voting Page</title>
  <link href="css/user_styles.css" rel="stylesheet" type="text/css" />
  <script language="JavaScript" src="js/user.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript">
    function getVote(val) {
      if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.open("GET", "save.php?vote=" + encodeURIComponent(val), true);
      xmlhttp.send();
    }

    function getPosition(str) {
      if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.open("GET", "vote.php?position=" + encodeURIComponent(str), true);
      xmlhttp.send();
    }
  </script>
</head>

<body bgcolor="tan">
  <center><a href="https://sourceforge.net/projects/pollingsystem/"><img src="images/logo" alt="site logo"></a></center><br>
  <center><b>
      <font color="brown" size="6">Simple PHP Polling System</font>
    </b></center><br><br>

  <div id="page">
    <div id="header">
      <h1>CURRENT POLLS</h1>
      <a href="student.php">Home</a> | <a href="vote.php">Current Polls</a> | <a href="manage-profile.php">Manage My Profile</a> | <a href="logout.php">Logout</a>
    </div>
    <div class="refresh"></div>
    <div id="container">
      <table width="420" align="center">
        <form name="fmNames" id="fmNames" method="post" action="vote.php" onsubmit="return positionValidate(this)">
          <tr>
            <td>Choose Position</td>
            <td>
              <SELECT NAME="position" id="position" onchange="getPosition(this.value)" required>
                <OPTION VALUE="">select</OPTION>
                <?php
                while ($row = mysqli_fetch_assoc($positions)) {
                    $pos_name = htmlspecialchars($row['position_name']);
                    $selected = (isset($_POST['position']) && $_POST['position'] == $row['position_name']) ? "selected" : "";
                    echo "<OPTION VALUE='$pos_name' $selected>$pos_name</OPTION>";
                }
                mysqli_free_result($positions);
                ?>
              </SELECT>
            </td>
            <td><input type="submit" name="Submit" value="See Candidates" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </form>
      </table>

      <table width="270" align="center">
        <form>
          <tr>
            <th>Candidates:</th>
          </tr>
          <?php
          if (isset($_POST['Submit']) && $result) {
              while ($row = mysqli_fetch_assoc($result)) {
                  $candidate_name = htmlspecialchars($row['candidate_name']);
                  echo "<tr>";
                  echo "<td>" . $candidate_name . "</td>";
                  echo "<td><input type='radio' name='vote' value='$candidate_name' onclick='getVote(this.value)' /></td>";
                  echo "</tr>";
              }
              mysqli_free_result($result);
              mysqli_stmt_close($stmt);
          }
          mysqli_close($link);
          ?>
          <tr>
            <td>
              <h3>NB: Click a circle under a respective candidate to cast your vote. You can't vote more than once in a respective position. This process can not be undone so think wisely before casting your vote.</h3>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </form>
      </table>
    </div>
    <div id="footer">
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>s
