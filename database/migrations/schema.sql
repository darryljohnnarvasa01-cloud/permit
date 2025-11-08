-- Valencia City Motorela Permit System Database Schema
-- Created: 2025

-- Create Database
CREATE DATABASE IF NOT EXISTS motorela_permit_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE motorela_permit_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'applicant') DEFAULT 'applicant',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table (Vehicle Type, Zone, etc.)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('vehicle_type', 'renewal_type', 'fare_zone', 'color_group', 'other') DEFAULT 'other',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permit Types Table
CREATE TABLE IF NOT EXISTS permit_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    requirements TEXT,
    fee DECIMAL(10,2) DEFAULT 0.00,
    validity_months INT DEFAULT 12,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Motorela Permit Applications
CREATE TABLE IF NOT EXISTS motorela_permits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permit_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    
    -- Owner Information
    owner_name VARCHAR(100) NOT NULL,
    owner_address TEXT NOT NULL,
    owner_contact VARCHAR(20),
    
    -- Driver Information
    driver_name VARCHAR(100) NOT NULL,
    driver_license VARCHAR(50),
    driver_contact VARCHAR(20),
    
    -- Vehicle Information
    plate_number VARCHAR(20) NOT NULL,
    chassis_number VARCHAR(50),
    engine_number VARCHAR(50),
    vehicle_make VARCHAR(50),
    vehicle_model VARCHAR(50),
    vehicle_year YEAR,
    vehicle_color VARCHAR(30),
    
    -- Category References
    vehicle_type_id INT,
    renewal_type_id INT,
    fare_zone_id INT,
    color_group_id INT,
    permit_type_id INT NOT NULL,
    
    -- Permit Details
    application_type ENUM('new', 'renewal') DEFAULT 'new',
    issue_date DATE,
    expiration_date DATE,
    status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
    
    -- Requirements
    requirements_json TEXT,
    
    -- Payment
    payment_status ENUM('unpaid', 'paid', 'partial') DEFAULT 'unpaid',
    payment_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_receipt VARCHAR(255),
    
    -- QR Code
    qr_code VARCHAR(255),
    
    -- Remarks
    remarks TEXT,
    rejected_reason TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by INT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_type_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (renewal_type_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (fare_zone_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (color_group_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (permit_type_id) REFERENCES permit_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_permit_number (permit_number),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_plate_number (plate_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    permit_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'online', 'check') DEFAULT 'cash',
    reference_number VARCHAR(100),
    receipt_path VARCHAR(255),
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_by INT,
    
    FOREIGN KEY (permit_id) REFERENCES motorela_permits(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_permit_id (permit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table (For system configuration)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
