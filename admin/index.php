<?php
// Pornim sesiunea și verificăm autentificarea
require_once('auth.php');

// Afișare erori pentru depanare
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('connection/config.php');

// Conexiune modernă MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Failed to connect to server: ' . mysqli_connect_error());
}

$flag_1 = 1;

// Inițializare variabile globale pentru interogări
$result_polls = null;
$excellent_value = 0;
$good_value = 0;
$average_value = 0;
$bad_value = 0;
$worse_value = 0;
$excellent_rate = 0;
$good_rate = 0;
$average_rate = 0;
$bad_rate = 0;
$worse_rate = 0;
$food_name = "";
$has_ratings = false;

// Numărare înregistrări pentru Status
$members = mysqli_query($link, "SELECT * FROM members");
$orders_placed = mysqli_query($link, "SELECT * FROM orders_details");
$orders_processed = mysqli_query($link, "SELECT * FROM orders_details WHERE flag='$flag_1'");
$tables_reserved = mysqli_query($link, "SELECT * FROM reservations_details WHERE table_flag='$flag_1'");
$partyhalls_reserved = mysqli_query($link, "SELECT * FROM reservations_details WHERE partyhall_flag='$flag_1'");
$tables_allocated = mysqli_query($link, "SELECT * FROM reservations_details WHERE flag='$flag_1' AND table_flag='$flag_1'");
$partyhalls_allocated = mysqli_query($link, "SELECT * FROM reservations_details WHERE flag='$flag_1' AND partyhall_flag='$flag_1'");

// Preluare mâncare pentru dropdown
$foods = mysqli_query($link, "SELECT * FROM food_details");

