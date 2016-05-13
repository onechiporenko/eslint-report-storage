SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `eslint`
--
CREATE DATABASE IF NOT EXISTS `eslint` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `eslint`;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(1000) NOT NULL,
  `subpath` varchar(1000) NOT NULL,
  `description` text NOT NULL,
  `repo` varchar(1000) NOT NULL,
  `raw` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(40) CHARACTER SET utf8 NOT NULL,
  `errors` int(10) unsigned NOT NULL DEFAULT '0',
  `warnings` int(10) unsigned NOT NULL DEFAULT '0',
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_details_by_file`
--

DROP TABLE IF EXISTS `report_details_by_file`;
CREATE TABLE IF NOT EXISTS `report_details_by_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `report_id` int(10) unsigned NOT NULL,
  `errors` smallint(5) unsigned NOT NULL DEFAULT '0',
  `warnings` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lines` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_details_by_rule`
--

DROP TABLE IF EXISTS `report_details_by_rule`;
CREATE TABLE IF NOT EXISTS `report_details_by_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `report_id` int(10) unsigned NOT NULL,
  `errors` smallint(5) unsigned NOT NULL DEFAULT '0',
  `warnings` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
CREATE TABLE IF NOT EXISTS `rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `report_details_by_file`
--
ALTER TABLE `report_details_by_file`
ADD CONSTRAINT `report_details_by_file_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
ADD CONSTRAINT `report_details_by_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`);

--
-- Constraints for table `report_details_by_rule`
--
ALTER TABLE `report_details_by_rule`
ADD CONSTRAINT `report_details_by_rule_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`),
ADD CONSTRAINT `report_details_by_rule_ibfk_2` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`);
