-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 04, 2026 at 05:17 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms_logistyka`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dokumenty`
--

CREATE TABLE `dokumenty` (
  `id` int(11) NOT NULL,
  `czy_zweryfikowany` bit(1) DEFAULT NULL,
  `data_przeslania` datetime(6) DEFAULT NULL,
  `id_zlecenia` int(11) NOT NULL,
  `typ_dokumentu` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `faktury`
--

CREATE TABLE `faktury` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) NOT NULL,
  `nr_faktury` int(11) DEFAULT NULL,
  `data_wystawienia` datetime DEFAULT NULL,
  `termin_platnosci` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `historia_trasy`
--

CREATE TABLE `historia_trasy` (
  `id` int(11) NOT NULL,
  `id_trasy` int(11) DEFAULT NULL,
  `szerokosc_geo` decimal(10,2) DEFAULT NULL,
  `dlugosc_geo` decimal(10,2) DEFAULT NULL,
  `data_odczytu` datetime DEFAULT NULL,
  `predkosc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `incydent`
--

CREATE TABLE `incydent` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `typ_incydentu` varchar(50) DEFAULT NULL,
  `opis_kierowcy` text DEFAULT NULL,
  `lokalizacja_GPS` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `incydenty`
--

CREATE TABLE `incydenty` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `typ` varchar(50) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'W toku',
  `data_zgloszenia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kierowca`
--

CREATE TABLE `kierowca` (
  `id` int(11) NOT NULL,
  `imie_nazwisko` varchar(100) NOT NULL,
  `status` varchar(50) DEFAULT 'Wolny'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kierowca`
--

INSERT INTO `kierowca` (`id`, `imie_nazwisko`, `status`) VALUES
(1, 'Jan Kowalski', 'Wolny'),
(2, 'Piotr Nowak', 'Wolny'),
(3, 'Mariusz Lewandowski', 'Wolny');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klient`
--

CREATE TABLE `klient` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) DEFAULT NULL,
  `nazwisko` varchar(50) DEFAULT NULL,
  `NIP` varchar(50) DEFAULT NULL,
  `adres` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klient`
--

INSERT INTO `klient` (`id`, `imie`, `nazwisko`, `NIP`, `adres`) VALUES
(1, 'Logistyka Polska', 'Sp. z o.o.', '1234567890', 'Warszawa, Al. Jerozolimskie 45'),
(2, 'Trans-Europe', 'S.A.', '9876543210', 'Poznań, ul. Głogowska 12'),
(3, 'AGD Market', 'Group Sp. k.', '5251122334', 'Wrocław, ul. Fabryczna 8');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `koszty_dodatkowe`
--

CREATE TABLE `koszty_dodatkowe` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) NOT NULL,
  `typ_kosztu` varchar(50) DEFAULT NULL,
  `kwota_netto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `licytacje`
--

CREATE TABLE `licytacje` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `id_przewoznika` int(11) DEFAULT NULL,
  `kwota` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oferty_frachtowe`
--

CREATE TABLE `oferty_frachtowe` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `id_przewoznika` int(11) DEFAULT NULL,
  `kwota_netto` decimal(10,2) DEFAULT NULL,
  `status_oferty` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pojazd`
--

CREATE TABLE `pojazd` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `nr_rejestracyjny` varchar(50) NOT NULL,
  `typ_zabudowy` varchar(50) NOT NULL,
  `max_ladownosc` int(11) NOT NULL,
  `status_dostepnosci` tinyint(1) NOT NULL,
  `ostatni_przeglad` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pojazd`
--

INSERT INTO `pojazd` (`id`, `id_zlecenia`, `nr_rejestracyjny`, `typ_zabudowy`, `max_ladownosc`, `status_dostepnosci`, `ostatni_przeglad`) VALUES
(1, 4, 'WI 7788A', 'Firanka', 24000, 0, '2026-05-01'),
(2, 4, 'WB 5522C', 'Chlodnia', 22000, 0, '2026-04-15'),
(3, 1, 'KR 9911X', 'Mega', 25000, 0, '2026-05-20');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `towary`
--

CREATE TABLE `towary` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `waga` float NOT NULL,
  `ldm` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `towary`
--

INSERT INTO `towary` (`id`, `nazwa`, `waga`, `ldm`) VALUES
(1, 'Palety Euro AGD', 400, 0.4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `trasa`
--

CREATE TABLE `trasa` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) DEFAULT NULL,
  `szacowane_paliwo` decimal(10,2) DEFAULT NULL,
  `szacowane_myto` decimal(10,2) DEFAULT NULL,
  `planowane_km` int(11) DEFAULT NULL,
  `planowane_eta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zlecenia`
--

