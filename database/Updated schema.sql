-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 25, 2026 at 09:27 AM
-- Server version: 10.3.39-MariaDB-cll-lve
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `almatech_ekoradio`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ip_address` varchar(60) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module_name`, `item_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'auth', 1, 'User logged in', '127.0.0.1', '2026-04-13 22:31:31'),
(2, 1, 'update', 'news', 3, 'Updated news post: EKO FM Live Outreach at Boma Grounds Draws Big Crowd', '127.0.0.1', '2026-04-15 01:35:29'),
(3, 1, 'update', 'news', 2, 'Updated news post: Community Voices: Youth Leaders Share Peace Stories', '127.0.0.1', '2026-04-15 01:35:42'),
(4, 1, 'update', 'news', 1, 'Updated news post: 3 Simple Health Tips to Stay Safe This Rainy Season', '127.0.0.1', '2026-04-15 01:35:51'),
(5, 1, 'login', 'auth', 1, 'User logged in', '102.85.19.197', '2026-04-19 14:45:09'),
(6, 1, 'logout', 'auth', 1, 'User logged out', '102.85.19.197', '2026-04-19 14:50:44'),
(7, 2, 'login', 'auth', 2, 'User logged in', '102.85.19.197', '2026-04-19 14:50:53'),
(8, 1, 'login', 'auth', 1, 'User logged in', '41.210.146.203', '2026-04-25 09:12:11');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dramas`
--

CREATE TABLE `dramas` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `short_description` text DEFAULT NULL,
  `category_name` varchar(120) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `audio_file` varchar(255) DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dramas`
--

INSERT INTO `dramas` (`id`, `title`, `slug`, `short_description`, `category_name`, `cover_image`, `audio_file`, `audio_url`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'When the rain doesn\'t come', 'when-the-rain-doesnt-come', '', 'General', 'dramas/covers/20260419194757_95aa3fa0.jpeg', 'dramas/audio/20260419194757_dc7e7ec5.mp3', '', 1, NULL, '2026-04-19 14:47:57', '2026-04-19 14:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `section_title` varchar(150) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `section_key`, `section_title`, `sort_order`, `status`, `updated_at`) VALUES
(1, 'about', 'About', 1, 1, '2026-04-13 22:29:05'),
(2, 'shows', 'Featured Shows', 2, 1, '2026-04-13 22:29:05'),
(3, 'latest_content', 'Latest Content', 3, 1, '2026-04-13 22:29:05'),
(4, 'partner_strip', 'Partner Strip', 4, 1, '2026-04-13 22:29:05'),
(5, 'services', 'What We Do', 5, 1, '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `ip_address` varchar(60) DEFAULT NULL,
  `successful` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `successful`, `created_at`) VALUES
(1, 'admin@ekofm.com', '127.0.0.1', 0, '2026-04-13 22:31:09'),
(2, 'admin@ekofm.com', '127.0.0.1', 1, '2026-04-13 22:31:31'),
(3, 'admin@ekoradio.com', '41.210.145.251', 0, '2026-04-14 20:06:32'),
(4, 'admin@ekoradio.fm', '102.85.19.197', 0, '2026-04-19 14:32:30'),
(5, 'admin@ekoradio.fm', '102.85.19.197', 0, '2026-04-19 14:32:47'),
(6, 'admin@ekoradio.fm', '102.85.19.197', 1, '2026-04-19 14:45:09'),
(7, 'news@ekoradio.fm', '102.85.19.197', 1, '2026-04-19 14:50:53'),
(8, 'admin@ekoradio.fm', '41.210.146.203', 1, '2026-04-25 09:12:11');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `category_name` varchar(120) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `title`, `category_name`, `file_path`, `file_type`, `file_size`, `created_by`, `created_at`) VALUES
(1, 'Community Outreach 1', 'community-outreach', '', 'image', 0, 1, '2026-04-13 22:29:05'),
(2, 'Studio Shot 1', 'studio-shots', '', 'image', 0, 1, '2026-04-13 22:29:05'),
(3, 'Event Photo 1', 'event-photos', '', 'image', 0, 1, '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_categories`
--

