CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_departments` (
`ID` INT(11) NOT NULL AUTO_INCREMENT,
`name` CHAR(255) NOT NULL,
`description` VARCHAR(5000) NOT NULL,
`is_active` ENUM('Y','N') NOT NULL DEFAULT 'Y',
`operators` TEXT NOT NULL,
`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID`),
UNIQUE INDEX `name` (`name`),
INDEX `is_active` (`is_active`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
ALTER TABLE `{%wp_prefix%}aiosc_departments` ADD meta LONGTEXT AFTER operators;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_premades` (
`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
`name` CHAR(255) NULL DEFAULT NULL,
`content` LONGTEXT NULL,
`is_shared` ENUM('Y','N') NULL DEFAULT 'N',
`author_id` BIGINT(20) NULL DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`ID`),
INDEX `is_shared` (`is_shared`),
INDEX `author_id` (`author_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_priorities` (
`ID` INT(11) NOT NULL AUTO_INCREMENT,
`name` CHAR(255) NOT NULL,
`description` VARCHAR(5000) NOT NULL,
`is_active` ENUM('Y','N') NOT NULL DEFAULT 'Y',
`level` INT(11) NOT NULL DEFAULT '0',
`color` CHAR(7) NOT NULL,
`date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID`),
UNIQUE INDEX `name` (`name`),
INDEX `is_active` (`is_active`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
ALTER TABLE `{%wp_prefix%}aiosc_priorities` ADD meta LONGTEXT AFTER description;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_replies` (
`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
`ticket_id` BIGINT(20) NOT NULL,
`author_id` BIGINT(20) NOT NULL,
`content` LONGTEXT NOT NULL,
`attachment_ids` TEXT NOT NULL,
`is_public` ENUM('Y','N') NOT NULL DEFAULT 'Y',
`is_staff_reply` ENUM('Y','N') NOT NULL DEFAULT 'N',
`via_email` ENUM('Y','N') NOT NULL DEFAULT 'N',
`date_created` DATETIME NOT NULL,
PRIMARY KEY (`ID`),
INDEX `ticket_id` (`ticket_id`),
INDEX `author_id` (`author_id`),
INDEX `is_public` (`is_public`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
ALTER TABLE `{%wp_prefix%}aiosc_replies` ADD meta LONGTEXT AFTER content;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_tickets` (
`ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
`subject` CHAR(255) NOT NULL,
`content` LONGTEXT NOT NULL,
`status` ENUM('queue','open','closed') NOT NULL DEFAULT 'queue',
`author_id` BIGINT(20) NOT NULL,
`priority_id` INT(11) NOT NULL DEFAULT '0',
`priority_level` INT(11) NOT NULL DEFAULT '0',
`department_id` INT(11) NOT NULL DEFAULT '0',
`op_id` BIGINT(20) NOT NULL,
`collab_ids` TEXT NOT NULL,
`attachment_ids` TEXT NOT NULL,
`ticket_meta` LONGTEXT NOT NULL,
`is_public` ENUM('Y','N') NOT NULL DEFAULT 'N',
`closure_note` VARCHAR(2000) NOT NULL,
`closure_requested` ENUM('Y','N') NOT NULL DEFAULT 'N',
`feedback_stars` TINYINT(5) NOT NULL,
`feedback_comment` VARCHAR(2000) NOT NULL,
`hash_code` CHAR(40) NOT NULL,
`date_created` DATETIME NOT NULL,
`date_open` DATETIME NOT NULL,
`date_closed` DATETIME NOT NULL,
PRIMARY KEY (`ID`),
INDEX `op_id` (`op_id`),
INDEX `author_id` (`author_id`),
INDEX `priority_id` (`priority_id`),
INDEX `department_id` (`department_id`),
INDEX `status` (`status`),
INDEX `is_public` (`is_public`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
ALTER TABLE `{%wp_prefix%}aiosc_tickets` ADD last_update DATETIME AFTER date_closed;
ALTER TABLE `{%wp_prefix%}aiosc_tickets` ADD awaiting_reply ENUM('Y','N') NOT NULL DEFAULT 'N' AFTER op_id;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_uploads` (
`ID` INT(11) NOT NULL AUTO_INCREMENT,
`owner_id` BIGINT(20) NOT NULL DEFAULT '0',
`encrypted_name` CHAR(200) NOT NULL,
`file_name` VARCHAR(1000) NOT NULL,
`file_ext` CHAR(255) NOT NULL,
`download_count` INT(11) NOT NULL DEFAULT '0',
`max_download_times` INT(11) NOT NULL DEFAULT '100',
`date_uploaded` DATETIME NOT NULL,
PRIMARY KEY (`ID`),
INDEX `owner_id` (`owner_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
CREATE TABLE IF NOT EXISTS `{%wp_prefix%}aiosc_cron` (
`ticket_id` BIGINT(20) NULL DEFAULT NULL,
`action` ENUM('reminder_inactivity','reminder_queue') NULL DEFAULT NULL,
`date_called` DATETIME NULL DEFAULT NULL,
INDEX `ticket_id` (`ticket_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM;