CREATE TABLE `users_birthday` (
  `birthday_id` int(10) unsigned NOT NULL auto_increment,
  `birthday_uid` int(11) unsigned NOT NULL default '0',
  `birthday_date` date NOT NULL,
  `birthday_photo` varchar(255) NOT NULL,
  `birthday_description` text NOT NULL,
  `birthday_firstname` varchar(150) NOT NULL,
  `birthday_lastname` varchar(150) NOT NULL,
  `birthday_comments` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`birthday_id`),
  KEY `birthday_lastname` (`birthday_lastname`),
  KEY `birthday_date` (`birthday_date`),
  KEY `birthday_uid` (`birthday_uid`)
) ENGINE=MyISAM;
