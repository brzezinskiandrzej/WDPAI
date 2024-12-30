-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 20 Sty 2022, 09:55
-- Wersja serwera: 10.1.19-MariaDB
-- Wersja PHP: 7.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `brzezinski_4tb`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `albumy`
--

CREATE TABLE `albumy` (
  `id` int(11) NOT NULL,
  `tytul` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `data` datetime NOT NULL,
  `id_uzytkownika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `albumy`
--

INSERT INTO `albumy` (`id`, `tytul`, `data`, `id_uzytkownika`) VALUES
(1, 'Abstrakcja', '2021-12-04 20:06:38', 1),
(2, 'Kwiaty', '2021-12-04 21:30:19', 3),
(3, 'klocki', '2021-12-04 22:04:20', 4),
(4, 'Konkurs', '2021-12-05 18:41:54', 4),
(5, 'Moj album', '2021-12-05 20:04:12', 1),
(6, 'probny', '2021-12-05 20:44:05', 5),
(7, 'tablice', '2021-12-05 20:46:26', 7),
(8, 'obrazki', '2021-12-05 20:48:49', 9),
(9, 'dom z papieru', '2021-12-05 20:50:28', 8),
(10, 'pole', '2021-12-05 20:52:23', 7),
(11, 'Widoki z lotu ptaka', '2021-12-05 20:53:27', 4),
(12, 'Gra', '2021-12-05 20:54:18', 1),
(13, 'Termobag', '2021-12-05 20:55:58', 5),
(14, 'Emoji', '2021-12-05 20:56:36', 2),
(15, 'Mapa Chin\r\n', '2021-12-05 20:58:21', 3),
(16, 'Fifa Karty', '2021-12-05 20:59:48', 1),
(17, 'Rakieta', '2021-12-05 21:01:04', 6),
(18, 'Głośniki', '2021-12-05 21:02:52', 2),
(19, 'Nagrobki', '2021-12-05 21:04:12', 7),
(20, 'Cristiano Ronaldo', '2021-12-05 21:05:56', 9),
(21, 'Statua Wolności', '2021-12-05 21:07:06', 4),
(22, 'Dreamlight Logos', '2021-12-05 21:08:52', 1),
(25, 'M''Orwell', '2022-01-03 14:47:57', 21),
(28, 'M''Orwell''Kubalonka', '2022-01-03 15:23:09', 21),
(29, 'Wisła', '2022-01-07 21:15:21', 22),
(30, 'WislaDwa', '2022-01-07 21:42:30', 22),
(31, 'Wisła3', '2022-01-07 23:00:14', 22),
(32, 'K''ubalonka', '2022-01-07 23:13:26', 22),
(33, 'Album Szkolny', '2022-01-13 22:27:17', 24),
(34, 'Szpital', '2022-01-13 22:28:16', 24),
(35, 'Szpitaldwa', '2022-01-13 22:32:07', 24),
(36, 'Zdjęcia kotków', '2022-01-17 11:04:41', 25),
(37, 'Wisla', '2022-01-17 11:19:13', 22),
(39, 'Zdjęcia kotków', '2022-01-17 11:31:18', 25),
(40, 'Zdjęcia kotków', '2022-01-17 11:32:08', 25);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `login` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `haslo` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_roman_ci NOT NULL,
  `zarejestrowany` date NOT NULL,
  `uprawnienia` enum('użytkownik','moderator','administrator') COLLATE utf8_polish_ci NOT NULL,
  `aktywny` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `login`, `haslo`, `email`, `zarejestrowany`, `uprawnienia`, `aktywny`) VALUES
(1, 'nevkeofficial', 'ae22425e479969568d3c3aa3ed0a91ed', 'nevvkeofficial@gamil.com', '2021-11-28', 'użytkownik', 1),
(2, 'bartoszek', 'cd104fe43e8b111626eafec3b3f707f6', 'bartoszek@gmail.com', '0000-00-00', 'użytkownik', 0),
(3, 'bartoszek2', 'cd104fe43e8b111626eafec3b3f707f6', 'bartoszek2@gmail.com', '2021-12-02', 'użytkownik', 1),
(4, 'Kazimierz', 'f5cc245dd37bbc84fd405802725a7519', 'kazimierz@gmail.com', '2021-12-02', 'użytkownik', 1),
(5, 'karoleka', '281180d76e3d5bd7e4bfb7b713db1849', 'karoleka1@gmail.com', '2021-12-03', 'użytkownik', 1),
(6, 'administrator', '2701ab6335f77c2fcba0aa9480e2de9b', 'admin@gmail.com', '2021-12-03', 'administrator', 1),
(7, 'tomaszkacper', '2eedb980f2ae7577aac2f52e795c9f86', 'tomasz@gmail.com', '2021-12-05', 'użytkownik', 1),
(8, 'kacperek', '06e7c8f9f0edd9034f4b01d83ffad92b', 'jutuberzy2@gmail.com', '2021-12-05', 'użytkownik', 1),
(9, 'qwertyuiop', '88ca80df2d72dbebcef83a756c323da1', 'qwerty@gmail.com', '2021-12-05', 'użytkownik', 1),
(10, 'gwizdalke', '8d101f40d882b7c2a733e5d72d2b4df4', 'gwizdalke@gmail.com', '2021-12-06', 'użytkownik', 1),
(12, 'nauczyciel2', 'ef561c79342ff86785c280a175b82faa', 'jutuberzy@gmail.com', '2021-12-09', 'użytkownik', 1),
(13, 'aqueelmusic', '958ae1a55b67f4577194ad8ae95df82a', 'bartekszczygiel5@gmail.com', '2022-01-02', 'użytkownik', 1),
(14, 'zxcvbnml', 'b3bbaf7c3b1709ed3f44d2a29f5f2dbf', 'malpakasia@gmail.com', '2022-01-02', 'użytkownik', 1),
(15, 'zxcvbnmli', 'b3bbaf7c3b1709ed3f44d2a29f5f2dbf', 'malpakasia@gmail.com', '2022-01-02', 'użytkownik', 1),
(16, 'zxcvbnmlisz', 'b3bbaf7c3b1709ed3f44d2a29f5f2dbf', 'malpakasia@gmail.com', '2022-01-02', 'użytkownik', 1),
(17, 'zxcvbnmliszkk', 'b3bbaf7c3b1709ed3f44d2a29f5f2dbf', 'malpakasia@gmail.com', '2022-01-02', 'użytkownik', 1),
(18, 'qwertyuiop1', '88ca80df2d72dbebcef83a756c323da1', 'jutuberzy2@gmail.com', '2022-01-02', 'użytkownik', 1),
(19, 'qwertyuiop12', '88ca80df2d72dbebcef83a756c323da1', 'jutuberzy2@gmail.com', '2022-01-02', 'użytkownik', 1),
(20, 'qwertyuiop122', '88ca80df2d72dbebcef83a756c323da1', 'jutuberzy2@gmail.com', '2022-01-02', 'użytkownik', 1),
(21, 'testowyuser', 'ab0c8572a189779862f438df7b604123', 'qwerty@gmail.com', '2022-01-03', 'użytkownik', 1),
(22, 'wislauser', 'd12d43d68283226616966c5fb2bf7119', 'wisla@op.pl', '2022-01-07', 'użytkownik', 1),
(24, 'uczenbrzezinski', '96fcd2e8201300092d6e103735511cc0', 'szkola@gmail.com', '2022-01-13', 'użytkownik', 1),
(25, 'erykkleryk', '35467274493e51e2e8ad74fac89f4e89', 'ghaster@gmail.com', '2022-01-17', 'użytkownik', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zdjecia`
--

CREATE TABLE `zdjecia` (
  `id` int(11) NOT NULL,
  `opis` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `id_albumu` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `zaakceptowane` tinyint(1) NOT NULL,
  `opiszdjecia` varchar(255) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `zdjecia`
--

INSERT INTO `zdjecia` (`id`, `opis`, `id_albumu`, `data`, `zaakceptowane`, `opiszdjecia`) VALUES
(1, 'logo1.png', 1, '2021-12-04 20:07:05', 1, ''),
(2, 'logo2.png', 1, '2021-12-04 20:09:48', 1, ''),
(3, 'logo3.png', 1, '2021-12-04 20:09:59', 1, ''),
(4, 'logo4.png', 1, '2021-12-04 20:10:10', 1, ''),
(5, 'kwiat1.jpg', 2, '2021-12-04 21:30:38', 1, ''),
(6, 'klocki1.jpg', 3, '2021-12-04 22:04:30', 0, ''),
(7, 'konkurs1.jpg', 4, '2021-12-05 18:42:51', 1, ''),
(8, 'konkurs2.jpg', 4, '2021-12-05 18:43:05', 1, ''),
(9, 'zdj1.jpg', 5, '2021-12-05 20:04:32', 1, ''),
(10, 'zdj2.jpg', 5, '2021-12-05 20:04:53', 1, ''),
(11, 'probny1.jpg', 6, '2021-12-05 20:44:29', 1, ''),
(12, 'probny2.jpg', 6, '2021-12-05 20:45:54', 0, ''),
(13, 'tablice1.png', 7, '2021-12-05 20:47:23', 1, ''),
(14, 'tablice2.png', 7, '2021-12-05 20:47:33', 1, ''),
(15, 'obrazek1.jpg', 8, '2021-12-05 20:49:38', 1, ''),
(16, 'obrazek2.jpg', 8, '2021-12-05 20:49:53', 1, ''),
(17, 'dom z papieru.jpg', 9, '2021-12-05 20:50:58', 1, ''),
(18, 'pole.jpg', 10, '2021-12-05 20:52:51', 1, ''),
(19, '1.jpg', 11, '2021-12-05 20:53:55', 1, ''),
(20, '2.jpg', 11, '2021-12-05 20:54:05', 1, ''),
(21, 'bosak.png', 12, '2021-12-05 20:54:57', 1, ''),
(22, 'bosak2.png', 12, '2021-12-05 20:55:07', 1, ''),
(23, 'torba.jpg', 13, '2021-12-05 20:56:13', 1, ''),
(24, 'torba2.jpg', 13, '2021-12-05 20:56:24', 1, ''),
(25, 'SorryEmoji.jpg', 14, '2021-12-05 20:57:10', 1, ''),
(26, 'tag emoji 9.png', 14, '2021-12-05 20:57:23', 1, ''),
(27, 'thinking-face.png', 14, '2021-12-05 20:57:34', 1, ''),
(28, 'chiny1.jpg', 15, '2021-12-05 20:58:32', 1, ''),
(29, 'chiny2.png', 15, '2021-12-05 20:58:44', 1, ''),
(30, 'Toty2014.png', 16, '2021-12-05 21:00:20', 1, ''),
(31, 'Toty2015.jpg', 16, '2021-12-05 21:00:33', 1, ''),
(32, 'IMG_20200927_175100.jpg', 17, '2021-12-05 21:01:46', 1, ''),
(33, 'IMG_20200927_175112.jpg', 17, '2021-12-05 21:01:58', 1, ''),
(34, 'IMG_20200927_175119.jpg', 17, '2021-12-05 21:02:11', 1, ''),
(35, 'creative.jpeg', 18, '2021-12-05 21:03:04', 1, ''),
(36, 'creative2.jpg', 18, '2021-12-05 21:03:26', 1, ''),
(37, 'r-i-p-grób-kamień-34707618.jpg', 19, '2021-12-05 21:04:31', 1, ''),
(38, 'cr7-1.png', 20, '2021-12-05 21:06:10', 1, ''),
(39, 'Cristiano_Ronaldo_Euro_2016.jpg', 20, '2021-12-05 21:06:09', 1, ''),
(40, 'USA.jpg', 21, '2021-12-05 21:08:29', 1, ''),
(41, '117949201_3491900074188437_6045014161093993081_n.png', 22, '2021-12-05 21:09:55', 1, ''),
(42, '117973613_1010173219419582_6709405740551839275_n.png', 22, '2021-12-05 21:10:14', 1, ''),
(43, '121966943_677016526525903_6930373371625284970_n.jpg', 22, '2021-12-05 21:10:15', 1, ''),
(44, 'gory.jpg', 29, '2022-01-08 21:54:50', 1, 'Góry'),
(45, 'Bez tytułu.png', 29, '2022-01-08 23:09:47', 1, 'amongus'),
(70, 'black.png', 29, '2022-01-09 19:34:16', 1, 'amongus'),
(74, 'straz miejska.jpg', 29, '2022-01-09 19:50:12', 1, 'straż'),
(76, 'spacja.png', 29, '2022-01-09 20:07:45', 1, 'spacja baner'),
(77, 'tenor.gif', 29, '2022-01-09 20:12:20', 1, 'animacja zabijania'),
(78, '269695056_600732711021166_1524724071430010214_n_Easy-Resize.com.jpg', 29, '2022-01-09 20:13:31', 1, 'guaranteed'),
(79, '270113841_622911998960199_1282227184844474626_n.jpg', 29, '2022-01-09 20:13:53', 1, 'francuski '),
(80, 'pexels-pixabay-290470.jpg', 29, '2022-01-09 20:14:05', 1, 'tło1'),
(81, 'pexels-janez-podnar-1424246.jpg', 29, '2022-01-09 20:14:15', 1, 'tło2'),
(82, '236872158_877903736455370_1820521426227787126_n.jpg', 29, '2022-01-09 20:14:28', 1, 'keithian'),
(83, 'pexels-luis-quintero-2471235 (1).jpg', 29, '2022-01-09 20:14:48', 1, 'tłologa'),
(84, 'pexels-photo-1555900.jpeg', 29, '2022-01-09 20:14:57', 1, 'tło3'),
(85, 'unnamed.jpg', 29, '2022-01-10 11:10:16', 0, 'one more night'),
(86, '1.jpg', 29, '2022-01-10 11:10:55', 0, 'wieża Eifla'),
(87, 'Flower_(166180281).jpeg', 29, '2022-01-10 11:11:15', 0, 'czerwony kwiatek'),
(88, 'pobrane.jpeg', 29, '2022-01-10 11:11:25', 0, 'Pole kwiatowe'),
(89, 'IMG_20200927_175100.jpg', 29, '2022-01-10 11:13:58', 0, 'rakiety'),
(90, 'creative.jpeg', 29, '2022-01-10 11:22:12', 0, 'głośniki'),
(91, 'creative2.jpg', 29, '2022-01-10 11:22:25', 0, 'glosniki2'),
(92, 'Toty2014.png', 29, '2022-01-10 11:22:39', 0, 'karty'),
(93, 'original_prepared_photo4_Moment.jpg', 33, '2022-01-13 22:27:48', 0, 'pokój muzyczny'),
(94, 'pobrane.jpg', 35, '2022-01-13 22:32:24', 0, 'zmarły'),
(95, '215270485_6088569947849665_5700937751452643882_n.jpg', 35, '2022-01-13 23:33:50', 0, 'pamorama'),
(96, 'pobrane.jpg', 36, '2022-01-17 11:05:22', 1, 'Fotograf');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zdjecia_komentarze`
--

CREATE TABLE `zdjecia_komentarze` (
  `id` int(11) NOT NULL,
  `id_zdjecia` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `komentarz` text COLLATE utf8_polish_ci NOT NULL,
  `zaakceptowany` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `zdjecia_komentarze`
--

INSERT INTO `zdjecia_komentarze` (`id`, `id_zdjecia`, `id_uzytkownika`, `data`, `komentarz`, `zaakceptowany`) VALUES
(1, 1, 22, '2022-01-13 08:46:00', 'Super Grafika , autor wykonał kawał dobrej roboty :D', 1),
(2, 1, 22, '2022-01-13 13:52:39', 'Naprawde dobra robota', 0),
(3, 1, 22, '2022-01-13 13:55:14', 'AAAAAAAAAAĆĆĆĆ', 0),
(5, 82, 11, '2022-01-13 14:05:32', 'Dobry piosenkarz', 0),
(6, 37, 22, '2022-01-13 22:57:50', 'Słabo, że ktoś umarł', 0),
(7, 1, 25, '2022-01-17 11:03:54', 'Ale super zdjecie', 0),
(8, 96, 25, '2022-01-17 11:06:27', 'Kox\r\n', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zdjecia_oceny`
--

CREATE TABLE `zdjecia_oceny` (
  `id` int(11) NOT NULL,
  `id_zdjecia` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `ocena` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `zdjecia_oceny`
--

INSERT INTO `zdjecia_oceny` (`id`, `id_zdjecia`, `id_uzytkownika`, `ocena`) VALUES
(8, 4, 22, 7),
(13, 83, 22, 5),
(14, 83, 22, 10),
(15, 84, 22, 5),
(16, 17, 22, 10),
(17, 1, 22, 10),
(18, 45, 22, 7),
(19, 2, 22, 7),
(20, 82, 11, 9),
(21, 37, 22, 3),
(22, 1, 25, 8),
(23, 96, 25, 10),
(24, 5, 22, 8);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `albumy`
--
ALTER TABLE `albumy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zdjecia`
--
ALTER TABLE `zdjecia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zdjecia_komentarze`
--
ALTER TABLE `zdjecia_komentarze`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zdjecia_oceny`
--
ALTER TABLE `zdjecia_oceny`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `albumy`
--
ALTER TABLE `albumy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT dla tabeli `zdjecia`
--
ALTER TABLE `zdjecia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT dla tabeli `zdjecia_komentarze`
--
ALTER TABLE `zdjecia_komentarze`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT dla tabeli `zdjecia_oceny`
--
ALTER TABLE `zdjecia_oceny`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
