<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Plaza: Login Failed</title>
  <!-- Bootstrap 5 pentru un design modern și adaptabil pe telefon -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font elegant și iconițe -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }

    .navbar {
      background-color: #ffffff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      color: #bd6f2f !important;
    }

    .nav-link {
      font-weight: 500;
      color: #555 !important;
      transition: color 0.3s;
    }

    .nav-link:hover,
    .nav-link.active {
      color: #bd6f2f !important;
    }

    .error-container {
      max-width: 500px;
      margin: 80px auto;
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 40px;
      text-align: center;
      border-top: 5px solid #dc3545;
    }

    .btn-custom {
      background-color: #bd6f2f;
      color: white;
      border: none;
    }

    .btn-custom:hover {
      background-color: #a45a20;
      color: white;
    }

    .footer {
      background-color: #222;
      color: #aaa;
      padding: 40px 0 20px 0;
      margin-top: 80px;
    }

    .footer a {
      color: #eee;
      text-decoration: none;
    }

    .footer a:hover {
      color: #bd6f2f;
    }
  </style>
</head>

<body>

  <!-- Meniu Modern Adaptabil (Navbar) -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-egg-fried"></i> Food Plaza</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="foodzone.php">Food Zone</a></li>
          <li class="nav-item"><a class="nav-link" href="specialdeals.php">Special Deals</a></li>
          <li class="nav-item"><a class="nav-link" href="member-index.php">My Account</a></li>
          <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Conținut principal - Eșec Autentificare -->
  <div class="container">
    <div class="error-container">
      <div class="mb-4">
        <i class="bi bi-exclamation-triangle-fill text-danger display-3"></i>
      </div>
      <h1 class="h3 fw-bold mb-3">Login Failed</h1>
      <p class="text-muted mb-4">Please check your email and password, then try again.</p>
      <a href="login-register.php" class="btn btn-custom w-100 py-2 fw-bold">Try Again</a>
    </div>
  </div>

  <!-- Subsol / Footer Modern -->
  <footer class="footer">
    <div class="container text-center">
      <div class="mb-3">
        <a href="index.php" class="mx-2">Home Page</a> |
        <a href="aboutus.php" class="mx-2">About Us</a> |
        <a href="specialdeals.php" class="mx-2">Special Deals</a> |
        <a href="foodzone.php" class="mx-2">Food Zone</a> |
        <a href="admin/index.php" target="_blank" class="mx-2 text-warning">Administrator</a>
      </div>
      <p class="small mb-0 text-muted">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</p>
    </div>
  </footer>

  <!-- Bootstrap JS linkat direct din CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
