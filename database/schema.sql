-- ==============================================================================
-- CƠ SỞ DỮ LIỆU HỆ THỐNG BẢN ĐỒ SỐ GIÁO DỤC TỈNH NINH BÌNH (MySQL 8.0+)
-- Chuẩn hóa theo Đề án Chuyển đổi số Ngành Giáo dục và Đào tạo
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `schoolmap_ninhbinh` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `schoolmap_ninhbinh`;

-- 1. BẢNG DANH MỤC CẤP HỌC (EDUCATION LEVELS)
DROP TABLE IF EXISTS `education_levels`;
CREATE TABLE `education_levels` (
  `id` VARCHAR(20) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#1e40af',
  `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-solid fa-graduation-cap',
  `display_order` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `education_levels` (`id`, `name`, `color`, `icon`, `display_order`) VALUES
('mam_non', 'Mầm non', '#f59e0b', 'fa-solid fa-shapes', 1),
('tieu_hoc', 'Tiểu học', '#3b82f6', 'fa-solid fa-book-open', 2),
('thcs', 'THCS', '#10b981', 'fa-solid fa-school', 3),
('thpt', 'THPT', '#8b5cf6', 'fa-solid fa-building-columns', 4),
('gdtx', 'GDTX - GDNN', '#f43f5e', 'fa-solid fa-chalkboard-user', 5),
('cao_dang', 'Cao đẳng', '#06b6d4', 'fa-solid fa-award', 6),
('dai_hoc', 'Đại học', '#d97706', 'fa-solid fa-university', 7);

-- 2. BẢNG LOẠI HÌNH TRƯỜNG HỌC (SCHOOL TYPES)
DROP TABLE IF EXISTS `school_types`;
CREATE TABLE `school_types` (
  `id` VARCHAR(20) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `school_types` (`id`, `name`) VALUES
('cong_lap', 'Công lập'),
('tu_thuc', 'Ngoài công lập / Tư thục');

-- 3. BẢNG ĐƠN VỊ HÀNH CHÍNH CẤP HUYỆN/THÀNH PHỐ (DISTRICTS)
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `name_en` VARCHAR(100) DEFAULT NULL,
  `center_lat` DECIMAL(10, 6) DEFAULT NULL,
  `center_lng` DECIMAL(10, 6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `districts` (`id`, `code`, `name`, `name_en`, `center_lat`, `center_lng`) VALUES
('tp-ninh-binh', '370', 'Thành phố Ninh Bình', 'Ninh Binh City', 20.2506, 105.9745),
('tp-tam-diep', '371', 'Thành phố Tam Điệp', 'Tam Diep City', 20.1558, 105.8850),
('h-nho-quan', '372', 'Huyện Nho Quan', 'Nho Quan District', 20.3180, 105.7480),
('h-gia-vien', '373', 'Huyện Gia Viễn', 'Gia Vien District', 20.3520, 105.9050),
('h-hoa-lu', '374', 'Huyện Hoa Lư', 'Hoa Lu District', 20.2820, 105.9320),
('h-yen-khanh', '375', 'Huyện Yên Khánh', 'Yen Khanh District', 20.2050, 106.0820),
('h-kim-son', '376', 'Huyện Kim Sơn', 'Kim Son District', 20.0350, 106.0850),
('h-yen-mo', '377', 'Huyện Yên Mô', 'Yen Mo District', 20.1420, 105.9980);

-- 4. BẢNG XÃ / PHƯỜNG / THỊ TRẤN (WARDS)
DROP TABLE IF EXISTS `wards`;
CREATE TABLE `wards` (
  `id` VARCHAR(50) NOT NULL,
  `district_id` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `area_km2` DECIMAL(8, 2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ward_district` (`district_id`),
  CONSTRAINT `fk_wards_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG CƠ SỞ GIÁO DỤC (SCHOOLS - BẢNG TRỌNG TÂM CÓ HỖ TRỢ GIS SPATIAL INDEX)
DROP TABLE IF EXISTS `schools`;
CREATE TABLE `schools` (
  `id` VARCHAR(50) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã định danh trường học do Bộ GD&ĐT cấp',
  `name` VARCHAR(255) NOT NULL COMMENT 'Tên đầy đủ cơ sở giáo dục',
  `education_level_id` VARCHAR(20) NOT NULL,
  `school_type_id` VARCHAR(20) NOT NULL,
  `district_id` VARCHAR(50) NOT NULL,
  `ward` VARCHAR(150) DEFAULT NULL,
  `legacy_province` VARCHAR(100) DEFAULT 'Ninh Bình' COMMENT 'Khu vực trước sáp nhập (Hà Nam / Nam Định / Ninh Bình)',
  `address` VARCHAR(255) DEFAULT NULL,
  
  -- Tọa độ địa lý (Lưu cả tọa độ số và trường hình học POINT GIS)
  `lat` DECIMAL(10, 6) NOT NULL,
  `lng` DECIMAL(10, 6) NOT NULL,
  `geom` POINT NOT NULL COMMENT 'Tọa độ không gian GIS WGS84 cho truy vấn nhanh Point(lng, lat)',

  -- Thông tin liên hệ & ban giám hiệu
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `principal` VARCHAR(100) DEFAULT NULL COMMENT 'Họ tên Hiệu trưởng',

  -- Tiêu chuẩn & Quy mô
  `is_national_standard` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Đạt chuẩn quốc gia, 0: Chưa',
  `national_standard_level` TINYINT DEFAULT NULL COMMENT 'Mức 1 hoặc Mức 2',
  `founded_year` INT DEFAULT NULL,
  `student_count` INT NOT NULL DEFAULT 0,
  `teacher_count` INT NOT NULL DEFAULT 0,
  `class_count` INT NOT NULL DEFAULT 0,
  `classroom_count` INT DEFAULT 0,
  `computer_room_count` INT DEFAULT 0,
  `library` TINYINT(1) DEFAULT 1,
  `lab_count` INT DEFAULT 0,
  `campus_area_m2` DECIMAL(10, 2) DEFAULT NULL,

  -- Trạng thái & Thẩm tra
  `status` ENUM('VERIFIED', 'NEED_REVIEW', 'UNVERIFIED', 'INACTIVE') NOT NULL DEFAULT 'VERIFIED',
  `last_verified_at` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_school_district` (`district_id`),
  KEY `idx_school_level` (`education_level_id`),
  KEY `idx_school_type` (`school_type_id`),
  KEY `idx_school_status` (`status`),
  
  -- CHỈ MỤC KHÔNG GIAN (SPATIAL INDEX) CỦA MYSQL 8.0+:
  -- Cho phép tính toán bán kính vùng đệm Buffer 1km/3km trong vài phần nghìn giây
  SPATIAL KEY `idx_spatial_geom` (`geom`),

  CONSTRAINT `fk_school_level` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`),
  CONSTRAINT `fk_school_type` FOREIGN KEY (`school_type_id`) REFERENCES `school_types` (`id`),
  CONSTRAINT `fk_school_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG LỊCH SỬ THỐNG KÊ QUY MÔ HỌC SINH / GIÁO VIÊN THEO NĂM HỌC (SCHOOL STATISTICS)
DROP TABLE IF EXISTS `school_statistics`;
CREATE TABLE `school_statistics` (
  `id` BIGINT AUTO_INCREMENT NOT NULL,
  `school_id` VARCHAR(50) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL COMMENT 'Ví dụ: 2023-2024, 2024-2025, 2025-2026',
  `students` INT NOT NULL DEFAULT 0,
  `teachers` INT NOT NULL DEFAULT 0,
  `classes` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_school_academic_year` (`school_id`, `academic_year`),
  CONSTRAINT `fk_stats_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG CƠ SỞ VẬT CHẤT CHI TIẾT (FACILITIES)
DROP TABLE IF EXISTS `school_facilities`;
CREATE TABLE `school_facilities` (
  `id` BIGINT AUTO_INCREMENT NOT NULL,
  `school_id` VARCHAR(50) NOT NULL,
  `classrooms` INT DEFAULT 0 COMMENT 'Số phòng học kiên cố',
  `computer_rooms` INT DEFAULT 0 COMMENT 'Phòng máy vi tính',
  `computers_count` INT DEFAULT 0 COMMENT 'Tổng số máy tính phục vụ học tập',
  `labs` INT DEFAULT 0 COMMENT 'Phòng thí nghiệm Lý/Hóa/Sinh',
  `has_library` TINYINT(1) DEFAULT 1,
  `has_multipurpose_building` TINYINT(1) DEFAULT 0 COMMENT 'Nhà tập đa năng',
  `sports_area_m2` DECIMAL(10, 2) DEFAULT NULL,
  `campus_area_m2` DECIMAL(10, 2) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_facilities_school` (`school_id`),
  CONSTRAINT `fk_facilities_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG NGƯỜI DÙNG & PHÂN QUYỀN (USERS & ROLES)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT AUTO_INCREMENT NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('SUPER_ADMIN', 'DISTRICT_ADMIN', 'VIEWER') NOT NULL DEFAULT 'VIEWER',
  `district_id` VARCHAR(50) DEFAULT NULL COMMENT 'Nếu là cán bộ huyện thì chỉ quản lý huyện đó',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_user_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm tài khoản mẫu
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES
('admin.gis', 'admin.gis@ninhbinh.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Hòa', 'SUPER_ADMIN'),
('canbo.qldl', 'canbo.qldl@ninhbinh.edu.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Mai', 'DISTRICT_ADMIN');

-- 9. BẢNG NHẬT KÝ HỆ THỐNG (AUDIT LOGS)
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` BIGINT AUTO_INCREMENT NOT NULL,
  `user_id` BIGINT DEFAULT NULL,
  `username` VARCHAR(50) DEFAULT NULL,
  `action` VARCHAR(50) NOT NULL COMMENT 'CREATE, UPDATE, DELETE, IMPORT_EXCEL',
  `target_type` VARCHAR(50) NOT NULL COMMENT 'SCHOOL, USER, DISTRICT',
  `target_id` VARCHAR(50) DEFAULT NULL,
  `note` VARCHAR(255) DEFAULT NULL,
  `old_values` JSON DEFAULT NULL,
  `new_values` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_user` (`user_id`),
  KEY `idx_log_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
