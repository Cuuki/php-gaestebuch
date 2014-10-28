CREATE TABLE `guestbook` (
  `id_entry` INT AUTO_INCREMENT PRIMARY KEY,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX created (created)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;