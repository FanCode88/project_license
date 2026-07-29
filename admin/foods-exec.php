<?php
//Start session
session_start();

//Include database connection details
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($link, $str);
}

//setup a directory where images will be saved
$target = "../images/";
$target = $target . basename($_FILES['photo']['name']);

//Sanitize the POST values
$name = clean($link, $_POST['name']);
$description = clean($link, $_POST['description']);
$price = clean($link, $_POST['price']);
$category = clean($link, $_POST['category']);
$photo = clean($link, $_FILES['photo']['name']);

//Create INSERT query using prepared statements
$stmt = mysqli_prepare($link, "INSERT INTO food_details(food_name, food_description, food_price, food_photo, food_category) VALUES(?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssdss", $name, $description, $price, $photo, $category);
$result = mysqli_stmt_execute($stmt);

//Check whether the query was successful or not
if ($result) {
  mysqli_stmt_close($stmt);

  //Writes the photo to the server
  $moved = move_uploaded_file($_FILES['photo']['tmp_name'], $target);

  if ($moved) {
    //everything is okay
    echo "The photo " . basename($_FILES['photo']['name']) . " has been uploaded, and your information has been added to the directory";
  } else {
    //Gives an error if its not okay
    echo "Sorry, there was a problem uploading your photo. " . $_FILES["photo"]["error"];
  }
  mysqli_close($link);
  header("location: foods.php");
  exit();
} else {
  $error = mysqli_error($link);
  mysqli_stmt_close($stmt);
  mysqli_close($link);
  die("Query failed " . $error);
}