CREATE TABLE `zlecenia` (
  `id` int(11) NOT NULL,
  `id_klienta` int(11) DEFAULT NULL,
  `numer_zlecenia` varchar(50) NOT NULL,
  `data_zaladunku` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `miejsce_zaladunku` varchar(255) DEFAULT NULL,
  `miejsce_rozladunku` varchar(255) DEFAULT NULL,
  `masa_towaru` decimal(38,2) DEFAULT NULL,
  `typ_towaru` varchar(50) DEFAULT NULL,
  `powod_anulowania` varchar(255) DEFAULT NULL,
  `stawka_frachtu` decimal(38,2) DEFAULT NULL,
  `waluta` varchar(10) DEFAULT NULL,
  `bhp_potwierdzone` int(11) DEFAULT 0,
  `id_kierowcy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zlecenia`
--

INSERT INTO `zlecenia` (`id`, `id_klienta`, `numer_zlecenia`, `data_zaladunku`, `status`, `miejsce_zaladunku`, `miejsce_rozladunku`, `masa_towaru`, `typ_towaru`, `powod_anulowania`, `stawka_frachtu`, `waluta`, `bhp_potwierdzone`, `id_kierowcy`) VALUES
(1, NULL, 'ZLEC/2026/001', '2026-06-02', 'Zatwierdzone', 'Warszawa', 'Berlin', 24000.00, 'GABARYT', NULL, NULL, NULL, 0, NULL),
(4, NULL, 'ZLEC/2026/678', '2026-06-04', 'W realizacji', 'PL - Warszawa', 'DE - Berlin', 18000.00, 'Przesyłki kurierskie', NULL, NULL, NULL, 0, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zlecenia_towaru`
--

CREATE TABLE `zlecenia_towaru` (
  `id` int(11) NOT NULL,
  `id_zlecenia` int(11) NOT NULL,
  `id_towaru` int(11) NOT NULL,
  `ilosc_jednostek` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dokumenty`
--
ALTER TABLE `dokumenty`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `faktury`
--
ALTER TABLE `faktury`
  ADD PRIMARY KEY (`id`,`id_zlecenia`),
  ADD KEY `IXFK_Faktury_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `historia_trasy`
--
ALTER TABLE `historia_trasy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Historia_Trasy_Trasa` (`id_trasy`);

--
-- Indeksy dla tabeli `incydent`
--
ALTER TABLE `incydent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Incydenty_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `incydenty`
--
ALTER TABLE `incydenty`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `kierowca`
--
ALTER TABLE `kierowca`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `klient`
--
ALTER TABLE `klient`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `koszty_dodatkowe`
--
ALTER TABLE `koszty_dodatkowe`
  ADD PRIMARY KEY (`id`,`id_zlecenia`),
  ADD KEY `IXFK_Koszty_dodatkowe_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `licytacje`
--
ALTER TABLE `licytacje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Licytacje_Przewoznicy` (`id_przewoznika`),
  ADD KEY `IXFK_Licytacje_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `oferty_frachtowe`
--
ALTER TABLE `oferty_frachtowe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Oferty_Frachtowe_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `pojazd`
--
ALTER TABLE `pojazd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Pojazd_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `towary`
--
ALTER TABLE `towary`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `trasa`
--
ALTER TABLE `trasa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Trasa_Zlecenia` (`id_zlecenia`);

--
-- Indeksy dla tabeli `zlecenia`
--
ALTER TABLE `zlecenia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_Zlecenia_Klienci` (`id_klienta`);

--
-- Indeksy dla tabeli `zlecenia_towaru`
--
ALTER TABLE `zlecenia_towaru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IXFK_zlecenia_towary_Towary` (`id_towaru`),
  ADD KEY `IXFK_zlecenia_towary_Zlecenia` (`id_zlecenia`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokumenty`
--
ALTER TABLE `dokumenty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faktury`
--
ALTER TABLE `faktury`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historia_trasy`
--
ALTER TABLE `historia_trasy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incydent`
--
ALTER TABLE `incydent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incydenty`
--
ALTER TABLE `incydenty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kierowca`
--
ALTER TABLE `kierowca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `klient`
--
ALTER TABLE `klient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `koszty_dodatkowe`
--
ALTER TABLE `koszty_dodatkowe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `licytacje`
--
ALTER TABLE `licytacje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oferty_frachtowe`
--
ALTER TABLE `oferty_frachtowe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pojazd`
--
ALTER TABLE `pojazd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `towary`
--
ALTER TABLE `towary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trasa`
--
ALTER TABLE `trasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zlecenia`
--
ALTER TABLE `zlecenia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `zlecenia_towaru`
--
ALTER TABLE `zlecenia_towaru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pojazd`
--
ALTER TABLE `pojazd`
  ADD CONSTRAINT `FK_Pojazd_Zlecenia` FOREIGN KEY (`id_zlecenia`) REFERENCES `zlecenia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `trasa`
--
ALTER TABLE `trasa`
  ADD CONSTRAINT `FK_Trasa_Zlecenia` FOREIGN KEY (`id_zlecenia`) REFERENCES `zlecenia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `zlecenia`
--
ALTER TABLE `zlecenia`
  ADD CONSTRAINT `FK_Zlecenia_Klienci` FOREIGN KEY (`id_klienta`) REFERENCES `klient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `zlecenia_towaru`
--
ALTER TABLE `zlecenia_towaru`
  ADD CONSTRAINT `FK_zlecenia_towary_Towary` FOREIGN KEY (`id_towaru`) REFERENCES `towary` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_zlecenia_towary_Zlecenia` FOREIGN KEY (`id_zlecenia`) REFERENCES `zlecenia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
