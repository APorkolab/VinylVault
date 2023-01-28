DROP DATABASE IF EXISTS vinylvault;

CREATE DATABASE vinylvault;

USE vinylvault;

GRANT ALL PRIVILEGES ON vinylvault.* TO 'admin'@'localhost' IDENTIFIED BY 'tM5nWLW2eNTYXsCk';

CREATE TABLE products (
id int(11) NOT NULL AUTO_INCREMENT,
name varchar(255) NOT NULL,
description text NOT NULL,
price decimal(10,2) NOT NULL,
is_avaible TINYINT(1) NOT NULL DEFAULT 0,
created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id),
INDEX (name)
);

CREATE TABLE user (
id INT NOT NULL AUTO_INCREMENT,
name VARCHAR(128) NOT NULL,
username VARCHAR(128) NOT NULL,
password_hash VARCHAR(255) NOT NULL,
api_key VARCHAR(32) NOT NULL,
PRIMARY KEY (id),
UNIQUE (username),
UNIQUE (api_key)
);

CREATE TABLE refresh_token (
token_hash VARCHAR(64) NOT NULL,
expires_at INT UNSIGNED NOT NULL,
PRIMARY KEY (token_hash),
INDEX (expires_at)
);

INSERT INTO products (name, description, price, is_avaible)
VALUES ("Vinyl 1", "A classic rock album", 19.99, true),
("Vinyl 2", "A popular hip-hop album", 24.99, true),
("Vinyl 3", "A jazz record from the 50s", 29.99, false);

INSERT INTO user (name, username, password_hash, api_key)
VALUES ("John Smith", "jsmith", "$2y$12$tM5nWLW2eNTYXsCka8hBPOj7yFc1yJX9q3DA/jK5C5/G1Kj5qf3pq", "abcdefgh12345678"),
("Jane Doe", "jdoe", "$2y$12$tM5nWLW2eNTYXsCka8hBPOj7yFc1yJX9q3DA/jK5C5/G1Kj5qf3pq1", "abcdefgh12345679");


INSERT INTO refresh_token (token_hash, expires_at)
VALUES ("$2y$12$tM5nWLW2eNTYXsCka8hBPOj7yFc1yJX9q3DA/jK5C5/G1Kj5qf3pq", UNIX_TIMESTAMP() + 60 * 60 * 24 * 30),
("$2y$12$tM5nWLW2eNTYXsCka8hBPOj7yFc1yJX9q3DA/jK5C5/G1Kj5qf3pq1", UNIX_TIMESTAMP() + 60 * 60 * 24 * 30);
