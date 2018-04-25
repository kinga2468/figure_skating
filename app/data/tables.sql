SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -- -----------------------------------------------------
-- -- Table `si_bookmarks`
-- -- -----------------------------------------------------
-- CREATE TABLE IF NOT EXISTS `si_bookmarks` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `created_at` DATETIME NOT NULL,
--   `modified_at` DATETIME NOT NULL,
--   `title` VARCHAR(128) NOT NULL,
--   `url` VARCHAR(128) NOT NULL,
--   `is_public` TINYINT UNSIGNED NOT NULL DEFAULT 0,
--   PRIMARY KEY (`id`),
--   UNIQUE INDEX `UQ_bookmarks_1` (`url` ASC))
-- ENGINE = InnoDB;
--
-- -- -----------------------------------------------------
-- -- Table `si_tags`
-- -- -----------------------------------------------------
-- CREATE TABLE IF NOT EXISTS `si_tags` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `name` VARCHAR(128) NOT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE INDEX `UQ_tags_1` (`name` ASC))
-- ENGINE = InnoDB;
--
-- -- -----------------------------------------------------
-- -- Table `si_bookmarks_tags`
-- -- -----------------------------------------------------
-- CREATE TABLE IF NOT EXISTS `si_bookmarks_tags` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `bookmark_id` INT UNSIGNED NOT NULL,
--   `tag_id` INT UNSIGNED NOT NULL,
--   PRIMARY KEY (`id`),
--   INDEX `FK_bookmarks_tags_1` (`bookmark_id` ASC),
--   INDEX `FK_bookmarks_tags_2` (`tag_id` ASC),
--   UNIQUE INDEX `UQ_bookmarks_tags_1` (`bookmark_id` ASC, `tag_id` ASC),
--   CONSTRAINT `FK_bookmarks_tags_1`
--     FOREIGN KEY (`bookmark_id`)
--     REFERENCES `si_bookmarks` (`id`)
--     ON DELETE NO ACTION
--     ON UPDATE NO ACTION,
--   CONSTRAINT `FK_bookmarks_tags_2`
--     FOREIGN KEY (`tag_id`)
--     REFERENCES `si_tags` (`id`)
--     ON DELETE NO ACTION
--     ON UPDATE NO ACTION)
-- ENGINE = InnoDB;
--
-- INSERT INTO `si_tags` (`id`, `name`) VALUES
--   (1, 'mysql'),
--   (2, 'php');
--
-- ALTER TABLE si_bookmarks_tags CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER TABLE si_bookmarks_tags DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER TABLE si_bookmarks CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER TABLE si_bookmarks DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER TABLE si_tags CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER TABLE si_tags DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

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
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_USER');

-- l: TestAdmin, p: kinga-admin
INSERT INTO `users` (`id`, `login`, `password`, `role_id`) VALUES ('1', 'TestAdmin', '$2y$13$u4AJOOeFW9dkYMulEFWideM436praACza09zZyf2sZu5p2Zn1.req', 1);
-- l: TestUser, p: kinga-user
INSERT INTO `users` (`id`, `login`, `password`, `role_id`) VALUES ('2', 'TestUser', '$2y$13$Nejor9ND8borDoWrxti5rOMMDqjUoOAK6u/EDuP7Gvrf3VqtKSEC.', 2);

-- -----------------------------------------------------
-- Table `video`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `video` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `championship` VARCHAR(45) NOT NULL,
  `year_championship` YEAR NOT NULL,
  `type` CHAR(2) NOT NULL,
  `users_id` INT UNSIGNED NOT NULL,
  `skaters_id` INT UNSIGNED NOT NULL,
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
  `free_record` DECIMAL(5,2) NULL,
  `total_record` DECIMAL(5,2) NULL,
  `short_record` DECIMAL(5,2) NULL,
  `img` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;