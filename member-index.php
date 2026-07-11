<?php
require_once('auth.php');
require_once('qrlib.php');
require_once('connection/config.php');

// Conectare MySQL (folosește MySQLi pentru compatibilitate)
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
  die('Failed to connect to server: ' . mysql_error());
}
$db = mysql_select_db(DB_DATABASE);
if (!$db) {
  die("Unable to select database");
}

$memberId = $_SESSION['SESS_MEMBER_ID'];

// Istoric comenzi
$result = mysql_query("SELECT * FROM orders_details,cart_details,food_details,categories,quantities,members
                       WHERE members.member_id='$memberId'
                       AND orders_details.member_id='$memberId'
                       AND orders_details.cart_id=cart_details.cart_id
                       AND cart_details.food_id=food_details.food_id
                       AND food_details.food_category=categories.category_id
                       AND cart_details.quantity_id=quantities.quantity_id")
  or die("There are no records to display ... \n" . mysql_error());

// Număr articole în coș
$flag_0 = 0;
$items = mysql_query("SELECT * FROM cart_details WHERE member_id='$memberId' AND flag='$flag_0'")
  or die("Something is wrong ... \n" . mysql_error());
$num_items = mysql_num_rows($items);

// Număr mesaje
$messages = mysql_query("SELECT * FROM messages")
  or die("Something is wrong ... \n" . mysql_error());
$num_messages = mysql_num_rows($messages);

// Monedă activă
$flag_1 = 1;
$currencies = mysql_query("SELECT * FROM currencies WHERE flag='$flag_1'")
  or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");

// Rezervări
$reservations = mysql_query("SELECT r.*, t.table_name
                             FROM reservations_details r
                             LEFT JOIN tables t ON r.table_id = t.table_id
                             WHERE r.member_id = '$memberId'
                             ORDER BY r.Reserve_Date DESC")
  or die("Eroare la rezervări: " . mysql_error());
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Food Plaza: Member Home</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,600,600i,700,700i|Satisfy|Comic+Neue:300,300i,400,400i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
   <link href="member-index.css" rel="stylesheet">

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
    </div>
  </header>

  <!-- ======= Main Content ======= -->
  <main id="main" style="margin-top: 120px;">
    <div class="page-wrapper">

      <!-- ===== CARD PRINCIPAL ===== -->
      <div class="main-card">

        <!-- Welcome Header -->
        <div class="welcome-header">
          <h1>👋 Bun venit, <span><?php echo $_SESSION['SESS_FIRST_NAME']; ?></span>!</h1>
          <p>Aici poți vizualiza istoricul comenzilor, rezervările și poți gestiona contul tău.</p>
        </div>

        <!-- Meniu Navigare -->
        <div class="nav-menu">
          <a href="member-profile.php" class="btn btn-outline-secondary">
            <i class="bi bi-person"></i> Profil
          </a>
          <a href="cart.php" class="btn btn-warning-custom">
            <i class="bi bi-cart"></i> Coș
            <span class="badge-cart"><?php echo $num_items; ?></span>
          </a>
          <a href="inbox.php" class="btn btn-outline-secondary">
            <i class="bi bi-envelope"></i> Mesaje
            <span class="badge-cart"><?php echo $num_messages; ?></span>
          </a>
          <a href="tables.php" class="btn btn-outline-secondary">
            <i class="bi bi-calendar-check"></i> Mese
          </a>
          <a href="partyhalls.php" class="btn btn-outline-secondary">
            <i class="bi bi-building"></i> Săli
          </a>
          <a href="ratings.php" class="btn btn-outline-secondary">
            <i class="bi bi-star"></i> Evaluări
          </a>
          <a href="logout.php" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </div>

        <!-- Buton Comandă -->
        <div class="text-center mb-4">
          <a href="foodzone.php" class="btn-order-more">
            <i class="bi bi-plus-circle"></i> Comandă mai multe produse!
          </a>
        </div>

        <!-- ===== SECȚIUNE ISTORIC COMENZI ===== -->
        <div class="section-title">
          <i class="bi bi-clock-history"></i>
          Istoric Comenzi
          <span class="subtitle">— vezi toate comenzile tale</span>
        </div>

        <div class="table-responsive-custom">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>ID Comandă</th>
                <th>Foto</th>
                <th>Produs</th>
                <th>Categorie</th>
                <th>Preț</th>
                <th>Cantitate</th>
                <th>Total</th>
                <th>Data Livrare</th>
                <th>Acțiuni</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $symbol = mysql_fetch_assoc($currencies);
              while ($row = mysql_fetch_array($result)) {
                echo "<tr>";
                echo "<td><strong>#" . $row['order_id'] . "</strong></td>";
                echo '<td><a href="images/' . $row['food_photo'] . '" target="_blank">
                                        <img src="images/' . $row['food_photo'] . '" alt="Food">
                                      </a></td>';
                echo "<td><strong>" . $row['food_name'] . "</strong></td>";
                echo "<td><span class='badge bg-secondary'>" . $row['category_name'] . "</span></td>";
                echo "<td>" . $symbol['currency_symbol'] . number_format($row['food_price'], 2) . "</td>";
                echo "<td><span class='badge bg-dark'>" . $row['quantity_value'] . "</span></td>";
                echo "<td><strong style='color:#ff7a18;'>" . $symbol['currency_symbol'] . number_format($row['total'], 2) . "</strong></td>";
                echo "<td>" . date('d.m.Y', strtotime($row['delivery_date'])) . "</td>";
                echo '<td>
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            <a href="cont.php?action=cancel&id=' . $row['order_id'] . '"
                                               class="btn btn-sm btn-outline-danger-custom"
                                               onclick="return confirm(\'Ești sigur că vrei să anulezi această comandă?\')">
                                                <i class="bi bi-x-circle"></i> Anulează
                                            </a>
                                            <a href="cont.php?action=order_again&id=' . $row['order_id'] . '"
                                               class="btn btn-sm btn-success-custom"
                                               onclick="return confirm(\'Ești sigur că vrei să comanzi din nou acest produs?\')">
                                                <i class="bi bi-arrow-repeat"></i> Repetă
                                            </a>
                                        </div>
                                      </td>';
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <hr style="margin: 35px 0;">

        <!-- ===== SECȚIUNE REZERVĂRI ===== -->
        <div class="section-title">
          <i class="bi bi-calendar2-week"></i>
          Rezervări Mese
          <span class="subtitle">— vezi rezervările tale</span>
        </div>

        <div class="table-responsive-custom">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>ID Rezervare</th>
                <th>Masa</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Acțiune</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($res = mysql_fetch_array($reservations)) {
                echo "<tr>";
                echo "<td><strong>#" . $res['ReservationID'] . "</strong></td>";
                echo "<td>" . htmlspecialchars($res['table_name']) . "</td>";
                echo "<td>" . date('d.m.Y', strtotime($res['Reserve_Date'])) . "</td>";
                echo "<td>" . date('H:i', strtotime($res['Reserve_Time'])) . "</td>";
                echo '<td>
    <a href="javascript:void(0);"
       class="btn btn-sm btn-outline-danger-custom"
       onclick="cancelReservation(' . $res['ReservationID'] . ', this)">
        <i class="bi bi-x-circle"></i> Anulează
    </a>
</td>';
                echo "</tr>";
              }

              // Eliberare resurse
              mysql_free_result($result);
              mysql_free_result($reservations);
              mysql_close($link);
              ?>
            </tbody>
          </table>
        </div>

      </div>
      <!-- ===== SFÂRȘIT MAIN-CARD ===== -->

      <!-- Footer -->
      <div class="footer-custom">
        <h3>Deluxe Restaurant</h3>
        <p>Cel mai bun restaurant multicuisine premium din Craiova, România. Aducem la masă arome din întreaga lume.</p>
        <div class="copyright">
          &copy; Copyright <strong>Deluxe</strong>. 2026 Toate drepturile rezervate.
        </div>
        <div class="credits" style="font-size:0.8rem; color:#aaa;">
          Designed by <a href="#" style="color:#ff7a18; text-decoration:none;">Saceanu Ionut Sorin</a>
        </div>
      </div>

    </div>
  </main>

  <!-- ======= Back to Top ======= -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- ======= JS Files ======= -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>



</body>

</html>
