-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2023 at 08:52 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `political_party`
--

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`) VALUES
(1, 1, 'DISTRITO NACIONAL'),
(2, 1, 'LA ALTAGRACIA'),
(3, 1, 'AZUA'),
(4, 1, 'BAHORUCO'),
(5, 1, 'BARAHONA'),
(6, 1, 'DAJABON'),
(7, 1, 'DUARTE'),
(8, 1, 'EL SEIBO'),
(9, 1, 'ELIAS PI¥A'),
(10, 1, 'ESPAILLAT'),
(11, 1, 'HATO MAYOR'),
(12, 1, 'INDEPENDENCIA'),
(13, 1, 'LA ROMANA'),
(14, 1, 'LA VEGA'),
(15, 1, 'MARIA TRINIDAD SANCHEZ'),
(16, 1, 'MONSE¥OR NOUEL'),
(17, 1, 'MONTE CRISTI'),
(18, 1, 'MONTE PLATA'),
(19, 1, 'PEDERNALES'),
(20, 1, 'PERAVIA'),
(21, 1, 'PUERTO PLATA'),
(22, 1, 'HERMANAS MIRABAL'),
(23, 1, 'SAMANA'),
(24, 1, 'SAN CRISTOBAL'),
(25, 1, 'SAN JUAN'),
(26, 1, 'SAN PEDRO DE MACORIS'),
(27, 1, 'SANCHEZ RAMIREZ'),
(28, 1, 'SANTIA'),
(29, 1, 'SANTIA RODRIGUEZ'),
(30, 1, 'VALVERDE'),
(31, 1, 'SAN JOSE DE OCOA'),
(32, 1, 'SANTO DOMIN'),
(61, 1, 'ESTADOS UNIDOS'),
(62, 1, 'VENEZUELA'),
(63, 1, 'ESPA¥A'),
(64, 1, 'CANADA'),
(66, 1, 'PANAMA'),
(67, 1, 'SUIZA'),
(68, 1, 'ITALIA'),
(69, 1, 'HOLANDA'),
(76, 1, 'ANTILLAS MENORES'),
(77, 1, 'FRANCIA'),
(78, 1, 'ALEMANIA'),
(79, 1, 'GUATEMALA'),
(80, 1, 'COSTA RICA'),
(81, 1, 'URUGUAY'),
(82, 1, 'NICARAGUA'),
(83, 1, 'LUXEMBUR'),
(84, 1, 'COLOMBIA'),
(85, 1, 'ISLAS TURCAS Y CAICAS'),
(86, 1, 'AUSTRIA'),
(87, 1, 'MARTINICA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `states_country_id_index` (`country_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
