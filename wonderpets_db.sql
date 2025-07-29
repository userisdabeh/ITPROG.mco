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

DROP DATABASE wonderpets_db;
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


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
