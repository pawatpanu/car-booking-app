CREATE DATABASE IF NOT EXISTS car_booking;
USE car_booking;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(100),
  role ENUM('admin','user') DEFAULT 'user'
);

CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  license_plate VARCHAR(20),
  brand VARCHAR(50),
  model VARCHAR(50),
  status ENUM('available','unavailable') DEFAULT 'available'
);

CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  vehicle_id INT,
  start_time DATETIME,
  end_time DATETIME,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  FOREIGN KEY(user_id) REFERENCES users(id),
  FOREIGN KEY(vehicle_id) REFERENCES vehicles(id)
);