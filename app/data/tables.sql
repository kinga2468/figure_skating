SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_USER');
-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IX_users_1` (`role_id` ASC),
  UNIQUE INDEX `UQ_users_1` (`login` ASC),
  CONSTRAINT `FK_users_1`
    FOREIGN KEY (`role_id`)
    REFERENCES `roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- l: adminadmin2, p: adminadmin2
INSERT INTO `users` (`id`, `login`, `password`, `role_id`) VALUES ('1', 'adminadmin2', '$2y$13$CXp.kFiedg0OTaWPBolvQu/3smyfs67k5OnMkYs0vKHimp9ExFbnK', 1);
-- l: useruser2, p: useruser2
INSERT INTO `users` (`id`, `login`, `password`, `role_id`) VALUES ('2', 'useruser2', '$2y$13$BlWhFoq42d.eETgE7A.6HO77ZN3w4P32Q3Hu69pnnTAQptXCqFnvG', 2);

-- -----------------------------------------------------
-- Table `comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(500) NOT NULL,
  `users_id` INT UNSIGNED NOT NULL,
  `video_id` INT UNSIGNED NOT NULL,
  `date_adding` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `video_id`),
  INDEX `fk_comments_users1_idx` (`users_id` ASC),
  INDEX `fk_comments_video1_idx` (`video_id` ASC),
  CONSTRAINT `fk_comments_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comments_video1`
    FOREIGN KEY (`video_id`)
    REFERENCES `video` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `video`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `video` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `link` VARCHAR(200) NOT NULL,
  `img_video_yt` VARCHAR(200) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `championship` VARCHAR(45) NOT NULL,
  `year_championship` CHAR(4) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `song` VARCHAR(43) NULL,
  `users_id` INT UNSIGNED NOT NULL,
  `skaters_id` INT UNSIGNED NOT NULL,
  `average_rating` DECIMAL(5,2) NOT NULL,
  PRIMARY KEY (`id`, `users_id`, `skaters_id`),
  INDEX `fk_video_users_idx` (`users_id` ASC),
  INDEX `fk_video_skaters1_idx` (`skaters_id` ASC),
  CONSTRAINT `fk_video_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_video_skaters1`
    FOREIGN KEY (`skaters_id`)
    REFERENCES `skaters` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `skaters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `skaters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `country` VARCHAR(45) NOT NULL,
  `info` VARCHAR(1000) NULL,
  `total_record` DOUBLE NOT NULL,
  `free_record` DOUBLE NOT NULL,
  `short_record` DOUBLE NOT NULL,
  `img` VARCHAR(60) NOT NULL,
  `birth_place` VARCHAR(45) NOT NULL,
  `height` INT(3) NOT NULL,
  `couch` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

ALTER TABLE roles CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE roles DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE users CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE users DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE comments CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE comments DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE skaters CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE skaters DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE video CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE video DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;