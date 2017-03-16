-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. Mrz 2017 um 12:43
-- Server-Version: 5.7.17-0ubuntu0.16.04.1
-- PHP-Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `chat`
--
CREATE DATABASE IF NOT EXISTS `chat` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chat`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `feedback`
--

CREATE TABLE `feedback` (
  `Id` int(11) NOT NULL,
  `chatIsGoodOrBad` int(11) NOT NULL,
  `foundEverything` tinyint(1) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `feedbackMissing`
--

CREATE TABLE `feedbackMissing` (
  `Id` int(11) NOT NULL,
  `missing` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `general_settings`
--

CREATE TABLE `general_settings` (
  `id` int(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `login`
--

CREATE TABLE `login` (
  `id` int(32) NOT NULL,
  `session_id` varchar(32) NOT NULL,
  `fk_user_id` int(32) NOT NULL,
  `time` int(20) NOT NULL,
  `display_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `messages_id` int(32) NOT NULL,
  `fk_room_id` int(32) NOT NULL,
  `fk_user_id` int(32) NOT NULL,
  `message` text NOT NULL,
  `time` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `personal_settings`
--

CREATE TABLE `personal_settings` (
  `id` int(32) NOT NULL,
  `fk_user_id` int(128) NOT NULL,
  `title` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `room_user_relation`
--

CREATE TABLE `room_user_relation` (
  `id` int(32) NOT NULL,
  `fk_room_id` int(32) NOT NULL,
  `fk_user_id` int(32) NOT NULL,
  `last_message` int(32) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rooms`
--

CREATE TABLE `rooms` (
  `id` int(32) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_activity`
--

CREATE TABLE `user_activity` (
  `fk_user_id` int(32) NOT NULL,
  `last_activity` int(32) NOT NULL,
  `fk_room_id` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`Id`);

--
-- Indizes für die Tabelle `feedbackMissing`
--
ALTER TABLE `feedbackMissing`
  ADD PRIMARY KEY (`Id`);

--
-- Indizes für die Tabelle `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messages_id`);

--
-- Indizes für die Tabelle `personal_settings`
--
ALTER TABLE `personal_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `room_user_relation`
--
ALTER TABLE `room_user_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`fk_user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `feedback`
--
ALTER TABLE `feedback`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `feedbackMissing`
--
ALTER TABLE `feedbackMissing`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `login`
--
ALTER TABLE `login`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `messages_id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `personal_settings`
--
ALTER TABLE `personal_settings`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `room_user_relation`
--
ALTER TABLE `room_user_relation`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
