<?php
//Start session
session_start();

//checking connection and connecting to a database
require_once('connection/config.php');

//Connect to mysql server using PDO (modern and secure approach)
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Failed to connect to server: " . $e->getMessage());
}

$question = "";
$account_exists = false;

// Handle Email Check (Submit)
if (isset($_POST['Submit'])) {
  $email = trim($_POST['email']);

  try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE login = :email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
      $_SESSION['member_id'] = $row['member_id'];
      session_write_close();
      $question_id = $row['question_id'];

      $stmt_q = $pdo->prepare("SELECT * FROM questions WHERE question_id = :question_id");
      $stmt_q->execute(['question_id' => $question_id]);
      $question_row = $stmt_q->fetch(PDO::FETCH_ASSOC);

      if ($question_row && !empty($question_row['question_text'])) {
        $question = $question_row['question_text'];
        $account_exists = true;
      }
    }
  } catch (PDOException $e) {
    // Handle error if needed
  }
}

// Handle Password Change (Change)
if (isset($_POST['Change'])) {
  if (isset($_SESSION['member_id']) && trim($_SESSION['member_id']) != '') {
    $member_id = $_SESSION['member_id'];
    $answer = trim($_POST['answer']);
    $new_password = trim($_POST['new_password']);

    // Hash using modern password_hash instead of md5
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    // Note: For security, security answers should also be hashed securely,
    // but keeping compatibility with existing MD5 if legacy requires it:
    $hashed_answer = md5($answer);

    try {
      $stmt_update = $pdo->prepare("UPDATE members SET passwd = :passwd WHERE member_id = :member_id AND answer = :answer");
      $result = $stmt_update->execute([
        'passwd' => $hashed_password,
        'member_id' => $member_id,
        'answer' => $hashed_answer
      ]);

      unset($_SESSION['member_id']);

      if ($stmt_update->rowCount() > 0) {
        header("Location: reset-success.php");
        exit;
      } else {
        header("Location: reset-failed.php");
        exit;
      }
    } catch (PDOException $e) {
      unset($_SESSION['member_id']);
      header("Location: reset-failed.php");
      exit;
    }
  } else {
    unset($_SESSION['member_id']);
    header("Location: reset-failed.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Food Plaza: Password Reset</title>

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
          <li><a class="nav-link scrollto" href="index.php#login">Login</a></li>
          <li><a class="nav-link scrollto" href="index.php#menu">Our Food</a></li>
          <li><a class="nav-link scrollto" href="index.php">Specials</a></li>
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

      <div class="main-card">
        <div class="welcome-header text-center mb-4">
          <h1><i class="bi bi-shield-lock"></i> Password Reset</h1>
          <p>Enter your account email to retrieve your security question and update your password.</p>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-6">
            <div class="p-4 border rounded bg-light mb-4">
              <form name="passwordResetForm" id="passwordResetForm" method="post" action="password-reset.php"
                onsubmit="return passwordResetValidate(this)">
                <div class="mb-3">
                  <label for="email" class="form-label"><b>Account Email</b></label>
                  <div class="input-group">
                    <input name="email" type="email" class="form-control" id="email" required />
                    <input type="submit" name="Submit" value="Check" class="btn btn-warning-custom" />
                  </div>
                </div>
              </form>

              <?php
              if (isset($_POST['Submit'])) {
                if ($account_exists) {
                  echo '<div class="alert alert-info mt-3 mb-0">';
                  echo '<b>Your Member ID:</b> ' . htmlspecialchars($_SESSION['member_id']) . '<br>';
                  echo '<b>Your Security Question:</b> ' . htmlspecialchars($question);
                  echo '</div>';
                } else {
                  echo '<div class="alert alert-danger mt-3 mb-0"><b>Your Security Question:</b> THIS ACCOUNT DOES NOT EXIST! PLEASE CHECK YOUR EMAIL AND TRY AGAIN.</div>';
                }
              }
              ?>
            </div>

            <div class="p-4 border rounded bg-light">
              <form name="passwordResetForm2" id="passwordResetForm2" method="post" action="password-reset.php"
                onsubmit="return passwordResetValidate_2(this)">
                <div class="text-center mb-3">
                  <span class="text-danger">*</span> Required fields
                </div>

                <div class="mb-3">
                  <label for="answer" class="form-label"><span class="text-danger">*</span> Your Security Answer</label>
                  <input name="answer" type="text" class="form-control" id="answer" required />
                </div>

                <div class="mb-3">
                  <label for="new_password" class="form-label"><span class="text-danger">*</span> New Password</label>
                  <input name="new_password" type="password" class="form-control" id="new_password" required />
                </div>

                <div class="mb-3">
                  <label for="confirm_new_password" class="form-label"><span class="text-danger">*</span> Confirm New
                    Password</label>
                  <input name="confirm_new_password" type="password" class="form-control" id="confirm_new_password"
                    required />
                </div>

                <div class="d-flex justify-content-between">
                  <input type="reset" name="Reset" value="Clear Fields" class="btn btn-outline-secondary px-4" />
                  <input type="submit" name="Change" value="Change Password" class="btn btn-warning-custom px-4" />
                </div>
              </form>
            </div>

          </div>
        </div>

      </div>

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
