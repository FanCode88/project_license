<?php
require_once('auth.php');
require_once('connection/config.php');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_DATABASE);

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $memberId = $_SESSION['SESS_MEMBER_ID'];

  // Executăm ștergerea
  $query = "DELETE FROM reservations_details WHERE ReservationID='$id' AND member_id='$memberId'";
  if (mysql_query($query)) {
    echo "success"; // Trimitem acest mesaj pentru ca JS să știe să șteargă rândul
  }
}
mysql_close($link);