// Procesare formular Ratings
if (isset($_POST['Submit'])) {
  function clean($link, $str)
  {
    return mysqli_real_escape_string($link, trim($str));
  }
  $id = clean($link, $_POST['food']);

  // Preluare ID-uri calificative
  $ratings = mysqli_query($link, "SELECT * FROM ratings");
  $rates = [];
  while ($r = mysqli_fetch_assoc($ratings)) {
    $rates[] = $r['rate_id'];
  }

  $excellent = isset($rates[0]) ? $rates[0] : 0;
  $good      = isset($rates[1]) ? $rates[1] : 0;
  $average   = isset($rates[2]) ? $rates[2] : 0;
  $bad       = isset($rates[3]) ? $rates[3] : 0;
  $worse     = isset($rates[4]) ? $rates[4] : 0;

  // Rulăm numărătorile direct din SQL pentru performanță și stabilitate
  $total_q     = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id'");
  $excellent_q = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id' AND rate_id='$excellent'");
  $good_q      = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id' AND rate_id='$good'");
  $average_q   = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id' AND rate_id='$average'");
  $bad_q       = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id' AND rate_id='$bad'");
  $worse_q     = mysqli_query($link, "SELECT COUNT(*) as total FROM polls_details WHERE food_id='$id' AND rate_id='$worse'");

  $total_value     = mysqli_fetch_assoc($total_q)['total'];
  $excellent_value = mysqli_fetch_assoc($excellent_q)['total'];
  $good_value      = mysqli_fetch_assoc($good_q)['total'];
  $average_value   = mysqli_fetch_assoc($average_q)['total'];
  $bad_value       = mysqli_fetch_assoc($bad_q)['total'];
  $worse_value     = mysqli_fetch_assoc($worse_q)['total'];

  if ($total_value > 0) {
    $excellent_rate = round(($excellent_value / $total_value) * 100, 2);
    $good_rate      = round(($good_value / $total_value) * 100, 2);
    $average_rate   = round(($average_value / $total_value) * 100, 2);
    $bad_rate       = round(($bad_value / $total_value) * 100, 2);
    $worse_rate     = round(($worse_value / $total_value) * 100, 2);
  }

  // Preluare nume mâncare selectată
  $food_info_q = mysqli_query($link, "SELECT food_name FROM food_details WHERE food_id='$id'");
  if ($food_info = mysqli_fetch_assoc($food_info_q)) {
    $food_name = $food_info['food_name'];
  }
  $has_ratings = true;
}
?>
<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Administrare</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,600,600i,700,700i|Satisfy|Comic+Neue:300,300i,400,400i,700,700i" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <header id="header" class="fixed-top d-flex align-items-center ">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <div class="logo me-auto">
        <h1><a href="../index.php">Deluxe restaurant</a></h1>
      </div>
      <a href="../index.php#menu" class="book-a-table-btn scrollto">Înapoi</a>
    </div>
  </header>

  <main id="main" style="margin-top: 100px;">
    <section class="breadcrumbs">
      <div class="container">
        <h1>Panou Control Administrator</h1>
        <a href="profile.php">Profil</a> | <a href="categories.php">Categorii</a> | <a href="foods.php">Produse</a> | <a href="accounts.php">Conturi</a>
        | <a href="orders.php">Comenzi</a> | <a href="reservations.php">Rezervări</a> | <a href="allocation.php">Personal</a>
        | <a href="messages.php">Mesaje</a> | <a href="options.php">Opțiuni</a> | <a href="logout.php">Ieșire</a>
      </div>
    </section>

    <section class="inner-page container mt-4">
      <table class="table table-bordered table-striped text-center">
        <caption>
          <h3>STATUS CURENT</h3>
        </caption>
        <thead class="table-dark">
          <tr>
            <th>Membri Înregistrați</th>
            <th>Comenzi Plasate</th>
            <th>Comenzi Procesate</th>
            <th>Comenzi În Așteptare</th>
            <th>Mese Rezervate</th>
            <th>Mese Alocate</th>
            <th>Mese În Așteptare</th>
            <th>Săli Rezervate</th>
            <th>Săli Alocate</th>
            <th>Săli În Așteptare</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $res1 = mysqli_num_rows($members);
          $res2 = mysqli_num_rows($orders_placed);
          $res3 = mysqli_num_rows($orders_processed);
          $res4 = $res2 - $res3;
          $res5 = mysqli_num_rows($tables_reserved);
          $res6 = mysqli_num_rows($tables_allocated);
          $res7 = $res5 - $res6;
          $res8 = mysqli_num_rows($partyhalls_reserved);
          $res9 = mysqli_num_rows($partyhalls_allocated);
          $res10 = $res8 - $res9;

          echo "<tr>";
          echo "<td>$res1</td><td>$res2</td><td>$res3</td><td>$res4</td>";
          echo "<td>$res5</td><td>$res6</td><td>$res7</td>";
          echo "<td>$res8</td><td>$res9</td><td>$res10</td>";
          echo "</tr>";
          ?>
        </tbody>
      </table>

      <hr>

      <form name="foodStatusForm" id="foodStatusForm" method="post" action="index.php">
        <table width="360" align="center" class="my-3">
          <caption>
            <h4>CALIFICATIVE CLIENȚI</h4>
          </caption>
          <tr>
            <td>Produs</td>
            <td width="168">
              <select name="food" id="food" class="form-select">
                <option value="select">- selectează produsul -</option>
                <?php
                while ($row = mysqli_fetch_array($foods)) {
                  echo "<option value='{$row['food_id']}'>{$row['food_name']}</option>";
                }
                ?>
              </select>
            </td>
            <td><input type="submit" name="Submit" value="Vezi Calificative" class="btn btn-primary" /></td>
          </tr>
        </table>
      </form>

      <?php if ($has_ratings): ?>
        <table class="table table-bordered text-center mt-3">
          <thead class="table-light">
            <tr>
              <th>Produs</th>
              <th>Excelent</th>
              <th>Bun</th>
              <th>Mediu</th>
              <th>Rău</th>
              <th>Foarte Rău</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th><?php echo htmlspecialchars($food_name); ?></th>
              <td><?php echo "$excellent_value ($excellent_rate%)"; ?></td>
              <td><?php echo "$good_value ($good_rate%)"; ?></td>
              <td><?php echo "$average_value ($average_rate%)"; ?></td>
              <td><?php echo "$bad_value ($bad_rate%)"; ?></td>
              <td><?php echo "$worse_value ($worse_rate%)"; ?></td>
            </tr>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </main>

  <footer id="footer" class="text-center py-4 border-top mt-5">
    <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved.</div>
  </footer>
</body>

</html>
