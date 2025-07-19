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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `current_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet_types`
--

CREATE TABLE `pet_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `breeds`
--

CREATE TABLE `breeds` (
  `id` int(11) NOT NULL,
  `pet_type_id` int(11) NOT NULL,
  `breed_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `size_category` enum('Small','Medium','Large','Extra Large') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet_photos`
--

CREATE TABLE `pet_photos` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `photo_path` varchar(500) NOT NULL,
  `photo_name` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet_documents`
--

CREATE TABLE `pet_documents` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `document_type` enum('vaccination_record','medical_history','spay_neuter_certificate','microchip_info','intake_form','other') NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
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
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_status_history`
--

CREATE TABLE `application_status_history` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adoptions`
--

CREATE TABLE `adoptions` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `related_table` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Sample data insertion
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

-- Insert admin user
INSERT INTO `users` (`full_name`, `email`, `password`, `age`, `current_address`, `permanent_address`, `phone`, `role`, `is_active`, `email_verified`) VALUES
('Admin User', 'admin@wonderpets.com', '1234', 30, 'Shelter Address', 'Shelter Address', '+1234567890', 'admin', 1, 1),
('Darren Sanchez', 'darren_sanchez@dlsu.edu.ph', '1234', 20, 'Mandaluyong', 'Mandaluyong', '+1234567890', 'user', 1, 1);

--
-- Indexes for dumped tables
--

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`);

ALTER TABLE `pet_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`),
  ADD KEY `idx_active` (`is_active`);

ALTER TABLE `breeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_type_id` (`pet_type_id`),
  ADD KEY `idx_breed_active` (`is_active`);

ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_type_id` (`pet_type_id`),
  ADD KEY `breed_id` (`breed_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_available` (`status`, `is_featured`),
  ADD KEY `idx_age` (`age_years`),
  ADD KEY `idx_gender` (`gender`),
  ADD KEY `idx_size` (`size`),
  ADD KEY `idx_microchip` (`microchip_id`);

ALTER TABLE `pet_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `idx_primary` (`is_primary`),
  ADD KEY `idx_order` (`display_order`);

ALTER TABLE `pet_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_doc_type` (`document_type`);

ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submitted` (`submitted_at`);

ALTER TABLE `application_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `changed_by` (`changed_by`);

ALTER TABLE `adoptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_id` (`application_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_adoption_date` (`adoption_date`);

ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_pet` (`user_id`, `pet_id`),
  ADD KEY `pet_id` (`pet_id`);

ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `pet_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `breeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pet_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pet_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `application_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `adoptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `breeds`
  ADD CONSTRAINT `breeds_ibfk_1` FOREIGN KEY (`pet_type_id`) REFERENCES `pet_types` (`id`) ON DELETE CASCADE;

ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`pet_type_id`) REFERENCES `pet_types` (`id`),
  ADD CONSTRAINT `pets_ibfk_2` FOREIGN KEY (`breed_id`) REFERENCES `breeds` (`id`) ON DELETE SET NULL;

ALTER TABLE `pet_photos`
  ADD CONSTRAINT `pet_photos_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE;

ALTER TABLE `pet_documents`
  ADD CONSTRAINT `pet_documents_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pet_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `application_status_history`
  ADD CONSTRAINT `application_status_history_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

ALTER TABLE `adoptions`
  ADD CONSTRAINT `adoptions_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `adoptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `adoptions_ibfk_3` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `adoptions_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`);

ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favorites_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE;

ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
