<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('auth.php');
require_once('connection/config.php');

// Conectare la baza de date prin PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Eroare la conectare: " . $e->getMessage());
}

if (!isset($_SESSION['SESS_MEMBER_ID'])) {
    header("Location: index.php#login");
    exit();
}

$member_id = (int) $_SESSION['SESS_MEMBER_ID'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: cart.php?error=" . urlencode("ID produs nevalid."));
    exit();
}

try {
    // 1. Preluare billing_id (se verifică dacă adresa există)
    $stmt_billing = $pdo->prepare("SELECT billing_id FROM billing_details WHERE member_id = :member_id LIMIT 1");
    $stmt_billing->execute(['member_id' => $member_id]);
    $billing_row = $stmt_billing->fetch(PDO::FETCH_ASSOC);

    if (!$billing_row || empty($billing_row['billing_id'])) {
        header("Location: cont.php?error=" . urlencode("Vă rugăm să vă completați datele de livrare/facturare înainte de plasarea comenzii."));
        exit();
    }

    $billing_id = (int) $billing_row['billing_id'];

    // 2. Setare fus orar
    $flag_1 = 1;
    $stmt_tz = $pdo->prepare("SELECT timezone_reference FROM timezones WHERE flag = :flag LIMIT 1");
    $stmt_tz->execute(['flag' => $flag_1]);
    $row_tz = $stmt_tz->fetch(PDO::FETCH_ASSOC);

    if ($row_tz) {
        date_default_timezone_set($row_tz['timezone_reference']);
    }

    $time_stamp = date("H:i:s");
    $delivery_date = date("Y-m-d H:i:s");

    // 3. Începe tranzacția SQL
    $pdo->beginTransaction();

    // Verificăm dacă produsul există în coș și are flag = 0
    $stmt_check = $pdo->prepare("SELECT cart_id FROM cart_details WHERE cart_id = :id AND member_id = :member_id AND flag = 0");
    $stmt_check->execute([
        'id' => $id,
        'member_id' => $member_id
    ]);

    if ($stmt_check->rowCount() === 0) {
        throw new Exception("Produsul nu există în coș sau a fost deja comandat.");
    }

    // 4. Inserare în orders_details
    $qry_create = "INSERT INTO orders_details (member_id, billing_id, cart_id, delivery_date, flag, time_stamp, StaffID)
                   VALUES (:member_id, :billing_id, :id, :delivery_date, 1, :time_stamp, NULL)";
    $stmt_insert = $pdo->prepare($qry_create);
    $stmt_insert->execute([
        'member_id' => $member_id,
        'billing_id' => $billing_id,
        'id' => $id,
        'delivery_date' => $delivery_date,
        'time_stamp' => $time_stamp
    ]);

    // 5. Actualizare status în cart_details
    $qry_update = "UPDATE cart_details SET flag = 1 WHERE cart_id = :id AND member_id = :member_id";
    $stmt_update = $pdo->prepare($qry_update);
    $stmt_update->execute([
        'id' => $id,
        'member_id' => $member_id
    ]);

    // Commit tranzacție
    $pdo->commit();

    header("Location: cont.php?success=1");
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: cart.php?error=" . urlencode($e->getMessage()));
    exit();
}
