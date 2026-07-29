<?php
//Start session
session_start();

//checking connection and connecting to a database
require_once('connection/config.php');

//Connect to mysql server using modern MySQLi
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

//Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str) {
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
}

if (isset($_POST['Update'])) {
    //define default values for flag_0 and flag_1
    $flag_0 = 0;
    $flag_1 = 1;

    //check whether there is an active currency using prepared statement
    $stmt = mysqli_prepare($link, "SELECT currency_id FROM currencies WHERE flag = ?");
    mysqli_stmt_bind_param($stmt, "i", $flag_1);
    mysqli_stmt_execute($stmt);
    $result_qry = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result_qry) > 0) {
        $row = mysqli_fetch_assoc($result_qry);
        $active_currency_id = $row['currency_id'];
        mysqli_stmt_close($stmt);

        // update the entry with a deactivation flag
        $stmt_deactivate = mysqli_prepare($link, "UPDATE currencies SET flag = ? WHERE currency_id = ?");
        mysqli_stmt_bind_param($stmt_deactivate, "ii", $flag_0, $active_currency_id);
        mysqli_stmt_execute($stmt_deactivate);
        mysqli_stmt_close($stmt_deactivate);

        //Sanitize the POST values
        $new_currency_id = clean($link, $_POST['currency']);

        // update the entry with an activation flag
        $stmt_activate = mysqli_prepare($link, "UPDATE currencies SET flag = ? WHERE currency_id = ?");
        mysqli_stmt_bind_param($stmt_activate, "ii", $flag_1, $new_currency_id);
        $result = mysqli_stmt_execute($stmt_activate);
        mysqli_stmt_close($stmt_activate);

        //check if query executed
        if ($result) {
            mysqli_close($link);
            // redirect back to the options page
            header("Location: options.php");
            exit();
        } else {
            mysqli_close($link);
            die("activating a currency failed ...");
        }
    } else {
        mysqli_stmt_close($stmt);

        //Sanitize the POST values
        $new_currency_id = clean($link, $_POST['currency']);

        // update the entry with an activation flag
        $stmt_activate = mysqli_prepare($link, "UPDATE currencies SET flag = ? WHERE currency_id = ?");
        mysqli_stmt_bind_param($stmt_activate, "ii", $flag_1, $new_currency_id);
        $result = mysqli_stmt_execute($stmt_activate);
        mysqli_stmt_close($stmt_activate);

        //check if query executed
        if ($result) {
            mysqli_close($link);
            // redirect back to the options page
            header("Location: options.php");
            exit();
        } else {
            mysqli_close($link);
            die("activating a currency failed ...");
        }
    }
}
else {
    mysqli_close($link);
}
