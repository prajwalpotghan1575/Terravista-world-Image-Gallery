<?php
require_once __DIR__ . '/config/db.php';

$query = "
    SELECT d.*, p.id as package_id, p.package_name, p.package_type, p.duration, 
           p.price_per_person, p.discount_price, p.rating, p.inclusions
    FROM destinations d
    LEFT JOIN packages p ON d.id = p.destination_id
    ORDER BY d.id ASC
";
$stmt = $pdo->query($query);
$destinations = $stmt->fetchAll();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TerraVista | World Visual Gallery & Curated Tour Packages</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <!-- Navigation Bar -->
  <header class="navbar">
    <div class="nav-container">
      <a href="index.php" class="brand">
        <span class="brand-icon"><i class="fa-solid fa-earth-americas"></i></span>
        <div class="brand-text">
          <h1>TerraVista</h1>
          <span>World Gallery & Journeys</span>
        </div>
      </a>

      <nav>
        <ul class="nav-links">
          <li><a href="#gallery"><i class="fa-solid fa-images"></i> Gallery</a></li>
          <li><a href="#packages"><i class="fa-solid fa-compass"></i> Tour Packages</a></li>
          <li><a href="#mapSection"><i class="fa-solid fa-map-location-dot"></i> Live Map</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <?php if ($user): ?>
          <div class="user-profile-badge">
            <span class="user-name">
              <i class="fa-solid fa-circle-user" style="color:var(--primary);"></i>
              <?= htmlspecialchars($user['name']) ?>
            </span>
            <a href="my-bookings.php" class="btn btn-outline" style="padding: 0.35rem 0.85rem; font-size: 0.8rem;">
              <i class="fa-solid fa-ticket"></i> My Trips
            </a>
            <a href="logout.php" class="logout-icon-btn" title="Log Out">
              <i class="fa-solid fa-right-from-bracket"></i>
            </a>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline"><i class="fa-regular fa-user"></i> Log In</a>
          <a href="register.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Join Free</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Hero Banner -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-pill">
        <i class="fa-solid fa-plane-departure"></i> Explore 7 Continents • Luxury Picnics & Tours
      </div>
      <h2>Discover The Wonders of Our <span>Planet</span></h2>
      <p>Breathtaking global galleries, verified GPS locations, and hand-crafted vacation & picnic itineraries designed for memories that last a lifetime.</p>

      <div class="hero-search-card">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="gallerySearch" placeholder="Search world wonders, cities, countries (Paris, Kyoto, Grand Canyon)..." />
        <a href="#gallery" class="btn btn-primary">Find Destinations</a>
      </div>

      <div class="hero-stats">
        <div class="stat-item"><h3>10+</h3><p>Global Wonders</p></div>
        <div class="stat-item"><h3>100%</h3><p>Verified GPS Pins</p></div>
        <div class="stat-item"><h3>4.9★</h3><p>Tour Rating</p></div>
        <div class="stat-item"><h3>2,500+</h3><p>Happy Travelers</p></div>
      </div>
    </div>
  </section>

  <!-- Section 1: Gallery -->
  <section class="section-wrapper" id="gallery">
    <div class="section-head">
      <span class="section-tag">Visual Heritage</span>
      <h2 class="section-title">Wonders of the World Gallery</h2>
      <p class="section-subtitle">High-resolution photographic collection of Earth's most breathtaking architectural and natural landscapes.</p>
    </div>

    <div class="filter-tabs">
      <button class="filter-btn active" data-filter="all">All Continents</button>
      <button class="filter-btn" data-filter="Europe">Europe</button>
      <button class="filter-btn" data-filter="Asia">Asia</button>
      <button class="filter-btn" data-filter="Americas">Americas</button>
      <button class="filter-btn" data-filter="Africa">Africa</button>
      <button class="filter-btn" data-filter="Oceania">Oceania</button>
    </div>

    <div class="gallery-grid" id="galleryGrid">
      <?php foreach ($destinations as $dest): ?>
        <article class="gallery-card" 
                 data-continent="<?= htmlspecialchars($dest['continent']) ?>"
                 data-lat="<?= $dest['latitude'] ?>"
                 data-lng="<?= $dest['longitude'] ?>"
                 data-title="<?= htmlspecialchars($dest['title']) ?>"
                 data-location="<?= htmlspecialchars($dest['city'] . ', ' . $dest['country']) ?>"
                 data-img="<?= htmlspecialchars($dest['image_url']) ?>"
                 data-package-id="<?= $dest['package_id'] ?>">
          <div class="gallery-img-box">
            <img src="<?= htmlspecialchars($dest['image_url']) ?>" alt="<?= htmlspecialchars($dest['title']) ?>" loading="lazy" />
            <div class="card-badges">
              <span class="badge badge-continent"><?= htmlspecialchars($dest['continent']) ?></span>
              <span class="badge"><?= htmlspecialchars($dest['best_season']) ?></span>
            </div>
          </div>

          <div class="gallery-card-body">
            <div class="card-geo">
              <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($dest['city'] . ', ' . $dest['country']) ?>
            </div>
            <h3 class="card-title"><?= htmlspecialchars($dest['title']) ?></h3>
            <p class="card-desc"><?= htmlspecialchars($dest['description']) ?></p>

            <div class="card-footer">
              <span class="card-coords">
                <i class="fa-solid fa-satellite"></i> <?= number_format($dest['latitude'], 4) ?>, <?= number_format($dest['longitude'], 4) ?>
              </span>
              <button class="btn btn-outline view-on-map-btn" 
                      style="padding: 0.35rem 0.85rem; font-size: 0.78rem;"
                      data-lat="<?= $dest['latitude'] ?>" 
                      data-lng="<?= $dest['longitude'] ?>">
                <i class="fa-solid fa-map-pin"></i> View on Map
              </button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Section 2: Packages -->
  <section class="section-wrapper" id="packages" style="background:#fff; border-top:1px solid var(--border-color); border-bottom:1px solid var(--border-color);">
    <div class="section-head">
      <span class="section-tag">Curated Itineraries</span>
      <h2 class="section-title">All-Inclusive Tour & Picnic Packages</h2>
      <p class="section-subtitle">From sunset gourmet lawn picnics by the Eiffel Tower to Andean mountain treks and Serengeti bush picnics.</p>
    </div>

    <div class="packages-grid">
      <?php foreach ($destinations as $dest): ?>
        <div class="package-card">
          <div class="package-img-box">
            <img src="<?= htmlspecialchars($dest['image_url']) ?>" alt="<?= htmlspecialchars($dest['package_name']) ?>" loading="lazy" />
            <span class="package-type-badge"><i class="fa-solid fa-basket-shopping"></i> <?= htmlspecialchars($dest['package_type']) ?></span>
          </div>

          <div class="package-body">
            <div class="package-meta">
              <span class="package-duration"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($dest['duration']) ?></span>
              <span class="package-rating"><i class="fa-solid fa-star"></i> <?= number_format($dest['rating'], 1) ?></span>
            </div>

            <h3 class="package-name"><?= htmlspecialchars($dest['package_name']) ?></h3>
            <div class="package-inclusions">
              <strong>Highlights:</strong> <?= htmlspecialchars(substr($dest['inclusions'], 0, 140)) ?>...
            </div>

            <div class="package-footer">
              <div class="package-pricing">
                <?php if ($dest['discount_price']): ?>
                  <span class="price-original">$<?= number_format($dest['price_per_person'], 2) ?></span>
                  <span class="price-current">$<?= number_format($dest['discount_price'], 2) ?> <span>/ person</span></span>
                <?php else: ?>
                  <span class="price-current">$<?= number_format($dest['price_per_person'], 2) ?> <span>/ person</span></span>
                <?php endif; ?>
              </div>

              <a href="package-details.php?id=<?= $dest['package_id'] ?>" class="btn btn-primary">
                Explore Details & Book <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Section 3: Live Interactive Map -->
  <section class="section-wrapper" id="mapSection">
    <div class="section-head">
      <span class="section-tag">Global Geo-Locations</span>
      <h2 class="section-title">Live World Location Map</h2>
      <p class="section-subtitle">Click on any marker to inspect live GPS coordinates, photography, and picnic packages.</p>
    </div>

    <div class="map-container-box">
      <div id="worldMap"></div>
      <div class="map-legend">
        <div><span class="dot-pin"></span> Live Destination Pin &bull; Click pin for details</div>
        <div>OpenStreetMap & Leaflet.js</div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col">
        <h4>TerraVista Globe</h4>
        <p>Combining world art and photography with seamless picnic and trip logistics across all continents.</p>
      </div>
      <div class="footer-col">
        <h4>Continents</h4>
        <ul class="footer-links">
          <li><a href="#gallery">Europe</a></li>
          <li><a href="#gallery">Asia</a></li>
          <li><a href="#gallery">Americas</a></li>
          <li><a href="#gallery">Africa</a></li>
          <li><a href="#gallery">Oceania</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Travelers</h4>
        <ul class="footer-links">
          <li><a href="login.php">Member Sign In</a></li>
          <li><a href="register.php">Create Free Account</a></li>
          <li><a href="my-bookings.php">My Reserved Trips</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <p><i class="fa-solid fa-envelope"></i> support@terravista.com</p>
        <p><i class="fa-solid fa-phone"></i> +1 (800) 456-WORLD</p>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; <?= date('Y') ?> TerraVista World Image Gallery & Travel Packages Project By-Prajwal Potghan
    </div>
  </footer>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
  <script src="js/main.js"></script>
</body>
</html>