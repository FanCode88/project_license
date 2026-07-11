<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('auth.php');
require_once('qrlib.php');
require_once('connection/config.php');

// Conexiune modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// ==================== ADAUGĂ MESAJELE AICI ====================
// Afișează mesaje de succes/eroare
if (isset($_GET['success'])) {
  $success_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Comanda a fost plasată cu succes!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

if (isset($_GET['error'])) {
  $error_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> ' . htmlspecialchars($_GET['error']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
// ==================== SFÂRȘIT MESAJE ====================

if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {

  $order_id  = (int)$_GET['id'];
  $member_id = $_SESSION['SESS_MEMBER_ID'];

  // aflăm cart_id
  $q = mysqli_query($link, "
        SELECT cart_id
        FROM orders_details
        WHERE order_id='$order_id'
        AND member_id='$member_id'
    ");

  if ($r = mysqli_fetch_assoc($q)) {

    $cart_id = $r['cart_id'];

    // ștergem produsul din cart
    mysqli_query($link, "
            DELETE FROM cart_details
            WHERE cart_id='$cart_id'
        ");

    // ștergem comanda
    mysqli_query($link, "
            DELETE FROM orders_details
            WHERE order_id='$order_id'
            AND member_id='$member_id'
        ");
  }

  header("Location: cont.php");
  exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'order_again' && isset($_GET['id'])) {

  $order_id  = (int)$_GET['id'];
  $member_id = $_SESSION['SESS_MEMBER_ID'];

  $q = mysqli_query($link, "
        SELECT cart_id, food_id, quantity_id
        FROM orders_details
        INNER JOIN cart_details
            ON orders_details.cart_id = cart_details.cart_id
        WHERE order_id='$order_id'
        AND member_id='$member_id'
    ");

  if ($r = mysqli_fetch_assoc($q)) {

    // adaugă produsul în coș
    mysqli_query($link, "
            INSERT INTO cart_details(member_id,food_id,quantity_id,flag)
            VALUES(
                '$member_id',
                '" . $r['food_id'] . "',
                '" . $r['quantity_id'] . "',
                '0'
            )
        ");

    // șterge produsul din istoric
    mysqli_query($link, "
            DELETE FROM orders_details
            WHERE order_id='$order_id'
            AND member_id='$member_id'
        ");
  }

  header("Location: cont.php");
  exit;
}

$memberId = $_SESSION['SESS_MEMBER_ID'];

// Interogare istoric comenzi - AFIȘEAZĂ DOAR COMENZILE FINALIZATE (flag=1)
$result = mysqli_query($link, "SELECT * FROM orders_details,cart_details,food_details,categories,quantities,members
                               WHERE members.member_id='$memberId'
                               AND orders_details.member_id='$memberId'
                               AND orders_details.cart_id=cart_details.cart_id
                               AND cart_details.food_id=food_details.food_id
                               AND food_details.food_category=categories.category_id
                               AND cart_details.quantity_id=quantities.quantity_id
                               AND cart_details.flag='1'")  // Adaugă această condiție
  or die("There are no records to display ... \n" . mysqli_error($link));

// Numărare elemente din coș
$flag_0 = 0;
$items = mysqli_query($link, "SELECT * FROM cart_details WHERE member_id='$memberId' AND flag='$flag_0'")
  or die(mysqli_error($link));
$num_items = mysqli_num_rows($items);

// Numărare mesaje active
$messages = mysqli_query($link, "SELECT * FROM messages") or die(mysqli_error($link));
$num_messages = mysqli_num_rows($messages);

// Preluare monedă activă
$flag_1 = 1;
$currencies = mysqli_query($link, "SELECT * FROM currencies WHERE flag='$flag_1'")
  or die(mysqli_error($link));
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
          <li><a class="nav-link scrollto" href="/RM/admin/access-denied.php">Specials</a></li>
          <li><a class="nav-link scrollto active" href="cont.php">Account</a></li>
          <li><a class="nav-link scrollto" href="index.php#chefs">Chefs</a></li>
          <li><a class="nav-link scrollto" href="index.php#gallery">Gallery</a></li>
          <li><a class="nav-link scrollto" href="contactus.php">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>

      <a href="logout.php" class="nav-link scrollto text-danger fw-bold" style="padding: 10px 20px;">Logout</a>
    </div>
  </header>

  <main id="main" style="margin-top: 110px;">
    <!-- ======= Breadcrumbs ======= -->
    <section class="breadcrumbs mb-4">
      <div class="container">
        <h2>Dashboard Client</h2>
      </div>
    </section>

    <section class="inner-page pt-0">
      <div class="container">

        <div id="center">
          <!-- ==================== AFIȘEAZĂ MESAJELE AICI ==================== -->
          <?php
          if (isset($success_message)) {
            echo $success_message;
          }
          if (isset($error_message)) {
            echo $error_message;
          }
          ?>
          <!-- ==================== SFÂRȘIT MESAJE ==================== -->

          <div class="alert alert-light border shadow-sm p-4 mb-4 text-center text-md-start">
            <h1 class="display-6 fw-bold">Welcome, <span class="text-warning"><?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']); ?></span>!</h1>
            <p class="text-muted">Here you can view order history, cancel pending requests, and manage table reservations.</p>
          </div>

          <!-- Meniu de Navigare Intern Modern sub formă de Butoane/Badges -->
          <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start mb-4">
            <a href="member-profile.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-person shadow-sm"></i> My Profile</a>
            <a href="cart.php" class="btn btn-warning text-white btn-sm position-relative">
              <i class="bi bi-cart"></i> Cart
              <span class="badge bg-danger ms-1"><?php echo $num_items; ?></span>
            </a>
            <a href="inbox.php" class="btn btn-outline-secondary btn-sm position-relative">
              <i class="bi bi-envelope"></i> Inbox
              <span class="badge bg-danger ms-1"><?php echo $num_messages; ?></span>
            </a>
            <a href="tables.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-calendar-check"></i> Tables</a>
            <a href="partyhalls.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-building"></i> Party-Halls</a>
            <a href="ratings.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-star"></i> Rate Us</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
          </div>

          <div class="text-center my-4">
            <a href="foodzone.php" class="btn btn-success btn-lg px-4 shadow-sm fw-bold">Order More Food!</a>
          </div>

          <!-- Tabel Istoric Comenzi Responsiv -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold text-center py-3">
              <i class="bi bi-clock-history"></i> ORDER HISTORY & INVOICES
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-bordered text-center align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Photo</th>
                    <th>Food Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total Cost</th>
                    <th>Delivery Date</th>
                    <th>Action</th>
                    <th>QR Code</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Ne asigurăm că directorul pentru coduri QR există
                  if (!file_exists('QRimg')) {
                    mkdir('QRimg', 0777, true);
                  }

                  $symbol = mysqli_fetch_assoc($currencies);

                  while ($row = mysqli_fetch_array($result)) {
                    $photo_encoded = str_replace(' ', '%20', $row['food_photo']);

                    // Configurare text și fișier pentru codul QR
                    $string = $row['food_name'] . " x" . $row['quantity_value'] . " Total: " . $row['total'];
                    $file = "QRimg/qr" . $row['order_id'] . ".png";

                    // Generare QR (folosind librăria inclusă)
                    QRcode::png($string, $file, 'H', 4, 2);

                    echo "<tr>";
                    echo "<td class='fw-bold'>#" . $row['order_id'] . "</td>";
                    echo "<td><a href='images/" . $photo_encoded . "' target='_blank'><img src='images/" . $photo_encoded . "' class='img-thumbnail' style='width:60px; height:50px; object-fit:cover;'></a></td>";
                    echo "<td class='fw-bold text-start'>" . htmlspecialchars($row['food_name']) . "</td>";
                    echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($row['category_name']) . "</span></td>";
                    echo "<td class='text-muted'>" . $symbol['currency_symbol'] . number_format($row['food_price'], 2) . "</td>";
                    echo "<td>" . $row['quantity_value'] . "</td>";
                    echo "<td class='text-danger fw-bold'>" . $symbol['currency_symbol'] . number_format($row['total'], 2) . "</td>";
                    echo "<td class='small'>" . $row['delivery_date'] . "</td>";
                    echo "<td><a href='cont.php?action=cancel&id=" . $row['order_id'] . "' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Cancel</a></td>";
                    echo "<td><a href='" . $file . "' target='_blank'><img src='" . $file . "' class='img-thumbnail shadow-sm style='width:50px; height:50px;'></a></td>";
                    echo "</tr>";
                  }
                  mysqli_free_result($result);
                  mysqli_close($link);
                  ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="mt-5">
    <div class="container py-4">
      <h3>Deluxe Restaurant</h3>
      <p class="text-muted small">Best premium multicuisine Restaurant in Craiova, Romania. Flavour from around the world straight to your table.</p>
      <div class="copyright pt-3 border-top text-secondary">
        &copy; Copyright <strong><span>Deluxe</span></strong>. 2026 Saceanu Ionut Sorin All Rights Reserved
      </div>
      <div class="credits text-muted xsmall text-secondary">
        Designed by <a href="#" class="text-warning text-decoration-none">Saceanu Ionut Sorin</a>
      </div>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>
