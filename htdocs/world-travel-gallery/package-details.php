<?php
require_once __DIR__ . '/config/db.php';

$package_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("
    SELECT p.*, d.title, d.continent, d.country, d.city, d.latitude, d.longitude, 
           d.image_url, d.description, d.best_season
    FROM packages p
    JOIN destinations d ON p.destination_id = d.id
    WHERE p.id = ?
");
$stmt->execute([$package_id]);
$package = $stmt->fetch();

if (!$package) {
    header("Location: index.php");
    exit;
}

$user = getCurrentUser();
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_trip') {
    if (!$user) {
        header("Location: login.php?redirect=" . urlencode("package-details.php?id=" . $package_id));
        exit;
    }

    $travel_date = trim($_POST['travel_date'] ?? '');
    $travelers_count = (int)($_POST['travelers_count'] ?? 1);
    $special_requests = trim($_POST['special_requests'] ?? '');

    if (empty($travel_date) || $travelers_count < 1) {
        $message = "Please select a valid date and traveler count.";
        $message_type = "danger";
    } else {
        $unit_price = $package['discount_price'] ?? $package['price_per_person'];
        $total_price = $unit_price * $travelers_count;

        $insertStmt = $pdo->prepare("
            INSERT INTO bookings (user_id, package_id, travel_date, travelers_count, special_requests, total_price, status)
            VALUES (?, ?, ?, ?, ?, ?, 'confirmed')
        ");
        $insertStmt->execute([$user['id'], $package_id, $travel_date, $travelers_count, $special_requests, $total_price]);

        header("Location: my-bookings.php?booked=success");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($package['package_name']) ?> | TerraVista</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <link rel="stylesheet" href="css/style.css" />

  <style>
    .details-hero {
      position: relative; height: 50vh; min-height: 360px;
      background: linear-gradient(rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.75)),
                  url('<?= htmlspecialchars($package['image_url']) ?>') center/cover no-repeat;
      display: flex; align-items: flex-end; color: #ffffff; padding: 3rem 1.5rem;
    }
    .details-grid {
      display: grid; grid-template-columns: 2fr 1.15fr; gap: 3rem;
      max-width: 1360px; margin: 3.5rem auto 5rem; padding: 0 1.5rem;
    }
    .detail-card {
      background: var(--bg-card); border: 1px solid var(--border-color);
      border-radius: var(--radius-lg); padding: 2.25rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm);
    }
    .detail-card h3 { font-family: var(--font-serif); font-size: 1.45rem; font-weight: 700; margin-bottom: 1.25rem; }
    .itinerary-step { display: flex; gap: 1.25rem; margin-bottom: 1.5rem; }
    .step-circle {
      width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: #fff;
      display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;
    }
    #singleMap { width: 100%; height: 320px; border-radius: var(--radius-md); margin-top: 1rem; }
    @media (max-width: 900px) { .details-grid { grid-template-columns: 1fr; } }
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
        <a href="index.php#packages" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Packages</a>
        <?php if ($user): ?>
          <a href="my-bookings.php" class="btn btn-primary"><i class="fa-solid fa-ticket"></i> My Bookings</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="details-hero">
    <div style="max-width:1360px; margin:0 auto; width:100%;">
      <div style="display:flex; gap:0.5rem; margin-bottom:0.75rem;">
        <span class="badge badge-continent"><?= htmlspecialchars($package['continent']) ?></span>
        <span class="badge"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($package['city'] . ', ' . $package['country']) ?></span>
        <span class="badge"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($package['duration']) ?></span>
      </div>
      <h1 style="font-family:var(--font-serif); font-size:clamp(1.8rem, 4vw, 3rem); font-weight:800;">
        <?= htmlspecialchars($package['package_name']) ?>
      </h1>
    </div>
  </div>

  <div class="details-grid">
    <div>
      <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="detail-card">
        <h3><i class="fa-solid fa-landmark" style="color:var(--primary);"></i> Destination Overview</h3>
        <p style="font-size:1.05rem; color:var(--text-muted); line-height:1.8; margin-bottom:1.5rem;">
          <?= htmlspecialchars($package['description']) ?>
        </p>
        <div style="display:flex; gap:2rem; flex-wrap:wrap; font-size:0.9rem;">
          <div><strong>Season:</strong> <?= htmlspecialchars($package['best_season']) ?></div>
          <div><strong>Group Limit:</strong> Up to <?= $package['max_group_size'] ?> guests</div>
          <div><strong>Rating:</strong> <?= number_format($package['rating'], 1) ?> ★</div>
        </div>
      </div>

      <div class="detail-card">
        <h3><i class="fa-solid fa-route" style="color:var(--primary);"></i> Daily Tour & Picnic Itinerary</h3>
        <?php 
          $days = explode("\n", trim($package['itinerary_summary']));
          foreach ($days as $i => $day): 
            $parts = explode(":", $day, 2);
        ?>
          <div class="itinerary-step">
            <div class="step-circle"><?= ($i + 1) ?></div>
            <div>
              <h4><?= htmlspecialchars(trim($parts[0] ?? "Day " . ($i + 1))) ?></h4>
              <p style="color:var(--text-muted); font-size:0.92rem;"><?= htmlspecialchars(trim($parts[1] ?? $day)) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="detail-card">
        <h3><i class="fa-solid fa-box-open" style="color:var(--primary);"></i> Package Inclusions</h3>
        <ul style="list-style:none; padding:0; display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:0.85rem;">
          <?php foreach (explode(",", $package['inclusions']) as $inc): ?>
            <li style="display:flex; align-items:center; gap:0.5rem; font-size:0.92rem; color:var(--text-muted);">
              <i class="fa-solid fa-circle-check" style="color:var(--emerald);"></i> <?= htmlspecialchars(trim($inc)) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="detail-card">
        <h3><i class="fa-solid fa-map-pin" style="color:var(--primary);"></i> Live Location & Coordinates</h3>
        <p style="font-size:0.88rem; color:var(--text-muted);">
          Coordinates: <?= number_format($package['latitude'], 6) ?>, <?= number_format($package['longitude'], 6) ?> (<?= htmlspecialchars($package['city'] . ', ' . $package['country']) ?>)
        </p>
        <div id="singleMap"></div>
      </div>
    </div>

    <!-- Booking Sidebar -->
    <div>
      <div class="detail-card" style="box-shadow:var(--shadow-lg); border-top:4px solid var(--primary);">
        <div style="margin-bottom:1.25rem;">
          <span style="font-size:0.82rem; color:var(--text-light); text-transform:uppercase; font-weight:700;">Price per guest</span>
          <div style="display:flex; align-items:baseline; gap:0.6rem;">
            <?php if ($package['discount_price']): ?>
              <span style="font-size:1.1rem; color:var(--text-light); text-decoration:line-through;">$<?= number_format($package['price_per_person'], 2) ?></span>
              <span style="font-size:2rem; font-weight:800; color:var(--navy);">$<?= number_format($package['discount_price'], 2) ?></span>
            <?php else: ?>
              <span style="font-size:2rem; font-weight:800; color:var(--navy);">$<?= number_format($package['price_per_person'], 2) ?></span>
            <?php endif; ?>
            <span style="color:var(--text-muted); font-size:0.88rem;">/ person</span>
          </div>
        </div>

        <form method="POST" action="package-details.php?id=<?= $package['id'] ?>">
          <input type="hidden" name="action" value="book_trip" />

          <div class="form-group">
            <label for="travel_date"><i class="fa-regular fa-calendar-days"></i> Travel Date</label>
            <input type="date" id="travel_date" name="travel_date" class="form-control" min="<?= date('Y-m-d', strtotime('+2 days')) ?>" required />
          </div>

          <div class="form-group">
            <label for="travelers_count"><i class="fa-solid fa-user-group"></i> Guests</label>
            <input type="number" id="travelers_count" name="travelers_count" class="form-control" min="1" max="<?= $package['max_group_size'] ?>" value="1" required />
          </div>

          <div class="form-group">
            <label for="special_requests"><i class="fa-solid fa-utensils"></i> Picnic & Dietary Preferences</label>
            <textarea id="special_requests" name="special_requests" class="form-control" rows="3" placeholder="e.g. Vegetarian picnic basket, red wine..."></textarea>
          </div>

          <div style="background:var(--bg-page); padding:1rem; border-radius:var(--radius-md); margin-bottom:1.25rem;">
            <div style="display:flex; justify-content:space-between; font-weight:700;">
              <span>Total Price:</span>
              <span id="totalDisplay">$<?= number_format($package['discount_price'] ?? $package['price_per_person'], 2) ?></span>
            </div>
          </div>

          <?php if ($user): ?>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:0.85rem;">
              <i class="fa-solid fa-check-circle"></i> Confirm Reservation
            </button>
          <?php else: ?>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:0.85rem;">
              <i class="fa-solid fa-lock"></i> Log In to Book Package
            </button>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
  <script>
    const lat = <?= (float)$package['latitude'] ?>;
    const lng = <?= (float)$package['longitude'] ?>;
    const singleMap = L.map('singleMap').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(singleMap);
    L.marker([lat, lng]).addTo(singleMap).bindPopup("<b><?= addslashes($package['title']) ?></b>").openPopup();

    const unitPrice = <?= (float)($package['discount_price'] ?? $package['price_per_person']) ?>;
    document.getElementById('travelers_count').addEventListener('input', (e) => {
      let count = parseInt(e.target.value) || 1;
      document.getElementById('totalDisplay').textContent = '$' + (unitPrice * Math.max(1, count)).toLocaleString();
    });
  </script>
</body>
</html>