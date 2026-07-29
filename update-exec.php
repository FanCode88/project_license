<?php
// Pornim sesiunea și forțăm afișarea erorilor pentru depanare
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificarea conexiunii și conectarea la baza de date folosind MySQLi modern
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Funcție pentru curățarea valorilor primite din formular (prevenire SQL injection)
function clean($link, $str)
{
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
}

// Curățarea valorilor din POST
$OldPassword = clean($link, $_POST['opassword']);
$NewPassword = clean($link, $_POST['npassword']);
$ConfirmNewPassword = clean($link, $_POST['cpassword']);

// Verificăm dacă noua parolă coincide cu confirmarea ei
if ($NewPassword !== $ConfirmNewPassword) {
    mysqli_close($link);
    header("Location: reset-failed.php?error=mismatch");
    exit();
}

// Verificăm dacă variabila 'id' este setată în URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $hashed_old_password = md5($OldPassword);
    $hashed_new_password = md5($NewPassword);

    // Folosim prepared statements pentru securitate maximă împotriva SQL injection
    $stmt = mysqli_prepare($link, "UPDATE members SET passwd = ? WHERE member_id = ? AND passwd = ?");
    mysqli_stmt_bind_param($stmt, "sis", $hashed_new_password, $id, $hashed_old_password);

    $success = mysqli_stmt_execute($stmt);

    if ($success && mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        // Redirecționare înapoi la profilul membrului
        header("Location: member-profile.php?success=password_changed");
        exit();
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        // Eșec la actualizarea parolei (parola veche greșită sau ID invalid)
        header("Location: reset-failed.php");
        exit();
    }
} else {
    mysqli_close($link);
    // Dacă ID-ul nu este setat, oprim execuția
    die("Password changing failed! Please try again after a few minutes");
}
?>
