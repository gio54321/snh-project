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
    `author` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL, 
    `price` INT NOT NULL, -- price in cents
    `image` VARCHAR(200) DEFAULT NULL, -- image url
    PRIMARY KEY (`id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `checkout`(
    `user_id` INT NOT NULL,
    `date` TIMESTAMP NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    PRIMARY KEY (`user_id`)
) CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `checkout_details`(
    `user_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `checkout`(`user_id`),
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`),
) CHARACTER SET=utf8mb4;