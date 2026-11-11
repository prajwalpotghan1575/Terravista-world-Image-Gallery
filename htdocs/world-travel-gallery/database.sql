CREATE DATABASE IF NOT EXISTS `world_gallery_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `world_gallery_db`;

-- Users Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Destinations Table (Gallery & GPS coordinates)
DROP TABLE IF EXISTS `destinations`;
CREATE TABLE `destinations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `continent` VARCHAR(50) NOT NULL,
  `country` VARCHAR(80) NOT NULL,
  `city` VARCHAR(80) NOT NULL,
  `latitude` DECIMAL(10, 6) NOT NULL,
  `longitude` DECIMAL(10, 6) NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `description` TEXT NOT NULL,
  `best_season` VARCHAR(80) DEFAULT 'Year-round',
  `featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tour & Picnic Packages Table
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `destination_id` INT NOT NULL,
  `package_name` VARCHAR(150) NOT NULL,
  `package_type` ENUM('Picnic & Day Tour', 'Holiday Trip', 'Adventure Trek', 'Cultural Heritage') DEFAULT 'Holiday Trip',
  `duration` VARCHAR(50) NOT NULL,
  `price_per_person` DECIMAL(10, 2) NOT NULL,
  `discount_price` DECIMAL(10, 2) DEFAULT NULL,
  `rating` DECIMAL(3, 1) DEFAULT 4.9,
  `inclusions` TEXT NOT NULL,
  `itinerary_summary` TEXT NOT NULL,
  `max_group_size` INT DEFAULT 12,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings Table
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `package_id` INT NOT NULL,
  `travel_date` DATE NOT NULL,
  `travelers_count` INT NOT NULL DEFAULT 1,
  `special_requests` TEXT,
  `total_price` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('confirmed', 'pending', 'cancelled') DEFAULT 'confirmed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Demo Users (Password: password123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Prajwal Potghan', 'prajwal@worldgallery.com', '$2y$10$wT8Kj6Yx8n3M1HjR.e9U0O5LqHj9u6rZz4SjFpPj0xZ6wR1ZzK6QG', 'admin'),
('Emma Watson', 'traveler@worldgallery.com', '$2y$10$wT8Kj6Yx8n3M1HjR.e9U0O5LqHj9u6rZz4SjFpPj0xZ6wR1ZzK6QG', 'user');

-- Seed Destinations with GPS Coordinates
INSERT INTO `destinations` (`id`, `title`, `continent`, `country`, `city`, `latitude`, `longitude`, `image_url`, `description`, `best_season`, `featured`) VALUES
(1, 'Eiffel Tower & Champ de Mars', 'Europe', 'France', 'Paris', 48.858370, 2.294481, 'https://images.unsplash.com/photo-1511739001486-6bfe10ce785f?auto=format&fit=crop&w=1200&q=80', 'The iconic iron lattice tower on the Champ de Mars, symbol of romance, elegance and art. Perfect for vintage sunset wine & cheese picnics on the lawns.', 'April - October', 1),
(2, 'Santorini Caldera & Oia Cliffside', 'Europe', 'Greece', 'Santorini', 36.461800, 25.375300, 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=1200&q=80', 'Perched atop volcanic volcanic cliffs overlooking the sparkling Aegean Sea. Famous for blue-domed churches, cycladic whitewashed alleys, and sunset terraces.', 'May - October', 1),
(3, 'Colosseum & Roman Forum', 'Europe', 'Italy', 'Rome', 41.890210, 12.492231, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?auto=format&fit=crop&w=1200&q=80', 'The colossal amphitheatre built during the Flavian emperors. A monument of gladiatorial history surrounded by ancient pine trees and scenic piazzas.', 'March - June', 1),
(4, 'Arashiyama Bamboo Grove & Kinkaku-ji', 'Asia', 'Japan', 'Kyoto', 35.016390, 135.671390, 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=80', 'A mesmerizing emerald corridor of towering bamboo stalks swaying in the breeze, alongside timeless Zen rock gardens and matcha tea pavilions.', 'March - May, Oct - Nov', 1),
(5, 'Ubud Rice Terraces & Waterfalls', 'Asia', 'Indonesia', 'Bali', -8.431200, 115.279800, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80', 'Lush subak stepped paddy fields carved into volcanic valleys. Tropical palm canopies, floating breakfast picnics, and sacred natural springs.', 'April - October', 1),
(6, 'Machu Picchu Sacred Valley Citadel', 'Americas', 'Peru', 'Cusco', -13.163141, -72.544963, 'https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=1200&q=80', 'Mystical 15th-century Inca citadel built high on an Andean ridge, shrouded in morning mountain mist and offering breathtaking scenic trails.', 'May - September', 1),
(7, 'Grand Canyon National Park', 'Americas', 'USA', 'Arizona', 36.054445, -112.140111, 'https://images.unsplash.com/photo-1474044159687-1ee9f3a51722?auto=format&fit=crop&w=1200&q=80', 'Immense geological chasm carved by the Colorado River across millions of years. Brilliant rock strata with rim-side sunset picnic lookouts.', 'March - May, Sept - Nov', 1),
(8, 'Great Pyramids of Giza & Sphinx', 'Africa', 'Egypt', 'Cairo', 29.979235, 31.134202, 'https://images.unsplash.com/photo-1503177119275-0aa32b3a9368?auto=format&fit=crop&w=1200&q=80', 'The only surviving wonder of the ancient world. Majestic limestone pyramids towering above the golden desert sands beside the timeless Nile River.', 'October - April', 1),
(9, 'Serengeti Endless Plains & Wildlife', 'Africa', 'Tanzania', 'Serengeti', -2.333333, 34.833333, 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1200&q=80', 'World-famous savanna ecosystem famous for the Great Wildebeest Migration, lion prides, and luxury bush picnic banquets under acacia trees.', 'June - October', 1),
(10, 'Sydney Opera House & Harbour', 'Oceania', 'Australia', 'Sydney', -33.856784, 151.215297, 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=1200&q=80', 'Expressionist architectural masterpiece on Bennelong Point surrounded by Sydney Harbour, blue waters, yacht cruises, and Royal Botanic Gardens.', 'September - April', 1);

-- Seed Packages
INSERT INTO `packages` (`id`, `destination_id`, `package_name`, `package_type`, `duration`, `price_per_person`, `discount_price`, `rating`, `inclusions`, `itinerary_summary`, `max_group_size`) VALUES
(1, 1, 'Parisian Romance & Gourmet Seine Picnic', 'Picnic & Day Tour', '4 Days / 3 Nights', 890.00, 750.00, 4.9, 'Champ de Mars luxury picnic basket, 2-hour Seine sunset cruise, Priority Eiffel Tower Summit elevator ticket, Boutique hotel accommodation, English-speaking guide', 'Day 1: Arrival & evening Seine river cruise.\nDay 2: Eiffel Tower summit & lawn picnic.\nDay 3: Louvre Museum private tour.\nDay 4: Departure transfer.', 8),
(2, 2, 'Santorini Caldera Sunset & Wine Tour', 'Holiday Trip', '5 Days / 4 Nights', 1250.00, 1050.00, 5.0, 'Cliffside suite with caldera sea view, Daily Greek breakfast, Catamaran cruise with snorkeling & barbecue on board, Oia sunset photography walk', 'Day 1: Ferry/Airport transfer to villa.\nDay 2: Full-day catamaran sail & picnic.\nDay 3: Traditional villages & wine tasting.\nDay 4: Oia sunset dinner.\nDay 5: Farewell.', 10),
(3, 3, 'Imperial Rome & Tuscan Vineyard Picnic', 'Cultural Heritage', '5 Days / 4 Nights', 1100.00, 920.00, 4.8, 'Colosseum underground arena skip-the-line pass, Day trip to Tuscan hills with farmhouse olive oil & pasta picnic, 4-star central Rome hotel, Vatican Museums entry', 'Day 1: Welcome to Rome.\nDay 2: Colosseum & Roman Forum historic walk.\nDay 3: Tuscany hills winery picnic lunch.\nDay 4: Vatican City & Sistine Chapel.\nDay 5: Check-out.', 12),
(4, 4, 'Kyoto Zen Gardens & Bamboo Forest Retreat', 'Picnic & Day Tour', '6 Days / 5 Nights', 1450.00, 1290.00, 4.9, 'Traditional Ryokan stay with private Onsen bath, Kaiseki gourmet dinner, Kimono rental experience for photo walk, Arashiyama Bamboo Grove picnic with bento boxes', 'Day 1: Arrive Kyoto, tea ceremony welcome.\nDay 2: Bamboo forest & bento picnic.\nDay 3: Golden Pavilion & Fushimi Inari.\nDay 4: Gion geisha district tour.\nDay 5: Nara deer park.\nDay 6: Departure.', 8),
(5, 5, 'Bali Tropical Waterfall & Rice Terrace Picnic', 'Adventure Trek', '5 Days / 4 Nights', 680.00, 540.00, 4.8, 'Private jungle pool villa, Floating flower breakfast, Trekking guide to waterfalls, Organic tropical fruit picnic overlooking Tegalalang rice terraces', 'Day 1: Pick-up & Ubud jungle villa check-in.\nDay 2: Campuhan ridge walk & picnic.\nDay 3: Hidden waterfalls trek.\nDay 4: Beach sunset dinner in Seminyak.\nDay 5: Airport drop-off.', 12),
(6, 6, 'Inca Trail & Sun Gate Sunrise Expedition', 'Adventure Trek', '6 Days / 5 Nights', 1580.00, 1350.00, 5.0, 'Official Inca Trail trekking permits, Professional Quechua mountain guide & porters, Luxury tented campsite with warm Andean meals, Scenic Vistadome train tickets', 'Day 1: Acclimatization in Sacred Valley.\nDay 2: Hike through ruins & mountain camp.\nDay 3: Crossing Dead Woman Pass.\nDay 4: Sunrise arrival at Sun Gate.\nDay 5: Lost City exploration.\nDay 6: Departure.', 10),
(7, 7, 'Grand Canyon Rim Trek & Helicopter Sunset Picnic', 'Adventure Trek', '3 Days / 2 Nights', 790.00, 680.00, 4.9, 'Scenic canyon rim helicopter flight, Sunset champagne & cheese picnic at Lipan Point, Glamping tent accommodation with campfire, Stargazing session', 'Day 1: South Rim campfire welcome.\nDay 2: Bright Angel Trail hike, helicopter tour & sunset picnic.\nDay 3: Sunrise & departure.', 8),
(8, 8, 'Pharaohs Giza Odyssey & Nile Dinner Cruise', 'Cultural Heritage', '5 Days / 4 Nights', 920.00, 780.00, 4.7, 'Private Egyptologist guide, Camel trek across Giza Plateau, Desert tented lunch overlooking the Great Sphinx, 5-star hotel with pyramid views, Nile cruise dinner', 'Day 1: Cairo arrival & hotel check-in.\nDay 2: Giza Pyramids, Sphinx & desert picnic.\nDay 3: Grand Egyptian Museum tour.\nDay 4: Bazaar & Nile dinner cruise.\nDay 5: Departure.', 14),
(9, 9, 'Serengeti Migration Safari & Bush Picnic', 'Adventure Trek', '6 Days / 5 Nights', 2150.00, 1890.00, 5.0, '4x4 customized pop-up safari jeep, All national park fees, Luxury tented safari lodge, Daily dawn and dusk game drives, Full bush picnic banquet surrounded by wilderness', 'Day 1: Fly into Serengeti & game drive.\nDay 2: Lion & leopard tracking with bush picnic.\nDay 3: Migration river crossing.\nDay 4: Hot Air Balloon safari.\nDay 5: Ngorongoro Crater.\nDay 6: Flight back.', 6),
(10, 10, 'Sydney Coastal Walk & Harbour Yacht Picnic', 'Holiday Trip', '4 Days / 3 Nights', 990.00, 840.00, 4.8, 'Private 3-hour catamaran yacht charter on Sydney Harbour with seafood picnic hamper, Guided Bondi to Coogee coastal walk, Sydney Opera House internal tour', 'Day 1: Arrive Sydney & check-in.\nDay 2: Opera House VIP tour & sailing picnic.\nDay 3: Bondi to Bronte clifftop walk.\nDay 4: Botanic Garden & departure.', 10);