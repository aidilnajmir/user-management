-- Create the database 'user_db' if it does not already exist
CREATE DATABASE user_db;

-- Select 'user_db' as the active database for subsequent commands
USE user_db; 

-- Create the 'users' table with the specified columns
CREATE TABLE users (
    -- Primary key with an auto-incremented ID, unsigned for positive values only
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Username with a maximum length of 30 characters, required (NOT NULL)
    username VARCHAR(30) NOT NULL UNIQUE,
    
    -- Email with a maximum length of 50 characters, required (NOT NULL)
    email VARCHAR(50) NOT NULL UNIQUE,
    
    -- Password column to store hashed passwords, required (NOT NULL)
    password VARCHAR(255) NOT NULL,
    
    -- Profile image path, optional with a default of NULL
    profile_image VARCHAR(255) DEFAULT NULL,
    
    -- Timestamp column to record creation time, defaults to the current timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
