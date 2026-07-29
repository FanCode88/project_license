<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once('auth.php');
require_once('qrlib.php');
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

if (!isset($_SESSION['SESS_MEMBER_ID'])) {
  die("Eroare: Sesiunea a expirat sau nu ești autentificat.");
}

$member_id = (int) $_SESSION['SESS_MEMBER_ID'];

// Mesaje Notificare
$success_message = '';
$error_message = '';

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

// Acțiunea Anulare Comandă
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
  $order_id = (int) $_GET['id'];

  $stmt_sel = mysqli_prepare($link, "SELECT cart_id FROM orders_details WHERE order_id = ? AND member_id = ?");
  mysqli_stmt_bind_param($stmt_sel, "ii", $order_id, $member_id);
  mysqli_stmt_execute($stmt_sel);
  $res_sel = mysqli_stmt_get_result($stmt_sel);

  if ($r = mysqli_fetch_assoc($res_sel)) {
    $cart_id = $r['cart_id'];

    $stmt_del1 = mysqli_prepare($link, "DELETE FROM cart_details WHERE cart_id = ?");
    mysqli_stmt_bind_param($stmt_del1, "i", $cart_id);
    mysqli_stmt_execute($stmt_del1);
    mysqli_stmt_close($stmt_del1);

    $stmt_del2 = mysqli_prepare($link, "DELETE FROM orders_details WHERE order_id = ? AND member_id = ?");
    mysqli_stmt_bind_param($stmt_del2, "ii", $order_id, $member_id);
    mysqli_stmt_execute($stmt_del2);
    mysqli_stmt_close($stmt_del2);
  }
  mysqli_stmt_close($stmt_sel);

  header("Location: cont.php");
  exit();
}

// Acțiunea Recomandă
if (isset($_GET['action']) && $_GET['action'] === 'order_again' && isset($_GET['id'])) {
  $order_id = (int) $_GET['id'];

  $stmt_again = mysqli_prepare($link, "
        SELECT orders_details.cart_id, cart_details.food_id, cart_details.quantity_id, food_details.food_price, quantities.quantity_value
        FROM orders_details
        INNER JOIN cart_details ON orders_details.cart_id = cart_details.cart_id
        INNER JOIN food_details ON cart_details.food_id = food_details.food_id
        INNER JOIN quantities ON cart_details.quantity_id = quantities.quantity_id
        WHERE orders_details.order_id = ? AND orders_details.member_id = ?
    ");
  mysqli_stmt_bind_param($stmt_again, "ii", $order_id, $member_id);
  mysqli_stmt_execute($stmt_again);
  $res_again = mysqli_stmt_get_result($stmt_again);

  if ($r = mysqli_fetch_assoc($res_again)) {
    $food_id = $r['food_id'];
    $quantity_id = $r['quantity_id'];
    $total = $r['food_price'] * $r['quantity_value'];
    $flag_zero = 0;

    $stmt_ins = mysqli_prepare($link, "INSERT INTO cart_details(member_id, food_id, quantity_id, total, flag) VALUES(?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_ins, "iiidi", $member_id, $food_id, $quantity_id, $total, $flag_zero);
    mysqli_stmt_execute($stmt_ins);
    mysqli_stmt_close($stmt_ins);

    $stmt_del = mysqli_prepare($link, "DELETE FROM orders_details WHERE order_id = ? AND member_id = ?");
    mysqli_stmt_bind_param($stmt_del, "ii", $order_id, $member_id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
  }
  mysqli_stmt_close($stmt_again);

  header("Location: cont.php");
  exit();
}

// Istoric Comenzi Finalizate (flag=1)
$stmt_orders = mysqli_prepare($link, "
    SELECT orders_details.order_id, food_details.food_name, food_details.food_photo,
           categories.category_name, food_details.food_price, quantities.quantity_value,
           cart_details.total, orders_details.delivery_date
    FROM orders_details
    INNER JOIN cart_details ON orders_details.cart_id = cart_details.cart_id
    INNER JOIN food_details ON cart_details.food_id = food_details.food_id
    INNER JOIN categories ON food_details.food_category = categories.category_id
    INNER JOIN quantities ON cart_details.quantity_id = quantities.quantity_id
    WHERE orders_details.member_id = ? AND cart_details.flag = 1
");
mysqli_stmt_bind_param($stmt_orders, "i", $member_id);
mysqli_stmt_execute($stmt_orders);
$result = mysqli_stmt_get_result($stmt_orders);

// Numărare elemente coș
$flag_0 = 0;
$stmt_items = mysqli_prepare($link, "SELECT COUNT(*) as count FROM cart_details WHERE member_id = ? AND flag = ?");
mysqli_stmt_bind_param($stmt_items, "ii", $member_id, $flag_0);
mysqli_stmt_execute($stmt_items);
$num_items = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_items))['count'] ?? 0;
mysqli_stmt_close($stmt_items);

// Numărare mesaje
$msg_res = mysqli_query($link, "SELECT COUNT(*) as count FROM messages");
$num_messages = mysqli_fetch_assoc($msg_res)['count'] ?? 0;

// Monedă
$flag_1 = 1;
$stmt_curr = mysqli_prepare($link, "SELECT currency_symbol FROM currencies WHERE flag = ?");
mysqli_stmt_bind_param($stmt_curr, "i", $flag_1);
mysqli_stmt_execute($stmt_curr);
$curr_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_curr));
$currency_symbol = $curr_row['currency_symbol'] ?? '$';
mysqli_stmt_close($stmt_curr);

