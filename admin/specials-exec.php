<?php
// 1. Pornim sesiunea
session_start();

// 2. Forțăm afișarea erorilor pentru depanare
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3. Includem detaliile de conectare
// Notă: Dacă acest fișier se află în folderul 'admin', iar 'connection' este în folderul părinte,
// ar putea fi necesar să schimbi calea în: require_once('../connection/config.php');
require_once('connection/config.php');

// 4. Conectare la server folosind MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// 5. Funcție modernă pentru igienizarea datelor (Prevenire SQL Injection)
function clean($link, $str)
{
  $str = @trim($str);
  return mysqli_real_escape_string($link, $str);
}

// 6. Configurarea directorului pentru imagini
$target = "../images/";
$target = $target . basename($_FILES['photo']['name']);

// 7. Preluarea și igienizarea valorilor primite prin formular
$name        = clean($link, $_POST['name']);
$description = clean($link, $_POST['description']);
$price       = clean($link, $_POST['price']);
$start_date  = clean($link, $_POST['start_date']);
$end_date    = clean($link, $_POST['end_date']);
$photo       = clean($link, $_FILES['photo']['name']);

// 8. Crearea și rularea interogării INSERT
$qry = "INSERT INTO specials(special_name, special_description, special_price, special_start_date, special_end_date, special_photo)
        VALUES('$name', '$description', '$price', '$start_date', '$end_date', '$photo')";

$result = mysqli_query($link, $qry);

// 9. Verificarea rezultatului
if ($result) {
  // Încărcăm poza fizic pe server
  $moved = move_uploaded_file($_FILES['photo']['tmp_name'], $target);

  if ($moved) {
    // Totul este în regulă
    // Notă: echo-ul nu va fi foarte vizibil deoarece redirecționarea header() se execută instant
    $_SESSION['success_msg'] = "The photo " . basename($_FILES['photo']['name']) . " has been uploaded successfully.";
  } else {
    // Problemă la încărcarea fișierului
    $_SESSION['error_msg'] = "Sorry, there was a problem uploading your photo. Error code: " . $_FILES["photo"]["error"];
  }

  // Redirecționare înapoi la pagina cu tabelul promoțiilor
  header("location: specials.php");
  exit();
} else {
  die("Query failed: " . mysqli_error($link));
}
