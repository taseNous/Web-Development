-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 16 Σεπ 2024 στις 12:14:41
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `web`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `admin_pos`
--

CREATE TABLE `admin_pos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lat` float UNSIGNED DEFAULT NULL,
  `lon` float UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `admin_pos`
--

INSERT INTO `admin_pos` (`id`, `user_id`, `lat`, `lon`) VALUES
(5, 33, 38.2472, 21.7368);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `product` varchar(20) NOT NULL,
  `volume` varchar(20) NOT NULL,
  `weight` varchar(20) NOT NULL,
  `pack_size` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `size` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `announcement`
--

INSERT INTO `announcement` (`id`, `user_id`, `category`, `product`, `volume`, `weight`, `pack_size`, `type`, `size`) VALUES
(47, 33, 'Beverages', 'Water', '', '', '', '', ''),
(48, 33, 'Kitchen Supplies', 'Pan', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `citizen`
--

CREATE TABLE `citizen` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `citizen`
--

INSERT INTO `citizen` (`id`, `citizen_id`, `first_name`, `last_name`, `phone`, `latitude`, `longitude`) VALUES
(4, 37, 'Johnny', 'Chase', '6981395703', 38.2482, 21.7341),
(5, 38, 'Vincent', 'Chase', '6984732071', 38.2463, 21.7317),
(6, 39, 'Eric', 'Murphy', '6984720732', 38.2435, 21.7345),
(7, 40, 'Sal', 'Assante', '6982134709', 38.247, 21.7391),
(9, 54, 'Ari', 'Gold', '6987342758', 38.2461, 21.7358);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `product` varchar(30) NOT NULL,
  `volume` varchar(20) NOT NULL,
  `weight` varchar(20) NOT NULL,
  `pack_size` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `size` varchar(20) NOT NULL,
  `quantity` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rescuer_id` int(11) DEFAULT NULL,
  `announcement_id` int(11) DEFAULT NULL,
  `category` varchar(20) NOT NULL,
  `product` varchar(20) NOT NULL,
  `quantity` varchar(20) NOT NULL,
  `date_made` datetime NOT NULL,
  `when_accepted` datetime(6) NOT NULL,
  `when_completed` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `offers`
--

INSERT INTO `offers` (`id`, `user_id`, `rescuer_id`, `announcement_id`, `category`, `product`, `quantity`, `date_made`, `when_accepted`, `when_completed`) VALUES
(40, 4, 4, 47, 'Beverages', 'Water', '1', '2024-08-25 13:16:55', '2024-08-25 13:30:39.000000', '0000-00-00 00:00:00.000000');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rescuer_id` int(11) DEFAULT NULL,
  `category` varchar(20) NOT NULL,
  `product` varchar(20) NOT NULL,
  `people` varchar(20) NOT NULL,
  `date_made` datetime(6) NOT NULL,
  `when_accepted` datetime DEFAULT NULL,
  `when_completed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `request`
--

INSERT INTO `request` (`id`, `user_id`, `rescuer_id`, `category`, `product`, `people`, `date_made`, `when_accepted`, `when_completed`) VALUES
(31, 9, 4, 'Food', 'Bread', '1', '2024-08-25 13:25:39.000000', '2024-08-25 13:31:10', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `rescuer`
--

CREATE TABLE `rescuer` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `has_tasks` tinyint(1) NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `rescuer`
--

INSERT INTO `rescuer` (`id`, `user_id`, `status`, `has_tasks`, `lat`, `lon`) VALUES
(3, 55, '', 0, 38.2458, 21.7379),
(4, 56, '', 0, 38.2473, 21.7369),
(5, 57, '', 0, 38.2475, 21.7368);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `rescuer_load`
--

CREATE TABLE `rescuer_load` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `product` varchar(20) NOT NULL,
  `volume` varchar(20) NOT NULL,
  `weight` varchar(20) NOT NULL,
  `pack_size` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `size` varchar(20) NOT NULL,
  `quantity` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `rescuer_load`
--

INSERT INTO `rescuer_load` (`id`, `user_id`, `category`, `product`, `volume`, `weight`, `pack_size`, `type`, `size`, `quantity`) VALUES
(25, 5, 'Beverages', 'Water', '', '', '', '', '', '1'),
(26, 5, 'Beverages', 'Water', '', '', '', '', '', '1'),
(27, 5, 'Kitchen Supplies', 'Pan', '', '', '', '', '', '1'),
(29, 4, 'Beverages', 'Water', '', '', '', '', '', '1');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `usertype` varchar(50) NOT NULL DEFAULT 'citizen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `user`
--

INSERT INTO `user` (`id`, `username`, `password_hash`, `usertype`) VALUES
(33, 'admin', '$2y$10$xXGKB7TZfQMSXvaMbkjK7uv6BRLqyjf9O7FvDeU8tn6uTs8YawMgS', 'admin'),
(37, 'drama', '$2y$10$xqIiadg42iH12YcAWdOD.uEHtWaxwe.VM1cKLsJ/iRmuF4PocEpUm', 'citizen'),
(38, 'vince', '$2y$10$xAL376awZ/RKj7729IvX1.C67lnkBcT6wnDUas1aQLmpcPTJn40om', 'citizen'),
(39, 'eric', '$2y$10$QBjB0tcStN.SSKoMniXaju5YcsUmLqqMuz9jLa/IDcGaBlGGTsMbO', 'citizen'),
(40, 'turtle', '$2y$10$UhZyOhlaJ5VpIt/mWE4AZum28R6ZAenT/sv6/EXQ8G9ij5t7REK.u', 'citizen'),
(54, 'ari', '$2y$10$7wTQiyIUQNFOlFwwCR50PenA9jG4NHr6.o/e8AOOMudIaQwy2RlPG', 'citizen'),
(55, 'sloan', '$2y$10$PBY9UPrV5mn9q1CPN/DBJOFBhYBpRmCsHd6IEOi.FMK06MtM8C4E6', 'rescuer'),
(56, 'dana', '$2y$10$jdwdCdiMzNA2/VOwJSI8X.6NFg3T02qrF93kxwaEba5pKLXPMQcLa', 'rescuer'),
(57, 'amanda', '$2y$10$7kEDvFwroNu0xLpsx6lAbOc6SWFBnrSnuQ.j2j5My8f5ezQ1nGH6u', 'rescuer');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `admin_pos`
--
ALTER TABLE `admin_pos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `citizen`
--
ALTER TABLE `citizen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`citizen_id`);

--
-- Ευρετήρια για πίνακα `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rescuer_id` (`rescuer_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Ευρετήρια για πίνακα `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rescuer_id` (`rescuer_id`);

--
-- Ευρετήρια για πίνακα `rescuer`
--
ALTER TABLE `rescuer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `rescuer_load`
--
ALTER TABLE `rescuer_load`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `admin_pos`
--
ALTER TABLE `admin_pos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT για πίνακα `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT για πίνακα `citizen`
--
ALTER TABLE `citizen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT για πίνακα `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT για πίνακα `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT για πίνακα `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT για πίνακα `rescuer`
--
ALTER TABLE `rescuer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT για πίνακα `rescuer_load`
--
ALTER TABLE `rescuer_load`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT για πίνακα `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `admin_pos`
--
ALTER TABLE `admin_pos`
  ADD CONSTRAINT `admin_pos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `announcement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `citizen`
--
ALTER TABLE `citizen`
  ADD CONSTRAINT `citizen_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `citizen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`rescuer_id`) REFERENCES `rescuer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offers_ibfk_3` FOREIGN KEY (`announcement_id`) REFERENCES `announcement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `citizen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`rescuer_id`) REFERENCES `rescuer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `rescuer`
--
ALTER TABLE `rescuer`
  ADD CONSTRAINT `rescuer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `rescuer_load`
--
ALTER TABLE `rescuer_load`
  ADD CONSTRAINT `rescuer_load_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `rescuer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
