-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2023 at 08:53 AM
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
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`) VALUES
(1, 1, 'DISTRITO NACIONAL'),
(2, 24, 'SAN CRISTOBAL'),
(3, 20, 'BANI'),
(4, 18, 'BAYAGUANA'),
(5, 18, 'YAMASA'),
(8, 18, 'MONTE PLATA'),
(10, 3, 'AZUA'),
(11, 25, 'LAS MATAS DE FARFAN'),
(12, 25, 'SAN JUAN DE LA MAGUANA'),
(13, 31, 'SAN JOSE DE OCOA'),
(14, 25, 'EL CERCADO'),
(15, 9, 'BANICA'),
(16, 9, 'COMENDADOR'),
(17, 3, 'PADRE LAS CASAS'),
(18, 5, 'BARAHONA'),
(19, 5, 'CABRAL'),
(20, 12, 'DUVERGE'),
(21, 5, 'ENRIQUILLO'),
(22, 4, 'NEYBA'),
(23, 26, 'SAN PEDRO DE MACORIS'),
(24, 26, 'LOS LLANOS'),
(25, 8, 'EL SEIBO'),
(26, 13, 'LA ROMANA'),
(27, 11, 'HATO MAYOR'),
(28, 2, 'HIGUEY'),
(29, 8, 'MICHES'),
(30, 26, 'RAMON SANTANA'),
(31, 28, 'SANTIA DE LOS CABALLEROS'),
(32, 28, 'TAMBORIL'),
(33, 30, 'ESPERANZA'),
(34, 30, 'MAO'),
(35, 28, 'JANICO'),
(36, 28, 'SAN JOSE DE LAS MATAS'),
(37, 21, 'PUERTO PLATA'),
(38, 21, 'IMBERT'),
(39, 21, 'ALTAMIRA'),
(40, 21, 'LUPERON'),
(41, 17, 'MONTECRISTI'),
(42, 29, 'MONCION'),
(43, 6, 'RESTAURACION'),
(44, 6, 'DAJABON'),
(45, 17, 'GUAYUBIN'),
(46, 29, 'SAN IGNACIO DE SABANETA'),
(47, 14, 'LA VEGA'),
(48, 16, 'BONAO'),
(49, 27, 'COTUI'),
(50, 14, 'JARABACOA'),
(51, 22, 'VILLA TAPIA'),
(52, 27, 'CEVICOS'),
(53, 14, 'CONSTANZA'),
(54, 10, 'MOCA'),
(55, 22, 'SALCEDO'),
(56, 7, 'SAN FRANCISCO DE MACORIS'),
(57, 7, 'PIMENTEL'),
(58, 7, 'VILLA RIVA'),
(59, 7, 'CASTILLO'),
(60, 15, 'CABRERA'),
(61, 10, 'GASPAR HERNANDEZ'),
(63, 7, 'EUGENIO MARIA DE HOSTOS'),
(64, 22, 'TENARES'),
(65, 23, 'SAMANA'),
(66, 23, 'SANCHEZ'),
(67, 11, 'SABANA DE LA MAR'),
(68, 24, 'VILLA ALTAGRACIA'),
(69, 19, 'PEDERNALES'),
(70, 12, 'LA DESCUBIERTA'),
(71, 15, 'NAGUA'),
(72, 17, 'VILLA VASQUEZ'),
(73, 6, 'LOMA DE CABRERA'),
(74, 9, 'PEDRO SANTANA'),
(75, 9, 'HONDO VALLE'),
(76, 4, 'TAMAYO'),
(77, 12, 'JIMANI'),
(78, 4, 'VILLA JARAGUA'),
(79, 5, 'VICENTE NOBLE'),
(80, 5, 'PARAISO'),
(81, 15, 'RIO SAN JUAN'),
(82, 24, 'YAGUATE'),
(83, 24, 'SABANA GRANDE DE PALENQUE'),
(84, 20, 'NIZAO'),
(85, 2, 'SAN RAFAEL DEL YUMA'),
(86, 17, 'PEPILLO SALCEDO'),
(87, 27, 'FANTINO'),
(88, 10, 'CAYETANO GERMOSEN'),
(90, 18, 'SABANA GRANDE DE BOYA'),
(91, 19, 'OVIEDO'),
(92, 30, 'LAGUNA SALADA'),
(93, 24, 'BAJOS DE HAINA'),
(94, 28, 'VILLA NZALEZ'),
(95, 28, 'LICEY AL MEDIO'),
(96, 28, 'VILLA BISONO -NAVARRETE-'),
(97, 21, 'SOSUA'),
(99, 12, 'POSTRER RIO'),
(100, 11, 'EL VALLE'),
(101, 17, 'CASTA¥UELAS'),
(102, 21, 'LOS HIDALS'),
(103, 13, 'GUAYMATE'),
(104, 24, 'CAMBITA GARABITOS'),
(105, 3, 'GUAYABAL'),
(106, 3, 'PERALTA'),
(107, 3, 'SABANA YEGUA'),
(108, 25, 'VALLEJUELO'),
(109, 25, 'BOHECHIO'),
(110, 9, 'EL LLANO'),
(111, 5, 'POLO'),
(112, 4, 'LOS RIOS '),
(113, 4, 'GALVAN'),
(114, 12, 'MELLA'),
(115, 6, 'PARTIDO'),
(116, 29, 'VILLA LOS ALMACIS'),
(117, 17, 'LAS MATAS DE SANTA CRUZ'),
(118, 16, 'MAIMON'),
(119, 7, 'ARENOSO'),
(120, 21, 'GUANANICO'),
(121, 21, 'VILLA ISABELA'),
(122, 14, 'JIMA ABAJO'),
(123, 16, 'PIEDRA BLANCA'),
(125, 3, 'LAS YAYAS DE VIAJAMA'),
(126, 3, 'TABARA ARRIBA'),
(128, 12, 'CRISTOBAL'),
(129, 25, 'JUAN DE HERRERA'),
(130, 5, 'EL PE¥ON'),
(131, 5, 'FUNDACION'),
(132, 3, 'ESTEBANIA'),
(133, 10, 'JAMAO AL NORTE'),
(134, 23, 'LAS TERRENAS'),
(135, 3, 'LAS CHARCAS'),
(136, 15, 'EL FACTOR'),
(137, 5, 'LAS SALINAS'),
(138, 26, 'CONSUELO'),
(139, 24, 'LOS CACAOS'),
(140, 24, 'SAN GRERIO DE NIGUA'),
(143, 7, 'LAS GUARANAS'),
(144, 9, 'JUAN SANTIA '),
(146, 26, 'QUISQUEYA'),
(148, 28, 'SABANA IGLESIA'),
(149, 10, 'SAN VICTOR'),
(150, 31, 'SABANA LARGA'),
(151, 6, 'EL PINO'),
(152, 31, 'RANCHO ARRIBA'),
(153, 18, 'PERALVILLO'),
(154, 3, 'PUEBLO VIEJO'),
(155, 27, 'VILLA LA MATA'),
(156, 20, 'MATANZAS'),
(160, 5, 'LA CIENAGA'),
(161, 28, 'BAITOA'),
(167, 5, 'JAQUIMEYES'),
(175, 21, 'VILLA MONTELLANO'),
(223, 32, 'SANTO DOMIN ESTE'),
(224, 32, 'SANTO DOMIN OESTE'),
(225, 32, 'SANTO DOMIN NORTE'),
(226, 32, 'BOCA CHICA'),
(227, 32, 'SAN ANTONIO DE GUERRA'),
(228, 32, 'PEDRO BRAND'),
(229, 32, 'LOS ALCARRIZOS'),
(295, 13, 'VILLA HERMOSA'),
(350, 28, 'PU¥AL'),
(364, 26, 'GUAYACANES'),
(402, 1, '-----------------'),
(432, 61, 'READING'),
(571, 63, 'ISLAS CANARIAS'),
(599, 76, 'MARTINICA'),
(610, 61, 'NEW YORK'),
(611, 61, 'MASSACHUSETTS'),
(612, 61, 'PUERTO RICO'),
(613, 62, 'VENEZUELA'),
(614, 63, 'MADRID'),
(615, 61, 'MIAMI'),
(616, 61, 'PHILADELPHIA'),
(617, 63, 'BARCELONA'),
(618, 64, 'MONTREAL'),
(619, 61, 'NEW JERSEY'),
(620, 76, 'SAN MARTIN'),
(621, 61, 'ORLANDO'),
(622, 64, 'TORONTO'),
(623, 63, 'VALENCIA'),
(624, 76, 'GUADALUPE'),
(633, 66, 'PANAMA'),
(634, 69, 'HOLANDA'),
(635, 67, 'ZURICH'),
(636, 68, 'MILAN'),
(637, 61, 'WASHINGTON DC'),
(648, 76, 'ANTIGUA Y BARBUDA'),
(650, 76, 'CURAZAO'),
(651, 76, 'ARUBA'),
(656, 76, 'GUYANA FRANCESA'),
(671, 61, 'PROVIDENCE  (RHODE ISLAND) - USA'),
(675, 76, 'WASHINGTON'),
(680, 61, 'LOUSIANA'),
(683, 61, 'CALIFORNIA'),
(685, 76, 'BONAIRE'),
(688, 64, 'OTTAWA'),
(689, 61, 'ILLINOIS'),
(900, 1, 'JUNTA CENTRAL ELECTORAL');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_index` (`state_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=901;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
