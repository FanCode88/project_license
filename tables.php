<?php
require_once('auth.php');
require_once('connection/config.php');

// Conectare modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Eroare la conectarea cu serverul: ' . mysqli_connect_error());
}

// Setare charset utf8mb4
mysqli_set_charset($link, "utf8mb4");

$memberId = isset($_SESSION['SESS_MEMBER_ID']) ? $_SESSION['SESS_MEMBER_ID'] : null;

// Preluare elemente din coș folosind interogare securizată / mysqli
$num_items = 0;
if ($memberId) {
  $stmt_cart = mysqli_prepare($link, "SELECT COUNT(*) FROM cart_details WHERE member_id = ? AND flag = ?");
  if ($stmt_cart) {
    $flag_0 = 0;
    mysqli_stmt_bind_param($stmt_cart, "ii", $memberId, $flag_0);
    mysqli_stmt_execute($stmt_cart);
    mysqli_stmt_bind_result($stmt_cart, $num_items);
    mysqli_stmt_fetch($stmt_cart);
    mysqli_stmt_close($stmt_cart);
  }
}

$tables = mysqli_query($link, "SELECT * FROM tables");
?>
<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rezervare Masă - Deluxe Restaurant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .reservation-card {
      max-width: 500px;
      margin: 50px auto;
      padding: 30px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">Deluxe Restaurant</a>
      <div class="navbar-nav">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="foodzone.php">Food Zone</a>
        <a class="nav-link" href="cont.php">My Account</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="reservation-card">
      <h2 class="text-center mb-4">Rezervă o Masă</h2>

      <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
        <a href="cont.php" class="btn btn-sm btn-outline-secondary">Dashboard</a>
        <a href="cart.php" class="btn btn-sm btn-outline-secondary">Cart [<?php echo (int) $num_items; ?>]</a>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
      </div>

      <form name="tableForm" method="post"
        action="reserve-exec.php?id=<?php echo htmlspecialchars($_SESSION['SESS_MEMBER_ID']); ?>">
        <div class="mb-3">
          <label class="form-label fw-bold">Selectează Masa:</label>
          <select name="table" class="form-select" required>
            <option value="">- alege masa -</option>
            <?php
            if ($tables) {
              while ($row = mysqli_fetch_assoc($tables)) {
                echo "<option value='" . htmlspecialchars($row['table_id']) . "'>" . htmlspecialchars($row['table_name']) . "</option>";
              }
              mysqli_free_result($tables);
            }
            ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Dată:</label>
          <input type="date" name="date" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Oră:</label>
          <input type="time" name="time" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirmă Rezervarea</button>
      </form>
    </div>
  </div>

  <footer class="text-center mt-5 p-4 text-muted">
    &copy; 2026 Saceanu Ionut Sorin. All Rights Reserved.
  </footer>

</body>

</html>
<?php
mysqli_close($link);
?>
