<?php
require_once('auth.php');
require_once('connection/config.php');

// Conectare securizată la baza de date folosind PDO
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8", DB_USER, DB_PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die('Eroare la conectarea cu serverul: ' . $e->getMessage());
}

// Preluare întrebări de securitate
try {
  $questions_stmt = $pdo->query("SELECT * FROM questions");
  $questions = $questions_stmt->fetchAll();
} catch (PDOException $e) {
  die("Ceva nu a funcționat corect... \n" . $e->getMessage());
}

// Selectare inițială detalii mâncare
try {
  $result_stmt = $pdo->query("SELECT * FROM food_details INNER JOIN categories ON food_details.food_category = categories.category_id");
  $food_items = $result_stmt->fetchAll();
} catch (PDOException $e) {
  die("A apărut o problemă... Echipa noastră lucrează la ea. Vă rugăm reveniți mai târziu.");
}

// Selectare promoții (specials)
try {
  $specials_stmt = $pdo->query("SELECT * FROM specials");
  $specials = $specials_stmt->fetchAll();
} catch (PDOException $e) {
  die("Nu există înregistrări de afișat... \n" . $e->getMessage());
}

// Preluare ID membru din sesiune (Corectat pentru PHP vechi)
$memberId = isset($_SESSION['SESS_MEMBER_ID']) ? $_SESSION['SESS_MEMBER_ID'] : null;

// Gestionare Cookie "Remember Me"
if (isset($_POST['Submit'])) {
  if (!empty($_POST['remember'])) {
    $year = time() + 31536000;
    setcookie('remember_me', $_POST['login'], $year, "/");
  } else {
    if (isset($_COOKIE['remember_me'])) {
      $past = time() - 100;
      setcookie('remember_me', 'gone', $past, "/");
    }
  }
}

// Preluare categorii
try {
  $categories_stmt = $pdo->query("SELECT * FROM categories");
  $categories = $categories_stmt->fetchAll();
} catch (PDOException $e) {
  die("A apărut o problemă la încărcarea categoriilor.");
}

// Definire valută activă
try {
  $currencies_stmt = $pdo->query("SELECT * FROM currencies WHERE flag = 1");
  $symbol = $currencies_stmt->fetch();
} catch (PDOException $e) {
  die("A apărut o problemă la încărcarea valutei.");
}

// Filtrare mâncare după categorie (dacă s-a trimis formularul)
if (isset($_POST['Submit']) && !empty($_POST['category']) && $_POST['category'] !== 'select') {
  $id = trim($_POST['category']);
  try {
    $result_stmt = $pdo->prepare("SELECT * FROM food_details INNER JOIN categories ON food_details.food_category = categories.category_id WHERE food_details.food_category = :id");
    $result_stmt->execute(['id' => $id]);
    $food_items = $result_stmt->fetchAll();
  } catch (PDOException $e) {
    die("Eroare la filtrarea produselor.");
  }
}

// Preluare număr elemente din coș
$num_items = 0;
if ($memberId) {
  try {
    $items_stmt = $pdo->prepare("SELECT COUNT(*) FROM cart_details WHERE member_id = :member_id AND flag = 0");
    $items_stmt->execute(['member_id' => $memberId]);
    $num_items = $items_stmt->fetchColumn();
  } catch (PDOException $e) {
    die("Eroare coș: " . $e->getMessage());
  }
}

// Preluare număr mesaje
try {
  $messages_stmt = $pdo->query("SELECT COUNT(*) FROM messages");
  $num_messages = $messages_stmt->fetchColumn();
} catch (PDOException $e) {
  die("Eroare mesaje: " . $e->getMessage());
}

