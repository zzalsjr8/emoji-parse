--
-- Database: `emojis`
--
CREATE DATABASE IF NOT EXISTS `emojis` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `emojis`;

/*
------------------------------------------------------------------------
CATEGORIES TABLE
------------------------------------------------------------------------
*/

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sequence` int DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `emoji` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `sequence`, `category`, `tag`, `emoji`) VALUES
(1, 1, 'Smileys & Emotion', 'smileys-and-emotion', '😀'),
(2, 2, 'People & Body', 'people-and-body', '🧒'),
(3, 3, 'Animals & Nature', 'animals-and-nature', '🐻'),
(4, 4, 'Food & Drink', 'food-and-drink', '🍔'),
(5, 6, 'Travel & Places', 'travel-and-places', '🚀'),
(6, 5, 'Activities', 'activities', '⚽'),
(7, 7, 'Objects', 'objects', '💡'),
(8, 8, 'Symbols', 'symbols', '💕'),
(9, 9, 'Flags', 'flags', '🎌');

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

/*
------------------------------------------------------------------------
EMOJIS TABLE
------------------------------------------------------------------------
*/

CREATE TABLE `emojis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `unicode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subgroup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emoji` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

