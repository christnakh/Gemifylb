CREATE TABLE `black_diamonds` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_diamond` varchar(255) NOT NULL,
  `video_diamond` varchar(255) DEFAULT NULL,
  `shape` varchar(255) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `price/ct` double(10,0) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `diamond` (
  `id` int(11) NOT NULL,
  `nature` varchar(255) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_diamond` varchar(255) NOT NULL,
  `video_diamond` varchar(255) DEFAULT NULL,
  `shape` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `clarity` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `cut_type` varchar(255) NOT NULL,
  `fluorescence_type` varchar(255) NOT NULL,
  `discount_type` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gadgets` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `photo_gadget` varchar(255) NOT NULL,
  `video_gadget` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `gemstone` (
  `id` int(11) NOT NULL,
  `gemstone_name` varchar(255) NOT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `photo_gemstone` varchar(255) NOT NULL,
  `video_gemstone` varchar(255) DEFAULT NULL,
  `weight` decimal(10,2) NOT NULL,
  `cut` varchar(255) NOT NULL,
  `shape` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `type` char(9) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `comment` text,
  `price/ct` double NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_approved` enum('Decline','Accept','Pending') NOT NULL DEFAULT 'Pending',
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `jewelry` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `photo_jewelry` varchar(255) DEFAULT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,0) NOT NULL,
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` enum('Pending','Accept','Decline') DEFAULT 'Pending',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `watches` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `photo_watch` varchar(255) DEFAULT NULL,
  `photo_certificate` varchar(255) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `brand` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,0) NOT NULL,
  `boost` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` enum('Pending','Accept','Decline') DEFAULT 'Pending',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
