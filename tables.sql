CREATE SCHEMA `backlog_db`;

USE `backlog_db`;

CREATE TABLE `backlog_db`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `mng_id` INT DEFAULT NULL,
  `email` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(45) NOT NULL,
  `priority` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL,
  `due_date` date NOT NULL,
  `date_added` date NOT NULL,
  `done_date` date NOT NULL,
  `description` varchar(120) DEFAULT NULL,
  `assignee_id` int(11) DEFAULT NULL,
  `assignee_mng_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  UNIQUE KEY `task_name_UNIQUE` (`task_name`),
  KEY `assignee_idx` (`assignee_id`),
  CONSTRAINT `assignee` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `assignee_mng` FOREIGN KEY (`assignee_mng_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `creator` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `backlog_db`.`users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`) VALUES ('1', 'ivanovi', 'qwerty', 'ivanovi@gmail.com', 'Ivan', 'Ivanov');
INSERT INTO `backlog_db`.`users` (`user_id`, `username`, `password`, `mng_id`, `email`, `first_name`, `last_name`) VALUES ('2', 'magdalenai', 'qwerty', '1', 'maggie@gmail.com', 'Magdalena', 'Ivanova');
INSERT INTO `backlog_db`.`users` (`user_id`, `username`, `password`, `mng_id`, `email`, `first_name`, `last_name`) VALUES ('3', 'asenovad', 'qwerty', '1', 'dess@gmail.com', 'Desislava', 'Asenova');