INSERT INTO `news_categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Health Tips', 'health-tips', 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(2, 'Community Stories', 'community-stories', 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(3, 'Event Coverage', 'event-coverage', 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(4, 'News', 'news', 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `news_posts`
--

CREATE TABLE `news_posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `social_image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_posts`
--

INSERT INTO `news_posts` (`id`, `category_id`, `title`, `slug`, `summary`, `content`, `featured_image`, `publish_date`, `status`, `meta_title`, `meta_description`, `social_image`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '3 Simple Health Tips to Stay Safe This Rainy Season', 'health-tips-rainy-season', 'Our health desk shares practical prevention tips for families in Kotido.', 'From safe water practices to preventing common infections, EKO DOCTOR outlines steps every household can apply right away.', 'news/20260414233551_6e1f4bfe.jpeg', '2026-04-13 00:00:00', 'published', 'Health Tips by EKO FM', 'Practical health advice for Karamoja listeners', NULL, 1, '2026-04-13 22:29:05', '2026-04-15 01:35:51'),
(2, 2, 'Community Voices: Youth Leaders Share Peace Stories', 'community-voices-youth-peace-stories', 'Young leaders in Karamoja are using dialogue to prevent conflict.', 'EKO FM spoke to local youth champions whose work is building trust and resilience across communities.', 'news/20260414233542_ace93490.jpeg', '2026-04-13 00:00:00', 'published', 'Community Stories on EKO FM', 'Local stories of peace and development', NULL, 1, '2026-04-13 22:29:05', '2026-04-15 01:35:42'),
(3, 3, 'EKO FM Live Outreach at Boma Grounds Draws Big Crowd', 'eko-fm-outreach-boma-grounds', 'Listeners joined the team for health talks, music and civic education.', 'The station took programming on-ground with partners, proving radio impact goes beyond the studio.', 'news/20260414233528_c97c8e6b.jpeg', '2026-04-13 00:00:00', 'published', 'Event Coverage - EKO FM', 'Highlights from EKO FM outreach in Kotido', NULL, 1, '2026-04-13 22:29:05', '2026-04-15 01:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `social_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `social_image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Home', 'home', 'EKO FM homepage', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(2, 'About Us', 'about', 'About EKO FM in Karamoja', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(3, 'Listen Live', 'listen-live', 'Listen to EKO FM live stream', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(4, 'Shows', 'shows', 'Flagship shows and details', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(5, 'Advertise / Partner', 'advertise-partner', 'Partner with EKO FM', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(6, 'News', 'news', 'News and blog updates', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(7, 'Schedule', 'schedule', 'Daily and weekly schedule', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(8, 'Gallery', 'gallery', 'Photos and media gallery', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(9, 'Contact', 'contact', 'Contact EKO FM', NULL, NULL, NULL, 1, 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section_key` varchar(120) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `cta_text` varchar(120) DEFAULT NULL,
  `cta_link` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `slug`, `description`) VALUES
(1, 'pages.manage', 'Manage pages and section blocks'),
(2, 'hero.manage', 'Manage hero areas'),
(3, 'news.manage', 'Manage news module full access'),
(4, 'news.view', 'View news'),
(5, 'news.create', 'Create news'),
(6, 'news.edit', 'Edit news'),
(7, 'news.delete', 'Delete news'),
(8, 'programs.manage', 'Manage programs'),
(9, 'schedule.manage', 'Manage schedules'),
(10, 'dramas.manage', 'Manage dramas'),
(11, 'gallery.manage', 'Manage gallery'),
(12, 'services.manage', 'Manage services'),
(13, 'ratecard.manage', 'Manage rate card'),
(14, 'contact.manage', 'Manage contact module'),
(15, 'media.manage', 'Manage media library'),
(16, 'users.manage', 'Manage users'),
(17, 'roles.manage', 'Manage roles and permissions'),
(18, 'radio.manage', 'Manage radio stream'),
(19, 'settings.manage', 'Manage global settings');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `presenter` varchar(150) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `cover_focus_x` tinyint(3) UNSIGNED NOT NULL DEFAULT 50,
  `cover_focus_y` tinyint(3) UNSIGNED NOT NULL DEFAULT 50,
  `day_of_week` varchar(40) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `slug`, `presenter`, `cover_image`, `cover_focus_x`, `cover_focus_y`, `day_of_week`, `start_time`, `end_time`, `description`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Mid-day Express', 'midday-express', 'Jennifer', 'programs/20260414220823_a4ff90d2.jpeg', 49, 27, 'Monday - Friday', '12:00:00', '14:00:00', 'Ride through your day with Midday Express — the perfect blend of music, updates, and lively conversation to keep your afternoon moving. From trending stories and quick news bites to interactive segments and listener shoutouts, we bring you energy, entertainment, and good vibes right when you need them most. Whether you’re at work, on the move, or taking a break, Midday Express keeps you connected and refreshed all afternoon long.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:13:30'),
(2, 'Eko Drive', 'eko-drive', 'Lydia Wealth', 'programs/20260414221754_10a4db6d.jpeg', 44, 20, 'Monday - Friday', '15:00:00', '19:00:00', 'Wind down your day with the ultimate evening drive-time show — a perfect mix of great music, timely updates, and engaging conversations. Stay informed with the latest news, traffic, and community highlights while enjoying back-to-back hits to keep you energized on your way home. It’s your smooth companion for the road, blending information and entertainment to close the day right.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:17:54'),
(3, 'The News Round Up', 'the-news-round-up', 'Lydia, Jennifer & Titus', 'programs/20260414224027_0c21ab13.jpeg', 50, 16, 'Saturday', '06:00:00', '08:00:00', 'Stay informed with The News Round-Up every Saturday — your comprehensive recap of the week’s top stories. From local headlines to national and international developments, we break down the news that matters most. With clear analysis, expert insights, and balanced reporting, this show keeps you up to date and ready for the week ahead.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:41:02'),
(4, 'Eko Sports', 'eko-sports', 'Capital P', 'programs/20260414221157_3033e1d8.jpeg', 48, 17, 'Monday - Friday', '14:00:00', '15:00:00', 'A dynamic and engaging sports show bringing you the latest updates, live discussions, match analysis, and expert opinions from the world of sports. From local talent to international leagues, we break down the action, highlight key moments, and give fans a voice. Stay informed, entertained, and connected to the game with real-time updates, interviews, and in-depth coverage of your favorite teams and athletes.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:11:57'),
(5, 'The Gospel Explosion', 'the-gospel-explosion', 'Lydia Wealth', 'programs/20260414223319_d053dcdf.jpeg', 48, 18, 'Sunday', '06:00:00', '10:00:00', 'Experience the power of praise with The Gospel Explosion — a soul-lifting show filled with inspiring gospel music, uplifting messages, and words of hope. Tune in for a blend of powerful worship, testimonies, and spiritual encouragement that strengthens your faith and brightens your day. It’s more than a show — it’s a celebration of faith, joy, and the presence of God.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:33:19'),
(6, 'Maata Karamoja', 'maata', 'Sharon & Titus', 'programs/20260414220009_e9d806cf.jpeg', 49, 17, 'Monday - Friday', '06:00:00', '10:00:00', 'Start your day right with an energetic and uplifting morning show that keeps you informed, inspired, and entertained. From the latest news, weather updates, and traffic reports to engaging conversations, music, and community stories — this show is your perfect daily companion. Tune in for motivation, laughter, and everything you need to kickstart your morning with positivity and purpose.', 1, NULL, '2026-04-13 22:29:05', '2026-04-15 00:12:52'),
(7, 'The Eko Situation', 'the-eko-situation', 'Lomojamoe', 'programs/20260414221934_c71ff0f5.jpeg', 49, 9, 'Monday - Friday', '19:00:00', '21:00:00', '', 1, NULL, '2026-04-15 00:19:34', '2026-04-15 00:19:34'),
(8, 'Eko After Dark', 'eko-after-dark', 'Lady Angel', 'programs/20260414222206_4250fedb.jpeg', 48, 30, 'Monday - Friday', '22:00:00', '01:00:00', '', 1, NULL, '2026-04-15 00:22:06', '2026-04-15 00:22:06'),
(9, 'Etem A Karamoja', 'etem-a-karamoja', 'Lomojamoe', 'programs/20260414222345_d9364c13.jpeg', 51, 13, 'Saturday', '10:00:00', '13:00:00', '', 1, NULL, '2026-04-15 00:23:45', '2026-04-15 00:23:45'),
(10, 'The Eko Top 20', 'the-eko-top-20', 'Lydia Wealth', 'programs/20260414222523_2df06583.jpeg', 48, 22, 'Saturday', '15:00:00', '17:00:00', '', 1, NULL, '2026-04-15 00:25:23', '2026-04-15 00:25:23'),
(11, 'Eko Classics', 'eko-classics', 'The One', 'programs/20260414222814_b47c9fbe.jpeg', 47, 11, 'Saturday', '17:00:00', '20:00:00', '', 1, NULL, '2026-04-15 00:28:14', '2026-04-15 00:28:14'),
(12, 'The Sports Digest', 'the-sports-digest', 'Capital P', 'programs/20260414223005_433a2196.jpeg', 49, 16, 'Saturday', '13:00:00', '15:00:00', '', 1, NULL, '2026-04-15 00:30:05', '2026-04-15 00:30:05'),
(13, 'Eko Live Wire', 'eko-live-wire', 'DJ Markxis', 'programs/20260414223208_938d528d.jpeg', 51, 12, 'Saturday', '20:00:00', '02:00:00', '', 1, NULL, '2026-04-15 00:32:08', '2026-04-15 00:32:08'),
(14, 'Eko Kids', 'eko-kids', 'Uncle Sam', 'programs/20260414223418_e981c7a6.jpeg', 54, 16, 'Sunday', '10:00:00', '12:00:00', '', 1, NULL, '2026-04-15 00:34:18', '2026-04-15 00:34:18'),
(15, 'Eko Country', 'eko-country', 'Capital P', 'programs/20260414223540_523d3cc9.jpeg', 50, 27, 'Sunday', '17:00:00', '19:00:00', 'Kick back and enjoy the rich sounds of country music on our Country Show — where storytelling meets melody. From timeless classics to modern country hits, we bring you heartfelt songs, artist spotlights, and the stories behind the music. Whether you’re a longtime fan or just discovering the genre, this show delivers good vibes, real emotion, and a touch of the countryside to your day.', 1, NULL, '2026-04-15 00:35:40', '2026-04-15 00:35:40'),
(16, 'Eko Raggae', 'eko-raggae', 'Angel', 'programs/20260414223709_dac36f3f.jpeg', 51, 25, 'Sunday', '21:00:00', '00:00:00', 'Feel the rhythm and embrace the vibe with our Reggae Show — a smooth blend of classic roots, dancehall hits, and conscious reggae music. From legendary icons to rising stars, we bring you the sounds that inspire, uplift, and move the soul. Tune in for positive energy, real messages, and pure island vibes that keep you relaxed and connected.', 1, NULL, '2026-04-15 00:37:09', '2026-04-15 00:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `radio_settings`
--

CREATE TABLE `radio_settings` (
  `id` int(11) NOT NULL,
  `stream_url` varchar(255) NOT NULL,
  `stream_title` varchar(200) DEFAULT NULL,
  `player_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `radio_settings`
--

INSERT INTO `radio_settings` (`id`, `stream_url`, `stream_title`, `player_enabled`, `updated_by`, `updated_at`) VALUES
(1, 'https://5.39.82.21:22094/listen.mp3', 'Now Playing: EKO FM Live', 1, 1, '2026-04-13 22:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `rate_cards`
--

CREATE TABLE `rate_cards` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price_label` varchar(120) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rate_cards`
--

INSERT INTO `rate_cards` (`id`, `category_name`, `title`, `description`, `price_label`, `sort_order`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Advertising', '30s Prime-Time Spot', 'Peak hours package', 'UGX 350,000', 1, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(2, 'Advertising', '60s Prime-Time Spot', 'Peak hours package', 'UGX 600,000', 2, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(3, 'Campaign', 'On-Air + Digital Bundle', 'Radio + social media', 'UGX 1,200,000', 3, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super_admin', '2026-04-13 22:29:04', '2026-04-13 22:29:04'),
(2, 'Editor', 'editor', '2026-04-13 22:29:04', '2026-04-13 22:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`) VALUES
(5, 2, 4),
(3, 2, 5),
(4, 2, 6),
(6, 2, 8),
(7, 2, 9),
(1, 2, 11),
(2, 2, 15);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_class` varchar(120) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon_class`, `image_path`, `sort_order`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Radio Broadcasting', 'High-quality programming that informs, entertains, and engages diverse audiences.', 'podcasts', NULL, 1, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(2, 'Advertising & Promotions', 'We help brands and organizations reach and connect with communities effectively.', 'campaign', NULL, 2, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(3, 'Community Engagement', 'From health campaigns to youth empowerment, we take radio beyond the studio.', 'groups', NULL, 3, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(4, 'Digital Content', 'We create and distribute engaging content across social media and online platforms.', 'smart_display', NULL, 4, 1, NULL, '2026-04-13 22:29:05', '2026-04-13 22:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(150) NOT NULL,
  `setting_value` mediumtext DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'EKO FM', '2026-04-15 01:14:12'),
(2, 'site_tagline', 'The Heartbeat of Karamoja', '2026-04-15 01:14:12'),
(3, 'footer_tagline', 'On-Air. Online. On-Ground. For Peace & Development', '2026-04-15 01:14:12'),
(4, 'home_hero_title', 'EKO FM', '2026-04-15 01:14:12'),
(5, 'home_hero_subtitle', 'The Heartbeat of Karamoja', '2026-04-15 01:14:12'),
(6, 'home_hero_line', 'On-Air. Online. On-Ground.', '2026-04-15 01:14:12'),
(7, 'home_hero_copy', 'We are a community-driven radio station delivering music, real conversations, and life-changing information that informs, inspires, and empowers.', '2026-04-15 01:14:12'),
(8, 'home_hero_cta_text', 'Listen Live', '2026-04-15 01:14:12'),
(9, 'home_hero_cta_link', '/listen-live', '2026-04-15 01:14:12'),
(10, 'home_meta_title', 'EKO FM | The Heartbeat of Karamoja', '2026-04-15 01:14:12'),
(11, 'home_meta_description', 'Music. Culture. Community. Impact. For Peace & Development.', '2026-04-15 01:14:12'),
(12, 'radio_stream_url', 'https://5.39.82.21:22094/listen.mp3', '2026-04-13 22:32:36'),
(13, 'radio_stream_title', 'Now Playing: EKO FM Live', '2026-04-13 22:32:36'),
(14, 'radio_player_enabled', '1', '2026-04-13 22:32:36'),
(15, 'contact_location', 'Kotido, Karamoja, Uganda', '2026-04-15 01:14:12'),
(16, 'contact_address', 'Abim Road, Lokore Cells, Near Boma Grounds', '2026-04-15 01:14:12'),
(17, 'contact_phone', '+256 751 161 355', '2026-04-15 01:14:12'),
(18, 'contact_email', 'info@ekoradio.fm', '2026-04-15 01:14:12'),
(19, 'contact_whatsapp', '0791 996450', '2026-04-15 01:14:12'),
(20, 'social_tiktok_handle', '91.2 Eko fm Live', '2026-04-15 01:14:12'),
(21, 'social_facebook_handle', 'Ekofm kotido 91.2', '2026-04-15 01:14:12'),
(22, 'social_instagram_handle', '91.2 Eko fm Live', '2026-04-15 01:14:12'),
(23, 'social_x_handle', '91.2 Eko fm live', '2026-04-15 01:14:12'),
(24, 'social_youtube_handle', '91.2 Eko fm live', '2026-04-15 01:14:12'),
(25, 'social_whatsapp_handle', '0791 996450', '2026-04-15 01:14:12'),
(26, 'social_x_url', 'https://x.com/ekofmkotido', '2026-04-15 01:14:12'),
(27, 'social_facebook_url', 'https://www.facebook.com/share/1CK8U1M63U/', '2026-04-15 01:14:12'),
(28, 'social_youtube_url', 'https://youtube.com/@ekofm-x2n1l?si=p2Z3IpjNiSMWvnBq', '2026-04-15 01:14:12'),
(29, 'social_tiktok_url', 'https://www.tiktok.com/@91.2.eko.fm?_r=1&_t=ZS-94z2dBOly7A', '2026-04-15 01:14:12'),
(30, 'social_instagram_url', 'https://www.instagram.com/ekofmlive?utm_source=qr&igsh=MXY3YnJ5ZGxlNGFkcQ==', '2026-04-15 01:14:12'),
(31, 'partner_cta_title', 'Work With Us', '2026-04-15 01:14:12'),
(32, 'partner_cta_text', 'EKO FM partners with NGOs, health organizations, government agencies, and businesses to deliver impactful communication campaigns.', '2026-04-15 01:14:12'),
(33, 'contact_page_intro', 'Reach Eko FM team for adverts, programs and community updates.', '2026-04-15 01:14:12'),
(67, 'home_hero_bg', 'settings/20260414192748_329f1947.jpeg', '2026-04-14 21:27:48'),
(98, 'home_hero_slides', '[{\"title\":\"EKO FM 91.2\",\"subtitle\":\"The Voice of Karamoja\",\"line\":\"Licensed Commercial Radio Station\",\"copy\":\"EKO FM is a privately owned commercial radio station located in Kotido Municipality, Karamoja Region. Fully licensed by the Uganda Communications Commission (UCC), the station serves communities with reliable information, entertainment, and impactful programming.\",\"button_primary_text\":\"Listen Live\",\"button_primary_link\":\"#\",\"button_secondary_text\":\"Contact Us\",\"button_secondary_link\":\"#\",\"badge\":\"Since 2014\",\"caption\":\"Booma Ground Road, Kotido\",\"foot\":\"Tel: +256 393 242 726 | Email: ekofmkotido@gmail.com\",\"card_opacity\":40,\"card_position\":\"left\",\"image_focus_x\":47,\"image_focus_y\":32,\"image_zoom\":1,\"image\":\"settings\\/20260414232941_28a1a52d.jpeg\"},{\"title\":\"Wide Regional Coverage\",\"subtitle\":\"Reaching Communities Across Karamoja\",\"line\":\"300KM Broadcast Radius\",\"copy\":\"EKO FM operates with a powerful 1KW crown digital transmitter and 6-bay antennas, enabling coverage across Kotido, Kaabong, Napak, Abim, Moroto, Agago, Otuke, and parts of Kitgum, Amuria, Pader, Alebtong, and Western Kenya.\",\"button_primary_text\":\"Advertise With Us\",\"button_primary_link\":\"#\",\"button_secondary_text\":\"View Map\",\"button_secondary_link\":\"#\",\"badge\":\"1KW Digital Transmitter\",\"caption\":\"Strong signal. Wider reach.\",\"foot\":\"Connecting urban and rural audiences\",\"card_opacity\":20,\"card_position\":\"left\",\"image_focus_x\":50,\"image_focus_y\":24,\"image_zoom\":1,\"image\":\"settings\\/20260414233052_1919b348.jpeg\"},{\"title\":\"Diverse Radio Programs\",\"subtitle\":\"Serving Every Listener\",\"line\":\"Community-Centered Content\",\"copy\":\"Our programs target urban and rural audiences including women, youth, farmers, and entrepreneurs. Content covers agriculture, health, education, environment, culture, sports, and economic empowerment.\",\"button_primary_text\":\"View Programs\",\"button_primary_link\":\"#\",\"button_secondary_text\":\"Partner With Us\",\"button_secondary_link\":\"#\",\"badge\":\"Inclusive Programming\",\"caption\":\"Content that transforms lives\",\"foot\":\"Empowering communities through information\",\"card_opacity\":20,\"card_position\":\"left\",\"image_focus_x\":52,\"image_focus_y\":20,\"image_zoom\":1,\"image\":\"settings\\/20260414233038_4df40bfb.jpeg\"},{\"title\":\"Our Aim\",\"subtitle\":\"Transforming Communities Through Media\",\"line\":\"Driven by Impact\",\"copy\":\"EKO FM aims to improve agricultural productivity, food security, and economic development by providing timely and reliable information that empowers communities to utilize their resources effectively.\",\"button_primary_text\":\"Join the Movement\",\"button_primary_link\":\"#\",\"button_secondary_text\":\"Learn More\",\"button_secondary_link\":\"#\",\"badge\":\"Sustainable Growth\",\"caption\":\"Information that builds futures\",\"foot\":\"From awareness to transformation\",\"card_opacity\":20,\"card_position\":\"left\",\"image_focus_x\":48,\"image_focus_y\":23,\"image_zoom\":1,\"image\":\"settings\\/20260414233116_fe630b68.jpeg\"},{\"title\":\"Advertise With EKO FM\",\"subtitle\":\"Flexible & Affordable Rates\",\"line\":\"Grow Your Brand\",\"copy\":\"EKO FM generates revenue through advertisements, sponsored programs, DJ mentions, talk shows, and announcements. Rates vary based on audience reach and time slots, with premium pricing during peak hours.\",\"button_primary_text\":\"View Rate Card\",\"button_primary_link\":\"#\",\"button_secondary_text\":\"Book Slot\",\"button_secondary_link\":\"#\",\"badge\":\"Prime & Standard Slots\",\"caption\":\"Maximum reach. Better results.\",\"foot\":\"Custom packages available\",\"card_opacity\":40,\"card_position\":\"left\",\"image_focus_x\":41,\"image_focus_y\":37,\"image_zoom\":1,\"image\":\"settings\\/20260414233149_90061873.jpeg\"}]', '2026-04-15 01:34:23'),
(166, 'site_logo', 'settings/crop_20260414230811_fe90b575.png', '2026-04-15 01:08:11'),
(167, 'site_favicon', 'settings/crop_20260414230447_eb8ebd64.png', '2026-04-15 01:04:47'),
(199, 'site_logo_zoom', '1', '2026-04-15 01:08:11'),
(271, 'contact_map_embed', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.3278719387563!2d34.10464099678954!3d3.0061787000000155!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x17767334275d2687%3A0x4206cb6ebc8f33!2sKotido%20Boma%20grounds!5e0!3m2!1sen!2sug!4v1776204532698!5m2!1sen!2sug\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '2026-04-15 01:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'admin@ekoradio.fm', '$2y$12$53mpNKP0Y4Muw8.F82sIZOQjsFMZvV1nudAWpx/Qrl0Fi0RrQTdAm', 1, '2026-04-13 22:29:05', '2026-04-13 22:29:05'),
(2, 2, 'News Editor', 'news@ekoradio.fm', '$2y$10$UPaF6k6C2XP4PUCV99pwL.fwK1RNpDrrnpDtT08YPPp636FEGV.Vi', 1, '2026-04-13 22:29:05', '2026-04-19 14:50:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`) VALUES
(6, 2, 3),
(7, 2, 4),
(3, 2, 5),
(5, 2, 6),
(4, 2, 7),
(8, 2, 8),
(10, 2, 9),
(1, 2, 10),
(11, 2, 12),
(9, 2, 13),
(2, 2, 15);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dramas`
--
ALTER TABLE `dramas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio_settings`
--
ALTER TABLE `radio_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `rate_cards`
--
ALTER TABLE `rate_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_role_perm` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_perm` (`user_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dramas`
--
ALTER TABLE `dramas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news_posts`
--
ALTER TABLE `news_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `radio_settings`
--
ALTER TABLE `radio_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rate_cards`
--
ALTER TABLE `rate_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `news_posts`
--
ALTER TABLE `news_posts`
  ADD CONSTRAINT `news_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_posts_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD CONSTRAINT `page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radio_settings`
--
ALTER TABLE `radio_settings`
  ADD CONSTRAINT `radio_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
