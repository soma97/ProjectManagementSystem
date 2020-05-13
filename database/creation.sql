-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema project_management_system
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema project_management_system
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `project_management_system` DEFAULT CHARACTER SET utf8 ;
USE `project_management_system` ;

-- -----------------------------------------------------
-- Table `project_management_system`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(55) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `auth_key` VARCHAR(255) NOT NULL,
  `access_token` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`project`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`project` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(200) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`activity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`activity` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `estimated_hours` INT NOT NULL,
  `project_id` INT NOT NULL,
  `parent_activity_id` INT NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_activity_project1_idx` (`project_id` ASC) VISIBLE,
  INDEX `fk_activity_activity1_idx` (`parent_activity_id` ASC) VISIBLE,
  CONSTRAINT `fk_activity_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project_management_system`.`project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity1`
    FOREIGN KEY (`parent_activity_id`)
    REFERENCES `project_management_system`.`activity` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`user_has_project`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`user_has_project` (
  `user_id` INT NOT NULL,
  `project_id` INT NOT NULL,
  `role` VARCHAR(45) NOT NULL,
  `internal` TINYINT(1) NOT NULL,
  PRIMARY KEY (`user_id`, `project_id`),
  INDEX `fk_user_has_project_project1_idx` (`project_id` ASC) VISIBLE,
  INDEX `fk_user_has_project_user_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_has_project_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `project_management_system`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_project_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project_management_system`.`project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`effort`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`effort` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hours` INT NOT NULL,
  `user_id` INT NOT NULL,
  `activity_id` INT NOT NULL,
  `created_at` INT NOT NULL,
  `updated_at` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_effort_user1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_effort_activity1_idx` (`activity_id` ASC) VISIBLE,
  CONSTRAINT `fk_effort_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `project_management_system`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_effort_activity1`
    FOREIGN KEY (`activity_id`)
    REFERENCES `project_management_system`.`activity` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`user_has_activity`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`user_has_activity` (
  `user_id` INT NOT NULL,
  `activity_id` INT NOT NULL,
  `role` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_id`, `activity_id`),
  INDEX `fk_user_has_activity_activity1_idx` (`activity_id` ASC) VISIBLE,
  INDEX `fk_user_has_activity_user1_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_has_activity_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `project_management_system`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_activity_activity1`
    FOREIGN KEY (`activity_id`)
    REFERENCES `project_management_system`.`activity` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `project_management_system`.`revenue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_management_system`.`revenue` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(1000) NOT NULL,
  `amount` FLOAT NOT NULL,
  `created_at` INT NOT NULL,
  `updated_at` INT NOT NULL,
  `project_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_revenue_project1_idx` (`project_id` ASC) VISIBLE,
  CONSTRAINT `fk_revenue_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project_management_system`.`project` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
