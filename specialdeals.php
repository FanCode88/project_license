<?php
// 1. Pornim sesiunea și forțăm afișarea erorilor
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Conectare la baza de date (suntem în folderul principal, deci calea e simplă)
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
  die('Eroare la conectarea cu serverul: ' . mysqli_connect_error());
}

// 3. Preluăm moneda activă pentru a afișa prețurile corect
$currency_query = mysqli_query($link, "SELECT * FROM currencies WHERE flag='1'");
$symbol = mysqli_fetch_assoc($currency_query);
$currency_symbol = isset($symbol['currency_symbol']) ? $symbol['currency_symbol'] : '$';

// 4. Preluăm doar ofertele valabile (unde data curentă este între start_date și end_date)
// Dacă vrei să le afișezi pe toate indiferent de dată, folosește doar: SELECT * FROM specials
$curent_date = date('Y-m-d');
$query = "SELECT * FROM specials WHERE '$curent_date' BETWEEN special_start_date AND special_end_date";
$result = mysqli_query($link, $query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Food Plaza: Oferte Speciale</title>
  <link href="stylesheets/user_styles.css" rel="stylesheet" type="text/css">
  <style>
    /* Stiluri rapide pentru a arăta promoțiile ca niște carduri atrăgătoare */
    .specials-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-top: 20px;
    }

    .special-card {
      border: 1px solid #bd6f2f;
      background: #fff;
      border-radius: 8px;
      width: 260px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .special-card img {
      border-radius: 5px;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .price-tag {
      font-size: 18px;
      color: #e44d26;
      font-weight: bold;
      margin: 10px 0;
    }

    .date-tag {
      font-size: 11px;
      color: #777;
      font-style: italic;
    }

    .btn-order {
      display: inline-block;
      background: #bd6f2f;
      color: #fff;
      padding: 8px 15px;
      text-decoration: none;
      border-radius: 4px;
      margin-top: 10px;
      font-weight: bold;
    }

    .btn-order:hover {
      background: #a05a20;
    }
  </style>
</head>

<body>
  <div id="page">
    <!-- Meniul principal al clienților -->
    <div id="menu">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="foodzone.php">Food Zone</a></li>
        <li><a href="specialdeals.php">Special Deals</a></li>
        <li><a href="member-index.php">My Account</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </div>

    <div id="header">
      <div id="logo"> <a href="index.php" class="blockLink"></a></div>
      <div id="company_name">Food Plaza Restaurant</div>
    </div>

    <div id="center">
      <h1>OFERTE SPECIALE & PROMOȚII</h1>
      <hr>
      <p style="text-align:center;">Profită de cele mai bune prețuri din meniul nostru, disponibile pentru o perioadă limitată!</p>

      <div class="specials-container">
        <?php
        $count = mysqli_num_rows($result);
        if ($count < 1) {
          echo "<p>Momentan nu avem nicio ofertă specială activă. Revino curând!</p>";
        } else {
          // Parcurgem toate ofertele adăugate de admin
          while ($row = mysqli_fetch_assoc($result)) {
            // Verificăm dacă există o imagine validă, altfel punem un placeholder
            $image_path = "images/" . $row['special_photo'];
            if (empty($row['special_photo']) || !file_exists($image_path)) {
              $image_path = "images/default-food.jpg"; // o imagine standard dacă lipsește poza
            }

            echo "<div class='special-card'>";
            echo "<img src='" . $image_path . "' width='220' height='160' alt='" . htmlspecialchars($row['special_name']) . "'>";
            echo "<h3>" . htmlspecialchars($row['special_name']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['special_description']) . "</p>";
            echo "<div class='price-tag'>" . $currency_symbol . $row['special_price'] . "</div>";
            echo "<div class='date-tag'>Valabil: " . $row['special_start_date'] . " până la " . $row['special_end_date'] . "</div>";
            // Trimite direct în coș (opțional, dacă ai structura asta pe link-ul respectiv)
            echo "<a class='btn-order' href='cart-exec.php?id=" . $row['special_id'] . "'>Comandă Acum</a>";
            echo "</div>";
          }
        }
        mysqli_free_result($result);
        mysqli_close($link);
        ?>
      </div>
    </div>

    <div id="footer">
      <div class="bottom_menu">
        <a href="index.php">Home Page</a> | <a href="aboutus.php">About Us</a> | <a href="specialdeals.php">Special Deals</a> | <a href="foodzone.php">Food Zone</a> | <a href="#">Affiliate Program</a> |<br>
        | <a href="admin/index.php" target="_blank">Administrator</a> |
      </div>
      <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
    </div>
  </div>
</body>

</html>
