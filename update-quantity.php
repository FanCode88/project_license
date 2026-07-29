<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('auth.php');
require_once('connection/config.php');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item'], $_POST['action'])) {

    if (!isset($_SESSION['SESS_MEMBER_ID'])) {
        mysqli_close($link);
        header("Location: cart.php?error=" . urlencode("Sesiunea a expirat. Te rugăm să te reautentifici."));
        exit();
    }

    $member_id = (int) $_SESSION['SESS_MEMBER_ID'];
    $cart_id = (int) $_POST['item'];
    $current_qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $action = $_POST['action'];

    // Modificare cantitate
    if ($action === 'plus') {
        $new_qty = $current_qty + 1;
    } elseif ($action === 'minus') {
        $new_qty = $current_qty - 1;
    } else {
        $new_qty = $current_qty;
    }

    if ($new_qty < 1) {
        $new_qty = 1;
    }

    $flag_0 = 0; // Se caută doar produsele active din coș (necomandate)

    // Obținere preț unitar
    $stmt_price = mysqli_prepare($link, "
        SELECT food_details.food_price
        FROM food_details
        INNER JOIN cart_details ON cart_details.food_id = food_details.food_id
        WHERE cart_details.member_id = ? AND cart_details.flag = ? AND cart_details.cart_id = ?
    ");
    mysqli_stmt_bind_param($stmt_price, "iii", $member_id, $flag_0, $cart_id);
    mysqli_stmt_execute($stmt_price);
    $result_price = mysqli_stmt_get_result($stmt_price);

    if ($row_price = mysqli_fetch_assoc($result_price)) {
        $food_price = (float) $row_price['food_price'];
        mysqli_stmt_close($stmt_price);

        // Recalculare total
        $total = $new_qty * $food_price;

        // Actualizare cantitate în coș
        $stmt_update = mysqli_prepare($link, "UPDATE cart_details SET quantity_id = ?, total = ? WHERE cart_id = ? AND member_id = ? AND flag = ?");
        mysqli_stmt_bind_param($stmt_update, "idiii", $new_qty, $total, $cart_id, $member_id, $flag_0);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        mysqli_close($link);
        header("Location: cart.php");
        exit();
    } else {
        // Dacă nu a fost găsit în coșul activ (flag = 0)
        mysqli_stmt_close($stmt_price);
        mysqli_close($link);
        header("Location: cart.php?error=" . urlencode("Produsul nu există în coș sau a fost deja comandat."));
        exit();
    }
} else {
    mysqli_close($link);
    header("Location: cart.php");
    exit();
}
