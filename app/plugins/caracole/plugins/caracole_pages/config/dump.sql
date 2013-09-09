CREATE TABLE IF NOT EXISTS `pages` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `order` mediumint(7) NOT NULL,
  `is_draft` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order` (`order`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;