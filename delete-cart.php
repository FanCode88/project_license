<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('auth.php');
require_once('connection/config.php');

// Conexiune modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Verificăm dacă avem ID-ul rândului din coș în URL
if (isset($_GET['id'])) {
  // Curățăm variabila pentru securitate
  $cart_id = mysqli_real_escape_string($link, trim($_GET['id']));

  // Ștergem produsul selectat din coș
  $query = "DELETE FROM cart_details WHERE cart_id = '$cart_id'";
  mysqli_query($link, $query) or die("A apărut o problemă la ștergerea produsului.");

  mysqli_close($link);

  // Ne întoarcem înapoi la coș, unde produsul a dispărut
  header("Location: cart.php");
  exit();
} else {
  // Dacă cineva accesează pagina direct, îl trimitem înapoi la coș
  header("Location: cart.php");
  exit();
}
