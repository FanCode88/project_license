<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asigură-te că sesiunea este pornită înainte de a citi din ea
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('qrlib.php');
require_once('auth.php');
require_once('connection/config.php');

// Conexiune modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

$flag_0 = 0;
if (!isset($_SESSION['SESS_MEMBER_ID'])) {
  die("Eroare: Sesiunea a expirat sau nu ești autentificat. Te rugăm să te reconectezi.");
}
$member_id = $_SESSION['SESS_MEMBER_ID'];

// Interogare coș de cumpărături principală
$result = mysqli_query($link, "SELECT food_name,food_description,food_price,food_photo,cart_id,quantity_value,total,flag,category_name FROM food_details,cart_details,categories,quantities WHERE cart_details.member_id='$member_id' AND cart_details.flag='$flag_0' AND cart_details.food_id=food_details.food_id AND food_details.food_category=categories.category_id AND cart_details.quantity_id=quantities.quantity_id")
  or die("A problem has occurred with the cart query.");

if (isset($_POST['Submit'])) {
  function clean($link, $str)
  {
    return mysqli_real_escape_string($link, trim($str));
  }
  $id = clean($link, $_POST['category']);
  $result = mysqli_query($link, "SELECT * FROM food_details WHERE food_category='$id'")
    or die("A problem has occurred with the category query.");
}

// Preluare cantități pentru dropdown-uri
$quantities = mysqli_query($link, "SELECT * FROM quantities") or die(mysqli_error($link));

// Preluare elemente din coș separat pentru a evita conflictele de cursor la refresh
$items = mysqli_query($link, "SELECT cart_id FROM cart_details WHERE member_id='$member_id' AND flag='$flag_0'") or die(mysqli_error($link));
$dropdown_items = [];
while ($item_row = mysqli_fetch_assoc($items)) {
  $dropdown_items[] = $item_row;
}

// Preluare monedă activă
$flag_1 = 1;
$currencies = mysqli_query($link, "SELECT * FROM currencies WHERE flag='$flag_1'") or die(mysqli_error($link));

