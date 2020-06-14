-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 01 Cze 2020, 23:24
-- Wersja serwera: 10.4.6-MariaDB
-- Wersja PHP: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `cf_test`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `debtor_id` int(11) NOT NULL,
  `telefon` int(11) NOT NULL,
  `adres` varchar(99) NOT NULL,
  `miasto` varchar(99) NOT NULL,
  `kod` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `contact`
--

INSERT INTO `contact` (`id`, `debtor_id`, `telefon`, `adres`, `miasto`, `kod`) VALUES
(1, 1, 0, 'ul. Zamenhofa 21', 'Leszno', '64-100'),
(2, 2, 672874333, 'Czapelska 12/3', 'Warszawa', '04-081'),
(3, 3, 602728579, 'ul. Kawaleryjska 67/2', 'Bia³ystok', '15-601'),
(4, 4, 0, 'Witomiñska 142/1', 'Gdynia', '81-619'),
(5, 5, 264464779, '£ysobyki 4', '£ysobyki', '54-964'),
(6, 6, 0, 'Grodziska 13/2', 'Koœcian', '64-001'),
(7, 7, 421005874, '8', 'Babiedo³y', '32-406'),
(8, 8, 380039575, 'ul. Kamienna 120/3', 'Wroc³aw', '50-549'),
(9, 9, 0, 'Mieros³awskiego Ludwika 4/6', 'Sopot', '81-737'),
(10, 10, 0, 'Mickiewicza 11/4', 'Leszno', '64-100'),
(11, 11, 988655517, 'ul. Dojazd 65/7', '', '42-216'),
(12, 15, 390895785, 'Gumniska 83', 'Tarnów', '33-106');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `debtor`
--

CREATE TABLE `debtor` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesel` bigint(11) DEFAULT NULL,
  `nip` bigint(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `debtor`
--

INSERT INTO `debtor` (`id`, `nazwa`, `pesel`, `nip`) VALUES
(1, 'Bartosz Zieliñski', 92051118335, NULL),
(2, 'Ireneusz Witkowski', 54092521415, NULL),
(3, 'Ksawery Chmielewski', 66052521131, NULL),
(4, 'Kewin Mróz', 57011961174, NULL),
(5, 'Anatol Ostrowski', 45032094478, NULL),
(6, 'Matylda Wojciechowska', 68061588149, NULL),
(7, 'Kaja Wysocka', 65100126889, NULL),
(8, 'Bogus³awa Stêpieñ', 75010911686, NULL),
(9, 'Hortensja Marciniak', 79021124721, NULL),
(10, 'Zak³ady Miêsne Leszno', NULL, 5244889426),
(11, 'Salon urody \'Pomarañcza\'', NULL, 5320732701);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `financial_condition`
--

CREATE TABLE `financial_condition` (
  `id` int(11) NOT NULL,
  `debtor_id` int(11) NOT NULL,
  `numer_umowy` varchar(99) NOT NULL,
  `numer_rachunku` longtext NOT NULL,
  `kapital` double NOT NULL,
  `odsetki` double NOT NULL,
  `prowizja` double NOT NULL,
  `saldo` double NOT NULL,
  `kwota_pozyczki` double NOT NULL,
  `data_umowy` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `financial_condition`
--

INSERT INTO `financial_condition` (`id`, `debtor_id`, `numer_umowy`, `numer_rachunku`, `kapital`, `odsetki`, `prowizja`, `saldo`, `kwota_pozyczki`, `data_umowy`) VALUES
(1, 1, 'UM000000002', '2,8144E+25', 30938.72, 2126.89, 17, 33182.61, 15000, '2018-12-12'),
(2, 2, 'UM000000003', '3,7124E+25', 3476.85, 849.51, 17, 4343.36, 5400, '2019-06-19'),
(3, 3, 'UM000000005', '8,3124E+25', 8654.62, 2654.25, 18.09, 11326.96, 5700, '2015-09-11'),
(4, 4, 'UM000000006', '7,416E+25', 8742.16, 1217.59, 1344, 11303.75, 4900, '2018-05-22'),
(5, 5, 'UM000000007', '6,3124E+25', 991.47, 160.72, 64.25, 1216.44, 4950, '2015-11-20'),
(6, 6, 'UM000000010', '7,98658E+25', 7036.14, 1586.66, 1322, 9944.8, 3800, '2017-11-03'),
(7, 7, 'UM000000011', '7,3103E+25', 9451.22, 671.36, 450, 10572.58, 4500, '2019-05-08'),
(8, 8, 'UM000000012', '7,29271E+25', 11455.59, 3662.48, 3578.63, 18696.7, 4950, '2016-05-11'),
(9, 9, 'UM000000016', '4,11241E+25', 6550.45, 2833.1, 0.82, 9384.37, 3500, '2016-07-07'),
(10, 10, 'UM000000017', '9,19484E+25', 2641.54, 381.09, 130, 3152.63, 2000, '2015-05-21'),
(11, 11, 'UM000000018', '9,0193E+25', 10757.35, 1319.6, 17, 12093.95, 7500, '2018-02-13'),
(12, 15, 'UM000000019', '8,6109E+25', 6426.62, 1376.98, 1298, 9101.6, 3200, '2018-06-07');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `financial_id` int(11) NOT NULL,
  `data_wplaty` date NOT NULL,
  `kwota` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `payment`
--

INSERT INTO `payment` (`id`, `financial_id`, `data_wplaty`, `kwota`) VALUES
(1, 1, '2020-04-11', 1300),
(2, 2, '2020-03-06', 650),
(3, 3, '2020-04-03', 1200),
(4, 4, '2020-02-02', 1300),
(5, 4, '2020-03-02', 1100),
(6, 5, '2020-04-23', 300),
(7, 6, '2020-02-13', 100),
(8, 6, '2020-03-14', 120),
(9, 6, '2020-03-17', 80.65),
(10, 6, '2020-04-11', 349.99),
(11, 7, '2020-03-25', 200),
(12, 8, '2020-04-14', 11),
(13, 10, '2020-01-12', 145),
(14, 11, '2020-02-14', 78.98),
(15, 11, '2020-03-15', 21.32),
(16, 12, '2020-02-07', 11.89),
(17, 12, '2020-04-02', 1000);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `debtor`
--
ALTER TABLE `debtor`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `financial_condition`
--
ALTER TABLE `financial_condition`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `debtor`
--
ALTER TABLE `debtor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT dla tabeli `financial_condition`
--
ALTER TABLE `financial_condition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
