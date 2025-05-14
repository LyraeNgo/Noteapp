
CREATE DATABASE IF NOT EXISTS `USERS` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `USERS`;


CREATE TABLE `userInfo` (
  `id` int PRIMARY KEY auto_increment NOT NULL,
  `name` varchar(128) NOT NULL,

  `pass` varchar(255) DEFAULT NULL
);

INSERT INTO `userInfo` (`name`, `pass`) VALUES
(`user1`,`user1`),
('user2',`user2`)