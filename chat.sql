-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 15. Feb 2017 um 15:55
-- Server-Version: 5.7.17-0ubuntu0.16.04.1
-- PHP-Version: 7.0.13-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `chat`
--

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
-- Tabellenstruktur für Tabelle `rooms`
--

CREATE TABLE `rooms` (
  `id` int(32) NOT NULL,
  `name` varchar(64) NOT NULL
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
-- Tabellenstruktur für Tabelle `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(32) NOT NULL,
  `fk_user_id` int(32) NOT NULL,
  `last_activity` int(32) NOT NULL,
  `fk_room_id` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

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
-- Indizes für die Tabelle `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `room_user_relation`
--
ALTER TABLE `room_user_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `login`
--
ALTER TABLE `login`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `messages_id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT für Tabelle `personal_settings`
--
ALTER TABLE `personal_settings`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `room_user_relation`
--
ALTER TABLE `room_user_relation`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
