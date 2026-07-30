CREATE DATABASE ticket_event_sekolah;
USE ticket_event_sekolah;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('admin','user') DEFAULT 'user'
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_event VARCHAR(100),
    deskripsi TEXT,
    tanggal DATE,
    lokasi VARCHAR(100),
    harga INT,
    gambar VARCHAR(255)
);

CREATE TABLE pemesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    jumlah_tiket INT,
    total_harga INT,
    status ENUM('pending','lunas') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);