// Asigură-te că există un folder temporar pentru codurile QR generate
$qr_dir = 'temp_qrcodes/';
if (!file_exists($qr_dir)) {
  mkdir($qr_dir, 0777, true);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Account</title>

  <!-- Google Fonts & Vendor CSS -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700|Satisfy|Comic+Neue:300,400,700" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  link
</head>

<body>

  <!-- ======= Top Bar ======= -->
  <section id="topbar" class="d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-center justify-content-lg-start">
      <i class="bi bi-phone d-flex align-items-center"><span>+0738740300</span></i>
      <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
    </div>
  </section>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <div class="logo me-auto">
        <h1><a href="index.php">Deluxe restaurant</a></h1>
      </div>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="index.php#login">Login</a></li>
          <li><a class="nav-link scrollto" href="index.php#menu">Our Food</a></li>
          <li><a class="nav-link scrollto" href="index.php">Specials</a></li>
          <li><a class="nav-link scrollto active" href="cont.php">Account</a></li>
          <li><a class="nav-link scrollto" href="index.php#chefs">Chefs</a></li>
          <li><a class="nav-link scrollto" href="index.php#gallery">Gallery</a></li>
          <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>

      <a href="index.php#menu" class="book-a-table-btn scrollto">Return</a>
    </div>
  </header>

  <main id="main" style="margin-top: 110px;">
    <section class="inner-page">
      <div class="container">

        <div class="text-center my-4">
          <h1 class="display-5 fw-bold text-uppercase" style="color: #ffb03b;">My Shopping Cart</h1>
          <p class="lead"><a href="foodzone.php" class="btn btn-outline-warning btn-sm">← Continue Shopping!</a></p>
        </div>

        <!-- Zonă modificare cantitate restructurată modern cu Bootstrap Flexbox -->
        <div class="card p-4 mb-4 shadow-sm">
          <form name="quantityForm" id="quantityForm" method="post" action="update-quantity.php" onsubmit="return updateQuantity(this)">
            <div class="row g-3 align-items-center justify-content-center">
              <div class="col-md-3">
                <label for="item" class="form-label fw-bold">Item ID</label>
                <select name="item" id="item" class="form-select">
                  <option value="select">- select ID -</option>
                  <?php
                  foreach ($dropdown_items as $item_data) {
                    echo "<option value='" . $item_data['cart_id'] . "'>" . $item_data['cart_id'] . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-3">
                <label for="quantity" class="form-label fw-bold">New Quantity</label>
                <select name="quantity" id="quantity" class="form-select">
                  <option value="select">- select qty -</option>
                  <?php
                  while ($row = mysqli_fetch_assoc($quantities)) {
                    echo "<option value='" . $row['quantity_id'] . "'>" . $row['quantity_value'] . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-2 mt-4 d-grid">
                <button type="submit" name="Submit" class="btn btn-warning text-white">Update</button>
              </div>
            </div>
          </form>
        </div>

        <!-- Tabel coș de cumpărături complet adaptabil (Responsive) -->
        <div class="table-responsive shadow-sm rounded">
          <table class="table table-bordered table-striped align-middle text-center mb-0">
            <thead class="table-dark">
              <tr>
                <th>Item ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Cost</th>
                <th>QR Code (Ingredients)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $symbol = mysqli_fetch_assoc($currencies);
              while ($row = mysqli_fetch_array($result)) {
                $photo_encoded = str_replace(' ', '%20', $row['food_photo']);

                // --- REPARARE DUPLICARE TEXT QR ---
                // Trimitem direct descrierea/ingredientele din baza de date fără prefixări redundante
                $qr_text = $row['food_description'];

                $filename = $qr_dir . 'qr_' . $row['cart_id'] . '.png';
                QRcode::png($qr_text, $filename, QR_ECLEVEL_L, 4);
                // -----------------------

                echo "<tr>";
                echo "<td class='fw-bold'>#" . $row['cart_id'] . "</td>";
                echo "<td><a href='images/" . $photo_encoded . "' target='_blank'><img src='images/" . $photo_encoded . "' class='img-thumbnail shadow-sm' style='max-width: 80px; height: 60px; object-fit: cover;'></a></td>";
                echo "<td class='fw-bold text-start'>" . htmlspecialchars($row['food_name']) . "</td>";
                echo "<td class='text-start text-muted small' style='max-width: 200px;'>" . htmlspecialchars($row['food_description']) . "</td>";
                echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($row['category_name']) . "</span></td>";
                echo "<td class='text-success fw-bold'>" . $symbol['currency_symbol'] . number_format($row['food_price'], 2) . "</td>";
                echo "<td><span class='badge bg-dark px-2.5 py-2'>" . $row['quantity_value'] . "</span></td>";
                echo "<td class='text-danger fw-bold'>" . $symbol['currency_symbol'] . number_format($row['total'], 2) . "</td>";

                // Codul QR interactiv care deschide fereastra modală la click
                echo "<td>";
                echo "  <img src='" . $filename . "' ";
                echo "       class='img-thumbnail shadow-sm' ";
                echo "       style='max-width: 70px; cursor: pointer;' ";
                echo "       alt='QR Code' ";
                echo "       data-bs-toggle='modal' ";
                echo "       data-bs-target='#qrModal' ";
                echo "       data-qr-src='" . $filename . "' ";
                echo "       data-food-name='" . htmlspecialchars($row['food_name']) . "' ";
                echo "       data-ingredients='" . htmlspecialchars($row['food_description']) . "'>";
                echo "</td>";

                // Zona de acțiuni
                echo "<td>";
                echo "  <div class='d-flex gap-2 justify-content-center'>";
                echo "    <a href='order-exec.php?id=" . $row['cart_id'] . "' class='btn btn-success btn-sm px-3 shadow-sm' onclick='placeOrder(event, " . $row['cart_id'] . ")'>Place Order</a>";
                echo "    <a href='delete-cart.php?id=" . $row['cart_id'] . "' class='btn btn-outline-danger btn-sm px-3 shadow-sm' onclick='return confirm(\"Sigur vrei să ștergi acest produs din coș?\")'>";
                echo "      <i class='bi bi-trash'></i> Șterge";
                echo "    </a>";
                echo "  </div>";
                echo "</td>";

                echo "</tr>";
              }
              mysqli_free_result($result);
              mysqli_close($link);
              ?>
            </tbody>
          </table>
        </div>

        <!-- Zona Footer a paginii -->
        <footer id="footer" class="mt-5 pt-4 border-top text-center">
          <div class="mb-2">
            <a href="index.php" class="text-decoration-none mx-2 text-secondary">Home Page</a> |
            <a href="admin/index.php" class="text-decoration-none mx-2 text-secondary">Administrator</a>
          </div>
          <p class="text-muted small">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</p>
        </footer>

      </div>
    </section>
  </main>

  <!-- ======= Fereastră Modală pentru mărire QR și detalii ======= -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-uppercase" id="qrModalLabel" style="color: #ffb03b;">Scanare Ingrediente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <h4 id="modalFoodName" class="fw-bold mb-3"></h4>

          <!-- Codul QR mărit -->
          <img id="modalQrImg" src="" class="img-fluid shadow rounded mb-4" style="max-width: 280px; width: 100%;" alt="QR Code Mare">

          <!-- Textul cu ingrediente dedesubt -->
          <div class="p-3 bg-light rounded text-start border">
            <h6 class="fw-bold"><i class="bi bi-card-text text-warning"></i> Listă Ingrediente:</h6>
            <p id="modalIngredients" class="text-muted small mb-0"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JS Vendor Files & Logică dinamică Pop-up -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var qrModal = document.getElementById('qrModal');

      qrModal.addEventListener('show.bs.modal', function(event) {
        var triggerImage = event.relatedTarget;

        var qrSrc = triggerImage.getAttribute('data-qr-src');
        var foodName = triggerImage.getAttribute('data-food-name');
        var ingredients = triggerImage.getAttribute('data-ingredients');

        document.getElementById('modalQrImg').src = qrSrc;
        document.getElementById('modalFoodName').textContent = foodName;
        document.getElementById('modalIngredients').textContent = ingredients;
      });
    });
  </script>

</body>

</html>
