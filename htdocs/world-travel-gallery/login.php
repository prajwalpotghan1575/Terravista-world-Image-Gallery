<?php
require_once __DIR__ . '/config/db.php';

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if (isLoggedIn()) {
    header("Location: " . $redirect);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Invalid email address or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In | TerraVista</title>

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
        <h2>Welcome Back</h2>
        <p>Sign in to manage your world trips, picnic bookings and saved galleries.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php?redirect=<?= urlencode($redirect) ?>">
        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required value="prajwal@worldgallery.com" />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-icon-box">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required value="password123" />
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:0.8rem; font-size:1rem; margin-top:0.5rem;">
          <i class="fa-solid fa-right-to-bracket"></i> Sign In
        </button>

        <div style="background:#f1f5f9; padding:0.75rem; border-radius:8px; margin-top:1.25rem; font-size:0.82rem; text-align:center; color:#475569;">
          💡 <strong>Demo User:</strong> prajwal@worldgallery.com | <strong>Pass:</strong> password123
        </div>
      </form>

      <div class="auth-footer">
        Don't have an account yet? <a href="register.php">Create Free Account</a>
        <br><br>
        <a href="index.php" style="color:var(--text-muted); font-size:0.85rem;"><i class="fa-solid fa-arrow-left"></i> Back to Home Gallery</a>
      </div>
    </div>
  </div>

</body>
</html>