USE yasbs;

CREATE TABLE IF NOT EXISTS `users`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(200) NOT NULL,
    `username` VARCHAR(200) NOT NULL,
    `password` CHAR(64) NOT NULL,
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `books`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL, 
    `price` INT NOT NULL, -- price in cents
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;