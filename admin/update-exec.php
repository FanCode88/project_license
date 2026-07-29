<?php
//Start session and check authentication
session_start();
require_once('auth.php');

//checking connection and connecting to a database
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

//Set charset to utf8mb4 for proper character encoding
mysqli_set_charset($link, "utf8mb4");

//Function to sanitize values received from the form. Prevents SQL injection
function clean($conn, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($conn, $str);
}

//Sanitize the POST values
$OldPassword = isset($_POST['opassword']) ? $_POST['opassword'] : '';
$NewPassword = isset($_POST['npassword']) ? $_POST['npassword'] : '';
$ConfirmNewPassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

// Basic validation
if ($NewPassword !== $ConfirmNewPassword) {
  mysqli_close($link);
  die("Password changing failed: New passwords do not match.");
}

// check if the 'id' variable is set in URL
if (isset($_GET['id'])) {
  // get id value and sanitize
  $id = clean($link, $_GET['id']);

  // First, verify the old password against the database record securely
  $stmt = mysqli_prepare($link, "SELECT Password FROM pizza_admin WHERE Admin_ID = ?");
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
      $admin = mysqli_fetch_assoc($result);
      mysqli_stmt_close($stmt);

      // Verify old password (supporting both hashed password_verify or legacy plain text)
      $is_old_valid = false;
      if (password_verify($OldPassword, $admin['Password']) || $OldPassword === $admin['Password']) {
        $is_old_valid = true;
      }

      if ($is_old_valid) {
        // Hash the new password securely
        $hashed_new_password = password_hash($NewPassword, PASSWORD_DEFAULT);

        // update the entry using prepared statements
        $update_stmt = mysqli_prepare($link, "UPDATE pizza_admin SET Password = ? WHERE Admin_ID = ?");
        if ($update_stmt) {
          mysqli_stmt_bind_param($update_stmt, "si", $hashed_new_password, $id);
          $update_result = mysqli_stmt_execute($update_stmt);

          if ($update_result) {
            mysqli_stmt_close($update_stmt);
            mysqli_close($link);
            // redirect back to the member profile
            header("Location: profile.php");
            exit();
          } else {
            $error = mysqli_error($link);
            mysqli_stmt_close($update_stmt);
            mysqli_close($link);
            die("Password update execution failed ... \n" . $error);
          }
        } else {
          $error = mysqli_error($link);
          mysqli_close($link);
          die("Statement preparation failed ... \n" . $error);
        }
      } else {
        mysqli_close($link);
        die("Password changing failed: Incorrect old password.");
      }
    } else {
      if ($stmt) {
        mysqli_stmt_close($stmt);
      }
      mysqli_close($link);
      die("The admin does not exist ...");
    }
  } else {
    $error = mysqli_error($link);
    mysqli_close($link);
    die("Query failed: " . $error);
  }
} else {
  // if id isn't set, give an error
  mysqli_close($link);
  die("Password changing failed ...");
}
