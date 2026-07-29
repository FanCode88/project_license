<?php
require_once('auth.php');
require_once('connection/config.php');

// Conectare MySQL folosind PDO
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Failed to connect to server: " . $e->getMessage());
}

// Obținere ID membru din sesiune
$memberId = $_SESSION['SESS_MEMBER_ID'];

// Preluare articole din coș bazat pe flag=0 (folosind Prepared Statements)
$flag_0 = 0;
$stmt_items = $pdo->prepare("SELECT * FROM cart_details WHERE member_id = :memberId AND flag = :flag");
$stmt_items->execute([
  'memberId' => $memberId,
  'flag' => $flag_0
]);
$num_items = $stmt_items->rowCount();

// Preluare număr mesaje din tabelul messages
$stmt_messages = $pdo->query("SELECT * FROM messages");
$num_messages = $stmt_messages->rowCount();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Food Plaza: My Profile</title>

  <!-- Google Fonts -->
  <link
    href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,600,600i,700,700i|Satisfy|Comic+Neue:300,300i,400,400i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="member-index.css" rel="stylesheet">

  <script language="JavaScript" src="validation/user.js"></script>
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
          <li><a class="nav-link scrollto" href="foodzone.php">Food Zone</a></li>
          <li><a class="nav-link scrollto" href="specialdeals.php">Special Deals</a></li>
          <li><a class="nav-link scrollto active" href="member-index.php">My Account</a></li>
          <li><a class="nav-link scrollto" href="contactus.php">Contact Us</a></li>
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

        <!-- Header Profil -->
        <div class="welcome-header">
          <h1><i class="bi bi-person-badge"></i> My Profile</h1>
          <p>Here you can change your password and also add a billing or delivery address. The delivery address will be
            used to bill your food orders as well as providing us with details on where to deliver your food. For more
            information <a href="contactus.php" style="color:#ff7a18; text-decoration:none;">Click Here</a> to contact
            us.</p>
        </div>

        <!-- Meniu Navigare Secundar -->
        <div class="nav-menu">
          <a href="member-index.php" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Home
          </a>
          <a href="cart.php" class="btn btn-warning-custom">
            <i class="bi bi-cart"></i> Cart
            <span class="badge-cart"><?php echo $num_items; ?></span>
          </a>
          <a href="inbox.php" class="btn btn-outline-secondary">
            <i class="bi bi-envelope"></i> Inbox
            <span class="badge-cart"><?php echo $num_messages; ?></span>
          </a>
          <a href="tables.php" class="btn btn-outline-secondary">
            <i class="bi bi-calendar-check"></i> Tables
          </a>
          <a href="partyhalls.php" class="btn btn-outline-secondary">
            <i class="bi bi-building"></i> Party-Halls
          </a>
          <a href="ratings.php" class="btn btn-outline-secondary">
            <i class="bi bi-star"></i> Rate Us
          </a>
          <a href="logout.php" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </div>

        <hr style="margin: 30px 0;">

        <!-- Formulare: Schimbare Parolă & Adăugare Adresă -->
        <div class="row g-4 justify-content-center">

          <!-- Secțiune Schimbare Parolă -->
          <div class="col-lg-6">
            <div class="p-4 border rounded bg-light h-100">
              <h3 class="mb-3 text-center" style="font-size: 1.25rem; font-weight: 600; color: #333;">CHANGE YOUR
                PASSWORD</h3>
              <form id="updateForm" name="updateForm" method="post"
                action="update-exec.php?id=<?php echo $_SESSION['SESS_MEMBER_ID']; ?>"
                onsubmit="return updateValidate(this)">
                <div class="text-center mb-3">
                  <span class="text-danger">*</span> Required fields
                </div>
                <div class="mb-3">
                  <label for="opassword" class="form-label"><span class="text-danger">*</span> Old Password</label>
                  <input name="opassword" type="password" class="form-control" id="opassword" required />
                </div>
                <div class="mb-3">
                  <label for="npassword" class="form-label"><span class="text-danger">*</span> New Password</label>
                  <input name="npassword" type="password" class="form-control" id="npassword" required />
                </div>
                <div class="mb-3">
                  <label for="cpassword" class="form-label"><span class="text-danger">*</span> Confirm New
                    Password</label>
                  <input name="cpassword" type="password" class="form-control" id="cpassword" required />
                </div>
                <div class="text-center">
                  <input type="submit" name="Submit" value="Change" class="btn btn-warning-custom px-4" />
                </div>
              </form>
            </div>
          </div>

          <!-- Secțiune Adăugare Adresă -->
          <div class="col-lg-6">
            <div class="p-4 border rounded bg-light h-100">
              <h3 class="mb-3 text-center" style="font-size: 1.25rem; font-weight: 600; color: #333;">ADD DELIVERY /
                BILLING ADDRESS</h3>
              <form id="billingForm" name="billingForm" method="post"
                action="billing-exec.php?id=<?php echo $_SESSION['SESS_MEMBER_ID']; ?>"
                onsubmit="return billingValidate(this)">
                <div class="text-center mb-3">
                  <span class="text-danger">*</span> Required fields
                </div>
                <div class="mb-3">
                  <label for="sAddress" class="form-label"><span class="text-danger">*</span> Street Address</label>
                  <input name="sAddress" type="text" class="form-control" id="sAddress" required />
                </div>
                <div class="mb-3">
                  <label for="box" class="form-label"><span class="text-danger">*</span> P.O. Box No</label>
                  <input name="box" type="text" class="form-control" id="box" required />
                </div>
                <div class="mb-3">
                  <label for="city" class="form-label"><span class="text-danger">*</span> City</label>
                  <input name="city" type="text" class="form-control" id="city" required />
                </div>
                <div class="mb-3">
                  <label for="mNumber" class="form-label"><span class="text-danger">*</span> Mobile No</label>
                  <input name="mNumber" type="text" class="form-control" id="mNumber" required />
                </div>
                <div class="mb-3">
                  <label for="lNumber" class="form-label">Landline No</label>
                  <input name="lNumber" type="text" class="form-control" id="lNumber" />
                </div>
                <div class="text-center">
                  <input type="submit" name="Submit" value="Add" class="btn btn-warning-custom px-4" />
                </div>
              </form>
            </div>
          </div>

        </div>

      </div>
      <!-- ===== SFÂRȘIT MAIN-CARD ===== -->

      <!-- Footer -->
      <div class="footer-custom">
        <h3>Deluxe Restaurant</h3>
        <p>Cel mai bun restaurant multicuisine premium din Craiova, România. Aducem la masă arome din întreaga lume.</p>
        <div class="bottom_menu mb-2" style="font-size: 0.9rem;">
          <a href="index.php" style="color:#ff7a18; text-decoration:none;">Home Page</a> |
          <a href="aboutus.php" style="color:#ff7a18; text-decoration:none;">About Us</a> |
          <a href="specialdeals.php" style="color:#ff7a18; text-decoration:none;">Special Deals</a> |
          <a href="foodzone.php" style="color:#ff7a18; text-decoration:none;">Food Zone</a> |
          <a href="#" style="color:#ff7a18; text-decoration:none;">Affiliate Program</a> |
          <a href="admin/index.php" target="_blank" style="color:#ff7a18; text-decoration:none;">Administrator</a>
        </div>
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
