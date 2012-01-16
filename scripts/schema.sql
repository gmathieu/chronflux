# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.15)
# Database: internal.chronflux.dev
# Generation Time: 2012-01-16 18:13:39 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` decimal(4,2) NOT NULL,
  `stop_time` decimal(4,2) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `jobs.date` (`date`),
  KEY `jobs.beg_time` (`start_time`),
  KEY `jobs.end_time` (`stop_time`),
  CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table jobs_total_hours_by_date
# ------------------------------------------------------------

DROP VIEW IF EXISTS `jobs_total_hours_by_date`;

CREATE TABLE `jobs_total_hours_by_date` (
   `user_id` INT(11) NOT NULL,
   `project_id` INT(11) NOT NULL,
   `task_id` INT(11) NOT NULL,
   `date` DATE NOT NULL,
   `total_hours_by_date` DECIMAL(27) DEFAULT NULL
) ENGINE=MyISAM;



# Dump of table projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table projects_total_users
# ------------------------------------------------------------

DROP VIEW IF EXISTS `projects_total_users`;

CREATE TABLE `projects_total_users` (
   `project_id` INT(11) NOT NULL DEFAULT '0',
   `total_users` BIGINT(21) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;



# Dump of table tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(5) DEFAULT '',
  `description` text,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tasks_total_users
# ------------------------------------------------------------

DROP VIEW IF EXISTS `tasks_total_users`;

CREATE TABLE `tasks_total_users` (
   `task_id` INT(11) NOT NULL DEFAULT '0',
   `total_users` BIGINT(21) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;



# Dump of table user_projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_projects`;

CREATE TABLE `user_projects` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `note` text,
  `order` tinyint(1) unsigned NOT NULL DEFAULT '99',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`project_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `user_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_projects_total_hours
# ------------------------------------------------------------

DROP VIEW IF EXISTS `user_projects_total_hours`;

CREATE TABLE `user_projects_total_hours` (
   `user_id` INT(11) NOT NULL,
   `project_id` INT(11) NOT NULL,
   `total_hours` DECIMAL(27) NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM;



# Dump of table user_tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_tasks`;

CREATE TABLE `user_tasks` (
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `color` varchar(6) NOT NULL,
  `order` tinyint(1) unsigned NOT NULL DEFAULT '99',
  PRIMARY KEY (`user_id`,`task_id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `user_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_tasks_total_hours
# ------------------------------------------------------------

DROP VIEW IF EXISTS `user_tasks_total_hours`;

CREATE TABLE `user_tasks_total_hours` (
   `user_id` INT(11) NOT NULL,
   `task_id` INT(11) NOT NULL,
   `total_hours` DECIMAL(27) NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) DEFAULT NULL,
  `service_type` enum('fb','twitter','google','local') DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `clock_in_at` tinyint(1) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user.username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





# Replace placeholder table for user_projects_total_hours with correct view syntax
# ------------------------------------------------------------

DROP TABLE `user_projects_total_hours`;
CREATE VIEW `user_projects_total_hours`
AS select
   `user_projects`.`user_id` AS `user_id`,
   `user_projects`.`project_id` AS `project_id`,ifnull(sum((`jobs`.`stop_time` - `jobs`.`start_time`)),0) AS `total_hours`
from (`user_projects` left join `jobs` on(((`user_projects`.`user_id` = `jobs`.`user_id`) and (`user_projects`.`project_id` = `jobs`.`project_id`)))) group by `jobs`.`user_id`,`jobs`.`project_id`;


# Replace placeholder table for projects_total_users with correct view syntax
# ------------------------------------------------------------

DROP TABLE `projects_total_users`;
CREATE VIEW `projects_total_users`
AS select
   `projects`.`id` AS `project_id`,ifnull(count(`user_projects`.`user_id`),0) AS `total_users`
from (`projects` left join `user_projects` on((`projects`.`id` = `user_projects`.`project_id`))) group by `projects`.`id`;


# Replace placeholder table for tasks_total_users with correct view syntax
# ------------------------------------------------------------

DROP TABLE `tasks_total_users`;
CREATE VIEW `tasks_total_users`
AS select
   `tasks`.`id` AS `task_id`,ifnull(count(`user_tasks`.`user_id`),0) AS `total_users`
from (`tasks` left join `user_tasks` on((`tasks`.`id` = `user_tasks`.`task_id`))) group by `tasks`.`id`;


DROP TABLE `jobs_total_hours_by_date`;
CREATE VIEW `jobs_total_hours_by_date`
AS select
   `jobs`.`user_id` AS `user_id`,
   `jobs`.`project_id` AS `project_id`,
   `jobs`.`task_id` AS `task_id`,
   `jobs`.`date` AS `date`,sum((`jobs`.`stop_time` - `jobs`.`start_time`)) AS `total_hours_by_date`
from `jobs` group by `jobs`.`user_id`,`jobs`.`project_id`,`jobs`.`date`;


# Replace placeholder table for user_tasks_total_hours with correct view syntax
# ------------------------------------------------------------

DROP TABLE `user_tasks_total_hours`;
CREATE VIEW `user_tasks_total_hours`
AS select
   `user_tasks`.`user_id` AS `user_id`,
   `user_tasks`.`task_id` AS `task_id`,ifnull(sum((`jobs`.`stop_time` - `jobs`.`start_time`)),0) AS `total_hours`
from (`user_tasks` left join `jobs` on(((`user_tasks`.`user_id` = `jobs`.`user_id`) and (`user_tasks`.`task_id` = `jobs`.`task_id`)))) group by `jobs`.`user_id`,`jobs`.`task_id`;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
