<?php
// Start sesiune
session_start();

// Include configurarea (asigură-te că folosește constantele corecte)
require_once('connection/config.php');

// Verificăm dacă datele au fost trimise
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $login = trim($_POST['login']);
  $password = $_POST['password'];

  try {
    // Conectare folosind PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Interogare securizată cu prepared statements
    $stmt = $pdo->prepare("SELECT * FROM members WHERE login = :login AND passwd = :password");

    // Executăm interogarea (md5 este păstrat doar dacă așa sunt parolele în DB)
    $stmt->execute([
      'login'    => $login,
      'password' => md5($password)
    ]);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member) {
      // Login Succes
      session_regenerate_id();
      $_SESSION['SESS_MEMBER_ID']   = $member['member_id'];
      $_SESSION['SESS_FIRST_NAME']  = $member['firstname'];
      $_SESSION['SESS_LAST_NAME']   = $member['lastname'];
      session_write_close();

      header("location: member-index.php");
      exit();
    } else {
      // Login eșuat
      header("location: login-failed.php");
      exit();
    }
  } catch (PDOException $e) {
    die("Eroare de sistem: Nu s-a putut realiza conexiunea.");
  }
} else {
  die("Acces neautorizat.");
}
