SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `publish_start` datetime NOT NULL,
  `publish_end` datetime NOT NULL,
  `is_draft` tinyint(1) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment_count` mediumint(7) NOT NULL,
  `spam_count` mediumint(7) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `display_start` (`publish_start`),
  KEY `display_end` (`publish_end`),
  KEY `is_draft` (`is_draft`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `post_id` mediumint(7) NOT NULL,
  `user_id` mediumint(7) NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `is_spam` tinyint(1) NOT NULL,
  `spam_js` tinyint(1) NOT NULL,
  `spam_headers` text COLLATE utf8_unicode_ci NOT NULL,
  `spam_delay` mediumint(7) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `is_spam` (`is_spam`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_count` mediumint(4) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `posts_tags`;
CREATE TABLE `posts_tags` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `post_id` mediumint(7) NOT NULL,
  `tag_id` mediumint(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`,`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
