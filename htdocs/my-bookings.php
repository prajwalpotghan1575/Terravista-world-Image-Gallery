<?php
require_once __DIR__ . '/config/db.php';

if (!isLoggedIn()) {
    header("Location: login.php?redirect=my-bookings.php");
    exit;
}

$user = getCurrentUser();

$stmt = $pdo->prepare("
    SELECT b.*, p.package_name, p.package_type, p.duration, d.title as destination_title, 
           d.city, d.country, d.image_url
    FROM bookings b
    JOIN packages p ON b.package_id = p.id
    JOIN destinations d ON p.destination_id = d.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Booked Trips & Picnics | TerraVista</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />

  <style>
    .booking-card {
      background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg);
      padding: 1.5rem; margin-bottom: 1.5rem; display: flex; gap: 1.5rem; align-items: center; box-shadow: var(--shadow-sm);
    }
    .booking-thumb { width: 140px; height: 110px; border-radius: var(--radius-md); object-fit: cover; flex-shrink: 0; }
    .booking-info { flex: 1; }
    .status-confirmed { background: #ecfdf5; color: #059669; font-size: 0.78rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 999px; }
  </style>
</head>
<body>

  <header class="navbar">
    <div class="nav-container">
      <a href="index.php" class="brand">
        <span class="brand-icon"><i class="fa-solid fa-earth-americas"></i></span>
        <div class="brand-text"><h1>TerraVista</h1><span>World Gallery & Journeys</span></div>
      </a>
      <div class="nav-actions">
        <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-house"></i> Home Gallery</a>
        <a href="logout.php" class="btn btn-primary"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </div>
  </header>

  <div class="section-wrapper" style="min-height:75vh;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
      <div>
        <h2 style="font-family:var(--font-serif); font-size:2rem; font-weight:800;">My Reserved Trips & Picnics</h2>
        <p style="color:var(--text-muted);">Welcome, <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= htmlspecialchars($user['email']) ?>)</p>
      </div>
      <a href="index.php#packages" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Book Another Trip</a>
    </div>

    <?php if (isset($_GET['booked']) && $_GET['booked'] === 'success'): ?>
      <div class="alert alert-success" style="margin-bottom:2rem;">
        <i class="fa-solid fa-circle-check"></i> <strong>Congratulations!</strong> Your trip reservation has been saved successfully in the database.
      </div>
    <?php endif; ?>

    <?php if (count($bookings) === 0): ?>
      <div style="text-align:center; padding:4rem 1.5rem; background:var(--bg-card); border-radius:var(--radius-lg); border:1px dashed var(--border-color);">
        <i class="fa-solid fa-plane-slash" style="font-size:3.5rem; color:var(--text-light); margin-bottom:1rem;"></i>
        <h3>No Bookings Yet</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">Browse packages to reserve your first journey.</p>
        <a href="index.php#packages" class="btn btn-primary">Browse All Packages</a>
      </div>
    <?php else: ?>
      <?php foreach ($bookings as $b): ?>
        <div class="booking-card">
          <img src="<?= htmlspecialchars($b['image_url']) ?>" alt="<?= htmlspecialchars($b['package_name']) ?>" class="booking-thumb" />
          <div class="booking-info">
            <div style="display:flex; justify-content:space-between; margin-bottom:0.35rem;">
              <span class="status-confirmed"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($b['status']) ?></span>
              <span style="font-size:1.25rem; font-weight:800; color:var(--primary);">$<?= number_format($b['total_price'], 2) ?></span>
            </div>
            <h3 style="font-family:var(--font-serif); font-size:1.25rem; margin-bottom:0.25rem;"><?= htmlspecialchars($b['package_name']) ?></h3>
            <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.6rem;">
              <i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> <?= htmlspecialchars($b['city'] . ', ' . $b['country']) ?> &bull; 
              <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($b['duration']) ?>
            </p>
            <div style="font-size:0.82rem; display:flex; gap:1.5rem; flex-wrap:wrap;">
              <div><strong>Travel Date:</strong> <?= date('F j, Y', strtotime($b['travel_date'])) ?></div>
              <div><strong>Travelers:</strong> <?= $b['travelers_count'] ?> guest(s)</div>
              <?php if (!empty($b['special_requests'])): ?>
                <div><strong>Note:</strong> <?= htmlspecialchars($b['special_requests']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <footer class="footer" style="padding:2rem 1.5rem; text-align:center; font-size:0.85rem; color:#94a3b8;">
    &copy; <?= date('Y') ?> TerraVista Travel Portal.
  </footer>

</body>
</html>