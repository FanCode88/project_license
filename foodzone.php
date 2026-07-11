<?php
// Checking connection and connecting to a database
require_once('connection/config.php');

// Connect to mysql server
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
  die('Failed to connect to server: ' . mysql_error());
}

// Select database
$db = mysql_select_db(DB_DATABASE);
if (!$db) {
  die("Unable to select database");
}

// Selecting all records from the food_details table
$result = mysql_query("SELECT * FROM food_details, categories WHERE food_details.food_category = categories.category_id")
  or die("A problem has occured ... Please check back after few hours.");

// Retrieve categories from the categories table
$categories = mysql_query("SELECT * FROM categories")
  or die("A problem has occured ... Please check back after few hours.");

// Retrieve a currency from the currencies table
$flag_1 = 1;
$currencies = mysql_query("SELECT * FROM currencies WHERE flag = '$flag_1'")
  or die("A problem has occured ... Please check back after few hours.");

if (isset($_POST['Submit'])) {
  // Function to sanitize values received from the form
  function clean($str)
  {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
      $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
  }

  // Get category id
  $id = clean($_POST['category']);

  // Selecting filtered records based on category id
  $result = mysql_query("SELECT * FROM food_details, categories WHERE food_category = '$id' AND food_details.food_category = categories.category_id")
    or die("A problem has occured ... Please check back after few hours.");
}
?>
<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Plaza: Foods</title>

  <!-- Am adăugat Bootstrap 5 pentru un design complet modern și adaptabil pe telefon -->
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

    .page-header {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/hero-bg.jpg') center center no-repeat;
      background-size: cover;
      color: white;
      padding: 60px 0;
      text-align: center;
      margin-bottom: 40px;
      border-radius: 0 0 20px 20px;
    }

    .food-card-table {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 25px;
      border: none !important;
    }

    .table img {
      border-radius: 8px;
      object-fit: cover;
      transition: transform 0.2s;
    }

    .table img:hover {
      transform: scale(1.1);
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
      margin-top: 60px;
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
      <button class="navbar-collapse-toggler navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active" href="foodzone.php">Food Zone</a></li>
          <li class="nav-item"><a class="nav-link" href="specialdeals.php">Special Deals</a></li>
          <li class="nav-item"><a class="nav-link" href="member-index.php">My Account</a></li>
          <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header stilizat tip Banner -->
  <div class="page-header shadow-sm">
    <div class="container">
      <h1 class="display-4 fw-bold text-uppercase">Choose Your Food</h1>
      <p class="lead mb-0">Explore our delicious menu tailored just for you</p>
    </div>
  </div>

  <div class="container">

    <!-- Zona Filtrare Categorie Stil Card -->
    <div class="card p-4 mb-4 shadow-sm border-0 bg-white rounded-3">
      <form name="categoryForm" id="categoryForm" method="post" action="foodzone.php" onsubmit="return categoriesValidate(this)">
        <div class="row g-3 align-items-center justify-content-center">
          <div class="col-auto">
            <label for="category" class="col-form-label fw-bold"><i class="bi bi-filter"></i> Filter by Category:</label>
          </div>
          <div class="col-md-4">
            <select name="category" id="category" class="form-select">
              <option value="select">- select category -</option>
              <?php
              while ($row = mysql_fetch_array($categories)) {
                echo "<option value=\"" . $row['category_id'] . "\">" . $row['category_name'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-auto">
            <button type="submit" name="Submit" class="btn btn-custom px-4">Show Foods</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Tabel Produse Modernizat -->
    <div class="food-card-table shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Photo</th>
              <th scope="col" class="text-start">Food Name</th>
              <th scope="col">QR Code</th>
              <th scope="col" class="text-start" style="width: 30%;">Description</th>
              <th scope="col">Category</th>
              <th scope="col">Price</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = mysql_num_rows($result);
            if (isset($_POST['Submit']) && $count < 1) {
              echo "<tr><td colspan='7' class='py-5 text-muted'><i class='bi bi-exclamation-circle fs-3 d-block mb-2'></i> No products found in this category.</td></tr>";
            } else {
              $symbol = mysql_fetch_assoc($currencies);

              while ($row = mysql_fetch_assoc($result)) {
                echo "<tr>";
                // Foto Produs
                echo "<td><a href='images/" . $row['food_photo'] . "' target='_blank'><img src='images/" . $row['food_photo'] . "' width='80' height='70' class='shadow-sm border'></a></td>";
                // Nume
                echo "<td class='fw-bold text-start'>" . htmlspecialchars($row['food_name']) . "</td>";
                // QR Code
                echo "<td><a href='images/" . $row['foodQR'] . "' target='_blank'><img src='images/" . $row['foodQR'] . "' width='65' height='65' class='shadow-sm border'></a></td>";
                // Descriere
                echo "<td class='text-start text-muted small'>" . htmlspecialchars($row['food_description']) . "</td>";
                // Categorie
                echo "<td><span class='badge bg-light text-dark border px-3 py-2'>" . htmlspecialchars($row['category_name']) . "</span></td>";
                // Pret
                echo "<td class='fw-bold text-success fs-5'>" . $symbol['currency_symbol'] . " " . number_format($row['food_price'], 2) . "</td>";
                // Buton adaugare
                echo '<td><a href="cart-exec.php?id=' . $row['food_id'] . '" class="btn btn-outline-success btn-sm rounded-pill px-3"><i class="bi bi-cart-plus"></i> Add To Cart</a></td>';
                echo "</tr>";
              }
            }
            mysql_free_result($result);
            mysql_close($link);
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Subsol / Footer Complet Negru/Modern -->
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
