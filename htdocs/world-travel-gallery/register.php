<?php
require_once __DIR__ . '/config/db.php';

$error = '';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $insert->execute([$name, $email, $hash]);

            $userId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Free Account | TerraVista</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">

  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-head">
        <a href="index.php" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem; margin-bottom:1rem; color:var(--primary); font-weight:700;">
          <i class="fa-solid fa-earth-americas" style="font-size:1.5rem;"></i> TerraVista
        </a>
        <h2>Join TerraVista</h2>
        <p>Unlock custom travel bookings, picnic packages and save favorite destinations.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <div class="form-group">
          <label for="name">Full Name</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-user"></i>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. John Doe" required />
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Create Password</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password" name="password" class="form-control" placeholder="At least 6 characters" minlength="6" required />
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-shield-halved"></i>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required />
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:0.8rem; font-size:1rem; margin-top:0.5rem;">
          <i class="fa-solid fa-user-plus"></i> Create Account
        </button>
      </form>

      <div class="auth-footer">
        Already have an account? <a href="login.php">Sign In</a>
        <br><br>
        <a href="index.php" style="color:var(--text-muted); font-size:0.85rem;"><i class="fa-solid fa-arrow-left"></i> Back to Home Gallery</a>
      </div>
    </div>
  </div>

</body>
</html>