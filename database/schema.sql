-- Eko FM Core PHP CMS Schema
-- Compatible with MySQL 5.7+ / MariaDB and PHP 7.x

CREATE DATABASE IF NOT EXISTS `ekofm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ekofm`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS radio_settings;
DROP TABLE IF EXISTS user_permissions;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS page_sections;
DROP TABLE IF EXISTS homepage_sections;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS news_posts;
DROP TABLE IF EXISTS news_categories;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS dramas;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS rate_cards;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS settings;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  UNIQUE KEY uniq_role_perm (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  permission_id INT NOT NULL,
  UNIQUE KEY uniq_user_perm (user_id, permission_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  content MEDIUMTEXT NULL,
  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(255) NULL,
  social_image VARCHAR(255) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE page_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  section_key VARCHAR(120) NOT NULL,
  title VARCHAR(200) NULL,
  content MEDIUMTEXT NULL,
  cta_text VARCHAR(120) NULL,
  cta_link VARCHAR(255) NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 1,
  is_visible TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE homepage_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section_key VARCHAR(100) NOT NULL UNIQUE,
  section_title VARCHAR(150) NULL,
  sort_order INT NOT NULL DEFAULT 1,
  status TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE news_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE news_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  summary TEXT NULL,
  content MEDIUMTEXT NULL,
  featured_image VARCHAR(255) NULL,
  publish_date DATETIME NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(255) NULL,
  social_image VARCHAR(255) NULL,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NULL,
  presenter VARCHAR(150) NULL,
  cover_image VARCHAR(255) NULL,
  day_of_week VARCHAR(40) NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  description TEXT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE dramas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  short_description TEXT NULL,
  category_name VARCHAR(120) NULL,
  cover_image VARCHAR(255) NULL,
  audio_file VARCHAR(255) NULL,
  audio_url VARCHAR(255) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  icon_class VARCHAR(120) NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 1,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE rate_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  price_label VARCHAR(120) NULL,
  sort_order INT NOT NULL DEFAULT 1,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NULL,
  category_name VARCHAR(120) NULL,
  file_path VARCHAR(255) NOT NULL,
  file_type VARCHAR(50) NULL,
  file_size INT NULL,
  created_by INT NULL,
  created_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(150) NOT NULL UNIQUE,
  setting_value MEDIUMTEXT NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(255) NULL,
  message TEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'new',
  created_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(50) NOT NULL,
  module_name VARCHAR(100) NOT NULL,
  item_id INT NULL,
  description VARCHAR(255) NULL,
  ip_address VARCHAR(60) NULL,
  created_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE radio_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  stream_url VARCHAR(255) NOT NULL,
  stream_title VARCHAR(200) NULL,
  player_enabled TINYINT(1) NOT NULL DEFAULT 1,
  updated_by INT NULL,
  updated_at DATETIME NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NULL,
  ip_address VARCHAR(60) NULL,
  successful TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- Seed Roles
INSERT INTO roles (id, name, slug, created_at, updated_at) VALUES
(1, 'Super Admin', 'super_admin', NOW(), NOW()),
(2, 'Editor', 'editor', NOW(), NOW());

-- Seed Permissions
INSERT INTO permissions (slug, description) VALUES
('pages.manage','Manage pages and section blocks'),
('hero.manage','Manage hero areas'),
('news.manage','Manage news module full access'),
('news.view','View news'),
('news.create','Create news'),
('news.edit','Edit news'),
('news.delete','Delete news'),
('programs.manage','Manage programs'),
('schedule.manage','Manage schedules'),
('dramas.manage','Manage dramas'),
('gallery.manage','Manage gallery'),
('services.manage','Manage services'),
('ratecard.manage','Manage rate card'),
('contact.manage','Manage contact module'),
('media.manage','Manage media library'),
('users.manage','Manage users'),
('roles.manage','Manage roles and permissions'),
('radio.manage','Manage radio stream'),
('settings.manage','Manage global settings');

-- Seed role permissions for Editor
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE slug IN ('news.view','news.create','news.edit','programs.manage','schedule.manage','gallery.manage','media.manage');

-- Seed users
INSERT INTO users (id, role_id, name, email, password, status, created_at, updated_at) VALUES
(1, 1, 'Super Admin', 'admin@ekofm.com', '$2y$12$53mpNKP0Y4Muw8.F82sIZOQjsFMZvV1nudAWpx/Qrl0Fi0RrQTdAm', 1, NOW(), NOW()),
(2, 2, 'News Editor', 'editor@ekofm.com', '$2y$12$53mpNKP0Y4Muw8.F82sIZOQjsFMZvV1nudAWpx/Qrl0Fi0RrQTdAm', 1, NOW(), NOW());

-- Seed content
INSERT INTO news_categories (name, slug, status, created_at, updated_at) VALUES
('Health Tips', 'health-tips', 1, NOW(), NOW()),
('Community Stories', 'community-stories', 1, NOW(), NOW()),
('Event Coverage', 'event-coverage', 1, NOW(), NOW()),
('News', 'news', 1, NOW(), NOW());

INSERT INTO news_posts (category_id, title, slug, summary, content, featured_image, publish_date, status, meta_title, meta_description, created_by, created_at, updated_at) VALUES
(1, '3 Simple Health Tips to Stay Safe This Rainy Season', 'health-tips-rainy-season', 'Our health desk shares practical prevention tips for families in Kotido.', 'From safe water practices to preventing common infections, EKO DOCTOR outlines steps every household can apply right away.', '', NOW(), 'published', 'Health Tips by EKO FM', 'Practical health advice for Karamoja listeners', 1, NOW(), NOW()),
(2, 'Community Voices: Youth Leaders Share Peace Stories', 'community-voices-youth-peace-stories', 'Young leaders in Karamoja are using dialogue to prevent conflict.', 'EKO FM spoke to local youth champions whose work is building trust and resilience across communities.', '', NOW(), 'published', 'Community Stories on EKO FM', 'Local stories of peace and development', 1, NOW(), NOW()),
(3, 'EKO FM Live Outreach at Boma Grounds Draws Big Crowd', 'eko-fm-outreach-boma-grounds', 'Listeners joined the team for health talks, music and civic education.', 'The station took programming on-ground with partners, proving radio impact goes beyond the studio.', '', NOW(), 'published', 'Event Coverage - EKO FM', 'Highlights from EKO FM outreach in Kotido', 1, NOW(), NOW());

INSERT INTO programs (title, slug, presenter, day_of_week, start_time, end_time, description, status, created_at, updated_at) VALUES
('HELLO KARAMOJA (MAATA KARAMOJA)', 'hello-karamoja-maata-karamoja', 'Host Team', 'Monday', '07:00:00', '09:00:00', 'Community stories, civic updates and culture.', 1, NOW(), NOW()),
('THE EKO DRIVE', 'the-eko-drive', 'Drive Team', 'Weekdays', '17:00:00', '19:00:00', 'Evening drive-time show with updates and music.', 1, NOW(), NOW()),
('EKO DOCTOR', 'eko-doctor', 'Dr. Guest Panel', 'Sunday', '19:00:00', '21:00:00', 'Your trusted health show on EKO FM - breaking myths, sharing truth, and saving lives.', 1, NOW(), NOW()),
('EKO KIDS', 'eko-kids', 'Kids Crew', 'Saturday', '10:00:00', '11:00:00', 'Fun and educational content for children.', 1, NOW(), NOW()),
('GOSPEL EXPLOSION', 'gospel-explosion', 'Faith Team', 'Sunday', '06:00:00', '08:00:00', 'Inspirational worship and faith reflections.', 1, NOW(), NOW()),
('EKO COUNTRY', 'eko-country', 'Country Host', 'Friday', '20:00:00', '22:00:00', 'Country hits and storytelling from the region.', 1, NOW(), NOW());

INSERT INTO dramas (title, slug, short_description, category_name, cover_image, audio_url, status, created_at, updated_at) VALUES
('Echoes of the Island', 'echoes-of-the-island', 'A gripping Lagos family saga.', 'Drama', '', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 1, NOW(), NOW()),
('The Third Mainland Secret', 'third-mainland-secret', 'Political thriller set in modern Lagos.', 'Thriller', '', 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3', 1, NOW(), NOW());

INSERT INTO services (title, description, icon_class, sort_order, status, created_at, updated_at) VALUES
('Radio Broadcasting', 'High-quality programming that informs, entertains, and engages diverse audiences.', 'podcasts', 1, 1, NOW(), NOW()),
('Advertising & Promotions', 'We help brands and organizations reach and connect with communities effectively.', 'campaign', 2, 1, NOW(), NOW()),
('Community Engagement', 'From health campaigns to youth empowerment, we take radio beyond the studio.', 'groups', 3, 1, NOW(), NOW()),
('Digital Content', 'We create and distribute engaging content across social media and online platforms.', 'smart_display', 4, 1, NOW(), NOW());

INSERT INTO rate_cards (category_name, title, description, price_label, sort_order, status, created_at, updated_at) VALUES
('Advertising', '30s Prime-Time Spot', 'Peak hours package', 'UGX 350,000', 1, 1, NOW(), NOW()),
('Advertising', '60s Prime-Time Spot', 'Peak hours package', 'UGX 600,000', 2, 1, NOW(), NOW()),
('Campaign', 'On-Air + Digital Bundle', 'Radio + social media', 'UGX 1,200,000', 3, 1, NOW(), NOW());

INSERT INTO pages (title, slug, content, status, created_by, created_at, updated_at) VALUES
('Home', 'home', 'EKO FM homepage', 1, 1, NOW(), NOW()),
('About Us', 'about', 'About EKO FM in Karamoja', 1, 1, NOW(), NOW()),
('Listen Live', 'listen-live', 'Listen to EKO FM live stream', 1, 1, NOW(), NOW()),
('Shows', 'shows', 'Flagship shows and details', 1, 1, NOW(), NOW()),
('Advertise / Partner', 'advertise-partner', 'Partner with EKO FM', 1, 1, NOW(), NOW()),
('News', 'news', 'News and blog updates', 1, 1, NOW(), NOW()),
('Schedule', 'schedule', 'Daily and weekly schedule', 1, 1, NOW(), NOW()),
('Gallery', 'gallery', 'Photos and media gallery', 1, 1, NOW(), NOW()),
('Contact', 'contact', 'Contact EKO FM', 1, 1, NOW(), NOW());

INSERT INTO homepage_sections (section_key, section_title, sort_order, status, updated_at) VALUES
('about', 'About', 1, 1, NOW()),
('shows', 'Featured Shows', 2, 1, NOW()),
('latest_content', 'Latest Content', 3, 1, NOW()),
('partner_strip', 'Partner Strip', 4, 1, NOW()),
('services', 'What We Do', 5, 1, NOW());

INSERT INTO media (title, category_name, file_path, file_type, file_size, created_by, created_at) VALUES
('Community Outreach 1', 'community-outreach', '', 'image', 0, 1, NOW()),
('Studio Shot 1', 'studio-shots', '', 'image', 0, 1, NOW()),
('Event Photo 1', 'event-photos', '', 'image', 0, 1, NOW());

INSERT INTO settings (setting_key, setting_value, updated_at) VALUES
('site_name', 'EKO FM', NOW()),
('site_tagline', 'The Heartbeat of Karamoja', NOW()),
('footer_tagline', 'On-Air. Online. On-Ground. For Peace & Development', NOW()),
('home_hero_title', 'EKO FM', NOW()),
('home_hero_subtitle', 'The Heartbeat of Karamoja', NOW()),
('home_hero_line', 'On-Air. Online. On-Ground.', NOW()),
('home_hero_copy', 'We are a community-driven radio station delivering music, real conversations, and life-changing information that informs, inspires, and empowers.', NOW()),
('home_hero_cta_text', 'Listen Live', NOW()),
('home_hero_cta_link', '/listen-live', NOW()),
('home_meta_title', 'EKO FM | The Heartbeat of Karamoja', NOW()),
('home_meta_description', 'Music. Culture. Community. Impact. For Peace & Development.', NOW()),
('radio_stream_url', 'https://5.39.82.219/22094/listen.mp3', NOW()),
('radio_stream_title', 'Now Playing: EKO FM Live', NOW()),
('radio_embed_script', '//myradiostream.com/embed/mayugefmuganda', NOW()),
('radio_player_enabled', '1', NOW()),
('contact_location', 'Kotido, Karamoja, Uganda', NOW()),
('contact_address', 'Abim Road, Lokore Cells, Near Boma Grounds', NOW()),
('contact_phone', '+256 751 161 355', NOW()),
('contact_email', 'info@eko.fm', NOW()),
('contact_whatsapp', '0791 996450', NOW()),
('social_tiktok_handle', '91.2 Eko fm Live', NOW()),
('social_facebook_handle', 'Ekofm kotido 91.2', NOW()),
('social_instagram_handle', '91.2 Eko fm Live', NOW()),
('social_x_handle', '91.2 Eko fm live', NOW()),
('social_youtube_handle', '91.2 Eko fm live', NOW()),
('social_whatsapp_handle', '0791 996450', NOW()),
('social_x_url', 'https://x.com/ekofmkotido', NOW()),
('social_facebook_url', 'https://www.facebook.com/share/1CK8U1M63U/', NOW()),
('social_youtube_url', 'https://youtube.com/@ekofm-x2n1l?si=p2Z3IpjNiSMWvnBq', NOW()),
('social_tiktok_url', 'https://www.tiktok.com/@91.2.eko.fm?_r=1&_t=ZS-94z2dBOly7A', NOW()),
('social_instagram_url', 'https://www.instagram.com/ekofmlive?utm_source=qr&igsh=MXY3YnJ5ZGxlNGFkcQ==', NOW()),
('partner_cta_title', 'Work With Us', NOW()),
('partner_cta_text', 'EKO FM partners with NGOs, health organizations, government agencies, and businesses to deliver impactful communication campaigns.', NOW()),
('contact_page_intro', 'Reach Eko FM team for adverts, programs and community updates.', NOW());
