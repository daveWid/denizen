INSERT INTO `oauth_clients` (`id`, `secret`, `name`, `auto_approve`) VALUES
('krAKQG20vByjJt40Xi50', 'LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx', 'Denizen API Test', 1);

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`) VALUES
(1, 'api@denizen.com', '$2y$12$gu3HdPTC3KjG/CHQmSq3Pet.ydMg28UFRnN2OTlF3FH.UcLEWlkua', 'Denizen', 'APIUser');