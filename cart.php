<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('qrlib.php');
require_once('auth.php');
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

if (!isset($_SESSION['SESS_MEMBER_ID'])) {
  die("Eroare: Sesiunea a expirat sau nu ești autentificat. Te rugăm să te reconectezi.");
}
$member_id = (int) $_SESSION['SESS_MEMBER_ID'];
$flag_0 = 0;

// Interogare securizată coș de cumpărături
$stmt_cart = mysqli_prepare($link, "
    SELECT food_details.food_name, food_details.food_description, food_details.food_price,
           food_details.food_photo, cart_details.cart_id, cart_details.quantity_id,
           cart_details.total, cart_details.flag, categories.category_name
    FROM cart_details
    INNER JOIN food_details ON cart_details.food_id = food_details.food_id
    INNER JOIN categories ON food_details.food_category = categories.category_id
    WHERE cart_details.member_id = ? AND cart_details.flag = ?
");
mysqli_stmt_bind_param($stmt_cart, "ii", $member_id, $flag_0);
mysqli_stmt_execute($stmt_cart);
$result = mysqli_stmt_get_result($stmt_cart);

// Preluare monedă activă
$flag_1 = 1;
$stmt_curr = mysqli_prepare($link, "SELECT currency_symbol FROM currencies WHERE flag = ?");
mysqli_stmt_bind_param($stmt_curr, "i", $flag_1);
mysqli_stmt_execute($stmt_curr);
$curr_res = mysqli_stmt_get_result($stmt_curr);
$symbol_row = mysqli_fetch_assoc($curr_res);
$currency_symbol = $symbol_row['currency_symbol'] ?? '$';
mysqli_stmt_close($stmt_curr);

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
  <title>Shopping Cart - Deluxe Restaurant</title>

  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700|Satisfy|Comic+Neue:300,400,700"
    rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .img-thumbnail {
      transition: .3s;
      border-radius: 10px;
    }

    .img-thumbnail:hover {
      transform: scale(1.08);
    }

    .qty-control {
      width: 130px;
      margin: 0 auto;
    }

    .qty-control input {
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <section id="topbar" class="d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-center justify-content-lg-start">
      <i class="bi bi-phone d-flex align-items-center"><span>+0738740300</span></i>
      <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
    </div>
  </section>

  <header id="header" class="fixed-top d-flex align-items-center">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <div class="logo me-auto">
        <h1><a href="index.php">Deluxe restaurant</a></h1>
      </div>
      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="index.php#menu">Our Food</a></li>
          <li><a class="nav-link scrollto" href="cont.php">Account</a></li>
          <li><a class="nav-link scrollto" href="contactus.php">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
      <a href="cont.php" class="book-a-table-btn scrollto">My Account</a>
    </div>
  </header>

  <main id="main" style="margin-top: 110px;">
    <section class="inner-page">
      <div class="container">

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-warning alert-dismissible fade show text-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="text-center my-4">
          <h1 class="display-5 fw-bold text-uppercase" style="color: #ffb03b;">My Shopping Cart</h1>
          <p class="lead"><a href="foodzone.php" class="btn btn-outline-warning btn-sm">← Continue Shopping!</a></p>
        </div>

        <div class="table-responsive shadow-sm rounded">
          <table class="table table-bordered table-striped align-middle text-center mb-0">
            <thead class="table-dark">
              <tr>
                <th>Item ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Ingredients</th>
                <th>Category</th>
                <th>Price</th>
                <th style="min-width: 140px;">Quantity</th>
                <th>Total Cost</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (mysqli_num_rows($result) === 0) {
                echo "<tr><td colspan='9' class='py-4 text-muted'>Your shopping cart is empty. <a href='foodzone.php' class='text-warning fw-bold'>Order food now!</a></td></tr>";
              }

              while ($row = mysqli_fetch_assoc($result)) {
                $cart_id = $row['cart_id'];
                $food_name = $row['food_name'];
                $food_description = $row['food_description'];
                $food_price = (float) $row['food_price'];
                $food_photo = $row['food_photo'];
                $quantity_val = (int) $row['quantity_id'];
                $total = (float) $row['total'];
                $category_name = $row['category_name'];

                $photo_encoded = str_replace(' ', '%20', $food_photo);
                $filename = $qr_dir . 'qr_' . $cart_id . '.png';

                if (!empty($food_description)) {
                  QRcode::png($food_description, $filename, QR_ECLEVEL_L, 4);
                }

                echo "<tr>";
                echo "<td class='fw-bold'>#" . htmlspecialchars($cart_id) . "</td>";
                echo "<td><a href='images/" . htmlspecialchars($photo_encoded) . "' target='_blank'><img src='images/" . htmlspecialchars($photo_encoded) . "' class='img-thumbnail shadow-sm' style='max-width: 80px; height: 60px; object-fit: cover;'></a></td>";
                echo "<td class='fw-bold text-start'>" . htmlspecialchars($food_name) . "</td>";
                echo "<td class='text-center'><img src='" . htmlspecialchars($filename) . "' class='img-thumbnail shadow-sm' style='max-width:80px; cursor:pointer' alt='QR Code' data-bs-toggle='modal' data-bs-target='#qrModal' data-qr-src='" . htmlspecialchars($filename) . "' data-food-name='" . htmlspecialchars($food_name) . "' data-ingredients='" . htmlspecialchars($food_description) . "'><div class='mt-1'><small class='text-success fw-semibold'>View Ingredients</small></div></td>";
                echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($category_name) . "</span></td>";
                echo "<td class='text-success fw-bold'>" . htmlspecialchars($currency_symbol) . number_format($food_price, 2) . "</td>";

                // Formular modificare cantitate
                echo "<td>";
                echo "  <form action='update-quantity.php' method='post' class='d-inline'>";
                echo "    <input type='hidden' name='item' value='" . htmlspecialchars($cart_id) . "'>";
                echo "    <div class='input-group input-group-sm qty-control'>";
                echo "      <button type='submit' name='action' value='minus' class='btn btn-outline-warning fw-bold'>-</button>";
                echo "      <input type='number' name='quantity' value='" . htmlspecialchars($quantity_val) . "' min='1' class='form-control text-center px-1' readonly>";
                echo "      <button type='submit' name='action' value='plus' class='btn btn-outline-warning fw-bold'>+</button>";
                echo "    </div>";
                echo "  </form>";
                echo "</td>";

                echo "<td class='text-danger fw-bold'>" . htmlspecialchars($currency_symbol) . number_format($total, 2) . "</td>";
                echo "<td>";
                echo "  <div class='d-flex gap-2 justify-content-center'>";
                echo "    <a href='order-exec.php?id=" . htmlspecialchars($cart_id) . "' class='btn btn-success btn-sm px-3 shadow-sm'>Place Order</a>";
                echo "    <a href='delete-cart.php?id=" . htmlspecialchars($cart_id) . "' class='btn btn-outline-danger btn-sm px-2 shadow-sm' onclick='return confirm(\"Sigur vrei să ștergi acest produs?\")'><i class='bi bi-trash'></i></a>";
                echo "  </div>";
                echo "</td>";
                echo "</tr>";
              }
              mysqli_stmt_close($stmt_cart);
              mysqli_close($link);
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- Modal pentru afișarea ingredientelor din QR Code -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-uppercase" style="color: #ffb03b;">Ingrediente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <h4 id="modalFoodName" class="fw-bold mb-3"></h4>
          <img id="modalQrImg" src="" class="img-fluid shadow rounded mb-3" style="max-width: 200px;" alt="QR Code">
          <div class="p-3 bg-light rounded text-start border">
            <h6 class="fw-bold"><i class="bi bi-card-text text-warning"></i> Detalii:</h6>
            <p id="modalIngredients" class="text-muted small mb-0"></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripturi JavaScript externe -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Includerea fișierului de validare și funcții JS -->
  <script src="assets/js/main.js"></script>

  <!-- Script inline pentru populating modal -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var qrModal = document.getElementById('qrModal');
      if (qrModal) {
        qrModal.addEventListener('show.bs.modal', function (event) {
          var triggerImage = event.relatedTarget;
          document.getElementById('modalQrImg').src = triggerImage.getAttribute('data-qr-src');
          document.getElementById('modalFoodName').textContent = triggerImage.getAttribute('data-food-name');
          document.getElementById('modalIngredients').textContent = triggerImage.getAttribute('data-ingredients');
        });
      }
    });
  </script>
</body>

</html>
