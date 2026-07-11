<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="UTF-8">
  <title>Administrator Login - Food Plaza</title>
  <link href="stylesheets/admin_styles.css" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="validation/admin.js"></script>
  <style>
    /* Compactare si centrare */
    .form-container-box {
      background: #fff;
      padding: 30px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .book-a-table-btn {
      background: #ffb03b;
      color: white;
      padding: 10px;
      border-radius: 5px;
    }
  </style>
</head>

<body style="background: #fdfaf6;">

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-4"> <!-- Am redus de la col-md-5 la col-md-4 pentru a fi mai compact -->

      <div class="form-container-box text-center">
        <h2 style="font-family: 'Satisfy', cursive; color: #ffb03b; margin-bottom: 20px;">Administrator Login</h2>

        <form id="loginForm" name="loginForm" method="post" action="login-exec.php" onsubmit="return loginValidate(this)">
          <div class="mb-3 text-start">
            <label style="font-size: 14px; font-weight: bold;">Username</label>
            <input name="login" type="text" id="login" placeholder="Utilizator" required />
          </div>

          <div class="mb-3 text-start">
            <label style="font-size: 14px; font-weight: bold;">Password</label>
            <input name="password" type="password" id="password" placeholder="Parolă" required />
          </div>

          <button type="submit" name="Submit" class="book-a-table-btn w-100 mt-2 rounded-3">Login</button>
        </form>
      </div>

      <div class="text-center mt-4 text-muted" style="font-size: 12px;">
        &copy; 2026 Saceanu Ionut Sorin. All Rights Reserved
      </div>
    </div>
  </div>

</body>

</html>
