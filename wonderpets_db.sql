-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 19, 2025 at 12:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wonderpets_db`
--

DROP DATABASE IF EXISTS wonderpets_db;
CREATE DATABASE wonderpets_db;
USE wonderpets_db;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `current_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_image` LONGBLOB DEFAULT NULL,
  `profile_image_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `pet_types`
--

CREATE TABLE `pet_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `breeds`
--

CREATE TABLE `breeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pet_type_id` int(11) NOT NULL,
  `breed_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `size_category` enum('Small','Medium','Large','Extra Large') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pet_type_id` (`pet_type_id`),
  CONSTRAINT `breeds_pet_type_fk` FOREIGN KEY (`pet_type_id`) REFERENCES `pet_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `pet_type_id` int(11) NOT NULL,
  `breed_id` int(11) DEFAULT NULL,
  `age_years` int(11) DEFAULT NULL,
  `age_months` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Unknown') NOT NULL,
  `size` enum('Small','Medium','Large','Extra Large') DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'Weight in kg',
  `color` varchar(100) DEFAULT NULL,
  `personality_traits` text DEFAULT NULL,
  `health_conditions` text DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `adoption_fee` decimal(10,2) DEFAULT 0.00,
  `is_spayed_neutered` tinyint(1) DEFAULT NULL,
  `is_house_trained` tinyint(1) DEFAULT NULL,
  `good_with_kids` tinyint(1) DEFAULT NULL,
  `good_with_pets` tinyint(1) DEFAULT NULL,
  `energy_level` enum('Low','Medium','High') DEFAULT NULL,
  `intake_date` date NOT NULL,
  `status` enum('available','pending','adopted','medical_hold','unavailable') NOT NULL DEFAULT 'available',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `microchip_id` varchar(50) DEFAULT NULL,
  `pet_image` LONGBLOB DEFAULT NULL,
  `pet_image_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pet_type_id` (`pet_type_id`),
  KEY `breed_id` (`breed_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `pets_type_fk` FOREIGN KEY (`pet_type_id`) REFERENCES `pet_types` (`id`),
  CONSTRAINT `pets_breed_fk` FOREIGN KEY (`breed_id`) REFERENCES `breeds` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `pet_photos`
--

CREATE TABLE `pet_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pet_id` int(11) NOT NULL,
  `photo_path` varchar(500) NOT NULL,
  `photo_name` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pet_id` (`pet_id`),
  KEY `idx_primary` (`is_primary`),
  CONSTRAINT `photos_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `pet_documents`
--

CREATE TABLE `pet_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pet_id` int(11) NOT NULL,
  `document_type` enum('vaccination_record','medical_history','spay_neuter_certificate','microchip_info','intake_form','other') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pet_id` (`pet_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `documents_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_user_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `adoption_applications`
--

CREATE TABLE `adoption_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `home_environment` text NOT NULL,
  `previous_experience` text DEFAULT NULL,
  `commitment_statement` text NOT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `status` enum('submitted','under_review','interview_required','approved','denied','completed','withdrawn') NOT NULL DEFAULT 'submitted',
  `denial_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `interview_scheduled_at` datetime DEFAULT NULL,
  `interview_completed_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pet_id` (`pet_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `applications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_approver_fk` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `adoption_history`
--

CREATE TABLE `adoption_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `history_application_fk` FOREIGN KEY (`application_id`) REFERENCES `adoption_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `history_user_fk` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `adoptions`
--

CREATE TABLE `adoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `adoption_date` date NOT NULL,
  `adoption_fee_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `adoption_contract_signed` tinyint(1) NOT NULL DEFAULT 0,
  `follow_up_required` tinyint(1) NOT NULL DEFAULT 1,
  `follow_up_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_id` (`application_id`),
  KEY `user_id` (`user_id`),
  KEY `pet_id` (`pet_id`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `adoptions_application_fk` FOREIGN KEY (`application_id`) REFERENCES `adoption_applications` (`id`),
  CONSTRAINT `adoptions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `adoptions_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  CONSTRAINT `adoptions_processor_fk` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_pet` (`user_id`, `pet_id`),
  KEY `pet_id` (`pet_id`),
  CONSTRAINT `favorites_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_pet_fk` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `related_table` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Insert sample data
--

-- Insert pet types
INSERT INTO `pet_types` (`type_name`, `description`) VALUES
('Dog', 'Canine companions of all breeds and sizes'),
('Cat', 'Feline friends, both domestic and mixed breeds'),
('Rabbit', 'Small mammals, great for families'),
('Bird', 'Various bird species available for adoption');

-- Insert sample breeds
INSERT INTO `breeds` (`pet_type_id`, `breed_name`, `size_category`) VALUES
(1, 'Golden Retriever', 'Large'),
(1, 'Labrador Retriever', 'Large'),
(1, 'German Shepherd', 'Large'),
(1, 'Bulldog', 'Medium'),
(1, 'Beagle', 'Medium'),
(1, 'Chihuahua', 'Small'),
(1, 'Mixed Breed', 'Medium'),
(2, 'Maine Coon', 'Large'),
(2, 'Persian', 'Medium'),
(2, 'Siamese', 'Medium'),
(2, 'British Shorthair', 'Medium'),
(2, 'Domestic Shorthair', 'Medium'),
(2, 'Domestic Longhair', 'Medium');

-- Insert admin user with default profile image
INSERT INTO `users` (`full_name`, `email`, `password`, `age`, `current_address`, `permanent_address`, `phone`, `role`, `is_active`) VALUES
('Admin User', 'admin@wonderpets.com', '1234', 30, 'Shelter Address', 'Shelter Address', '+1234567890', 'admin', 1),
('Darren Sanchez', 'darren_sanchez@dlsu.edu.ph', '1234', 20, 'Mandaluyong', 'Mandaluyong', '+1234567890', 'user', 1);

-- Insert sample pets
INSERT INTO `pets` (`name`, `pet_type_id`, `breed_id`, `age_years`, `age_months`, `gender`, `size`, `weight`, `color`, `personality_traits`, `health_conditions`, `special_needs`, `description`, `adoption_fee`, `is_spayed_neutered`, `is_house_trained`, `good_with_kids`, `good_with_pets`, `energy_level`, `intake_date`, `status`, `is_featured`, `microchip_id`) VALUES

-- Dogs
('Buddy', 1, 1, 3, 6, 'Male', 'Large', 28.50, 'Golden', 'Friendly, energetic, loves to play fetch. Great with children and very loyal. Enjoys long walks and outdoor activities.', 'Healthy, up to date on all vaccinations', NULL, 'Buddy is a wonderful Golden Retriever who loves people and other dogs. He would make a perfect family companion and is great with kids of all ages. He knows basic commands and walks well on a leash.', 350.00, 1, 1, 1, 1, 'High', '2024-11-15', 'available', 1, 'MC001234567'),
('Luna', 1, 2, 2, 3, 'Female', 'Large', 25.00, 'Yellow', 'Calm, intelligent, loves swimming. Very trainable and eager to please.', 'Healthy', NULL, 'Luna is a sweet Labrador who loves water and would be perfect for an active family. She is well-behaved, house-trained, and gets along with everyone she meets.', 300.00, 1, 1, 1, 1, 'Medium', '2024-12-01', 'available', 0, 'MC001234568'),
('Max', 1, 3, 4, 0, 'Male', 'Large', 32.00, 'Black and Tan', 'Protective, loyal, intelligent. Well-trained and obedient.', 'Healthy', NULL, 'Max is a well-trained German Shepherd who would make an excellent companion for someone looking for a loyal and protective dog. He knows many commands and is very well-behaved.', 400.00, 1, 1, 1, 0, 'Medium', '2024-10-20', 'available', 1, 'MC001234569'),
('Rosie', 1, 4, 5, 2, 'Female', 'Medium', 22.00, 'Brindle', 'Gentle, loves naps, good with seniors. Low maintenance and very sweet.', 'Mild arthritis, managed with supplements', 'Senior care recommended', 'Rosie is a gentle Bulldog who would be perfect for someone looking for a calm, loving companion. She enjoys short walks and lots of cuddles.', 250.00, 1, 1, 1, 1, 'Low', '2024-09-15', 'available', 0, 'MC001234570'),
('Charlie', 1, 5, 1, 8, 'Male', 'Medium', 12.00, 'Tri-color', 'Playful, curious, loves exploring. Great with kids and very social.', 'Healthy', NULL, 'Charlie is a young Beagle with lots of energy and curiosity. He would do well with an active family who can provide him with plenty of exercise and mental stimulation.', 275.00, 1, 0, 1, 1, 'High', '2024-12-10', 'available', 0, 'MC001234571'),
('Bella', 1, 6, 3, 0, 'Female', 'Small', 3.50, 'Cream', 'Confident, alert, loves to be carried. Perfect lap dog.', 'Healthy', NULL, 'Bella is a tiny Chihuahua with a big personality. She would be perfect for someone looking for a small companion who loves to be close to their owner.', 200.00, 1, 1, 0, 0, 'Medium', '2024-11-20', 'available', 0, 'MC001234572'),
('Rocky', 1, 7, 2, 6, 'Male', 'Medium', 18.00, 'Brown and White', 'Friendly, adaptable, loves everyone. Mixed breed with the best of many breeds.', 'Healthy', NULL, 'Rocky is a wonderful mixed breed who gets along with everyone. He is the perfect size for most families and has a great temperament.', 225.00, 1, 1, 1, 1, 'Medium', '2024-11-05', 'available', 0, 'MC001234573'),
('Sophie', 1, 1, 1, 2, 'Female', 'Large', 22.00, 'Light Golden', 'Puppy energy, loves training, very smart and eager to learn.', 'Healthy, puppy vaccinations complete', 'Puppy training needed', 'Sophie is an adorable Golden Retriever puppy who is ready to learn and grow with her new family. She is very intelligent and responds well to positive training.', 400.00, 0, 0, 1, 1, 'High', '2025-01-10', 'available', 1, 'MC001234580'),
('Diesel', 1, 3, 6, 0, 'Male', 'Large', 35.00, 'Sable', 'Mature, calm, excellent guard dog. Gentle with family but protective.', 'Healthy, senior dog health check complete', 'Regular exercise, joint supplements', 'Diesel is a mature German Shepherd who would be perfect for someone wanting a well-trained, protective companion. He is gentle with his family but alert to strangers.', 300.00, 1, 1, 1, 0, 'Medium', '2024-08-30', 'available', 0, 'MC001234581'),
('Daisy', 1, 7, 0, 10, 'Female', 'Small', 8.00, 'Black and White', 'Puppy, very social, loves other dogs and people.', 'Healthy', 'Puppy socialization and training', 'Daisy is a sweet mixed breed puppy who loves everyone she meets. She would do well in a home with other pets and children.', 250.00, 0, 0, 1, 1, 'High', '2025-01-05', 'available', 0, 'MC001234582'),

-- Cats
('Whiskers', 2, 8, 2, 0, 'Male', 'Large', 6.80, 'Orange Tabby', 'Gentle giant, loves to be brushed, very calm and affectionate.', 'Healthy', NULL, 'Whiskers is a beautiful Maine Coon who loves attention and being pampered. He would be perfect for someone who wants a large, gentle cat companion.', 150.00, 1, 1, 1, 1, 'Low', '2024-10-30', 'available', 1, 'MC001234574'),
('Princess', 2, 9, 4, 6, 'Female', 'Medium', 4.20, 'White', 'Elegant, prefers quiet environments, loves to be groomed.', 'Healthy', NULL, 'Princess is a beautiful Persian cat who would thrive in a calm, quiet home. She loves being brushed and pampered.', 175.00, 1, 1, 0, 0, 'Low', '2024-09-25', 'available', 0, 'MC001234575'),
('Shadow', 2, 10, 1, 10, 'Male', 'Medium', 4.50, 'Seal Point', 'Vocal, intelligent, very social and loves to communicate.', 'Healthy', NULL, 'Shadow is a talkative Siamese who loves to "chat" with his humans. He is very social and would do well with an active family.', 125.00, 1, 1, 1, 1, 'High', '2024-12-05', 'available', 0, 'MC001234576'),
('Mittens', 2, 12, 0, 8, 'Female', 'Medium', 3.20, 'Black and White', 'Playful kitten, loves toys, very curious and energetic.', 'Healthy', NULL, 'Mittens is an adorable kitten who loves to play and explore. She would be perfect for a family who wants to raise a kitten.', 100.00, 1, 0, 1, 1, 'High', '2024-12-15', 'available', 1, 'MC001234577'),
('Smokey', 2, 12, 6, 0, 'Male', 'Medium', 5.50, 'Gray', 'Senior cat, very calm, loves sunny spots and gentle pets.', 'Mild dental issues', 'Senior care, soft food recommended', 'Smokey is a gentle senior cat who would be perfect for someone looking for a calm, loving companion. He enjoys sunny spots and gentle attention.', 75.00, 1, 1, 1, 1, 'Low', '2024-08-10', 'available', 0, 'MC001234578'),
('Ginger', 2, 13, 3, 3, 'Female', 'Medium', 4.80, 'Orange', 'Independent but affectionate, loves to hunt toy mice.', 'Healthy', NULL, 'Ginger is a beautiful orange longhair cat who is independent but loves her humans. She would do well in a quiet home.', 125.00, 1, 1, 1, 0, 'Medium', '2024-10-05', 'available', 0, 'MC001234579'),
('Oliver', 2, 11, 3, 0, 'Male', 'Medium', 5.20, 'Blue-Gray', 'Calm, regal, loves quiet companionship. Perfect indoor cat.', 'Healthy', NULL, 'Oliver is a handsome British Shorthair who loves peaceful environments. He is perfect for someone who wants a calm, loving indoor companion.', 140.00, 1, 1, 1, 1, 'Low', '2024-11-08', 'available', 0, 'MC001234583'),
('Cleo', 2, 12, 0, 6, 'Female', 'Medium', 2.80, 'Calico', 'Kitten, very playful, loves climbing and exploring.', 'Healthy, kitten vaccinations complete', 'Kitten-proofed home needed', 'Cleo is an adorable calico kitten who is full of energy and curiosity. She would be perfect for a family who wants to watch a kitten grow up.', 120.00, 0, 0, 1, 1, 'High', '2025-01-15', 'available', 1, 'MC001234584'),
('Jasper', 2, 12, 8, 0, 'Male', 'Medium', 6.00, 'Black', 'Senior cat, very affectionate, loves lap time and quiet moments.', 'Hyperthyroidism, managed with medication', 'Daily medication, regular vet check-ups', 'Jasper is a loving senior cat who would be perfect for someone who wants a gentle, affectionate companion. He loves to sit quietly with his humans.', 50.00, 1, 1, 1, 1, 'Low', '2024-07-20', 'available', 0, 'MC001234585'),
('Nala', 2, 13, 2, 6, 'Female', 'Medium', 4.30, 'Tortoiseshell', 'Playful, loves interactive toys, very smart and curious.', 'Healthy', NULL, 'Nala is a beautiful tortoiseshell who loves to play and solve puzzle toys. She would be perfect for someone who wants an interactive, intelligent cat.', 130.00, 1, 1, 1, 0, 'Medium', '2024-12-20', 'available', 0, 'MC001234586'),
('Leo', 2, 8, 4, 0, 'Male', 'Large', 7.50, 'Brown Tabby', 'Gentle giant, loves children, very patient and calm.', 'Healthy', NULL, 'Leo is a magnificent Maine Coon who is wonderful with children. He is patient, gentle, and would make an excellent family cat.', 160.00, 1, 1, 1, 1, 'Low', '2024-09-10', 'available', 0, 'MC001234587');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