// Preluare mese disponibile
try {
  $tables_stmt = $pdo->query("SELECT * FROM tables");
  $tables = $tables_stmt->fetchAll();
} catch (PDOException $e) {
  die("Eroare mese: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Deluxe Restaurant - Index</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,600,600i,700,700i|Satisfy|Comic+Neue:300,300i,400,400i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Top Bar ======= -->
  <section id="topbar" class="d-flex align-items-center fixed-top topbar-transparent">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-center justify-content-lg-start">
      <i class="bi bi-phone d-flex align-items-center"><span>+40738740300</span></i>
      <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
    </div>
  </section>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center header-transparent">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

      <div class="logo me-auto">
        <h1><a href="index.php">Deluxe Restaurant</a></h1>
      </div>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="#menu">Our Food</a></li>
          <li><a class="nav-link" href="admin/specials.php">Specials</a></li>

          <?php if (isset($_SESSION['SESS_MEMBER_ID'])): ?>
            <!-- Link-uri vizibile doar cand ESTI logat -->
            <li><a class="nav-link scrollto" href="cont.php">Client Account</a></li>
          <?php else: ?>
            <!-- Link-uri vizibile doar cand NU esti logat -->
            <li><a class="nav-link scrollto" href="#login">Login</a></li>
          <?php endif; ?>

          <li><a class="nav-link scrollto" href="admin/index.php" target="_blank">Admin</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>

      <!-- Butonul din dreapta sus -->
      <?php if (isset($_SESSION['SESS_MEMBER_ID'])): ?>
        <a href="logout.php" class="book-a-table-btn scrollto">Logout</a>
      <?php else: ?>
        <a href="#login" class="book-a-table-btn scrollto">Login</a>
      <?php endif; ?>

    </div>
  </header>

  <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="hero-container">
      <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
        <div class="carousel-inner" role="listbox">

          <!-- Slide 1 -->
          <div class="carousel-item active" style="background: url(assets/img/slide/slide-1.jpg);">
            <div class="carousel-container">
              <div class="carousel-content">
                <h2 class="animate__animated animate__fadeInDown"><span>Deluxe</span> Restaurant</h2>
                <p class="animate__animated animate__fadeInUp">Bun venit în sistemul de comenzi online al restaurantului Deluxe! Comandă-ți astăzi mâncarea de la Deluxe și îți va fi livrată direct la ușă. Profită de ofertele noastre speciale săptămânale din meniul „Oferte Speciale” (Special Deals).</p>
                <div>
                  <a href="#menu" class="btn-menu animate__animated animate__fadeInUp scrollto">Our Food</a>
                  <a href="#book-a-table" class="btn-book animate__animated animate__fadeInUp scrollto">Book a Table</a>
                </div>
                <hr>
              </div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item" style="background: url(assets/img/slide/slide-2.jpg);">
            <div class="carousel-container">
              <div class="carousel-content">
                <h2 class="animate__animated animate__fadeInDown">Deluxe restaurant</h2>
                <p class="animate__animated animate__fadeInUp">Bun venit în sistemul de comenzi online al restaurantului Deluxe! Comandă-ți astăzi mâncarea de la Deluxe, iar noi ți-o vom livra direct la ușă.</p>
                <div>
                  <a href="#menu" class="btn-menu animate__animated animate__fadeInUp scrollto">Our Menu</a>
                  <a href="member-index.php" class="btn-book animate__animated animate__fadeInUp scrollto">Book a Table</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="carousel-item" style="background: url(assets/img/slide/slide-3.jpg);">
            <div class="carousel-container">
              <div class="carousel-content">
                <h2 class="animate__animated animate__fadeInDown">Deluxe restaurant</h2>
                <p class="animate__animated animate__fadeInUp">Comandă-ți astăzi mâncarea de la Deluxe, iar noi ți-o vom livra direct la ușă. Nu rata ofertele noastre speciale săptămânale din meniul „Oferte Speciale”!</p>
                <div>
                  <a href="#menu" class="btn-menu animate__animated animate__fadeInUp scrollto">Our Food</a>
                  <a href="#book-a-table" class="btn-book animate__animated animate__fadeInUp scrollto">Book a Table</a>
                </div>
              </div>
            </div>
          </div>

        </div>

        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>
      </div>
    </div>
  </section>

  <main id="main">

    <!-- ======= Login & Register Section ======= -->
    <section id="login" class="about">
      <div class="container" data-aos="fade-up">

        <div class="row g-4">

          <!-- Coloana Stângă: Video / Imagine de fundal -->
          <div class="col-lg-6 video-box" style="background-image: url('assets/img/about.jpg');">
            <a href="https://www.youtube.com/watch?v=jDDaplaOz7Q" class="venobox play-btn mb-4" data-vbtype="video" data-autoplay="true"></a>
          </div>

          <!-- Coloana Dreaptă: Panoul de Formulare (cu Tab-uri) -->
          <div class="col-lg-6 d-flex flex-column justify-content-center">
            <div class="form-container-box">

              <!-- Navigație între Login și Register -->
              <ul class="nav nav-pills custom-tabs mb-4 justify-content-center" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-panel" type="button" role="tab" aria-controls="login-panel" aria-selected="true">Autentificare</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-panel" type="button" role="tab" aria-controls="register-panel" aria-selected="false">Cont Nou</button>
                </li>
              </ul>

              <!-- Conținutul Tab-urilor -->
              <div class="tab-content" id="authTabsContent">

                <!-- Panel Login -->
                <div class="tab-pane fade show active" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
                  <form id="loginForm" class="mx-auto p-4 bg-white rounded-3 shadow" style="max-width: 400px;">
                    <div class="mb-3">
                      <label for="login_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                      <input name="login" type="text" id="login_email" class="form-control form-control-lg border-secondary shadow-sm" placeholder="introdu email-ul..." required />
                    </div>

                    <div class="mb-3">
                      <label for="password" class="form-label fw-bold">Parolă <span class="text-danger">*</span></label>
                      <input name="password" type="password" id="password" class="form-control form-control-lg border-secondary shadow-sm" placeholder="introdu parola..." required />
                    </div>

                    <button type="submit" class="book-a-table-btn w-100 mt-3 border-0 shadow">Conectare</button>
                  </form>
                </div>

                <!-- Panel Înregistrare -->
                <div class="tab-pane fade" id="register-panel" role="tabpanel" aria-labelledby="register-tab">
                  <form id="registerForm" name="registerForm" method="post" action="register-exec.php" onsubmit="return registerValidate(this)" class="p-4 bg-white rounded-3 shadow">

                    <div class="row g-2 mb-3">
                      <div class="col-md-6">
                        <label for="fname" class="fw-bold">Prenume <span class="text-danger">*</span></label>
                        <input name="fname" type="text" id="fname" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Ex: Ion" required />
                      </div>
                      <div class="col-md-6">
                        <label for="lname" class="fw-bold">Nume <span class="text-danger">*</span></label>
                        <input name="lname" type="text" id="lname" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Ex: Popescu" required />
                      </div>
                    </div>

                    <div class="mb-3">
                      <label for="reg_email" class="fw-bold">Email <span class="text-danger">*</span></label>
                      <input name="login" type="text" id="reg_email" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Ex: nume@yahoo.com" required />
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-md-6">
                        <label for="reg_password" class="fw-bold">Parolă <span class="text-danger">*</span></label>
                        <input name="password" type="password" id="reg_password" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Minim 6 caractere" required />
                      </div>
                      <div class="col-md-6">
                        <label for="cpassword" class="fw-bold">Confirmă <span class="text-danger">*</span></label>
                        <input name="cpassword" type="password" id="cpassword" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Repetă parola" required />
                      </div>
                    </div>

                    <div class="mb-3">
                      <label for="question" class="fw-bold">Întrebare de Securitate <span class="text-danger">*</span></label>
                      <select name="question" id="question" class="form-select form-select-lg border-secondary shadow-sm">
                        <option value="select">- Selectează întrebarea -</option>
                        <?php foreach ($questions as $q): ?>
                          <option value="<?php echo $q['question_id']; ?>"><?php echo htmlspecialchars($q['question_text']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="answer" class="fw-bold">Răspuns <span class="text-danger">*</span></label>
                      <input name="answer" type="text" id="answer" class="form-control form-control-lg border-secondary shadow-sm" placeholder="Răspunsul tău secret..." required />
                    </div>

                    <button type="submit" class="book-a-table-btn w-100 mt-2 border-0 shadow">Creează Cont</button>
                  </form>
                </div>

              </div>

            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- ======= Why Us Section ======= -->
    <section id="why-us" class="why-us">
      <div class="container">
        <div class="section-title">
          <h2>De ce să alegi <span>DELUXE</span></h2>
          <p>Pasiunea noastră pentru gastronomie ne-a condus la crearea Olive, un restaurant cu specific mediteranean și bucătărie responsabilă.</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="box">
              <span>01</span>
              <h4>Ambianța restaurantului nostru</h4>
              <p>La DELUXE, ne mândrim cu unul dintre cele mai frumoase restaurante din țară și nu numai.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="box">
              <span>02</span>
              <h4>Standardele noastre de calitate</h4>
              <p>La DELUXE, ne mândrim cu unul dintre cele mai frumoase restaurante din țară și nu numai.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======= Menu Section ======= -->
    <section id="menu" class="menu">
      <div class="container">
        <div class="section-title">
          <h2>Descoperă preparatele noastre</h2>
        </div>

        <form name="categoryForm" id="categoryForm" method="post" action="foodzone.php" onsubmit="return categoriesValidate(this)" class="mb-4">
          <div class="d-flex justify-content-center align-items-center gap-2 max-w-500 mx-auto">
            <select name="category" id="category" style="margin-top:0; max-width: 250px;">
              <option value="select">- Select Categorie -</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
              <?php endforeach; ?>
            </select>
            <input type="submit" class="book-a-table-btn" name="Submit" value="Show Foods" style="margin-left:0;" />
          </div>
        </form>

        <div class="php-table-wrapper">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Food Photo</th>
                <th>Food Name</th>
                <th>QR Code</th>
                <th>Food Category</th>
                <th>Food Price</th>
                <th>Action(s)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (isset($_POST['Submit']) && count($food_items) < 1): ?>
                <script>
                  alert('Momentan nu există produse în categoria selectată.');
                </script>
              <?php else: ?>
                <?php foreach ($food_items as $item): ?>
                  <tr>
                    <td><a href="images/<?php echo $item['food_photo']; ?>" target="_blank"><img src="images/<?php echo $item['food_photo']; ?>" width="80" height="70" class="rounded"></a></td>
                    <td><b><?php echo htmlspecialchars($item['food_name']); ?></b></td>
                    <td><a href="images/<?php echo $item['foodQR']; ?>" target="_blank"><img src="images/<?php echo $item['foodQR']; ?>" width="80" height="70"></a></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td><span class="text-success font-weight-bold"><?php echo (isset($symbol['currency_symbol']) ? $symbol['currency_symbol'] : '$') . $item['food_price']; ?></span></td>
                    <td><a href="cart-exec.php?id=<?php echo $item['food_id']; ?>" class="btn btn-sm btn-warning rounded-pill px-3">Add To Cart</a></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ======= Specials Section ======= -->
    <section id="specials" class="specials">
      <div class="container">
        <div class="section-title text-center">
          <h2>Promoțiile Săptămânii</h2>
          <p>Descoperă ofertele noastre de mai jos. Atenție, acestea sunt disponibile pentru o perioadă limitată! Nu lăsa ocazia să treacă.</p>
          <small class="text-muted"><strong>Notă:</strong> Pentru a plasa o comandă, te rugăm să accesezi pagina <strong>Food Zone</strong> și să alegi categoria <strong>Specials</strong> din listă.</small>
        </div>

        <div class="php-table-wrapper">
          <table class="table text-center align-middle">
            <thead>
              <tr>
                <th>Promo Photo</th>
                <th>Promo Name</th>
                <th>Promo Description</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Promo Price</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($specials as $row): ?>
                <tr>
                  <td><a href="images/<?php echo $row['special_photo']; ?>" target="_blank"><img src="images/<?php echo $row['special_photo']; ?>" width="80" height="70" class="rounded"></a></td>
                  <td><b><?php echo htmlspecialchars($row['special_name']); ?></b></td>
                  <td style="max-width: 250px; text-align: left;"><small><?php echo htmlspecialchars($row['special_description']); ?></small></td>
                  <td><small><?php echo $row['special_start_date']; ?></small></td>
                  <td><small><?php echo $row['special_end_date']; ?></small></td>
                  <td><span class="text-success font-weight-bold"><?php echo (isset($symbol['currency_symbol']) ? $symbol['currency_symbol'] : '$') . $row['special_price']; ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ======= Chefs Section ======= -->
    <section id="chefs" class="chefs">
      <div class="container">
        <div class="section-title">
          <h2>Bucătarii noștri <span>profesioniști</span></h2>
          <p>Pasiunea și talentul echipei noastre culinare transformă fiecare ingredient într-o experiență memorabilă.</p>
        </div>

        <div class="row g-4">
          <div class="col-lg-4 col-md-6">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-1.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Walter White</h4>
                <span>Bucătar Șeff</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-2.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Sarah Jhonson</h4>
                <span>Cofetar</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-3.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>William Anderson</h4>
                <span>Bucătar Preparator</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======= Book A Table Section ======= -->
    <section id="book-a-table" class="book-a-table">
      <div class="container">
        <div class="section-title text-center">
          <h2>Rezervă o <span>Masă</span></h2>
          <p>Te invităm să faci o rezervare pentru o experiență culinară memorabilă.</p>
        </div>

        <div class="form-container-box max-w-600 mx-auto text-center">
          <div class="mb-4 d-flex justify-content-center gap-2 flex-wrap">
            <a href="member-index.php" class="badge bg-secondary p-2">Home</a>
            <a href="cart.php" class="badge bg-primary p-2">Cart [<?php echo $num_items; ?>]</a>
            <a href="inbox.php" class="badge bg-info p-2">Inbox [<?php echo $num_messages; ?>]</a>
            <a href="tables.php" class="badge bg-success p-2">Tables</a>
            <a href="ratings.php" class="badge bg-warning p-2">Rate Us</a>
            <a href="logout.php" class="badge bg-danger p-2">Logout</a>
          </div>

          <form name="tableForm" id="tableForm" method="post" action="reserve-exec.php?id=<?php echo htmlspecialchars($memberId); ?>" onsubmit="return tableValidate(this)">
            <div class="mb-3 text-start">
              <label><b>Masa Nume/Numar:</b></label>
              <select name="table" id="table" required>
                <option value="select">- Select Number Table -</option>
                <?php foreach ($tables as $t): ?>
                  <option value="<?php echo $t['table_id']; ?>"><?php echo htmlspecialchars($t['table_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3 text-start">
              <label><b>Data:</b></label>
              <input type="date" name="date" id="date" required />
            </div>
            <div class="mb-4 text-start">
              <label><b>Timp:</b></label>
              <input type="time" name="time" id="time" required />
            </div>
            <button type="submit" class="book-a-table-btn w-100">Rezervă acum</button>
          </form>
        </div>
      </div>
    </section>

    <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery">
      <div class="container-fluid">
        <div class="section-title text-center">
          <h2>Câteva imagini din <span>restaurantul nostru</span></h2>
          <p>Te invităm să descoperi atmosfera caldă și designul rafinat care fac din DELUXE locul ideal pentru momentele tale speciale.</p>
        </div>
        <div class="row g-1">
          <?php for ($i = 1; $i <= 8; $i++): ?>
            <div class="col-lg-3 col-md-4">
              <div class="gallery-item">
                <a href="assets/img/gallery/gallery-<?php echo $i; ?>.jpg" class="gallery-lightbox">
                  <img src="assets/img/gallery/gallery-<?php echo $i; ?>.jpg" alt="" class="img-fluid">
                </a>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">
        <div class="section-title text-center">
          <h2><span>Contactează-ne</span></h2>
          <p>Ai întrebări sau dorești să ne transmiți un mesaj? Suntem la dispoziția ta.</p>
        </div>

        <!-- Info Section cu Card-uri Bootstrap -->
        <div class="row g-4 mb-5">
          <!-- Location -->
          <div class="col-lg-3 col-md-6">
            <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
              <i class="bi bi-geo-alt fs-2 text-warning mb-3 d-block"></i>
              <h4 class="fw-bold">Locație:</h4>
              <p class="text-muted">Calea Caracal no. 28<br>Craiova 200345</p>
            </div>
          </div>
          <!-- Open Hours -->
          <div class="col-lg-3 col-md-6">
            <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
              <i class="bi bi-clock fs-2 text-warning mb-3 d-block"></i>
              <h4 class="fw-bold">Deschis:</h4>
              <p class="text-muted">Mon-Sat:<br>11:00 AM - 23:00 PM</p>
            </div>
          </div>
          <!-- Email -->
          <div class="col-lg-3 col-md-6">
            <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
              <i class="bi bi-envelope fs-2 text-warning mb-3 d-block"></i>
              <h4 class="fw-bold">Email:</h4>
              <p class="text-muted">deluxerestaurant@gmail.com</p>
            </div>
          </div>
          <!-- Call -->
          <div class="col-lg-3 col-md-6">
            <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
              <i class="bi bi-phone fs-2 text-warning mb-3 d-block"></i>
              <h4 class="fw-bold">Telefon:</h4>
              <p class="text-muted">+40738740300<br>+40251555407</p>
            </div>
          </div>
        </div>

        <!-- Formular cu design Bootstrap -->
        <section id="contact" class="contact">
          <div class="container">
            <form action="forms/contact.php" method="post" role="form" class="php-email-form bg-white p-5 shadow-lg">
              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <input type="text" name="name" class="form-control form-control-lg border-secondary" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mb-3">
                  <input type="email" class="form-control form-control-lg border-secondary" name="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mb-3">
                <input type="text" class="form-control form-control-lg border-secondary" name="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mb-3">
                <textarea class="form-control form-control-lg border-secondary" name="message" rows="5" placeholder="Message" required></textarea>
              </div>
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-warning btn-lg px-5 text-white fw-bold shadow">Send Message</button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </section>

  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">
      <h3>Delicious – Gustul care te surprinde.</h3>
      <div class="social-links mb-3">
        <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
        <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
        <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
        <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
      </div>
      <div class="copyright">
        &copy; Copyright <strong><span>Delicious</span></strong> 2026 Saceanu Ionut Sorin
        <br>
        All Rights Reserved.
      </div>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
</body>

</html>
