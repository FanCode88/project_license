<?php
// Verificare și conectare la baza de date folosind MySQLi
require_once('connection/config.php');

// Conectare la serverul MySQL
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

// Setăm charset-ul pentru conexiune
mysqli_set_charset($link, "utf8mb4");

// Funcție modernizată de curățare a valorilor primite
function clean($link, $str)
{
  $str = trim($str);
  return mysqli_real_escape_string($link, $str);
}

// Gestionare Categorie selectată (păstrată fie prin POST, fie prin GET la paginare)
$selected_category = '';
if (isset($_POST['Submit']) && isset($_POST['category']) && $_POST['category'] !== 'select') {
  $selected_category = clean($link, $_POST['category']);
} elseif (isset($_GET['category']) && $_GET['category'] !== '') {
  $selected_category = clean($link, $_GET['category']);
}

// --- PAGINARE ---
$results_per_page = 4; // Câte produse să fie pe pagină
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
  $page = 1;
$start_from = ($page - 1) * $results_per_page;

// Construire interogare de numărare totală și interogare de date cu LIMIT
if ($selected_category !== '') {
  $count_query = "SELECT COUNT(*) AS total FROM food_details WHERE food_category = '$selected_category'";
  $query_data = "SELECT * FROM food_details JOIN categories ON food_details.food_category = categories.category_id WHERE food_details.food_category = '$selected_category' LIMIT $start_from, $results_per_page";
} else {
  $count_query = "SELECT COUNT(*) AS total FROM food_details";
  $query_data = "SELECT * FROM food_details JOIN categories ON food_details.food_category = categories.category_id LIMIT $start_from, $results_per_page";
}

// Aflăm numărul total de rânduri pentru calcularea paginilor
$count_result = mysqli_query($link, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $results_per_page);

// Executăm interogarea principală pentru produsele de pe pagina curentă
$result = mysqli_query($link, $query_data)
  or die("A problem has occured ... Please check back after few hours.");

// Preluare categorii din tabela categories pentru dropdown
$categories = mysqli_query($link, "SELECT * FROM categories")
  or die("A problem has occured ... Please check back after few hours.");
?>
<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Plaza: Foods</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font & Iconițe -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: orange;
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

    .ingredient-qr {
      text-align: center;
    }

    .ingredient-qr img {
      transition: .3s;
      border-radius: 10px;
    }

    .ingredient-qr img:hover {
      transform: scale(1.08);
    }

    .ingredient-qr strong {
      display: block;
      color: #198754;
      margin-top: 6px;
      font-size: 15px;
    }

    .ingredient-qr small {
      color: #6c757d;
      line-height: 1.4;
    }

    .table {
      margin: 0 auto;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }

    .table th {
      text-align: center;
    }

    .btn-outline-success {
      background: #ff6b35;
      color: #ffff;
      border: none;
    }

    .btn-outline-success:hover {
      background: orangered;
    }

    .ingredient-text {
      color: #ff6b35 !important;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: 0.3px;
    }
  </style>
</head>

<body>

  <!-- Meniu Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-egg-fried"></i> Food Plaza</a>
      <button class="navbar-collapse-toggler navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav">
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

  <!-- Header Tip Banner -->
  <div class="page-header shadow-sm">
    <div class="container">
      <h1 class="display-4 fw-bold text-uppercase">Choose Your Food</h1>
      <p class="lead mb-0">Explore our delicious menu tailored just for you</p>
    </div>
  </div>

  <div class="container" style="max-width:1200px;">

    <!-- Zona Filtrare Categorie -->
    <div class="card p-4 mb-4 shadow-sm border-0 bg-white rounded-3">
      <form name="categoryForm" id="categoryForm" method="post" action="foodzone.php"
        onsubmit="return categoriesValidate(this)">
        <div class="row g-3 align-items-center justify-content-center">
          <div class="col-auto">
            <label for="category" class="col-form-label fw-bold"><i class="bi bi-filter"></i> Filter by
              Category:</label>
          </div>
          <div class="col-md-4">
            <select name="category" id="category" class="form-select">
              <option value="select">- select category -</option>
              <?php
              mysqli_data_seek($categories, 0); // reset pointer
              while ($row_cat = mysqli_fetch_array($categories)) {
                $selected = ($selected_category == $row_cat['category_id']) ? 'selected' : '';
                echo "<option value=\"" . $row_cat['category_id'] . "\" $selected>" . htmlspecialchars($row_cat['category_name']) . "</option>";
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

    <!-- Tabel Produse fără coloana de Preț -->
    <div class="food-card-table shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:15%">Photo</th>
              <th style="width:25%">Food Name</th>
              <th style="width:25%">Ingredients</th>
              <th style="width:15%">Category</th>
              <th style="width:20%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $num_rows = mysqli_num_rows($result);
            if ($num_rows < 1) {
              echo "<tr><td colspan='5' class='py-5 text-muted'><i class='bi bi-exclamation-circle fs-3 d-block mb-2'></i> No products found.</td></tr>";
            } else {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                // Foto Produs
                echo "<td><a href='images/" . htmlspecialchars($row['food_photo']) . "' target='_blank'><img src='images/" . htmlspecialchars($row['food_photo']) . "' width='80' height='70' class='shadow-sm border'></a></td>";
                // Nume
                echo "<td class='fw-bold'>" . htmlspecialchars($row['food_name']) . "</td>";
                // QR / Ingrediente
                echo "<td><div class='ingredient-qr'><a href='images/" . htmlspecialchars($row['foodQR']) . "' target='_blank'><img src='images/" . htmlspecialchars($row['foodQR']) . "' width='90' height='90' class='shadow-sm border rounded'></a><div class='mt-2'><small class='ingredient-text'>Scan to View</small></div></div></td>";
                // Categorie
                echo "<td><span class='badge bg-light text-dark border px-3 py-2'>" . htmlspecialchars($row['category_name']) . "</span></td>";
                // Buton Adăugare
                echo '<td><a href="cart-exec.php?id=' . $row['food_id'] . '" class="btn btn-outline-success btn-sm rounded-pill px-3"><i class="bi bi-cart-plus"></i> Add To Cart</a></td>';
                echo "</tr>";
              }
            }
            mysqli_free_result($result);
            ?>
          </tbody>
        </table>
      </div>

      <!-- Paginare Bootstrap -->
      <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center mb-0">
            <!-- Buton Anterior -->
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link"
                href="foodzone.php?page=<?php echo $page - 1; ?><?php echo ($selected_category !== '') ? '&category=' . $selected_category : ''; ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link"
                  href="foodzone.php?page=<?php echo $i; ?><?php echo ($selected_category !== '') ? '&category=' . $selected_category : ''; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <!-- Buton Următor -->
            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
              <a class="page-link"
                href="foodzone.php?page=<?php echo $page + 1; ?><?php echo ($selected_category !== '') ? '&category=' . $selected_category : ''; ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </div>

  <?php
  mysqli_close($link);
  ?>

  <!-- Footer -->
  <footer class="footer">
    <div class="container text-center">
      <div class="mb-3">
        <a href="index.php" class="mx-2">Acasă</a> |
        <a href="aboutus.php" class="mx-2">Despre noi</a> |
        <a href="specialdeals.php" class="mx-2">Oferte speciale</a> |
        <a href="foodzone.php" class="mx-2">Zona culinară</a> |
        <a href="admin/index.php" target="_blank" class="mx-2 text-warning">Administrator</a>
      </div>
      <p class="small mb-0 text-muted">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
