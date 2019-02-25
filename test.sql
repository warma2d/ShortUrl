CREATE TABLE `short_url` (
  `id` int(11) NOT NULL,
  `code` char(5) NOT NULL,
  `url` text NOT NULL,
  `expires_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;