$qr_dir = 'QRimg/';
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

  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700|Satisfy|Comic+Neue:300,400,700"
    rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
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
    <section class="breadcrumbs mb-4">
      <div class="container">
        <h2>Dashboard Client</h2>
      </div>
    </section>

    <section class="inner-page pt-0">
      <div class="container">
        <div id="center">
          <?php echo $success_message; ?>
          <?php echo $error_message; ?>

          <div class="alert alert-light border shadow-sm p-4 mb-4 text-center text-md-start">
            <h1 class="display-6 fw-bold">Welcome, <span
                class="text-warning"><?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME'] ?? ''); ?></span>!</h1>
            <p class="text-muted">Here you can view order history, cancel pending requests, and manage table
              reservations.</p>
          </div>

          <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start mb-4">
            <a href="member-profile.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-person shadow-sm"></i>
              My Profile</a>
            <a href="cart.php" class="btn btn-warning text-white btn-sm position-relative">
              <i class="bi bi-cart"></i> Cart
              <span class="badge bg-danger ms-1"><?php echo $num_items; ?></span>
            </a>
            <a href="inbox.php" class="btn btn-outline-secondary btn-sm position-relative">
              <i class="bi bi-envelope"></i> Inbox
              <span class="badge bg-danger ms-1"><?php echo $num_messages; ?></span>
            </a>
            <a href="tables.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-calendar-check"></i>
              Tables</a>
            <a href="partyhalls.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-building"></i>
              Party-Halls</a>
            <a href="ratings.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-star"></i> Rate Us</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
          </div>

          <div class="text-center my-4">
            <a href="foodzone.php" class="btn btn-success btn-lg px-4 shadow-sm fw-bold">Order More Food!</a>
          </div>

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
                  while ($row = mysqli_fetch_assoc($result)) {
                    $order_id = $row['order_id'];
                    $food_name = $row['food_name'];
                    $food_photo = $row['food_photo'];
                    $category_name = $row['category_name'];
                    $food_price = (float) $row['food_price'];
                    $quantity_value = $row['quantity_value'];
                    $total = (float) $row['total'];
                    $delivery_date = $row['delivery_date'];

                    $photo_encoded = str_replace(' ', '%20', $food_photo);
                    $string = $food_name . " x" . $quantity_value . " Total: " . $total;
                    $file = "QRimg/qr" . $order_id . ".png";

                    if (!empty($string)) {
                      QRcode::png($string, $file, 'H', 4, 2);
                    }

                    echo "<tr>";
                    echo "<td class='fw-bold'>#" . htmlspecialchars($order_id) . "</td>";
                    echo "<td><a href='images/" . htmlspecialchars($photo_encoded) . "' target='_blank'><img src='images/" . htmlspecialchars($photo_encoded) . "' class='img-thumbnail' style='width:60px; height:50px; object-fit:cover;'></a></td>";
                    echo "<td class='fw-bold text-start'>" . htmlspecialchars($food_name) . "</td>";
                    echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($category_name) . "</span></td>";
                    echo "<td class='text-muted'>" . htmlspecialchars($currency_symbol) . number_format($food_price, 2) . "</td>";
                    echo "<td>" . htmlspecialchars($quantity_value) . "</td>";
                    echo "<td class='text-danger fw-bold'>" . htmlspecialchars($currency_symbol) . number_format($total, 2) . "</td>";
                    echo "<td class='small'>" . htmlspecialchars($delivery_date) . "</td>";
                    echo "<td><a href='cont.php?action=cancel&id=" . htmlspecialchars($order_id) . "' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Cancel</a></td>";
                    echo "<td><a href='" . htmlspecialchars($file) . "' target='_blank'><img src='" . htmlspecialchars($file) . "' class='img-thumbnail shadow-sm' style='width:50px; height:50px;'></a></td>";
                    echo "</tr>";
                  }
                  mysqli_stmt_close($stmt_orders);
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

  <footer id="footer" class="mt-5">
    <div class="container py-4">
      <h3>Deluxe Restaurant</h3>
      <p class="text-muted small">Best premium multicuisine Restaurant in Craiova, Romania. Flavour from around the
        world straight to your table.</p>
      <div class="copyright pt-3 border-top text-secondary">
        &copy; Copyright <strong><span>Deluxe</span></strong>. 2026 Saceanu Ionut Sorin All Rights Reserved
      </div>
      <div class="credits text-muted xsmall text-secondary">
        Designed by <a href="#" class="text-warning text-decoration-none">Saceanu Ionut Sorin</a>
      </div>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>
