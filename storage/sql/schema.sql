--
-- Table structure for table `sfz_categories`
--

DROP TABLE IF EXISTS `sfz_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_categories` (
  `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT '(Default)',
  `description` text,
  `icon` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_migrations`
--

DROP TABLE IF EXISTS `sfz_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_migrations` (
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_peer`
--

DROP TABLE IF EXISTS `sfz_peer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_peer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(40) NOT NULL,
  `user_agent` varchar(80) DEFAULT NULL,
  `ip_address` varchar(64) NOT NULL,
  `passkey` varchar(32) NOT NULL,
  `port` int(5) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `passkey` (`passkey`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_peer_torrent`
--

DROP TABLE IF EXISTS `sfz_peer_torrent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_peer_torrent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `peer_id` int(10) unsigned NOT NULL,
  `torrent_id` int(9) unsigned NOT NULL,
  `uploaded` int(15) unsigned NOT NULL DEFAULT '0',
  `downloaded` int(15) unsigned NOT NULL DEFAULT '0',
  `left` int(15) unsigned NOT NULL DEFAULT '0',
  `stopped` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `peer_id` (`peer_id`),
  KEY `torrent_id` (`torrent_id`),
  CONSTRAINT `sfz_peer_torrent_ibfk_1` FOREIGN KEY (`torrent_id`) REFERENCES `sfz_torrent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sfz_peer_torrent_ibfk_2` FOREIGN KEY (`peer_id`) REFERENCES `sfz_peer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_torrent`
--

DROP TABLE IF EXISTS `sfz_torrent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_torrent` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `filename` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `category` int(2) unsigned NOT NULL,
  `nfo` mediumtext CHARACTER SET latin1,
  `info_hash` varchar(40) CHARACTER SET latin1 NOT NULL,
  `hash` varchar(40) CHARACTER SET latin1 NOT NULL,
  `size` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '1',
  `seeders` int(5) unsigned NOT NULL,
  `leechers` int(5) unsigned NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `nuked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nuked_reason` text CHARACTER SET latin1,
  `views` int(7) unsigned NOT NULL DEFAULT '0',
  `times_completed` int(7) unsigned NOT NULL DEFAULT '0',
  `last_action` timestamp NULL DEFAULT NULL,
  `comments_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `free_leech` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(9) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_UNIQUE` (`hash`),
  KEY `idx_sfz_torrent_category` (`category`),
  KEY `idx_sfz_torrent_uploaded_by` (`user_id`),
  KEY `idx_sfz_torrent_name` (`name`),
  CONSTRAINT `sfz_torrent_ibfk_2` FOREIGN KEY (`category`) REFERENCES `sfz_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sfz_torrent_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `sfz_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_user`
--

DROP TABLE IF EXISTS `sfz_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_user` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `secret` char(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_seen` timestamp NULL DEFAULT NULL,
  `account_status` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sfz_user_passkeys`
--

DROP TABLE IF EXISTS `sfz_user_passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sfz_user_passkeys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(9) unsigned NOT NULL,
  `passkey` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `passkey` (`passkey`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sfz_user_passkeys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sfz_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-12-27  0:32:04
