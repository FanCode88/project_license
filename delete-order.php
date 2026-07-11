<?php
session_start();
require_once('auth.php');
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (!$link) {
  die(mysqli_connect_error());
}

if (isset($_GET['id'])) {

  $order_id  = (int)$_GET['id'];
  $member_id = $_SESSION['SESS_MEMBER_ID'];

  // aflăm cart_id
  $q = mysqli_query($link, "
        SELECT cart_id
        FROM orders_details
        WHERE order_id='$order_id'
        AND member_id='$member_id'
    ");

  if ($r = mysqli_fetch_assoc($q)) {

    $cart_id = $r['cart_id'];

    // ștergem produsul din cart
    mysqli_query($link, "
            DELETE FROM cart_details
            WHERE cart_id='$cart_id'
        ");

    // ștergem comanda
    mysqli_query($link, "
            DELETE FROM orders_details
            WHERE order_id='$order_id'
            AND member_id='$member_id'
        ");
  }
}

header("Location: cont.php");
exit;
