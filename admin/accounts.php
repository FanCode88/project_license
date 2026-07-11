<?php
require_once('auth.php');
require_once('connection/config.php');

// Conexiune securizată PDO (Standard modern)
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Interogare modernă
  $stmt = $pdo->query("SELECT * FROM members");
  $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Eroare: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ro">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrare Membri | Delicious</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .sidebar {
      background: #343a40;
      min-height: 100vh;
      color: white;
    }

    .nav-link {
      color: #adb5bd;
    }

    .nav-link:hover {
      color: white;
    }
  </style>
</head>

<body>

  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-2 d-none d-md-block sidebar p-3">
        <h4 class="text-white">Delicious Admin</h4>
        <hr>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active text-white" href="members.php">Membri</a></li>
          <li class="nav-item"><a class="nav-link" href="orders.php">Comenzi</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
      </nav>

      <main class="col-md-10 p-4">
        <div class="card shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Lista Membrilor</h3>
          </div>
          <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nume</th>
                  <th>Prenume</th>
                  <th>Email</th>
                  <th>Acțiuni</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($members as $row): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($row['login']); ?></td>
                    <td>
                      <a href="delete-member.php?id=<?php echo $row['member_id']; ?>"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Ești sigur că vrei să ștergi acest membru?')">
                        Șterge
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <footer class="mt-5 text-center text-muted">
          &copy; 2026 Saceanu Ionut Sorin. All Rights Reserved.
        </footer>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
