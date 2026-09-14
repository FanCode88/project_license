<?php
// Conectare la baza de date
require_once('connection/config.php');

// Conectare MySQLi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$conn) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Setăm charset-ul
mysqli_set_charset($conn, "utf8mb4");

$questions = mysqli_query($conn, "SELECT * FROM questions") or die("Error: " . mysqli_error($conn));

// Logica cookie "Remember Me"
if (isset($_POST['Submit'])) {
    if (isset($_POST['remember'])) {
        setcookie('remember_me', $_POST['login'], time() + 31536000, "/");
    } else {
        setcookie('remember_me', 'gone', time() - 100, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Food Plaza: Login</title>
    <!-- CSS-ul tău principal -->
    <link href="stylesheets/user_styles.css" rel="stylesheet" type="text/css" />

    <!-- STILURI EMBEDDED PENTRU A EVITA PROBLEMELE DE CALE (PATH) LA SCRIPTURI -->
    <style>
        .auth-container {
            display: flex;
            justify-content: space-between;
            gap: 25px;
            max-width: 850px;
            margin: 20px auto;
        }

        .auth-card {
            flex: 1;
            background: #ffffff;
            border: 1px solid #d19b6f;
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 4px 12px rgba(189, 111, 47, 0.12);
            box-sizing: border-box;
        }

        .auth-card h3 {
            margin-top: 0;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f9dec7;
            color: #bd6f2f;
            font-size: 18px;
            text-align: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
            text-align: left;
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group label {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d19b6f;
            border-radius: 4px;
            font-size: 12px;
            box-sizing: border-box;
            background: #fff;
            font-family: tahoma, sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #B80000;
            box-shadow: 0 0 4px rgba(184, 0, 0, 0.3);
        }

        .form-group-checkbox {
            margin: 12px 0 18px 0;
            font-size: 11px;
            text-align: left;
        }

        .form-group-checkbox label {
            cursor: pointer;
            color: #333;
        }

        .btn-submit {
            background: #bd6f2f;
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            text-transform: uppercase;
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: #B80000;
        }
    </style>

    <script src="validation/user.js"></script>
</head>

<body>
    <div id="wrap">
        <div id="page">
            <div id="header">
                <div id="logo"><a href="index.php" class="blockLink"></a></div>
                <div id="company_name">Food Plaza Restaurant</div>
            </div>

            <div id="center">
                <h1>Autentificare & Înregistrare</h1>

                <div class="auth-container">
                    <!-- Login Box -->
                    <div class="auth-card">
                        <h3>Login</h3>
                        <form name="loginForm" method="post" action="login-exec.php"
                            onsubmit="return loginValidate(this)">
                            <div class="form-group">
                                <label for="login_email">Email</label>
                                <input name="login" id="login_email" type="text" placeholder="adresa@email.com" />
                            </div>

                            <div class="form-group">
                                <label for="login_password">Password</label>
                                <input name="password" id="login_password" type="password" placeholder="••••••••" />
                            </div>

                            <div class="form-group-checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="1" <?php if (isset($_COOKIE['remember_me']))
                                        echo 'checked="checked"'; ?> />
                                    Remember me
                                </label>
                            </div>

                            <input type="submit" name="Submit" value="Login" class="btn-submit" />
                        </form>
                    </div>

                    <!-- Register Box -->
                    <div class="auth-card">
                        <h3>Register</h3>
                        <form name="registerForm" method="post" action="register-exec.php"
                            onsubmit="return registerValidate(this)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="reg_fname">First Name</label>
                                    <input name="fname" id="reg_fname" type="text" placeholder="Nume" />
                                </div>
                                <div class="form-group">
                                    <label for="reg_lname">Last Name</label>
                                    <input name="lname" id="reg_lname" type="text" placeholder="Prenume" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reg_login">Email</label>
                                <input name="login" id="reg_login" type="text" placeholder="adresa@email.com" />
                            </div>

                            <div class="form-group">
                                <label for="reg_password">Password</label>
                                <input name="password" id="reg_password" type="password" placeholder="••••••••" />
                            </div>

                            <div class="form-group">
                                <label for="reg_question">Security Question</label>
                                <select name="question" id="reg_question">
                                    <option value="select">- selectează -</option>
                                    <?php
                                    mysqli_data_seek($questions, 0);
                                    while ($row = mysqli_fetch_assoc($questions)) {
                                        echo "<option value='{$row['question_id']}'>{$row['question_text']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <input type="submit" name="Submit" value="Register" class="btn-submit" />
                        </form>
                    </div>
                </div>
            </div>

            <div id="footer">
                <div class="bottom_addr">&copy; 2026 Saceanu Ionut Sorin. All Rights Reserved</div>
            </div>
        </div>
    </div>
</body>

</html>
    