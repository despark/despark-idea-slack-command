CREATE DATABASE `database`;

USE `database`;

DROP TABLE IF EXISTS `ids`;

CREATE TABLE `ids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slack_id` varchar(255) NOT NULL DEFAULT '',
  `trello_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;