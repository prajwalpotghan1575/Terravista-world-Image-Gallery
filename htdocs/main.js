document.addEventListener("DOMContentLoaded", () => {
  // Leaflet Map Initialization
  const mapElement = document.getElementById("worldMap");

  if (mapElement && typeof L !== "undefined") {
    const map = L.map("worldMap", {
      center: [20.0, 0.0],
      zoom: 2,
      minZoom: 2,
      maxZoom: 18,
      scrollWheelZoom: false
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: '&copy; OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map);

    const markers = [];
    const destinationCards = document.querySelectorAll(".gallery-card");

    destinationCards.forEach((card) => {
      const lat = parseFloat(card.getAttribute("data-lat"));
      const lng = parseFloat(card.getAttribute("data-lng"));
      const title = card.getAttribute("data-title");
      const location = card.getAttribute("data-location");
      const img = card.getAttribute("data-img");
      const packageId = card.getAttribute("data-package-id");

      if (!isNaN(lat) && !isNaN(lng)) {
        const customIcon = L.divIcon({
          className: "custom-map-pin",
          html: `
            <div style="background:#0284c7; width:30px; height:30px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); border:2px solid #fff; box-shadow:0 3px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center;">
              <span style="transform:rotate(45deg); color:#fff; font-size:13px;"><i class="fa-solid fa-location-dot"></i></span>
            </div>
          `,
          iconSize: [30, 30],
          iconAnchor: [15, 30],
          popupAnchor: [0, -30]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
        const popupContent = `
          <div class="map-popup-card">
            <img src="${img}" alt="${title}">
            <div class="map-popup-info">
              <h4>${title}</h4>
              <p><i class="fa-solid fa-location-dot" style="color:#0284c7;"></i> ${location}</p>
              <a href="package-details.php?id=${packageId}" class="map-popup-btn">
                <i class="fa-solid fa-suitcase"></i> View Picnic & Trip Package
              </a>
            </div>
          </div>
        `;
        marker.bindPopup(popupContent);
        markers.push({ id: packageId, marker, lat, lng });
      }
    });

    // View on map button click
    document.querySelectorAll(".view-on-map-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const lat = parseFloat(btn.getAttribute("data-lat"));
        const lng = parseFloat(btn.getAttribute("data-lng"));

        mapElement.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => {
          map.flyTo([lat, lng], 8, { duration: 1.5 });
          const matched = markers.find((m) => Math.abs(m.lat - lat) < 0.001 && Math.abs(m.lng - lng) < 0.001);
          if (matched) matched.marker.openPopup();
        }, 600);
      });
    });
  }

  // Continent Filters
  const filterBtns = document.querySelectorAll(".filter-btn");
  const galleryCards = document.querySelectorAll(".gallery-card");

  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const continent = btn.getAttribute("data-filter");
      galleryCards.forEach((card) => {
        const cardContinent = card.getAttribute("data-continent");
        card.style.display = (continent === "all" || cardContinent === continent) ? "flex" : "none";
      });
    });
  });

  // Live Search
  const searchInput = document.getElementById("gallerySearch");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const term = e.target.value.toLowerCase().trim();
      galleryCards.forEach((card) => {
        const title = card.getAttribute("data-title").toLowerCase();
        const location = card.getAttribute("data-location").toLowerCase();
        card.style.display = (title.includes(term) || location.includes(term)) ? "flex" : "none";
      });
    });
  }
});