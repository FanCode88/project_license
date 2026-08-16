<?php
// Start sesiune
session_start();

// Distrugem sesiunea complet
$_SESSION = array();
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Plaza: Logged Out</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff9f5;
      color: #4a3b32;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .navbar {
      background-color: #ffffff;
      box-shadow: 0 2px 12px rgba(189, 111, 47, 0.08);
      padding: 12px 0;
    }

    .navbar-brand {
      font-weight: 700;
      color: #bd6f2f !important;
    }

    .nav-link {
      font-weight: 500;
      color: #6c584c !important;
      transition: color 0.2s ease;
    }

    .nav-link:hover,
    .nav-link.active {
      color: #bd6f2f !important;
    }

    .logout-card {
      width: 100%;
      max-width: 400px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(189, 111, 47, 0.2);
      padding: 35px 25px;
      text-align: center;
      border: 1px solid #f3e5dc;
    }

    .icon-box {
      width: 60px;
      height: 60px;
      background-color: #fef3ec;
      color: #bd6f2f;
      font-size: 26px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 auto 18px auto;
    }

    .btn-custom {
      background-color: #bd6f2f;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 18px;
      font-weight: 600;
      font-size: 14px;
      transition: background-color 0.2s ease;
      margin-top: 10px;
    }

    .btn-custom:hover {
      background-color: #a45a20;
      color: white;
    }

    .footer {
      background-color: #3b2f2f;
      color: #d1c7bc;
      padding: 25px 0;
      font-size: 13px;
    }

    .footer a {
      color: #f3e5dc;
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer a:hover {
      color: #bd6f2f;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand fs-5" href="index.php"><i class="bi bi-egg-fried"></i> Food Plaza</a>
      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center gap-1">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="foodzone.php">Food Zone</a></li>
          <li class="nav-item"><a class="nav-link" href="specialdeals.php">Special Deals</a></li>
          <li class="nav-item"><a class="nav-link" href="member-index.php">My Account</a></li>
          <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Conținut principal -->
  <main>
    <div class="logout-card">
      <div class="icon-box">
        <i class="bi bi-check-lg"></i>
      </div>
      <h1 class="h5 fw-bold mb-2 text-dark">Logged Out</h1>
      <p class="text-muted small mb-1">You have been successfully logged out.</p>
      <a href="login-register.php" class="btn btn-custom w-100 shadow-sm">Click Here to Login Again</a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer text-center">
    <div class="container">
      <div class="mb-2 d-flex flex-wrap justify-content-center gap-2">
        <a href="index.php">Home Page</a>
        <span>•</span>
        <a href="aboutus.php">About Us</a>
        <span>•</span>
        <a href="specialdeals.php">Special Deals</a>
        <span>•</span>
        <a href="foodzone.php">Food Zone</a>
        <span>•</span>
        <a href="admin/index.php" target="_blank" class="text-warning">Administrator</a>
      </div>
      <p class="mb-0 text-muted small">&copy; <?php echo date("Y"); ?> Saceanu Ionut Sorin. All Rights Reserved</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
