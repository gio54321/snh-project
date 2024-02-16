USE yasbs;

CREATE TABLE IF NOT EXISTS `users`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(200) NOT NULL,
    `username` VARCHAR(200) NOT NULL,
    `password` CHAR(64) NOT NULL,
    `verified` BOOLEAN NOT NULL DEFAULT FALSE,
    `verification_token` CHAR(64) DEFAULT NULL,
    `reset_password_token` CHAR(64) DEFAULT NULL,
    `unsuccessful_login_attempts` INT NOT NULL DEFAULT 0,
    `unsuccessful_login_timestap` TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:01',
    `locked` BOOLEAN NOT NULL DEFAULT FALSE,
    `unlock_token` CHAR(64) DEFAULT NULL,
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `books`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `author` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL, 
    `price` INT NOT NULL, -- price in cents
    `image` VARCHAR(200) DEFAULT NULL, -- image url
    `file` VARCHAR(200) DEFAULT NULL,
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders`(
    `id` INT NOT NULL AUTO_INCREMENT,
    `date` TIMESTAMP NOT NULL,
    `user_id` INT NOT NULL,
    `fullname` VARCHAR(200) NOT NULL,
    `address` VARCHAR(200) NOT NULL,
    `city` VARCHAR(200) NOT NULL,
    `zipcode` INT NOT NULL,
    `country` VARCHAR(200) NOT NULL,
    `phone_number` VARCHAR(200) NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_details`(
    `order_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`),
    PRIMARY KEY (`order_id`, `book_id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `owned_books`(
    `user_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`),
    PRIMARY KEY (`user_id`, `book_id`)
) CHARACTER SET=utf8mb4;