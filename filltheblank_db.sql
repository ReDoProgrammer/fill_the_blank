-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 14, 2024 lúc 05:03 PM
-- Phiên bản máy phục vụ: 10.4.27-MariaDB
-- Phiên bản PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `filltheblank_db`
--

DELIMITER $$
--
-- Các hàm
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_first_name` (`fullname` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE first_name VARCHAR(255);
    SET first_name = TRIM(SUBSTRING_INDEX(fullname, ' ', -1));
    RETURN first_name;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) NOT NULL,
  `teaching_id` bigint(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `number_of_questions` int(1) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL,
  `mode` int(1) NOT NULL DEFAULT 0 COMMENT 'Nếu lớn hơn 0 sẽ là id của config\r\nNếu 0 <=> random\r\nNếu -1 <=> tuỳ chỉnh',
  `thumbnail` varchar(200) NOT NULL,
  `begin_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `subject_id` int(11) NOT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_answers`
--

CREATE TABLE `exam_answers` (
  `id` int(11) NOT NULL,
  `exam_result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_blank_id` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_answers`
--

INSERT INTO `exam_answers` (`id`, `exam_result_id`, `question_id`, `question_blank_id`, `answer`) VALUES
(1, 31, 17, 46, '<ul>'),
(2, 32, 4, 55, '<body>'),
(3, 32, 4, 56, '</html>'),
(4, 32, 5, 15, '<p>'),
(5, 32, 5, 16, '</p>'),
(6, 32, 6, 17, '<html>'),
(7, 32, 7, 37, '</body>'),
(8, 32, 8, 19, '<!DOCTYPE html>'),
(9, 32, 8, 20, '</p>'),
(10, 32, 9, 21, '<!DOCTYPE html>'),
(11, 32, 9, 22, '<body>'),
(12, 32, 9, 23, '</body>'),
(13, 32, 10, 38, ''),
(14, 32, 10, 39, 'tgfdsfg'),
(15, 32, 10, 40, 'gfsd'),
(16, 32, 11, 27, ''),
(17, 32, 11, 28, 'gfdsgds'),
(18, 32, 11, 29, 'gsfd'),
(19, 32, 12, 30, '<!DOCTYPE html>'),
(20, 32, 12, 31, '<body>'),
(21, 32, 12, 32, '<p>'),
(22, 32, 12, 33, '<p>'),
(23, 32, 13, 34, '<!DOCTYPE html>'),
(24, 32, 13, 35, '<!DOCTYPE html>'),
(25, 32, 13, 36, '<!DOCTYPE html>'),
(26, 33, 4, 55, '<body>'),
(27, 33, 4, 56, '</html>'),
(28, 33, 5, 15, '<p>'),
(29, 33, 5, 16, '</p>'),
(30, 33, 6, 17, '<html>'),
(31, 33, 7, 37, '</body>'),
(32, 33, 8, 19, '<!DOCTYPE html>'),
(33, 33, 8, 20, '</p>'),
(34, 33, 9, 21, '<!DOCTYPE html>'),
(35, 33, 9, 22, '<body>'),
(36, 33, 9, 23, '</body>'),
(37, 33, 10, 38, '<!DOCTYPE html>'),
(38, 33, 10, 39, '</title>'),
(39, 33, 10, 40, '</body>'),
(40, 33, 11, 27, '<!DOCTYPE html>'),
(41, 33, 11, 28, '<p>'),
(42, 33, 11, 29, '</p>'),
(43, 33, 12, 30, '<!DOCTYPE html'),
(44, 33, 12, 31, '<body>'),
(45, 33, 12, 32, ''),
(46, 33, 12, 33, ''),
(47, 33, 13, 34, '<!DOCTYPE html>'),
(48, 33, 13, 35, '</head>'),
(49, 33, 13, 36, '</body>');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_configs`
--

CREATE TABLE `exam_configs` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL COMMENT 'tiêu đề',
  `subject_id` int(11) NOT NULL COMMENT 'mã môn học tham chiếu khoá ngoại tới subjects',
  `levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'mảng số câu hỏi và điểm' CHECK (json_valid(`levels`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_configs`
--

INSERT INTO `exam_configs` (`id`, `title`, `subject_id`, `levels`) VALUES
(14, 'Kiểm tra thường xuyên HTML', 25, '[{\"quantity\":1,\"mark\":0.75},{\"quantity\":2,\"mark\":1.25},{\"quantity\":10,\"mark\":4},{\"quantity\":4,\"mark\":4.5},{\"quantity\":5,\"mark\":4.75},{\"quantity\":3,\"mark\":5}]'),
(15, 'Kiểm tra định kỳ HTML', 25, '[{\"quantity\":5,\"mark\":0.25},{\"quantity\":4,\"mark\":0.5},{\"quantity\":8,\"mark\":1},{\"quantity\":7,\"mark\":1.5},{\"quantity\":7,\"mark\":4.75},{\"quantity\":9,\"mark\":5}]'),
(16, 'Kiểm tra định kỳ CSS', 26, '[{\"quantity\":3,\"mark\":0.5},{\"quantity\":4,\"mark\":0.75},{\"quantity\":2,\"mark\":1},{\"quantity\":3,\"mark\":1.25},{\"quantity\":8,\"mark\":1.5},{\"quantity\":7,\"mark\":3.25},{\"quantity\":10,\"mark\":4},{\"quantity\":3,\"mark\":5}]'),
(17, 'Kiểm tra thường xuyên CSS', 26, '[{\"quantity\":2,\"mark\":0.25},{\"quantity\":1,\"mark\":0.75},{\"quantity\":2,\"mark\":4},{\"quantity\":8,\"mark\":4.25},{\"quantity\":4,\"mark\":4.5},{\"quantity\":5,\"mark\":4.75},{\"quantity\":3,\"mark\":5}]'),
(18, 'Kiểm tra thường xuyên JavaScript', 27, '[{\"quantity\":3,\"mark\":1},{\"quantity\":7,\"mark\":3.75},{\"quantity\":7,\"mark\":4.5},{\"quantity\":3,\"mark\":4.75},{\"quantity\":5,\"mark\":5}]'),
(19, 'Kiểm tra định kỳ JavaScript', 27, '[{\"quantity\":1,\"mark\":0.5},{\"quantity\":7,\"mark\":1.75},{\"quantity\":11,\"mark\":2.25},{\"quantity\":10,\"mark\":2.75},{\"quantity\":10,\"mark\":3},{\"quantity\":1,\"mark\":5}]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lession_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_results`
--

INSERT INTO `exam_results` (`id`, `user_id`, `subject_id`, `lession_id`, `created_at`) VALUES
(2, 2, 26, 33, '2024-11-03 17:23:10'),
(3, 2, 27, 52, '2024-11-03 17:23:46'),
(4, 2, 27, 53, '2024-11-03 17:26:05'),
(5, 2, 27, 53, '2024-11-03 17:34:31'),
(6, 2, 27, 55, '2024-11-03 17:34:50'),
(7, 2, 27, 55, '2024-11-03 17:34:59'),
(8, 2, 27, 55, '2024-11-03 17:35:13'),
(9, 54, 25, 23, '2024-11-04 11:39:46'),
(10, 54, 27, 59, '2024-11-04 11:43:41'),
(11, 54, 27, 58, '2024-11-04 11:44:31'),
(12, 54, 27, 59, '2024-11-04 11:44:43'),
(13, 54, 27, 58, '2024-11-04 11:45:04'),
(14, 54, 27, 57, '2024-11-04 11:45:27'),
(15, 54, 27, 56, '2024-11-04 11:45:45'),
(16, 54, 27, 55, '2024-11-04 11:46:02'),
(17, 54, 27, 53, '2024-11-04 11:46:24'),
(18, 54, 27, 52, '2024-11-04 11:46:36'),
(19, 54, 27, 51, '2024-11-04 11:47:03'),
(20, 54, 26, 35, '2024-11-05 15:43:06'),
(21, 54, 26, 35, '2024-11-05 15:44:18'),
(22, 57, 26, 33, '2024-11-07 15:56:30'),
(23, 57, 26, 34, '2024-11-07 15:57:00'),
(24, 57, 26, 35, '2024-11-07 15:57:30'),
(25, 57, 26, 36, '2024-11-07 15:57:58'),
(26, 57, 26, 37, '2024-11-07 15:58:13'),
(27, 57, 26, 37, '2024-11-07 15:58:21'),
(28, 57, 25, 22, '2024-11-07 16:05:49'),
(29, 57, 26, 33, '2024-11-07 16:36:43'),
(30, 2, 26, 33, '2024-11-07 16:37:23'),
(31, 50, 25, 26, '2024-12-14 13:25:55'),
(32, 50, 25, 22, '2024-12-14 13:46:28'),
(33, 50, 25, 22, '2024-12-14 14:30:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lessions`
--

CREATE TABLE `lessions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `meta` varchar(255) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lessions`
--

INSERT INTO `lessions` (`id`, `name`, `meta`, `subject_id`, `created_at`, `updated_at`) VALUES
(22, 'Cấu trúc cơ bản của HTML', 'cau-truc-co-ban-cua-html', 25, '2024-10-16 08:58:45', '2024-10-16 08:58:45'),
(23, 'Thẻ tiêu đề và đoạn văn', 'the-tieu-de-va-doan-van', 25, '2024-10-16 08:59:17', '2024-10-16 08:59:17'),
(24, 'Thẻ liên kết (Links)', 'the-lien-ket-links', 25, '2024-10-16 08:59:41', '2024-10-16 09:01:07'),
(25, 'Thẻ hình ảnh (Images)', 'the-hinh-anh-images', 25, '2024-10-16 09:00:31', '2024-10-16 09:00:59'),
(26, 'Danh sách (Lists)', 'danh-sach-lists', 25, '2024-10-16 09:00:51', '2024-10-16 09:00:51'),
(27, 'Bảng (Tables)', 'bang-tables', 25, '2024-10-16 09:01:20', '2024-10-16 09:01:20'),
(28, 'Form (Biểu mẫu)', 'form-bieu-mau', 25, '2024-10-16 09:01:34', '2024-10-16 09:01:34'),
(29, 'Thẻ đa phương tiện (Multimedia)', 'the-da-phuong-tien-multimedia', 25, '2024-10-16 09:02:00', '2024-10-16 09:02:00'),
(30, 'Thẻ nhúng (Embed)', 'the-nhung-embed', 25, '2024-10-16 09:02:16', '2024-10-16 09:02:16'),
(31, 'Định dạng văn bản', 'dinh-dang-van-ban', 25, '2024-10-16 09:02:36', '2024-10-16 09:02:36'),
(32, 'Thẻ metadata và SEO', 'the-metadata-va-seo', 25, '2024-10-16 09:02:51', '2024-10-16 09:03:02'),
(33, 'Cú pháp cơ bản của CSS', 'cu-phap-co-ban-cua-css', 26, '2024-10-16 09:04:12', '2024-10-16 09:04:12'),
(34, 'Màu sắc và đơn vị đo lường', 'mau-sac-va-don-vi-do-luong', 26, '2024-10-16 09:04:40', '2024-10-16 09:04:40'),
(35, 'Kiểu chữ (Fonts)', 'kieu-chu-fonts', 26, '2024-10-16 09:04:50', '2024-10-16 09:04:50'),
(36, 'Viền, lề và khoảng cách', 'vien-le-va-khoang-cach', 26, '2024-10-16 09:05:21', '2024-10-16 09:05:21'),
(37, 'Căn chỉnh và hiển thị', 'can-chinh-va-hien-thi', 26, '2024-10-16 09:05:40', '2024-10-16 09:05:40'),
(38, 'Float và Clear', 'float-va-clear', 26, '2024-10-16 09:05:53', '2024-10-16 09:05:53'),
(39, 'Định dạng background và ảnh', 'dinh-dang-background-va-anh', 26, '2024-10-16 09:06:32', '2024-10-16 09:06:32'),
(40, 'Bố cục Flexbox', 'bo-cuc-flexbox', 26, '2024-10-16 09:06:47', '2024-10-16 09:06:47'),
(41, 'CSS Grid Layout', 'css-grid-layout', 26, '2024-10-16 09:07:30', '2024-10-16 09:07:30'),
(42, 'Hiệu ứng chuyển đổi', 'hieu-ung-chuyen-doi', 26, '2024-10-16 09:07:42', '2024-10-16 09:07:42'),
(43, 'Hiệu ứng hoạt ảnh', 'hieu-ung-hoat-anh', 26, '2024-10-16 09:07:55', '2024-10-16 09:07:55'),
(44, 'Thiết kế đáp ứng', 'thiet-ke-dap-ung', 26, '2024-10-16 09:08:13', '2024-10-16 09:08:13'),
(45, 'Thuộc tính Flexbox nâng cao', 'thuoc-tinh-flexbox-nang-cao', 26, '2024-10-16 09:08:27', '2024-10-16 09:08:27'),
(46, 'Pseudo-classes và Pseudo-elements', 'pseudo-classes-va-pseudo-elements', 26, '2024-10-16 09:08:47', '2024-10-16 09:08:47'),
(47, 'Sử dụng biến trong CSS', 'su-dung-bien-trong-css', 26, '2024-10-16 09:08:59', '2024-10-16 09:08:59'),
(48, 'CSS Grid nâng cao', 'css-grid-nang-cao', 26, '2024-10-16 09:09:29', '2024-10-16 09:09:29'),
(49, 'Kiểu hóa Input và Form', 'kieu-hoa-input-va-form', 26, '2024-10-16 09:09:39', '2024-10-16 09:09:39'),
(50, 'Hiệu ứng Hover và Active', 'hieu-ung-hover-va-active', 26, '2024-10-16 09:09:54', '2024-10-16 09:09:54'),
(51, 'Khai báo biến và kiểu dữ liệu', 'khai-bao-bien-va-kieu-du-lieu', 27, '2024-10-16 09:15:09', '2024-10-16 09:15:09'),
(52, 'Toán tử trong JavaScript', 'toan-tu-trong-javascript', 27, '2024-10-16 09:15:19', '2024-10-16 09:15:19'),
(53, 'Câu lệnh điều kiện (if-else, switch)', 'cau-lenh-dieu-kien-if-else-switch', 27, '2024-10-16 09:15:31', '2024-10-16 09:15:31'),
(54, 'Vòng lặp (for, while, do-while)', 'vong-lap-for-while-do-while', 27, '2024-10-16 09:15:42', '2024-10-16 09:15:42'),
(55, 'Hàm (Functions)', 'ham-functions', 27, '2024-10-16 09:15:53', '2024-10-16 09:15:53'),
(56, 'Mảng (Arrays)', 'mang-arrays', 27, '2024-10-16 09:16:05', '2024-10-16 09:16:05'),
(57, 'Đối tượng (Objects)', 'doi-tuong-objects', 27, '2024-10-16 09:16:14', '2024-10-16 09:16:14'),
(58, 'Lập trình hướng đối tượng', 'lap-trinh-huong-doi-tuong', 27, '2024-10-16 09:16:25', '2024-10-16 09:16:25'),
(59, 'Xử lý chuỗi (Strings)', 'xu-ly-chuoi-strings', 27, '2024-10-16 09:16:34', '2024-10-16 09:16:34'),
(60, 'Xử lý sự kiện trong JavaScript', 'xu-ly-su-kien-trong-javascript', 27, '2024-10-16 09:16:44', '2024-10-16 09:16:44'),
(61, 'Lỗi và Debugging', 'loi-va-debugging', 27, '2024-10-16 09:16:53', '2024-10-16 09:16:53'),
(62, 'Thao tác với DOM', 'thao-tac-voi-dom', 27, '2024-10-16 09:17:04', '2024-10-16 09:17:04'),
(63, 'JSON (JavaScript Object Notation)', 'json-javascript-object-notation', 27, '2024-10-16 09:17:18', '2024-10-16 09:17:18'),
(64, 'Asynchronous JavaScript', 'asynchronous-javascript', 27, '2024-10-16 09:17:35', '2024-10-16 09:17:35'),
(65, 'Fetch API và AJAX', 'fetch-api-va-ajax', 27, '2024-10-16 09:17:46', '2024-10-16 09:17:46'),
(66, 'Module trong JavaScript', 'module-trong-javascript', 27, '2024-10-16 09:17:57', '2024-10-16 09:17:57'),
(67, 'Các tính năng nâng cao', 'cac-tinh-nang-nang-cao', 27, '2024-10-16 09:18:23', '2024-10-16 09:18:23'),
(68, 'Async/Await', 'async-await', 27, '2024-10-16 09:18:41', '2024-10-16 09:18:41'),
(69, 'Xử lý thời gian (setTimeout, setInterval)', 'xu-ly-thoi-gian-settimeout-setinterval', 27, '2024-10-16 09:19:14', '2024-10-16 09:19:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `lession_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`id`, `lession_id`, `question_text`, `created_at`, `updated_at`) VALUES
(4, 22, '<p><strong>Hãy điền các thẻ HTML vào các chỗ trống sau:</strong></p><p>&lt;!DOCTYPE html&gt;</p><p>&lt;html&gt;</p><p>___</p><p>		&lt;h1&gt;Tiêu đề đầu tiên của tôi &lt;/h1&gt;</p><p>		&lt;p&gt; Đoạn văn bản đầu tiên của tôi. &lt;/p&gt;</p><p>&lt;/body&gt;</p><p>___</p>', '2024-10-16 11:30:32', '2024-10-16 12:30:23'),
(5, 22, '<p><strong>Để tạo một đoạn văn bản trong HTML chúng ta sử dụng thẻ nào cho đúng?</strong></p><p>___ Nội dung của đoạn văn. ___</p>', '2024-10-16 11:40:25', '2024-10-16 11:40:25'),
(6, 22, '<p><strong>Đoạn mã dưới đây thiếu thẻ mở HTML, hãy điền vào chỗ trống.</strong></p><p>&lt;!DOCTYPE html&gt;</p><p>___</p><p>&lt;body&gt;</p><p>&lt;h1&gt;Tiêu đề đầu tiên của tôi&lt;/h1&gt;</p><p>&lt;/body&gt;</p><p>&lt;/html&gt;</p>', '2024-10-16 11:42:09', '2024-10-16 11:42:09'),
(7, 22, '<p><strong>Để đóng thẻ body trong HTML, chúng ta sử dụng thẻ nào?</strong></p><p>		&lt;/h1&gt;</p><p>		&lt;p&gt;Some text.&lt;/p&gt;</p><p>___</p><p>&lt;/html&gt;</p>', '2024-10-16 11:43:45', '2024-10-16 12:09:55'),
(8, 22, '<p><strong>Hãy điền thẻ thích hợp để mở và đóng tài liệu HTML.</strong></p><p>___</p><p>&lt;html&gt;</p><p>&lt;body&gt;</p><p>		&lt;p&gt; Sample text. ___</p><p>&lt;/body&gt;</p><p>&lt;/html&gt;</p>', '2024-10-16 11:55:29', '2024-10-16 11:55:29'),
(9, 22, '<p><strong>Điền các thẻ HTML phù hợp để hoàn thiện cấu trúc trang web.</strong></p><p>___</p><p>&lt;html&gt;</p><p>___</p><p>		&lt;h1&gt; Hello World &lt;/h1&gt;</p><p>		&lt;p&gt; Welcome to my website. &lt;/p&gt;</p><p>___</p><p>&lt;/html&gt;</p>', '2024-10-16 11:58:33', '2024-10-16 11:58:33'),
(10, 22, '<p><strong>Hãy điền thẻ phù hợp để mở và đóng tài liệu HTML cùng với thẻ tiêu đề.</strong></p><p>___</p><p>&lt;html&gt;</p><p>&lt;head&gt;</p><p>&lt;title&gt; My Page ___</p><p>&lt;/head&gt;</p><p>&lt;body&gt;</p><p>		&lt;p&gt; Some content here. &lt;/p&gt;</p><p>___</p><p>&lt;/html&gt;</p>', '2024-10-16 12:02:15', '2024-10-16 12:10:13'),
(11, 22, '<p><strong>Điền các thẻ vào chỗ trống để hiển thị một đoạn văn trong trang HTML đơn giản.</strong></p><p>___</p><p>&lt;html&gt;</p><p>&lt;body&gt;</p><p>		___ Hello World! ___</p><p>&lt;/body&gt;</p><p>&lt;/html&gt;</p>', '2024-10-16 12:03:47', '2024-10-16 12:03:47'),
(12, 22, '<p><strong>Hoàn thiện đoạn mã HTML dưới đây bằng cách điền vào các thẻ còn thiếu.</strong></p><p>___</p><p>&lt;html&gt;</p><p>___</p><p>		&lt;h1&gt;Welcome&lt;/h1&gt;</p><p>		___ This is a paragraph. ___</p><p>&lt;/body&gt;</p><p>&lt;/html&gt;</p>', '2024-10-16 12:06:05', '2024-10-16 12:06:05'),
(13, 22, '<p><strong>Điền thẻ để đóng thẻ html và head trong cấu trúc HTML.</strong></p><p>___</p><p>&lt;html&gt;</p><p>&lt;head&gt;</p><p>&lt;title&gt; My Webpage &lt;/title&gt;</p><p>___</p><p>&lt;body&gt;</p><p>		&lt;p&gt;This is some text.&lt;/p&gt;</p><p>___</p><p>&lt;/html&gt;</p>', '2024-10-16 12:08:10', '2024-10-16 12:08:10'),
(14, 23, '<p><strong>Để tạo một tiêu đề h2, hãy điền thẻ phù hợp vào chỗ trống.</strong></p><p>___ Second Heading ___</p>', '2024-10-16 12:13:02', '2024-10-16 12:13:02'),
(15, 24, '<p><strong>Điền thẻ để tạo liên kết đến \"</strong><a href=\"https://example.com/\" target=\"_blank\" style=\"color: windowtext;\"><strong>https://example.com</strong></a><strong>\".</strong></p><p>&lt;a ___&gt; Visit Example &lt;/a&gt;</p>', '2024-10-16 12:14:53', '2024-10-16 12:16:40'),
(16, 25, '<p><strong>Điền thuộc tính để hiển thị hình ảnh từ file \"image.jpg\".</strong></p><p>&lt;img ___ = \"image.jpg\"&gt;</p>', '2024-10-16 12:17:36', '2024-10-16 12:17:36'),
(17, 26, '<p><strong>Điền thẻ để tạo danh sách không có thứ tự.</strong></p><p>___</p><p>&lt;li&gt;Item 1&lt;/li&gt;</p><p>&lt;li&gt;Item 2&lt;/li&gt;</p><p>&lt;/ul&gt;</p>', '2024-10-16 12:18:52', '2024-10-16 12:18:52'),
(18, 27, '<p><strong>Điền thẻ để tạo một bảng trong HTML.</strong></p><p>___</p><p>&lt;tr&gt;&lt;td&gt; Cell 1 &lt;/td&gt;&lt;/tr&gt;</p><p>&lt;/table&gt;</p>', '2024-10-16 12:20:11', '2024-10-16 12:20:11'),
(19, 28, '<p><strong>Điền thẻ để bắt đầu một form trong HTML.</strong></p><p>___ action=\"/submit\" method=\"post\"&gt;</p><p>&lt;input type=\"text\" name=\"username\"&gt;</p><p>&lt;/form&gt;</p>', '2024-10-16 12:21:27', '2024-10-16 12:21:27'),
(20, 29, '<p><strong>Điền thẻ để nhúng video từ một file \"video.mp4\".</strong></p><p>___ controls&gt;</p><p>&lt;source src=\"video.mp4\" type=\"video/mp4\"&gt;</p><p>&lt;/video&gt;</p>', '2024-10-16 12:23:06', '2024-10-16 12:23:06'),
(21, 30, '<p><strong>Điền thẻ để nhúng một file PDF vào trang web.</strong></p><p>___ src=\"document.pdf\" width=\"600\" height=\"500\"&gt;&lt;/embed&gt;</p>', '2024-10-16 12:25:04', '2024-10-16 12:25:04'),
(22, 31, '<p><strong>Điền thẻ để làm chữ in đậm.</strong></p><p>___ This is bold text ___</p>', '2024-10-16 12:26:20', '2024-10-16 12:26:20'),
(23, 32, '<p><strong>Điền thẻ để định nghĩa tiêu đề của trang web.</strong></p><p>&lt;html&gt;</p><p>&lt;head&gt;</p><p>___ My Web Page ___</p><p>&lt;/head&gt;</p><p>&lt;body&gt;</p><p>&lt;/body&gt;</p><p>&lt;/html&gt;</p>', '2024-10-16 12:28:08', '2024-10-16 12:28:08'),
(24, 33, '<p><strong>Đặt màu văn bản thành màu đỏ cho tất cả các phần tử &lt;p&gt;.</strong></p><p>&lt;style&gt;</p><p>___{</p><p>		___ red;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:34:31', '2024-10-16 12:34:31'),
(25, 34, '<p><strong>Để đặt màu văn bản thành màu xanh lá cây, hãy điền các thuộc tính CSS.</strong></p><p>&lt;style&gt;</p><p>p {</p><p>		___ green;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:36:40', '2024-10-16 12:36:40'),
(26, 35, '<p><strong>Để thay đổi font của toàn bộ trang thành Arial, hãy điền thuộc tính CSS.</strong></p><p>&lt;style&gt;</p><p>body {</p><p>		___ Arial, sans-serif;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:38:17', '2024-10-16 12:38:17'),
(27, 36, '<p><strong>Đặt viền cho thẻ div với màu đỏ, độ dày 2px, và dạng viền liền.</strong></p><p>&lt;style&gt;</p><p>div {</p><p>		___ 2px solid red;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:39:44', '2024-10-16 12:39:44'),
(28, 37, '<p><strong>Để căn giữa đoạn văn bản bên trong thẻ div, hãy điền thuộc tính CSS.</strong></p><p>&lt;style&gt;</p><p>div {</p><p>		___ center;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:41:20', '2024-10-16 12:41:20'),
(29, 38, '<p><strong>Để căn thẻ div sang bên trái, hãy điền thuộc tính CSS.</strong></p><p>&lt;style&gt;</p><p>div {</p><p>		___ left;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:42:26', '2024-10-16 12:42:26'),
(30, 39, '<p><strong>Điền thuộc tính CSS để đặt hình ảnh nền cho trang web từ file \"background.jpg\".</strong></p><p>&lt;style&gt;</p><p>body {</p><p>		___ url(\'background.jpg\');</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:43:41', '2024-10-16 12:43:41'),
(31, 40, '<p><strong>Sử dụng Flexbox để căn phần tử con của một container ở giữa theo cả hai chiều.</strong></p><p>&lt;style&gt;</p><p>.container {</p><p>		___ flex;</p><p>&nbsp;&nbsp;		___ center;</p><p>&nbsp;&nbsp;		___ center;</p><p>}</p><p>&lt;/style&gt;</p>', '2024-10-16 12:46:19', '2024-10-16 12:46:19'),
(32, 51, '<p><strong>Để khai báo một biến có tên x trong JavaScript, chúng ta sử dụng từ khóa nào?</strong></p><p>___ x = 5;</p>', '2024-10-16 12:48:11', '2024-10-16 12:48:11'),
(33, 52, '<p><strong>Điền vào chỗ trống để tính tổng hai số a và b.</strong></p><p>let sum = a ___ b;</p>', '2024-10-16 12:49:07', '2024-10-16 12:49:07'),
(34, 53, '<p><strong>Để kiểm tra xem age có lớn hơn 18 hay không, sử dụng cú pháp điều kiện if nào?</strong></p><p>if (___ &gt; 18) {</p><p>		console.log(\"Adult\");</p><p>}</p>', '2024-10-16 12:50:44', '2024-10-16 12:50:44'),
(35, 53, '<p><strong>Điền vào cú pháp để tạo vòng lặp for chạy từ 1 đến 5.</strong></p><p>for (let i = 1; i ___ 5; i++) {</p><p>		console.log(i);</p><p>}</p>', '2024-10-16 12:53:34', '2024-10-16 12:53:34'),
(36, 55, '<p><strong>Để định nghĩa một hàm greet, điền từ khóa đúng.</strong></p><p>___ greet() {</p><p>		console.log(\"Hello!\");</p><p>}</p>', '2024-10-16 12:54:38', '2024-10-16 12:54:38'),
(37, 56, '<p><strong>Để khai báo một mảng chứa các số [1, 2, 3], điền cú pháp thích hợp.</strong></p><p>let numbers = ___;</p>', '2024-10-16 12:55:38', '2024-10-16 12:55:38'),
(38, 57, '<p><strong>Để tạo một đối tượng person với các thuộc tính name và age, điền cú pháp đúng.</strong></p><p>let person = {</p><p>&nbsp;		___: \"John\",</p><p>		___: 30</p><p>};</p>', '2024-10-16 12:56:47', '2024-10-16 12:57:33'),
(39, 58, '<p>Để tạo một lớp Car với thuộc tính brand, điền cú pháp đúng.</p><p>___ Car {</p><p>		constructor(___) {</p><p>				this.brand = brand;</p><p>		}</p><p>}</p>', '2024-10-16 12:58:57', '2024-10-16 12:58:57'),
(40, 59, '<p><strong>Để nối hai chuỗi firstName và lastName, điền cú pháp thích hợp.</strong></p><p>let fullName = firstName ___ lastName;</p>', '2024-10-16 12:59:57', '2024-10-16 12:59:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `question_blanks`
--

CREATE TABLE `question_blanks` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `blank_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `question_blanks`
--

INSERT INTO `question_blanks` (`id`, `question_id`, `position`, `blank_text`) VALUES
(15, 5, 1, '<p>'),
(16, 5, 2, '</p>'),
(17, 6, 1, '<html>'),
(19, 8, 1, '<!DOCTYPE html>'),
(20, 8, 2, '</p>'),
(21, 9, 1, '<!DOCTYPE html>'),
(22, 9, 2, '<body>'),
(23, 9, 3, '</body>'),
(27, 11, 1, '<!DOCTYPE html>'),
(28, 11, 2, '<p>'),
(29, 11, 3, '</p>'),
(30, 12, 1, '<!DOCTYPE html>'),
(31, 12, 2, '<body>'),
(32, 12, 3, '<p>'),
(33, 12, 4, '</p>'),
(34, 13, 1, '<!DOCTYPE html>'),
(35, 13, 2, '</head>'),
(36, 13, 3, '</body>'),
(37, 7, 1, '</body>'),
(38, 10, 1, '<!DOCTYPE html>'),
(39, 10, 2, '</title>'),
(40, 10, 3, '</body>'),
(41, 14, 1, '<h2>'),
(42, 14, 2, '</h2>'),
(44, 15, 1, 'href='),
(45, 16, 1, 'src'),
(46, 17, 1, '<ul>'),
(47, 18, 1, '<table>'),
(48, 19, 1, '<form>'),
(49, 20, 1, '<video>'),
(50, 21, 1, '<embed'),
(51, 22, 1, '<b>'),
(52, 22, 2, '</b>'),
(53, 23, 1, '<title>'),
(54, 23, 2, '</title>'),
(55, 4, 1, '<body>'),
(56, 4, 2, '</html>'),
(57, 24, 1, 'p'),
(58, 24, 2, 'color:'),
(59, 25, 1, 'color:'),
(60, 26, 1, 'font-family:'),
(61, 27, 1, 'border:'),
(62, 28, 1, 'text-align:'),
(63, 29, 1, 'float:'),
(64, 30, 1, 'background-image:'),
(65, 31, 1, 'display:'),
(66, 31, 2, 'justify-content:'),
(67, 31, 3, 'align-items:'),
(68, 32, 1, 'let'),
(69, 33, 1, '+'),
(70, 34, 1, 'age'),
(71, 35, 1, '<='),
(72, 36, 1, 'function'),
(73, 37, 1, '[1, 2, 3]'),
(76, 38, 1, 'name'),
(77, 38, 2, 'age'),
(78, 39, 1, 'class'),
(79, 39, 2, 'brand'),
(80, 40, 1, '+');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quizs`
--

CREATE TABLE `quizs` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `question` varchar(255) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `mark` float DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quizs`
--

INSERT INTO `quizs` (`id`, `subject_id`, `question`, `options`, `mark`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(712, 25, 'HTML là gì?', '{\"option_1\":\"Hypertext Markdown Language\",\"option_2\":\"Hypertext Markup Language\",\"option_3\":\"Hypermedia Markup Language\",\"option_4\":\"Hyper Markup Language\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(713, 25, 'Thẻ HTML dùng để tạo đoạn văn bản là thẻ nào?', '{\"option_1\":\"<br>\",\"option_2\":\"<p>\",\"option_3\":\"<div>\",\"option_4\":\"<span>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(714, 25, 'Thuộc tính nào trong HTML dùng để đặt tên duy nhất cho một phần tử?', '{\"option_1\":\"id\",\"option_2\":\"class\",\"option_3\":\"name\",\"option_4\":\"style\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(715, 25, 'HTML là viết tắt của từ gì?', '{\"option_1\":\"Hypertext Transfer Language\",\"option_2\":\"Hyperlink Text Markup Language\",\"option_3\":\"Hypertext Markup Language\",\"option_4\":\"Hypertext Model Language\",\"correct_option\":3}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(716, 25, 'Thẻ HTML nào được sử dụng để chèn hình ảnh vào trang web?', '{\"option_1\":\"<img>\",\"option_2\":\"<image>\",\"option_3\":\"<picture>\",\"option_4\":\"<figure>\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(717, 25, 'Thuộc tính nào bắt buộc trong thẻ <img> để chỉ định đường dẫn hình ảnh?', '{\"option_1\":\"src\",\"option_2\":\"alt\",\"option_3\":\"href\",\"option_4\":\"link\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(718, 25, 'Thẻ HTML nào dùng để tạo danh sách không có thứ tự (unordered list)?', '{\"option_1\":\"<ul>\",\"option_2\":\"<ol>\",\"option_3\":\"<li>\",\"option_4\":\"<list>\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(719, 25, 'Thẻ nào dùng để tạo liên kết trong HTML?', '{\"option_1\":\"<link>\",\"option_2\":\"<a>\",\"option_3\":\"<href>\",\"option_4\":\"<url>\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(720, 25, 'Thẻ nào dùng để tạo bảng trong HTML?', '{\"option_1\":\"<table>\",\"option_2\":\"<tb>\",\"option_3\":\"<th>\",\"option_4\":\"<td>\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(721, 25, 'Để tạo tiêu đề lớn nhất trên trang web, chúng ta dùng thẻ nào?', '{\"option_1\":\"<h6>\",\"option_2\":\"<h1>\",\"option_3\":\"<header>\",\"option_4\":\"<title>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(722, 25, 'Thẻ nào dùng để tạo một ô dữ liệu trong bảng HTML?', '{\"option_1\":\"<th>\",\"option_2\":\"<td>\",\"option_3\":\"<tr>\",\"option_4\":\"<table>\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(723, 25, 'Thuộc tính nào trong HTML dùng để mở liên kết trong tab mới?', '{\"option_1\":\"_blank\",\"option_2\":\"_self\",\"option_3\":\"_parent\",\"option_4\":\"_top\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(724, 25, 'Thẻ HTML nào được sử dụng để tạo một dòng ngắt ngang?', '{\"option_1\":\"<br>\",\"option_2\":\"<hr>\",\"option_3\":\"<line>\",\"option_4\":\"<break>\",\"correct_option\":2}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(725, 25, 'Thẻ nào dùng để xác định đoạn mã code trong HTML?', '{\"option_1\":\"<code>\",\"option_2\":\"<pre>\",\"option_3\":\"<script>\",\"option_4\":\"<var>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(726, 25, 'Để nhúng một file CSS ngoài vào tài liệu HTML, ta sử dụng thẻ nào?', '{\"option_1\":\"<style>\",\"option_2\":\"<css>\",\"option_3\":\"<link>\",\"option_4\":\"<script>\",\"correct_option\":3}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(727, 25, 'Thuộc tính nào dùng để chỉ định một liên kết trong thẻ <a>?', '{\"option_1\":\"src\",\"option_2\":\"href\",\"option_3\":\"link\",\"option_4\":\"ref\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(728, 25, 'Thuộc tính nào được dùng để chỉ định chiều cao của hình ảnh trong thẻ <img>?', '{\"option_1\":\"height\",\"option_2\":\"size\",\"option_3\":\"width\",\"option_4\":\"alt\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(729, 25, 'Thẻ HTML nào dùng để tạo danh sách có thứ tự (ordered list)?', '{\"option_1\":\"<ul>\",\"option_2\":\"<li>\",\"option_3\":\"<ol>\",\"option_4\":\"<list>\",\"correct_option\":3}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(730, 25, 'HTML có phải là ngôn ngữ lập trình không?', '{\"option_1\":\"C\\u00f3\",\"option_2\":\"Kh\\u00f4ng\",\"option_3\":\"Ch\\u1ec9 khi k\\u1ebft h\\u1ee3p v\\u1edbi JavaScript\",\"option_4\":\"T\\u00f9y v\\u00e0o tr\\u01b0\\u1eddng h\\u1ee3p\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(731, 25, 'Thuộc tính nào trong thẻ <form> chỉ định phương thức gửi dữ liệu?', '{\"option_1\":\"action\",\"option_2\":\"method\",\"option_3\":\"enctype\",\"option_4\":\"formtarget\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(732, 25, 'Thẻ nào dùng để tạo một vùng nhập liệu trong biểu mẫu?', '{\"option_1\":\"<input>\",\"option_2\":\"<form>\",\"option_3\":\"<textarea>\",\"option_4\":\"<select>\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(733, 25, 'Thuộc tính nào trong thẻ <input> dùng để xác định loại dữ liệu đầu vào?', '{\"option_1\":\"name\",\"option_2\":\"id\",\"option_3\":\"type\",\"option_4\":\"value\",\"correct_option\":3}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(734, 25, 'Thẻ nào dùng để hiển thị văn bản in đậm?', '{\"option_1\":\"<strong>\",\"option_2\":\"<em>\",\"option_3\":\"<b>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 c \\u0111\\u1ec1u \\u0111\\u00fang\",\"correct_option\":4}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(735, 25, 'Thuộc tính nào trong thẻ <form> dùng để chỉ định nơi xử lý dữ liệu khi form được gửi?', '{\"option_1\":\"method\",\"option_2\":\"action\",\"option_3\":\"enctype\",\"option_4\":\"target\",\"correct_option\":2}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(736, 25, 'Thẻ HTML nào dùng để nhúng video vào trang web?', '{\"option_1\":\"<media>\",\"option_2\":\"<video>\",\"option_3\":\"<source>\",\"option_4\":\"<embed>\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(737, 25, 'Thuộc tính nào của thẻ <input> giúp kiểm tra email hợp lệ?', '{\"option_1\":\"required\",\"option_2\":\"pattern\",\"option_3\":\"type=\\\"email\\\"\",\"option_4\":\"placeholder\",\"correct_option\":3}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(738, 25, 'Thẻ nào dùng để thêm một hàng trong bảng HTML?', '{\"option_1\":\"<tr>\",\"option_2\":\"<td>\",\"option_3\":\"<table>\",\"option_4\":\"<th>\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(739, 25, 'Thuộc tính nào dùng để xác định chú thích cho một hình ảnh?', '{\"option_1\":\"caption\",\"option_2\":\"alt\",\"option_3\":\"title\",\"option_4\":\"description\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(740, 25, 'HTML5 cho phép nhúng âm thanh vào trang web bằng thẻ nào?', '{\"option_1\":\"<audio>\",\"option_2\":\"<sound>\",\"option_3\":\"<track>\",\"option_4\":\"<media>\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(741, 25, 'Thuộc tính nào của thẻ <input> xác định liệu trường có bắt buộc nhập hay không?', '{\"option_1\":\"required\",\"option_2\":\"placeholder\",\"option_3\":\"name\",\"option_4\":\"value\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(742, 25, 'Thẻ nào dùng để chèn một tập tin Flash vào trang HTML?', '{\"option_1\":\"<embed>\",\"option_2\":\"<object>\",\"option_3\":\"<flash>\",\"option_4\":\"<video>\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(743, 25, 'Thẻ HTML nào chứa siêu dữ liệu (metadata) của trang web?', '{\"option_1\":\"<head>\",\"option_2\":\"<meta>\",\"option_3\":\"<link>\",\"option_4\":\"<header>\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(744, 25, 'Thẻ nào dùng để tạo dropdown trong biểu mẫu HTML?', '{\"option_1\":\"<select>\",\"option_2\":\"<dropdown>\",\"option_3\":\"<option>\",\"option_4\":\"<input>\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(745, 25, 'Thuộc tính nào của thẻ <form> dùng để mã hóa dữ liệu khi gửi?', '{\"option_1\":\"enctype\",\"option_2\":\"method\",\"option_3\":\"action\",\"option_4\":\"name\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(746, 25, 'Thẻ nào dùng để tạo danh sách thả xuống với các tùy chọn trong HTML?', '{\"option_1\":\"<option>\",\"option_2\":\"<select>\",\"option_3\":\"<list>\",\"option_4\":\"<dropdown>\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(747, 25, 'Thẻ nào dùng để nhúng tệp JavaScript vào trang web?', '{\"option_1\":\"<script>\",\"option_2\":\"<link>\",\"option_3\":\"<javascript>\",\"option_4\":\"<js>\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(748, 25, 'Thuộc tính nào trong thẻ <input> để chỉ định văn bản gợi ý cho trường nhập?', '{\"option_1\":\"value\",\"option_2\":\"name\",\"option_3\":\"placeholder\",\"option_4\":\"required\",\"correct_option\":3}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(749, 25, 'Thẻ nào dùng để tạo mục trong danh sách?', '{\"option_1\":\"<ul>\",\"option_2\":\"<li>\",\"option_3\":\"<ol>\",\"option_4\":\"<list>\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(750, 25, 'Thẻ nào dùng để chèn một phần tử trong dòng văn bản?', '{\"option_1\":\"<span>\",\"option_2\":\"<div>\",\"option_3\":\"<p>\",\"option_4\":\"<br>\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(751, 25, 'HTML có phân biệt chữ hoa và chữ thường không?', '{\"option_1\":\"C\\u00f3\",\"option_2\":\"Kh\\u00f4ng\",\"option_3\":\"T\\u00f9y theo phi\\u00ean b\\u1ea3n\",\"option_4\":\"T\\u00f9y theo tr\\u00ecnh duy\\u1ec7t\",\"correct_option\":2}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(752, 25, 'Thẻ nào dùng để nhóm các phần tử trong biểu mẫu HTML?', '{\"option_1\":\"<div>\",\"option_2\":\"<fieldset>\",\"option_3\":\"<form>\",\"option_4\":\"<section>\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(753, 25, 'Thẻ nào trong HTML5 dùng để xác định khu vực điều hướng trên trang web?', '{\"option_1\":\"<nav>\",\"option_2\":\"<header>\",\"option_3\":\"<section>\",\"option_4\":\"<aside>\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(754, 25, 'Thẻ nào dùng để xác định một phần nội dung chính trong HTML5?', '{\"option_1\":\"<main>\",\"option_2\":\"<section>\",\"option_3\":\"<header>\",\"option_4\":\"<footer>\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(755, 25, 'Thuộc tính nào của thẻ <a> để ngăn không mở liên kết trong một trang mới?', '{\"option_1\":\"_blank\",\"option_2\":\"_self\",\"option_3\":\"_top\",\"option_4\":\"_none\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(756, 25, 'Thuộc tính nào của thẻ <input> để tạo hộp kiểm (checkbox)?', '{\"option_1\":\"type=\\\"checkbox\\\"\",\"option_2\":\"type=\\\"radio\\\"\",\"option_3\":\"type=\\\"text\\\"\",\"option_4\":\"type=\\\"button\\\"\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(757, 25, 'Thẻ nào dùng để tạo một vùng nội dung có thể cuộn trong HTML?', '{\"option_1\":\"<div>\",\"option_2\":\"<textarea>\",\"option_3\":\"<section>\",\"option_4\":\"<iframe>\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(758, 25, 'Thuộc tính nào của thẻ <table> để gộp các ô theo chiều ngang?', '{\"option_1\":\"rowspan\",\"option_2\":\"colspan\",\"option_3\":\"merge\",\"option_4\":\"group\",\"correct_option\":2}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(759, 25, 'Thẻ HTML nào dùng để định nghĩa tiêu đề của một bảng?', '{\"option_1\":\"<thead>\",\"option_2\":\"<th>\",\"option_3\":\"<tr>\",\"option_4\":\"<caption>\",\"correct_option\":4}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(760, 25, 'Thẻ nào dùng để xác định phần chân trang của tài liệu hoặc phần nội dung?', '{\"option_1\":\"<footer>\",\"option_2\":\"<header>\",\"option_3\":\"<section>\",\"option_4\":\"<aside>\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(761, 25, 'Thẻ nào trong HTML5 dùng để nhúng tệp nhạc vào trang web?', '{\"option_1\":\"<sound>\",\"option_2\":\"<audio>\",\"option_3\":\"<embed>\",\"option_4\":\"<track>\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(762, 25, 'Thẻ nào dùng để định nghĩa một vùng chứa cho đồ họa hoặc hình ảnh trong HTML5?', '{\"option_1\":\"<canvas>\",\"option_2\":\"<svg>\",\"option_3\":\"<img>\",\"option_4\":\"<picture>\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(763, 25, 'Thuộc tính nào của thẻ <input> dùng để chỉ định văn bản mặc định hiển thị trong ô nhập liệu?', '{\"option_1\":\"name\",\"option_2\":\"id\",\"option_3\":\"value\",\"option_4\":\"placeholder\",\"correct_option\":3}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(764, 25, 'Thẻ nào dùng để nhúng tệp tài liệu bên ngoài vào HTML?', '{\"option_1\":\"<object>\",\"option_2\":\"<embed>\",\"option_3\":\"<iframe>\",\"option_4\":\"<link>\",\"correct_option\":3}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(765, 25, 'Thẻ nào dùng để định nghĩa mục nhập trong biểu mẫu dạng thả xuống?', '{\"option_1\":\"<option>\",\"option_2\":\"<input>\",\"option_3\":\"<select>\",\"option_4\":\"<list>\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(766, 25, 'Thuộc tính nào của thẻ <form> chỉ định URL mà biểu mẫu sẽ gửi dữ liệu tới?', '{\"option_1\":\"action\",\"option_2\":\"method\",\"option_3\":\"enctype\",\"option_4\":\"target\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(767, 25, 'Thẻ nào trong HTML5 được sử dụng để đánh dấu một đoạn nội dung quan trọng?', '{\"option_1\":\"<b>\",\"option_2\":\"<i>\",\"option_3\":\"<strong>\",\"option_4\":\"<em>\",\"correct_option\":3}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(768, 25, 'Thuộc tính nào của thẻ <input> giúp giới hạn số lượng ký tự người dùng có thể nhập?', '{\"option_1\":\"size\",\"option_2\":\"maxlength\",\"option_3\":\"minlength\",\"option_4\":\"pattern\",\"correct_option\":2}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(769, 25, 'Thẻ nào được dùng để tạo văn bản gạch chân?', '{\"option_1\":\"<u>\",\"option_2\":\"<ins>\",\"option_3\":\"<s>\",\"option_4\":\"<del>\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(770, 25, 'Thẻ nào trong HTML5 dùng để chứa các phần tử có nội dung phụ hoặc thông tin bổ sung?', '{\"option_1\":\"<aside>\",\"option_2\":\"<footer>\",\"option_3\":\"<header>\",\"option_4\":\"<section>\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(771, 25, 'Thẻ nào dùng để hiển thị văn bản in nghiêng?', '{\"option_1\":\"<b>\",\"option_2\":\"<i>\",\"option_3\":\"<em>\",\"option_4\":\"<mark>\",\"correct_option\":2}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(772, 25, 'Thuộc tính nào của thẻ <input> dùng để tạo trường mật khẩu?', '{\"option_1\":\"type=\\\"password\\\"\",\"option_2\":\"type=\\\"text\\\"\",\"option_3\":\"type=\\\"email\\\"\",\"option_4\":\"type=\\\"submit\\\"\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(773, 25, 'Thẻ nào dùng để xác định một phần thông tin trong bảng?', '{\"option_1\":\"<td>\",\"option_2\":\"<th>\",\"option_3\":\"<tr>\",\"option_4\":\"<tfoot>\",\"correct_option\":2}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(774, 25, 'Thẻ nào dùng để nhóm các hàng trong bảng HTML?', '{\"option_1\":\"<thead>\",\"option_2\":\"<tfoot>\",\"option_3\":\"<tbody>\",\"option_4\":\"<table>\",\"correct_option\":3}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(775, 25, 'Thuộc tính nào của thẻ <textarea> giúp giới hạn số ký tự tối đa người dùng có thể nhập?', '{\"option_1\":\"maxlength\",\"option_2\":\"minlength\",\"option_3\":\"size\",\"option_4\":\"length\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(776, 25, 'Thẻ nào trong HTML5 dùng để hiển thị tiến độ của một nhiệm vụ?', '{\"option_1\":\"<progress>\",\"option_2\":\"<meter>\",\"option_3\":\"<range>\",\"option_4\":\"<input type=\\\"progress\\\">\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(777, 25, 'Thẻ nào trong HTML dùng để tạo một nhóm lựa chọn radio?', '{\"option_1\":\"<input type=\\\"radio\\\">\",\"option_2\":\"<radio>\",\"option_3\":\"<checkbox>\",\"option_4\":\"<select>\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(778, 25, 'Thẻ nào được sử dụng để chứa nội dung đa phương tiện như video và audio?', '{\"option_1\":\"<embed>\",\"option_2\":\"<object>\",\"option_3\":\"<media>\",\"option_4\":\"<figure>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(779, 25, 'Thẻ nào trong HTML5 được sử dụng để tạo một đường tiến độ với giá trị cụ thể?', '{\"option_1\":\"<progress>\",\"option_2\":\"<meter>\",\"option_3\":\"<range>\",\"option_4\":\"<input type=\\\"progress\\\">\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(780, 25, 'Thẻ nào dùng để tạo một phần tiêu đề (header) cho trang web hoặc một phần nội dung?', '{\"option_1\":\"<header>\",\"option_2\":\"<head>\",\"option_3\":\"<h1>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(781, 25, 'Thuộc tính nào của thẻ <input> giúp xác định giá trị ban đầu cho trường nhập?', '{\"option_1\":\"placeholder\",\"option_2\":\"name\",\"option_3\":\"value\",\"option_4\":\"type\",\"correct_option\":3}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(782, 25, 'Thẻ nào trong HTML dùng để xác định một phân đoạn độc lập hoặc khu vực trong tài liệu?', '{\"option_1\":\"<div>\",\"option_2\":\"<section>\",\"option_3\":\"<article>\",\"option_4\":\"<aside>\",\"correct_option\":3}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(783, 25, 'Thẻ nào dùng để chèn biểu tượng favicon vào trang web?', '{\"option_1\":\"<icon>\",\"option_2\":\"<favicon>\",\"option_3\":\"<link rel=\\\"icon\\\">\",\"option_4\":\"<meta>\",\"correct_option\":3}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(784, 25, 'Thuộc tính nào của thẻ <input> dùng để xác định nhiều tập tin có thể được tải lên cùng lúc?', '{\"option_1\":\"multiple\",\"option_2\":\"many\",\"option_3\":\"multi\",\"option_4\":\"several\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(785, 25, 'Thẻ nào dùng để chứa phần chân trang của trang web?', '{\"option_1\":\"<footer>\",\"option_2\":\"<bottom>\",\"option_3\":\"<section>\",\"option_4\":\"<aside>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(786, 25, 'Thẻ nào dùng để tạo tiêu đề phụ (subheading) trong tài liệu HTML?', '{\"option_1\":\"<h2>\",\"option_2\":\"<h3>\",\"option_3\":\"<header>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 b \\u0111\\u1ec1u \\u0111\\u00fang\",\"correct_option\":4}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(787, 25, 'Thẻ HTML nào được sử dụng để chỉ định một khối văn bản được đánh dấu?', '{\"option_1\":\"<mark>\",\"option_2\":\"<highlight>\",\"option_3\":\"<b>\",\"option_4\":\"<em>\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(788, 25, 'Thẻ nào dùng để nhóm các tùy chọn trong một danh sách thả xuống?', '{\"option_1\":\"<option>\",\"option_2\":\"<select>\",\"option_3\":\"<optgroup>\",\"option_4\":\"<group>\",\"correct_option\":3}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(789, 25, 'Thẻ nào dùng để chèn script JavaScript trực tiếp vào HTML?', '{\"option_1\":\"<script>\",\"option_2\":\"<js>\",\"option_3\":\"<style>\",\"option_4\":\"<link>\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(790, 25, 'Thẻ nào trong HTML5 được sử dụng để tạo thành phần điều hướng chính của trang?', '{\"option_1\":\"<nav>\",\"option_2\":\"<header>\",\"option_3\":\"<section>\",\"option_4\":\"<footer>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(791, 25, 'Thẻ nào dùng để định nghĩa chú thích cho một bảng HTML?', '{\"option_1\":\"<caption>\",\"option_2\":\"<th>\",\"option_3\":\"<td>\",\"option_4\":\"<table>\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(792, 25, 'Thuộc tính nào trong thẻ <meta> để chỉ định từ khóa cho trang web?', '{\"option_1\":\"content\",\"option_2\":\"keywords\",\"option_3\":\"description\",\"option_4\":\"name\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(793, 25, 'Thẻ HTML nào dùng để chèn nội dung dạng video từ YouTube?', '{\"option_1\":\"<iframe>\",\"option_2\":\"<embed>\",\"option_3\":\"<video>\",\"option_4\":\"<object>\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(794, 25, 'Thẻ nào trong HTML5 dùng để định nghĩa một vùng chứa cho các phần tử nội dung ngoài lề?', '{\"option_1\":\"<aside>\",\"option_2\":\"<section>\",\"option_3\":\"<article>\",\"option_4\":\"<footer>\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(795, 25, 'Thẻ HTML nào dùng để xác định các mốc thời gian hoặc thời hạn trong tài liệu?', '{\"option_1\":\"<time>\",\"option_2\":\"<date>\",\"option_3\":\"<datetime>\",\"option_4\":\"<timestamp>\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(796, 25, 'Thẻ nào dùng để tạo văn bản đánh số trong HTML?', '{\"option_1\":\"<ul>\",\"option_2\":\"<ol>\",\"option_3\":\"<li>\",\"option_4\":\"<list>\",\"correct_option\":2}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(797, 25, 'Thuộc tính nào của thẻ <input> để cho phép tải lên nhiều tệp tin cùng một lúc?', '{\"option_1\":\"multiple\",\"option_2\":\"many\",\"option_3\":\"files\",\"option_4\":\"several\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(798, 25, 'Thẻ nào được sử dụng để tạo ra một danh sách có các hộp kiểm (checkbox)?', '{\"option_1\":\"<input type=\\\"checkbox\\\">\",\"option_2\":\"<input type=\\\"radio\\\">\",\"option_3\":\"<select>\",\"option_4\":\"<list>\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(799, 25, 'Thẻ nào trong HTML được sử dụng để tạo các phần tử tương tác trong tài liệu?', '{\"option_1\":\"<input>\",\"option_2\":\"<div>\",\"option_3\":\"<form>\",\"option_4\":\"<fieldset>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(800, 25, 'Thuộc tính nào trong HTML được sử dụng để xác định bảng mã ký tự của tài liệu?', '{\"option_1\":\"charset\",\"option_2\":\"encoding\",\"option_3\":\"type\",\"option_4\":\"code\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(801, 25, 'Thẻ HTML nào được dùng để xác định các phần tử văn bản mang tính thay đổi?', '{\"option_1\":\"<del>\",\"option_2\":\"<em>\",\"option_3\":\"<var>\",\"option_4\":\"<kbd>\",\"correct_option\":3}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(802, 25, 'Thẻ nào trong HTML5 được sử dụng để xác định số đo trong một biểu đồ?', '{\"option_1\":\"<meter>\",\"option_2\":\"<progress>\",\"option_3\":\"<range>\",\"option_4\":\"<input type=\\\"meter\\\">\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(803, 25, 'Thuộc tính nào trong thẻ <link> được dùng để chỉ định biểu tượng trang (favicon)?', '{\"option_1\":\"rel\",\"option_2\":\"href\",\"option_3\":\"type\",\"option_4\":\"icon\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(804, 25, 'Thẻ nào trong HTML dùng để tạo trường nhập liệu cho tệp tin?', '{\"option_1\":\"<input type=\\\"file\\\">\",\"option_2\":\"<textarea>\",\"option_3\":\"<input type=\\\"text\\\">\",\"option_4\":\"<input type=\\\"submit\\\">\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(805, 25, 'Thuộc tính nào của thẻ <img> dùng để xác định văn bản thay thế khi ảnh không tải được?', '{\"option_1\":\"alt\",\"option_2\":\"src\",\"option_3\":\"href\",\"option_4\":\"text\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(806, 25, 'Thẻ nào trong HTML5 được sử dụng để hiển thị nội dung nhúng (embedded)?', '{\"option_1\":\"<embed>\",\"option_2\":\"<object>\",\"option_3\":\"<iframe>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 c \\u0111\\u1ec1u \\u0111\\u00fang\",\"correct_option\":4}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(807, 25, 'Thẻ nào trong HTML5 được dùng để chứa tệp phương tiện như video hoặc audio?', '{\"option_1\":\"<source>\",\"option_2\":\"<track>\",\"option_3\":\"<media>\",\"option_4\":\"<figure>\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(808, 25, 'Thẻ nào được sử dụng để xác định vùng điều hướng trong trang web?', '{\"option_1\":\"<nav>\",\"option_2\":\"<aside>\",\"option_3\":\"<section>\",\"option_4\":\"<header>\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(809, 25, 'Thẻ nào trong HTML5 được dùng để chỉ định ngôn ngữ của tài liệu?', '{\"option_1\":\"<meta charset>\",\"option_2\":\"<html lang>\",\"option_3\":\"<meta http-equiv>\",\"option_4\":\"<lang>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(810, 25, 'Thuộc tính nào của thẻ <input> dùng để tạo một trường email?', '{\"option_1\":\"type=\\\"email\\\"\",\"option_2\":\"type=\\\"text\\\"\",\"option_3\":\"type=\\\"number\\\"\",\"option_4\":\"type=\\\"submit\\\"\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(811, 25, 'Thẻ nào dùng để chèn các đoạn văn bản chú thích trong trang HTML?', '{\"option_1\":\"<comment>\",\"option_2\":\"<note>\",\"option_3\":\"<!-- -->\",\"option_4\":\"<span>\",\"correct_option\":3}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(812, 25, 'Thẻ nào trong HTML5 dùng để nhóm các phần tử nội dung có liên quan?', '{\"option_1\":\"<section>\",\"option_2\":\"<article>\",\"option_3\":\"<div>\",\"option_4\":\"<aside>\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(813, 25, 'Thẻ HTML nào xác định tài liệu HTML này là phiên bản HTML5?', '{\"option_1\":\"<!DOCTYPE html>\",\"option_2\":\"<!DOCTYPE HTML5>\",\"option_3\":\"<!DOCTYPE>\",\"option_4\":\"<html>\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(814, 25, 'Thẻ HTML nào dùng để tạo một đoạn trích dẫn từ một nguồn khác?', '{\"option_1\":\"<cite>\",\"option_2\":\"<blockquote>\",\"option_3\":\"<quote>\",\"option_4\":\"<reference>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(815, 25, 'Thẻ nào dùng để xác định một tiêu đề cho tài liệu HTML?', '{\"option_1\":\"<header>\",\"option_2\":\"<title>\",\"option_3\":\"<head>\",\"option_4\":\"<h1>\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(816, 25, 'Thẻ nào trong HTML được dùng để tạo biểu tượng đầu trang (header icon)?', '{\"option_1\":\"<img>\",\"option_2\":\"<header>\",\"option_3\":\"<icon>\",\"option_4\":\"<figure>\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(817, 25, 'Thẻ nào dùng để chèn phần nội dung không liên quan đến nội dung chính của trang web?', '{\"option_1\":\"<aside>\",\"option_2\":\"<footer>\",\"option_3\":\"<section>\",\"option_4\":\"<div>\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(818, 25, 'Thẻ nào trong HTML dùng để tạo một vùng nội dung được cuộn theo chiều ngang?', '{\"option_1\":\"<div style=\\\"overflow-x:auto;\\\">\",\"option_2\":\"<section>\",\"option_3\":\"<header>\",\"option_4\":\"<footer>\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(819, 25, 'Thẻ nào dùng để chứa các nút radio liên quan trong một nhóm?', '{\"option_1\":\"<fieldset>\",\"option_2\":\"<input type=\\\"radio\\\">\",\"option_3\":\"<form>\",\"option_4\":\"<label>\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(820, 25, 'Thuộc tính nào của thẻ <input> dùng để tạo một nút bấm gửi dữ liệu trong biểu mẫu?', '{\"option_1\":\"type=\\\"submit\\\"\",\"option_2\":\"type=\\\"button\\\"\",\"option_3\":\"type=\\\"text\\\"\",\"option_4\":\"type=\\\"reset\\\"\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(821, 25, 'Thuộc tính nào của thẻ <input> dùng để tạo một trường chọn ngày tháng?', '{\"option_1\":\"type=\\\"date\\\"\",\"option_2\":\"type=\\\"datetime\\\"\",\"option_3\":\"type=\\\"calendar\\\"\",\"option_4\":\"type=\\\"time\\\"\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(822, 25, 'Thẻ nào trong HTML5 dùng để chỉ định một hình ảnh nền cho trang web?', '{\"option_1\":\"<style>\",\"option_2\":\"<img>\",\"option_3\":\"<background>\",\"option_4\":\"Kh\\u00f4ng c\\u00f3 th\\u1ebb, d\\u00f9ng CSS\",\"correct_option\":4}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(823, 25, 'Thẻ nào được dùng để định nghĩa một khu vực có thể được click vào trong hình ảnh?', '{\"option_1\":\"<area>\",\"option_2\":\"<map>\",\"option_3\":\"<img>\",\"option_4\":\"<link>\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(824, 25, 'Thẻ HTML nào được sử dụng để tạo đường dẫn hình ảnh từ một bản đồ hình ảnh?', '{\"option_1\":\"<map>\",\"option_2\":\"<area>\",\"option_3\":\"<img>\",\"option_4\":\"<path>\",\"correct_option\":2}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(825, 25, 'Thẻ nào được dùng để tạo một đường ngang ngắt quãng trong tài liệu?', '{\"option_1\":\"<hr>\",\"option_2\":\"<br>\",\"option_3\":\"<line>\",\"option_4\":\"<break>\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(826, 25, 'Thuộc tính nào của thẻ <input> dùng để tạo nút đặt lại (reset) trong biểu mẫu?', '{\"option_1\":\"type=\\\"reset\\\"\",\"option_2\":\"type=\\\"clear\\\"\",\"option_3\":\"type=\\\"refresh\\\"\",\"option_4\":\"type=\\\"submit\\\"\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(827, 25, 'Thẻ nào trong HTML được dùng để thêm định dạng chú thích trên hình ảnh?', '{\"option_1\":\"<figure>\",\"option_2\":\"<img>\",\"option_3\":\"<caption>\",\"option_4\":\"<figcaption>\",\"correct_option\":4}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(828, 25, 'Thẻ nào trong HTML5 dùng để tạo ra vùng nội dung không cuộn?', '{\"option_1\":\"<div style=\\\"overflow: hidden;\\\">\",\"option_2\":\"<div>\",\"option_3\":\"<span>\",\"option_4\":\"<section>\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(829, 25, 'Thẻ nào trong HTML dùng để tạo một trang nhúng PDF?', '{\"option_1\":\"<embed>\",\"option_2\":\"<pdf>\",\"option_3\":\"<iframe>\",\"option_4\":\"<object>\",\"correct_option\":4}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(830, 25, 'Thẻ nào trong HTML dùng để nhúng một khung văn bản có thể cuộn và chỉnh sửa?', '{\"option_1\":\"<textarea>\",\"option_2\":\"<input>\",\"option_3\":\"<iframe>\",\"option_4\":\"<div>\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(831, 25, 'Thuộc tính nào của thẻ <a> để thêm tooltip khi di chuột qua liên kết?', '{\"option_1\":\"title\",\"option_2\":\"alt\",\"option_3\":\"src\",\"option_4\":\"href\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(832, 25, 'Thẻ nào trong HTML dùng để tạo tiêu đề cho bảng?', '{\"option_1\":\"<thead>\",\"option_2\":\"<caption>\",\"option_3\":\"<th>\",\"option_4\":\"<table>\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(833, 25, 'Thẻ nào được sử dụng để xác định một liên kết email?', '{\"option_1\":\"<a href=\\\"mailto:\\\">\",\"option_2\":\"<email>\",\"option_3\":\"<link type=\\\"email\\\">\",\"option_4\":\"<mailto>\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(834, 25, 'Thẻ HTML nào dùng để tạo một cột mới trong bảng?', '{\"option_1\":\"<td>\",\"option_2\":\"<th>\",\"option_3\":\"<col>\",\"option_4\":\"<tr>\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(835, 25, 'Thẻ nào trong HTML được sử dụng để chèn một đoạn văn bản dạng code?', '{\"option_1\":\"<code>\",\"option_2\":\"<pre>\",\"option_3\":\"<script>\",\"option_4\":\"<var>\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(836, 25, 'Thuộc tính nào của thẻ <input> giúp kiểm soát định dạng ngày tháng hợp lệ?', '{\"option_1\":\"type=\\\"date\\\"\",\"option_2\":\"format=\\\"date\\\"\",\"option_3\":\"pattern=\\\"date\\\"\",\"option_4\":\"type=\\\"text\\\"\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(837, 25, 'Thẻ nào trong HTML được dùng để nhóm nhiều đường dẫn liên kết (hyperlinks)?', '{\"option_1\":\"<nav>\",\"option_2\":\"<menu>\",\"option_3\":\"<aside>\",\"option_4\":\"<section>\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(838, 25, 'Thẻ nào trong HTML dùng để tạo một vùng nội dung không hiển thị trong trình duyệt?', '{\"option_1\":\"<noscript>\",\"option_2\":\"<hidden>\",\"option_3\":\"<div>\",\"option_4\":\"<span>\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(839, 25, 'Thẻ nào trong HTML5 được sử dụng để xác định chi tiết ẩn mà người dùng có thể mở rộng?', '{\"option_1\":\"<details>\",\"option_2\":\"<summary>\",\"option_3\":\"<section>\",\"option_4\":\"<aside>\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(840, 25, 'Thẻ nào dùng để nhóm các nút lệnh hoặc trường nhập liệu có liên quan trong biểu mẫu?', '{\"option_1\":\"<fieldset>\",\"option_2\":\"<form>\",\"option_3\":\"<label>\",\"option_4\":\"<input>\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(841, 25, 'Thẻ nào được sử dụng để tạo các mục trong danh sách định nghĩa (definition list)?', '{\"option_1\":\"<dd>\",\"option_2\":\"<dt>\",\"option_3\":\"<dl>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 b\",\"correct_option\":4}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(842, 25, 'Thẻ nào trong HTML được sử dụng để hiển thị dữ liệu có mối quan hệ với các thẻ khác trong bảng?', '{\"option_1\":\"<td>\",\"option_2\":\"<th>\",\"option_3\":\"<tr>\",\"option_4\":\"<table>\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(843, 25, 'Thẻ nào trong HTML dùng để hiển thị các khối mã có định dạng trước?', '{\"option_1\":\"<pre>\",\"option_2\":\"<code>\",\"option_3\":\"<var>\",\"option_4\":\"<script>\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(844, 25, 'Thuộc tính nào của thẻ <input> để tạo trường nhập thời gian?', '{\"option_1\":\"type=\\\"time\\\"\",\"option_2\":\"type=\\\"date\\\"\",\"option_3\":\"type=\\\"datetime\\\"\",\"option_4\":\"type=\\\"timestamp\\\"\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(845, 25, 'Thẻ HTML nào dùng để xác định tiêu đề nhóm các nút radio?', '{\"option_1\":\"<legend>\",\"option_2\":\"<fieldset>\",\"option_3\":\"<form>\",\"option_4\":\"<label>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(846, 25, 'Thẻ nào trong HTML dùng để tạo một nút tùy chọn (radio button)?', '{\"option_1\":\"<input type=\\\"radio\\\">\",\"option_2\":\"<input type=\\\"checkbox\\\">\",\"option_3\":\"<select>\",\"option_4\":\"<form>\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(847, 25, 'Thẻ nào dùng để tạo danh sách mô tả trong HTML?', '{\"option_1\":\"<dl>\",\"option_2\":\"<dd>\",\"option_3\":\"<dt>\",\"option_4\":\"<ul>\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(848, 25, 'Thẻ nào trong HTML được dùng để định nghĩa một từ viết tắt hoặc tên viết tắt?', '{\"option_1\":\"<abbr>\",\"option_2\":\"<acronym>\",\"option_3\":\"<initial>\",\"option_4\":\"<abbrname>\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(849, 25, 'Thẻ HTML nào dùng để tạo một phần nội dung hiển thị dữ liệu dạng biểu đồ?', '{\"option_1\":\"<canvas>\",\"option_2\":\"<svg>\",\"option_3\":\"<figure>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 b\",\"correct_option\":4}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(850, 25, 'Thẻ nào được dùng để nhóm các tiêu đề trong một bảng HTML?', '{\"option_1\":\"<thead>\",\"option_2\":\"<tfoot>\",\"option_3\":\"<tbody>\",\"option_4\":\"<table>\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(851, 25, 'Thẻ HTML nào dùng để định nghĩa chú thích cho một nhóm mục nhập trong biểu mẫu?', '{\"option_1\":\"<legend>\",\"option_2\":\"<form>\",\"option_3\":\"<fieldset>\",\"option_4\":\"<label>\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(852, 25, 'Thuộc tính nào của thẻ <input> cho phép tạo một trường nhập chọn file?', '{\"option_1\":\"type=\\\"file\\\"\",\"option_2\":\"type=\\\"text\\\"\",\"option_3\":\"type=\\\"password\\\"\",\"option_4\":\"type=\\\"submit\\\"\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(853, 25, 'Thẻ HTML nào dùng để xác định một phần tử văn bản có thể kéo và thả?', '{\"option_1\":\"<div draggable=\\\"true\\\">\",\"option_2\":\"<drag>\",\"option_3\":\"<span>\",\"option_4\":\"<section>\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(854, 25, 'Thẻ nào trong HTML5 dùng để tạo phần tiêu đề cho một vùng nội dung hoặc tài liệu?', '{\"option_1\":\"<header>\",\"option_2\":\"<head>\",\"option_3\":\"<title>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(855, 25, 'Thẻ HTML nào được dùng để định nghĩa một mục trong danh sách định nghĩa?', '{\"option_1\":\"<dd>\",\"option_2\":\"<dt>\",\"option_3\":\"<dl>\",\"option_4\":\"<li>\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(856, 25, 'Thuộc tính nào trong thẻ <meta> để thiết lập độ rộng trang web?', '{\"option_1\":\"viewport\",\"option_2\":\"width\",\"option_3\":\"content\",\"option_4\":\"charset\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(857, 25, 'Thẻ HTML nào dùng để tạo các mục trong danh sách không theo thứ tự?', '{\"option_1\":\"<li>\",\"option_2\":\"<ul>\",\"option_3\":\"<ol>\",\"option_4\":\"<dl>\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(858, 25, 'Thuộc tính nào trong thẻ <a> dùng để xác định một vị trí neo trong trang web?', '{\"option_1\":\"href=\\\"#id\\\"\",\"option_2\":\"name=\\\"#id\\\"\",\"option_3\":\"src=\\\"#id\\\"\",\"option_4\":\"link=\\\"#id\\\"\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(859, 25, 'Thẻ nào được sử dụng để hiển thị văn bản dưới dạng đoạn trích dẫn ngắn?', '{\"option_1\":\"<q>\",\"option_2\":\"<blockquote>\",\"option_3\":\"<cite>\",\"option_4\":\"<code>\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(860, 25, 'Thẻ HTML nào dùng để tạo một đoạn văn bản mô tả ngắn (summary) có thể mở rộng?', '{\"option_1\":\"<summary>\",\"option_2\":\"<details>\",\"option_3\":\"<aside>\",\"option_4\":\"<section>\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(861, 25, 'Thẻ nào trong HTML được dùng để định nghĩa văn bản gạch bỏ (strikethrough)?', '{\"option_1\":\"<s>\",\"option_2\":\"<del>\",\"option_3\":\"<strike>\",\"option_4\":\"C\\u1ea3 a v\\u00e0 c\",\"correct_option\":4}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(862, 26, 'CSS là gì?', '{\"option_1\":\"Cascading Style Scripts\",\"option_2\":\"Cascading Style Sheets\",\"option_3\":\"Creative Style Sheets\",\"option_4\":\"Colorful Style Sheets\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(863, 26, 'Thuộc tính CSS nào dùng để thay đổi màu văn bản?', '{\"option_1\":\"text-color\",\"option_2\":\"fgcolor\",\"option_3\":\"color\",\"option_4\":\"font-color\",\"correct_option\":3}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(864, 26, 'Thuộc tính nào dùng để thay đổi phông chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"text-decoration\",\"option_3\":\"font-family\",\"option_4\":\"font-weight\",\"correct_option\":3}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(865, 26, 'Thuộc tính nào được sử dụng để thay đổi kích thước phông chữ?', '{\"option_1\":\"font-family\",\"option_2\":\"font-size\",\"option_3\":\"text-size\",\"option_4\":\"size\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(866, 26, 'Thuộc tính nào để thêm nền màu vào một phần tử?', '{\"option_1\":\"bgcolor\",\"option_2\":\"background-color\",\"option_3\":\"color\",\"option_4\":\"background-image\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(867, 26, 'Lựa chọn nào dưới đây là cú pháp đúng của CSS?', '{\"option_1\":\"body {color: black;}\",\"option_2\":\"{body = black;}\",\"option_3\":\"body = black;\",\"option_4\":\"{body; color;}\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(868, 26, 'Thuộc tính nào dùng để căn lề cho văn bản?', '{\"option_1\":\"text-indent\",\"option_2\":\"text-align\",\"option_3\":\"text-style\",\"option_4\":\"text-format\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(869, 26, 'Lựa chọn nào đúng để áp dụng lề trong CSS?', '{\"option_1\":\"margin: 10px;\",\"option_2\":\"padding: 10px;\",\"option_3\":\"space: 10px;\",\"option_4\":\"gap: 10px;\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(870, 26, 'Thuộc tính CSS nào được dùng để thay đổi độ trong suốt của một phần tử?', '{\"option_1\":\"transparency\",\"option_2\":\"opacity\",\"option_3\":\"visible\",\"option_4\":\"display\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(871, 26, 'Cú pháp nào được sử dụng để thay đổi kiểu chữ của một đoạn văn bản?', '{\"option_1\":\"font-family: \'Arial\';\",\"option_2\":\"text-family: \'Arial\';\",\"option_3\":\"font-style: \'Arial\';\",\"option_4\":\"text-type: \'Arial\';\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(872, 26, 'Thuộc tính nào dùng để thay đổi màu của đường viền?', '{\"option_1\":\"border-color\",\"option_2\":\"border-style\",\"option_3\":\"outline-color\",\"option_4\":\"background-color\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(873, 26, 'Cú pháp nào đúng để đặt nền ảnh cho một phần tử?', '{\"option_1\":\"background: image(\'background.jpg\');\",\"option_2\":\"background-image: url(\'background.jpg\');\",\"option_3\":\"background-file: \'background.jpg\';\",\"option_4\":\"background-src: \'background.jpg\';\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(874, 26, 'Thuộc tính nào điều chỉnh độ đậm nhạt của phông chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"font-variant\",\"option_3\":\"font-weight\",\"option_4\":\"font-color\",\"correct_option\":3}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(875, 26, 'Thuộc tính nào thay đổi khoảng cách giữa các từ trong một đoạn văn bản?', '{\"option_1\":\"word-spacing\",\"option_2\":\"letter-spacing\",\"option_3\":\"text-spacing\",\"option_4\":\"line-height\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(876, 26, 'Giá trị nào của text-align sẽ căn chỉnh văn bản sang phải?', '{\"option_1\":\"left\",\"option_2\":\"right\",\"option_3\":\"center\",\"option_4\":\"justify\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(877, 26, 'Cú pháp nào để ẩn một phần tử nhưng vẫn giữ nguyên không gian cho phần tử đó trên trang?', '{\"option_1\":\"display: hidden;\",\"option_2\":\"visibility: hidden;\",\"option_3\":\"display: none;\",\"option_4\":\"opacity: 0;\",\"correct_option\":2}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(878, 26, 'Thuộc tính nào kiểm soát kiểu của danh sách dạng số thứ tự?', '{\"option_1\":\"list-type\",\"option_2\":\"list-style\",\"option_3\":\"list-style-type\",\"option_4\":\"list-number\",\"correct_option\":3}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(879, 26, 'Cú pháp nào đúng để thiết lập kích thước chiều cao cho một phần tử?', '{\"option_1\":\"height: 100px;\",\"option_2\":\"size: 100px;\",\"option_3\":\"max-height: 100px;\",\"option_4\":\"length: 100px;\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(880, 26, 'Thuộc tính nào điều chỉnh khoảng cách giữa các phần tử trong danh sách?', '{\"option_1\":\"list-item-spacing\",\"option_2\":\"item-spacing\",\"option_3\":\"list-gap\",\"option_4\":\"list-style-position\",\"correct_option\":4}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(881, 26, 'Thuộc tính nào được dùng để điều chỉnh hướng của văn bản?', '{\"option_1\":\"direction\",\"option_2\":\"text-align\",\"option_3\":\"text-direction\",\"option_4\":\"writing-mode\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(882, 26, 'Cú pháp nào đúng để thay đổi độ lớn của ảnh nền mà không làm méo ảnh?', '{\"option_1\":\"background-size: contain;\",\"option_2\":\"background-stretch: fit;\",\"option_3\":\"background-width: contain;\",\"option_4\":\"background-height: fit;\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(883, 26, 'Thuộc tính nào để chỉ định kiểu dấu chấm đầu dòng bên ngoài hay bên trong danh sách?', '{\"option_1\":\"list-style-position\",\"option_2\":\"list-item-position\",\"option_3\":\"list-position\",\"option_4\":\"item-position\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(884, 26, 'Cú pháp nào dùng để đặt hình nền ở vị trí trung tâm của phần tử?', '{\"option_1\":\"background-position: center;\",\"option_2\":\"background-location: center;\",\"option_3\":\"background-align: middle;\",\"option_4\":\"background-center: true;\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(885, 26, 'Thuộc tính nào dùng để thay đổi khoảng cách giữa các dòng văn bản?', '{\"option_1\":\"line-spacing\",\"option_2\":\"line-height\",\"option_3\":\"text-height\",\"option_4\":\"letter-spacing\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(886, 26, 'Thuộc tính nào xác định chiều rộng của đường viền phần tử?', '{\"option_1\":\"border-width\",\"option_2\":\"border-style\",\"option_3\":\"width\",\"option_4\":\"border\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(887, 26, 'Thuộc tính nào dùng để thay đổi khoảng cách bên trong phần tử?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"spacing\",\"option_4\":\"border-spacing\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(888, 26, 'Thuộc tính nào xác định chiều rộng của một phần tử?', '{\"option_1\":\"width\",\"option_2\":\"height\",\"option_3\":\"size\",\"option_4\":\"max-width\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(889, 26, 'Thuộc tính nào để ẩn một phần tử nhưng vẫn giữ không gian của nó trong trang?', '{\"option_1\":\"visibility: hidden;\",\"option_2\":\"display: none;\",\"option_3\":\"opacity: 0;\",\"option_4\":\"z-index: -1;\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(890, 26, 'CSS float có chức năng gì?', '{\"option_1\":\"\\u0110\\u1eb7t ph\\u1ea7n t\\u1eed l\\u00ean \\u0111\\u1ea7u trang\",\"option_2\":\"\\u0110\\u1ecbnh v\\u1ecb ph\\u1ea7n t\\u1eed sang tr\\u00e1i ho\\u1eb7c ph\\u1ea3i\",\"option_3\":\"\\u1ea8n ph\\u1ea7n t\\u1eed\",\"option_4\":\"Th\\u00eam h\\u00ecnh \\u1ea3nh\",\"correct_option\":2}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(891, 26, 'Thuộc tính nào thay đổi cách phần tử tương tác với các phần tử xung quanh?', '{\"option_1\":\"position\",\"option_2\":\"display\",\"option_3\":\"align-items\",\"option_4\":\"flex\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(892, 26, 'Thuộc tính nào dùng để tạo lưới layout?', '{\"option_1\":\"grid-template-columns\",\"option_2\":\"flex-direction\",\"option_3\":\"float\",\"option_4\":\"clear\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(893, 26, 'Thuộc tính position: fixed; làm gì?', '{\"option_1\":\"\\u0110\\u1eb7t ph\\u1ea7n t\\u1eed \\u1edf v\\u1ecb tr\\u00ed c\\u1ed1 \\u0111\\u1ecbnh d\\u1ef1a tr\\u00ean viewport\",\"option_2\":\"\\u0110\\u1eb7t ph\\u1ea7n t\\u1eed c\\u1ed1 \\u0111\\u1ecbnh trong b\\u1ed1 c\\u1ee5c\",\"option_3\":\"\\u1ea8n ph\\u1ea7n t\\u1eed\",\"option_4\":\"G\\u1eafn ph\\u1ea7n t\\u1eed v\\u00e0o cu\\u1ed1i trang\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(894, 26, 'z-index chỉ áp dụng khi phần tử có thuộc tính gì?', '{\"option_1\":\"float\",\"option_2\":\"opacity\",\"option_3\":\"position\",\"option_4\":\"display\",\"correct_option\":3}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23');
INSERT INTO `quizs` (`id`, `subject_id`, `question`, `options`, `mark`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(895, 26, 'Cú pháp nào dùng để tạo lề trên và dưới là 10px, trái và phải là 20px?', '{\"option_1\":\"margin: 10px, 20px;\",\"option_2\":\"margin: 10px 20px;\",\"option_3\":\"margin: 10px 20px 10px 20px;\",\"option_4\":\"margin: 10-20px;\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(896, 26, 'Trong Box Model, thuộc tính nào xác định khoảng cách từ nội dung đến viền?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"spacing\",\"correct_option\":2}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(897, 26, 'Cú pháp nào dùng để áp dụng đường viền bo góc cho phần tử?', '{\"option_1\":\"border-radius: 10px;\",\"option_2\":\"border-curve: 10px;\",\"option_3\":\"corner-radius: 10px;\",\"option_4\":\"round-border: 10px;\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(898, 26, 'Thuộc tính nào của Box Model không bao gồm kích thước thực tế của phần tử?', '{\"option_1\":\"content\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"margin\",\"correct_option\":4}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(899, 26, 'Giá trị nào của thuộc tính position để phần tử di chuyển dựa trên viewport?', '{\"option_1\":\"absolute\",\"option_2\":\"relative\",\"option_3\":\"fixed\",\"option_4\":\"static\",\"correct_option\":3}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(900, 26, 'Thuộc tính box-sizing: border-box; có tác dụng gì?', '{\"option_1\":\"T\\u00ednh to\\u00e1n k\\u00edch th\\u01b0\\u1edbc ph\\u1ea7n t\\u1eed bao g\\u1ed3m c\\u1ea3 border v\\u00e0 padding\",\"option_2\":\"Lo\\u1ea1i tr\\u1eeb padding kh\\u1ecfi k\\u00edch th\\u01b0\\u1edbc ph\\u1ea7n t\\u1eed\",\"option_3\":\"T\\u00ednh to\\u00e1n ch\\u1ec9 k\\u00edch th\\u01b0\\u1edbc n\\u1ed9i dung ph\\u1ea7n t\\u1eed\",\"option_4\":\"Lo\\u1ea1i tr\\u1eeb margin kh\\u1ecfi k\\u00edch th\\u01b0\\u1edbc ph\\u1ea7n t\\u1eed\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(901, 26, 'Lựa chọn nào đúng để tạo một viền kép (double border) cho phần tử?', '{\"option_1\":\"border-style: double;\",\"option_2\":\"border-type: double;\",\"option_3\":\"border-shape: double;\",\"option_4\":\"border-thickness: double;\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(902, 26, 'Cú pháp nào để tạo khoảng cách đều cho tất cả các cạnh của một phần tử?', '{\"option_1\":\"margin: 10px;\",\"option_2\":\"padding: 10px;\",\"option_3\":\"border: 10px;\",\"option_4\":\"padding-margin: 10px;\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(903, 26, 'Lựa chọn nào đúng để thêm viền cho phần tử mà không ảnh hưởng đến kích thước của phần tử?', '{\"option_1\":\"box-sizing: border-box;\",\"option_2\":\"padding: border-box;\",\"option_3\":\"border-box: box;\",\"option_4\":\"box-model: border;\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(904, 26, 'Thuộc tính nào của Box Model để thay đổi khoảng cách giữa các phần tử xung quanh?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"spacing\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(905, 26, 'Giá trị nào của thuộc tính position đặt phần tử trong luồng bình thường của tài liệu?', '{\"option_1\":\"static\",\"option_2\":\"relative\",\"option_3\":\"absolute\",\"option_4\":\"fixed\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(906, 26, 'Cú pháp nào đúng để đặt chiều cao tối đa cho một phần tử?', '{\"option_1\":\"max-height: 500px;\",\"option_2\":\"min-height: 500px;\",\"option_3\":\"height: max(500px);\",\"option_4\":\"height-limit: 500px;\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(907, 26, 'Giá trị nào của thuộc tính position làm cho phần tử di chuyển theo vị trí của phần tử cha?', '{\"option_1\":\"relative\",\"option_2\":\"absolute\",\"option_3\":\"fixed\",\"option_4\":\"static\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(908, 26, 'Thuộc tính nào giúp điều chỉnh khoảng cách giữa nội dung của phần tử và đường viền của nó?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border-width\",\"option_4\":\"line-height\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(909, 26, 'Lựa chọn nào đúng để ẩn phần tử khỏi màn hình mà không thay đổi bố cục của trang?', '{\"option_1\":\"visibility: hidden;\",\"option_2\":\"display: none;\",\"option_3\":\"opacity: 0;\",\"option_4\":\"z-index: -1;\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(910, 26, 'Thuộc tính nào được dùng để thêm khoảng cách giữa các đường viền của phần tử và phần tử con của nó?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border-spacing\",\"option_4\":\"content-spacing\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(911, 26, 'Thuộc tính nào dùng để tạo hiệu ứng chuyển động?', '{\"option_1\":\"transition\",\"option_2\":\"animation\",\"option_3\":\"transform\",\"option_4\":\"keyframes\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(912, 26, 'Thuộc tính transform: scale(2) làm gì?', '{\"option_1\":\"Ph\\u00f3ng to ph\\u1ea7n t\\u1eed g\\u1ea5p \\u0111\\u00f4i\",\"option_2\":\"Thu nh\\u1ecf ph\\u1ea7n t\\u1eed c\\u00f2n m\\u1ed9t n\\u1eeda\",\"option_3\":\"Xoay ph\\u1ea7n t\\u1eed 180 \\u0111\\u1ed9\",\"option_4\":\"D\\u1ecbch chuy\\u1ec3n ph\\u1ea7n t\\u1eed\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(913, 26, 'Lựa chọn nào đúng để làm cho một phần tử biến mất dần?', '{\"option_1\":\"opacity: 1 to 0;\",\"option_2\":\"opacity: 0 to 1;\",\"option_3\":\"transition: opacity 2s ease;\",\"option_4\":\"transform: opacity 2s;\",\"correct_option\":3}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(914, 26, 'Cú pháp nào đúng để thêm shadow vào phần tử?', '{\"option_1\":\"box-shadow: 10px 10px 5px #888888;\",\"option_2\":\"shadow: 10px 10px 5px #888888;\",\"option_3\":\"text-shadow: 10px 10px 5px #888888;\",\"option_4\":\"filter: shadow(10px, 5px);\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(915, 26, 'Thuộc tính nào để căn chỉnh các phần tử theo trục chính trong Flexbox?', '{\"option_1\":\"align-items\",\"option_2\":\"justify-content\",\"option_3\":\"flex-direction\",\"option_4\":\"align-content\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(916, 26, 'Giá trị nào của thuộc tính flex-direction sẽ sắp xếp các mục từ trên xuống dưới?', '{\"option_1\":\"row\",\"option_2\":\"column\",\"option_3\":\"row-reverse\",\"option_4\":\"column-reverse\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(917, 26, 'Thuộc tính nào định nghĩa không gian giữa các phần tử trong Flexbox?', '{\"option_1\":\"gap\",\"option_2\":\"spacing\",\"option_3\":\"padding\",\"option_4\":\"margin\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(918, 26, 'Làm cách nào để chia đều các phần tử trong Flexbox?', '{\"option_1\":\"justify-content: space-around;\",\"option_2\":\"justify-content: center;\",\"option_3\":\"justify-content: space-between;\",\"option_4\":\"justify-content: flex-end;\",\"correct_option\":3}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(919, 26, 'Thuộc tính nào được dùng để thay đổi thứ tự các phần tử trong Flexbox?', '{\"option_1\":\"order\",\"option_2\":\"flex-grow\",\"option_3\":\"align-self\",\"option_4\":\"flex-basis\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(920, 26, 'Lựa chọn nào đúng để căn giữa một phần tử cả theo chiều ngang lẫn chiều dọc trong Flexbox?', '{\"option_1\":\"justify-content: center; align-items: center;\",\"option_2\":\"flex-align: middle;\",\"option_3\":\"align-content: center; align-items: center;\",\"option_4\":\"justify-items: middle; align-self: center;\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(921, 26, 'Thuộc tính flex-wrap: wrap; dùng để làm gì?', '{\"option_1\":\"Cho ph\\u00e9p c\\u00e1c ph\\u1ea7n t\\u1eed con xu\\u1ed1ng d\\u00f2ng khi c\\u1ea7n thi\\u1ebft\",\"option_2\":\"T\\u1ea1o kh\\u00f4ng gian gi\\u1eefa c\\u00e1c ph\\u1ea7n t\\u1eed\",\"option_3\":\"X\\u1ebfp c\\u00e1c ph\\u1ea7n t\\u1eed theo chi\\u1ec1u d\\u1ecdc\",\"option_4\":\"X\\u1ebfp c\\u00e1c ph\\u1ea7n t\\u1eed theo chi\\u1ec1u ngang\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(922, 26, 'Thuộc tính nào để căn chỉnh các phần tử con theo trục phụ trong Flexbox?', '{\"option_1\":\"align-items\",\"option_2\":\"justify-content\",\"option_3\":\"flex-wrap\",\"option_4\":\"align-content\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(923, 26, 'Thuộc tính nào xác định tỷ lệ phát triển của phần tử trong Flexbox?', '{\"option_1\":\"flex-shrink\",\"option_2\":\"flex-grow\",\"option_3\":\"flex-basis\",\"option_4\":\"order\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(924, 26, 'Trong Grid Layout, thuộc tính nào định nghĩa số cột?', '{\"option_1\":\"grid-template-columns\",\"option_2\":\"grid-template-rows\",\"option_3\":\"grid-auto-flow\",\"option_4\":\"grid-area\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(925, 26, 'Lựa chọn nào đúng để tạo bố cục Grid với 3 cột có kích thước bằng nhau?', '{\"option_1\":\"grid-template-columns: 1fr 1fr 1fr;\",\"option_2\":\"grid-template-columns: repeat(3, auto);\",\"option_3\":\"grid-template-columns: auto auto auto;\",\"option_4\":\"grid-template-columns: repeat(3, 1fr);\",\"correct_option\":4}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(926, 26, 'Thuộc tính nào dùng để căn lề các phần tử trong một Grid?', '{\"option_1\":\"grid-align\",\"option_2\":\"justify-items\",\"option_3\":\"align-self\",\"option_4\":\"align-content\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(927, 26, 'Thuộc tính nào dùng để đặt khoảng cách giữa các cột trong Grid?', '{\"option_1\":\"grid-gap\",\"option_2\":\"column-gap\",\"option_3\":\"row-gap\",\"option_4\":\"gap\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(928, 26, 'Cú pháp nào đúng để tạo một grid có 2 cột và 3 hàng?', '{\"option_1\":\"grid-template: repeat(2, 1fr) \\/ repeat(3, 1fr);\",\"option_2\":\"grid-template-rows: 2 \\/ grid-template-columns: 3;\",\"option_3\":\"grid-template-rows: repeat(3, 1fr); grid-template-columns: repeat(2, 1fr);\",\"option_4\":\"grid-area: 3 \\/ 2;\",\"correct_option\":3}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(929, 26, 'Giá trị nào của grid-auto-flow để xếp các phần tử tự động từ trái sang phải theo dòng?', '{\"option_1\":\"column\",\"option_2\":\"dense\",\"option_3\":\"row\",\"option_4\":\"grid-row\",\"correct_option\":3}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(930, 26, 'Thuộc tính nào dùng để căn chỉnh các mục trong Grid theo trục dọc?', '{\"option_1\":\"align-items\",\"option_2\":\"justify-content\",\"option_3\":\"grid-template\",\"option_4\":\"align-content\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(931, 26, 'Lựa chọn nào đúng để mở rộng một mục qua nhiều cột trong Grid?', '{\"option_1\":\"grid-column: span 2;\",\"option_2\":\"grid-row: span 2;\",\"option_3\":\"grid-area: 1 \\/ span 2;\",\"option_4\":\"column-span: 2;\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(932, 26, 'Cú pháp nào để tạo khoảng cách giữa các hàng và cột trong Grid?', '{\"option_1\":\"gap: 10px;\",\"option_2\":\"grid-gap: 10px;\",\"option_3\":\"row-gap: 10px; column-gap: 10px;\",\"option_4\":\"margin: 10px;\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(933, 26, 'Thuộc tính nào đặt kích thước tối thiểu của cột trong Grid?', '{\"option_1\":\"min-width\",\"option_2\":\"grid-auto-columns\",\"option_3\":\"minmax()\",\"option_4\":\"grid-template-columns\",\"correct_option\":3}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(934, 26, 'Lựa chọn nào để đặt phần tử thứ 2 của Grid vào hàng thứ 3?', '{\"option_1\":\"grid-row: 3;\",\"option_2\":\"grid-column: 3;\",\"option_3\":\"grid-template-rows: 2 \\/ 3;\",\"option_4\":\"row-start: 3;\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(935, 26, 'Thuộc tính nào xác định độ rộng tối đa trong media queries?', '{\"option_1\":\"max-width\",\"option_2\":\"min-width\",\"option_3\":\"width\",\"option_4\":\"media-width\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(936, 26, 'Media query nào dùng để áp dụng style khi kích thước màn hình nhỏ hơn 768px?', '{\"option_1\":\"@media only screen and (min-width: 768px)\",\"option_2\":\"@media only screen and (max-width: 768px)\",\"option_3\":\"@media screen (max-width: 768px)\",\"option_4\":\"@media (screen-width < 768px)\",\"correct_option\":2}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(937, 26, 'Lựa chọn nào đúng để thay đổi nền của trang trên thiết bị có màn hình nhỏ hơn 600px?', '{\"option_1\":\"@media screen and (max-width: 600px) { body { background-color: blue; } }\",\"option_2\":\"@media (max-device-width: 600px) { body { background-color: blue; } }\",\"option_3\":\"@media only (max-width: 600px) { body { background: blue; } }\",\"option_4\":\"@media (max-width: 600px) { body { background-color: blue; } }\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(938, 26, 'Giá trị nào của thuộc tính orientation trong media queries dùng để xác định màn hình đang ở chế độ ngang?', '{\"option_1\":\"portrait\",\"option_2\":\"landscape\",\"option_3\":\"horizontal\",\"option_4\":\"vertical\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(939, 26, 'Cú pháp nào đúng để thay đổi font-size khi kích thước màn hình nhỏ hơn 500px?', '{\"option_1\":\"@media screen and (max-width: 500px) { body { font-size: 12px; } }\",\"option_2\":\"@media (min-width: 500px) { body { font-size: 12px; } }\",\"option_3\":\"@media (screen-width < 500px) { body { font-size: 12px; } }\",\"option_4\":\"@media screen (max-width: 500px) { body { font-size: 12px; } }\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(940, 26, 'Media query nào đúng để áp dụng style trên các thiết bị di động?', '{\"option_1\":\"@media only screen and (max-device-width: 480px)\",\"option_2\":\"@media screen and (min-width: 480px)\",\"option_3\":\"@media screen (max-device: 480px)\",\"option_4\":\"@media (device-width: 480px)\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(941, 26, 'Cú pháp nào đúng để ẩn một phần tử trên thiết bị có màn hình lớn hơn 1024px?', '{\"option_1\":\"@media screen and (min-width: 1024px) { element { display: none; } }\",\"option_2\":\"@media screen and (max-width: 1024px) { element { display: none; } }\",\"option_3\":\"@media (min-device-width: 1024px) { element { visibility: hidden; } }\",\"option_4\":\"@media screen (max-width: 1024px) { element { display: hidden; } }\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(942, 26, 'Thuộc tính nào trong media query xác định thiết bị là màn hình cảm ứng?', '{\"option_1\":\"touch-screen\",\"option_2\":\"hover\",\"option_3\":\"pointer\",\"option_4\":\"any-pointer\",\"correct_option\":3}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(943, 26, 'Lựa chọn nào để áp dụng style cho các thiết bị có độ phân giải cao (retina)?', '{\"option_1\":\"@media only screen and (min-resolution: 2dppx)\",\"option_2\":\"@media only screen and (-webkit-min-device-pixel-ratio: 2)\",\"option_3\":\"@media (max-resolution: 300dpi)\",\"option_4\":\"@media (min-device-pixel-ratio: 1.5)\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(944, 26, 'Giá trị nào của max-width thường dùng để xác định một breakpoint cho máy tính bảng?', '{\"option_1\":\"768px\",\"option_2\":\"1024px\",\"option_3\":\"1200px\",\"option_4\":\"640px\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(945, 26, 'Cú pháp nào đúng để làm cho trang web có thể thích ứng trên mọi thiết bị?', '{\"option_1\":\"meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\"\",\"option_2\":\"meta name=\\\"device\\\" content=\\\"width=auto, scale=1\\\"\",\"option_3\":\"meta name=\\\"media\\\" content=\\\"device-width, scale=1\\\"\",\"option_4\":\"meta name=\\\"screen\\\" content=\\\"width=auto, initial-scale=1\\\"\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(946, 26, 'Lựa chọn nào để thay đổi kiểu chữ khi thiết bị chuyển sang chế độ nằm ngang?', '{\"option_1\":\"@media only screen and (orientation: landscape) { body { font-size: 18px; } }\",\"option_2\":\"@media only screen and (orientation: portrait) { body { font-size: 18px; } }\",\"option_3\":\"@media screen and (device-orientation: landscape) { body { font-size: 18px; } }\",\"option_4\":\"@media (screen-orientation: horizontal) { body { font-size: 18px; } }\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(947, 26, 'Media query nào để áp dụng style cho cả điện thoại và máy tính bảng?', '{\"option_1\":\"@media screen and (max-width: 1024px)\",\"option_2\":\"@media screen and (min-width: 1024px)\",\"option_3\":\"@media screen and (device-width: 1024px)\",\"option_4\":\"@media (min-device-width: 1024px)\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(948, 26, 'Thuộc tính nào của media query để chỉ định tỷ lệ màn hình?', '{\"option_1\":\"aspect-ratio\",\"option_2\":\"resolution\",\"option_3\":\"device-ratio\",\"option_4\":\"screen-ratio\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(949, 26, 'Media query nào chỉ định kiểu màn hình không phải là in ấn?', '{\"option_1\":\"@media screen\",\"option_2\":\"@media print\",\"option_3\":\"@media handheld\",\"option_4\":\"@media all\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(950, 26, 'Thuộc tính nào để tạo hiệu ứng chuyển tiếp cho các thuộc tính CSS?', '{\"option_1\":\"animation\",\"option_2\":\"transition\",\"option_3\":\"transform\",\"option_4\":\"keyframes\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(951, 26, 'Cú pháp nào để thêm hiệu ứng chuyển động vào một phần tử?', '{\"option_1\":\"animation: move 2s infinite;\",\"option_2\":\"transition: move 2s infinite;\",\"option_3\":\"keyframes: move 2s infinite;\",\"option_4\":\"transform: move 2s infinite;\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(952, 26, 'Thuộc tính nào để kiểm soát thời gian chuyển đổi giữa các trạng thái trong CSS?', '{\"option_1\":\"delay\",\"option_2\":\"duration\",\"option_3\":\"transition-duration\",\"option_4\":\"animation-duration\",\"correct_option\":3}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(953, 26, 'Cú pháp nào để tạo hiệu ứng làm mờ dần một phần tử?', '{\"option_1\":\"animation: fade 2s ease-in;\",\"option_2\":\"transition: opacity 2s ease;\",\"option_3\":\"keyframes: fade 2s linear;\",\"option_4\":\"transform: opacity 2s ease;\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(954, 26, 'Lựa chọn nào dùng để định nghĩa các khung hình của một animation trong CSS?', '{\"option_1\":\"@frames\",\"option_2\":\"@keyframes\",\"option_3\":\"@animations\",\"option_4\":\"@stages\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(955, 26, 'Thuộc tính transform: rotate(45deg); làm gì?', '{\"option_1\":\"D\\u1ecbch chuy\\u1ec3n ph\\u1ea7n t\\u1eed sang tr\\u00e1i 45 \\u0111\\u1ed9\",\"option_2\":\"Xoay ph\\u1ea7n t\\u1eed 45 \\u0111\\u1ed9 theo chi\\u1ec1u kim \\u0111\\u1ed3ng h\\u1ed3\",\"option_3\":\"Thu nh\\u1ecf ph\\u1ea7n t\\u1eed c\\u00f2n 45%\",\"option_4\":\"K\\u00e9o d\\u00e0i ph\\u1ea7n t\\u1eed theo g\\u00f3c 45 \\u0111\\u1ed9\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(956, 26, 'Thuộc tính nào xác định thời gian bắt đầu của một animation?', '{\"option_1\":\"animation-delay\",\"option_2\":\"transition-delay\",\"option_3\":\"transform-delay\",\"option_4\":\"keyframe-delay\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(957, 26, 'Cú pháp nào để làm cho một animation chạy mãi mãi?', '{\"option_1\":\"animation-iteration-count: infinite;\",\"option_2\":\"animation-iteration: always;\",\"option_3\":\"transition-repeat: infinite;\",\"option_4\":\"keyframe-iteration-count: infinite;\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(958, 26, 'Thuộc tính nào để làm cho animation quay ngược khi kết thúc?', '{\"option_1\":\"animation-direction: reverse;\",\"option_2\":\"animation-direction: alternate;\",\"option_3\":\"animation-play-state: reverse;\",\"option_4\":\"animation-iteration-count: backwards;\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(959, 26, 'Thuộc tính nào dùng để làm cho một phần tử thay đổi kích thước trong CSS?', '{\"option_1\":\"transform: scale(1.5);\",\"option_2\":\"transition: size(1.5);\",\"option_3\":\"animation: scale(1.5);\",\"option_4\":\"keyframe: size(1.5);\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(960, 26, 'Lựa chọn nào để làm cho animation bắt đầu từ trạng thái cuối cùng khi hoàn thành?', '{\"option_1\":\"animation-fill-mode: forwards;\",\"option_2\":\"animation-direction: alternate;\",\"option_3\":\"transition-fill-mode: end;\",\"option_4\":\"keyframe-fill-mode: final;\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(961, 26, 'Thuộc tính nào để thêm bóng vào văn bản?', '{\"option_1\":\"text-shadow\",\"option_2\":\"box-shadow\",\"option_3\":\"shadow\",\"option_4\":\"filter\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(962, 26, 'Thuộc tính nào để tạo hiệu ứng phóng to hoặc thu nhỏ văn bản khi hover chuột?', '{\"option_1\":\"transform: scale();\",\"option_2\":\"transition: zoom();\",\"option_3\":\"animation: zoom();\",\"option_4\":\"hover-effect: scale();\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(963, 26, 'Thuộc tính nào để dịch chuyển phần tử theo trục Y trong CSS?', '{\"option_1\":\"transform: translateY();\",\"option_2\":\"transform: moveY();\",\"option_3\":\"transition: translateY();\",\"option_4\":\"animation: moveY();\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(964, 26, 'Lựa chọn nào để làm cho một phần tử mờ dần ra khỏi màn hình?', '{\"option_1\":\"opacity: 0;\",\"option_2\":\"display: none;\",\"option_3\":\"visibility: hidden;\",\"option_4\":\"fade: out;\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(965, 26, 'Thuộc tính nào để xác định điểm bắt đầu và kết thúc của một animation?', '{\"option_1\":\"@start-end\",\"option_2\":\"@frames\",\"option_3\":\"@keyframes\",\"option_4\":\"@animate\",\"correct_option\":3}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(966, 26, 'Lựa chọn nào để tạo hiệu ứng chuyển màu nền trong 3 giây?', '{\"option_1\":\"transition: background-color 3s;\",\"option_2\":\"animation: background-color 3s;\",\"option_3\":\"keyframes: background 3s;\",\"option_4\":\"transform: background-color 3s;\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(967, 26, 'Thuộc tính nào để tạo độ cong cho góc của phần tử?', '{\"option_1\":\"border-radius\",\"option_2\":\"corner-radius\",\"option_3\":\"radius\",\"option_4\":\"border-corner\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(968, 26, 'Lựa chọn nào đúng để tạo hiệu ứng mờ dần khi một phần tử xuất hiện?', '{\"option_1\":\"opacity: 0 to 1;\",\"option_2\":\"transition: opacity 2s ease;\",\"option_3\":\"animation: fade-in 2s ease-in-out;\",\"option_4\":\"keyframes: fade(0 to 1);\",\"correct_option\":3}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(969, 26, 'Thuộc tính transform: skew(20deg, 10deg); làm gì?', '{\"option_1\":\"L\\u00e0m nghi\\u00eang ph\\u1ea7n t\\u1eed theo hai g\\u00f3c kh\\u00e1c nhau\",\"option_2\":\"Xoay ph\\u1ea7n t\\u1eed 20 \\u0111\\u1ed9\",\"option_3\":\"Thu nh\\u1ecf ph\\u1ea7n t\\u1eed theo t\\u1ec9 l\\u1ec7 20:10\",\"option_4\":\"Ph\\u00f3ng to ph\\u1ea7n t\\u1eed theo t\\u1ec9 l\\u1ec7 20:10\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(970, 26, 'Thuộc tính transform: translateX(100px); làm gì?', '{\"option_1\":\"D\\u1ecbch chuy\\u1ec3n ph\\u1ea7n t\\u1eed theo tr\\u1ee5c X th\\u00eam 100px\",\"option_2\":\"Ph\\u00f3ng to ph\\u1ea7n t\\u1eed 100px theo tr\\u1ee5c X\",\"option_3\":\"Xoay ph\\u1ea7n t\\u1eed 100px\",\"option_4\":\"L\\u00e0m bi\\u1ebfn d\\u1ea1ng ph\\u1ea7n t\\u1eed\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(971, 26, 'Thuộc tính nào để kiểm soát thời gian một phần tử duy trì trạng thái trước khi chuyển đổi?', '{\"option_1\":\"transition-delay\",\"option_2\":\"animation-delay\",\"option_3\":\"keyframe-delay\",\"option_4\":\"delay-timing\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(972, 26, 'Lựa chọn nào đúng để làm cho phần tử trượt từ bên trái vào màn hình?', '{\"option_1\":\"animation: slide-in-left 2s;\",\"option_2\":\"transition: slide-in 2s;\",\"option_3\":\"transform: translateX(0);\",\"option_4\":\"keyframes: slide-left 2s;\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(973, 26, 'Thuộc tính nào để thay đổi độ trong suốt của phần tử từ 1 đến 0 khi hover?', '{\"option_1\":\"opacity\",\"option_2\":\"visibility\",\"option_3\":\"filter\",\"option_4\":\"transparency\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(974, 26, 'Thuộc tính transform: rotate(90deg); làm gì?', '{\"option_1\":\"Xoay ph\\u1ea7n t\\u1eed 90 \\u0111\\u1ed9\",\"option_2\":\"Thu nh\\u1ecf ph\\u1ea7n t\\u1eed c\\u00f2n 90%\",\"option_3\":\"D\\u1ecbch chuy\\u1ec3n ph\\u1ea7n t\\u1eed 90px\",\"option_4\":\"T\\u1ea1o b\\u00f3ng cho ph\\u1ea7n t\\u1eed\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(975, 26, 'Thuộc tính nào dùng để ẩn phần tử khỏi trang nhưng không ảnh hưởng đến bố cục?', '{\"option_1\":\"visibility: hidden;\",\"option_2\":\"display: none;\",\"option_3\":\"opacity: 0;\",\"option_4\":\"position: absolute;\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(976, 26, 'Lựa chọn nào để làm cho một phần tử hiển thị lại từ vị trí ban đầu khi animation kết thúc?', '{\"option_1\":\"animation-fill-mode: backwards;\",\"option_2\":\"animation-fill-mode: none;\",\"option_3\":\"animation-fill-mode: forwards;\",\"option_4\":\"animation-fill-mode: reset;\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(977, 26, 'Giá trị nào của thuộc tính display làm phần tử trở thành khối (block-level)?', '{\"option_1\":\"block\",\"option_2\":\"inline-block\",\"option_3\":\"inline\",\"option_4\":\"flex\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(978, 26, 'Giá trị nào của thuộc tính position làm cho một phần tử di chuyển theo vị trí tuyệt đối (absolute)?', '{\"option_1\":\"absolute\",\"option_2\":\"relative\",\"option_3\":\"fixed\",\"option_4\":\"static\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(979, 26, 'Thuộc tính nào dùng để xóa bỏ hiệu ứng gợn sóng của outline khi click vào phần tử?', '{\"option_1\":\"outline: none;\",\"option_2\":\"border: none;\",\"option_3\":\"outline-width: 0px;\",\"option_4\":\"border-color: transparent;\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(980, 26, 'Lựa chọn nào dùng để căn giữa một phần tử theo chiều ngang trong bố cục Flexbox?', '{\"option_1\":\"justify-content: center;\",\"option_2\":\"align-items: center;\",\"option_3\":\"align-content: center;\",\"option_4\":\"flex-direction: center;\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(981, 26, 'Thuộc tính nào để điều khiển mức độ hiển thị của bóng đổ?', '{\"option_1\":\"box-shadow\",\"option_2\":\"text-shadow\",\"option_3\":\"opacity\",\"option_4\":\"visibility\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(982, 26, 'Giá trị nào của thuộc tính position sẽ không làm phần tử bị dịch chuyển khi trang cuộn?', '{\"option_1\":\"fixed\",\"option_2\":\"relative\",\"option_3\":\"absolute\",\"option_4\":\"sticky\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(983, 26, 'Thuộc tính nào kiểm soát các mục trong dòng chảy của flexbox khi không có đủ không gian?', '{\"option_1\":\"flex-wrap\",\"option_2\":\"flex-flow\",\"option_3\":\"justify-items\",\"option_4\":\"align-self\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(984, 26, 'Thuộc tính nào để điều chỉnh độ rộng của phần tử dựa trên nội dung?', '{\"option_1\":\"width: auto;\",\"option_2\":\"min-width: 100%;\",\"option_3\":\"width: content;\",\"option_4\":\"max-width: none;\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(985, 26, 'Lựa chọn nào để căn giữa một phần tử cả theo chiều ngang lẫn chiều dọc trong Grid Layout?', '{\"option_1\":\"justify-content: center; align-items: center;\",\"option_2\":\"align-content: middle;\",\"option_3\":\"grid-align: center;\",\"option_4\":\"center-content: true;\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(986, 26, 'Thuộc tính nào dùng để định nghĩa nền trong suốt trong CSS?', '{\"option_1\":\"background-color: transparent;\",\"option_2\":\"opacity: 0;\",\"option_3\":\"visibility: hidden;\",\"option_4\":\"background-opacity: 0;\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(987, 26, 'Thuộc tính float trong CSS có tác dụng gì?', '{\"option_1\":\"Cho ph\\u00e9p ph\\u1ea7n t\\u1eed \\\"tr\\u00f4i\\\" sang tr\\u00e1i ho\\u1eb7c ph\\u1ea3i\",\"option_2\":\"\\u0110\\u1eb7t ph\\u1ea7n t\\u1eed l\\u00ean \\u0111\\u1ea7u trang\",\"option_3\":\"X\\u00f3a ph\\u1ea7n t\\u1eed kh\\u1ecfi lu\\u1ed3ng b\\u00ecnh th\\u01b0\\u1eddng\",\"option_4\":\"T\\u1ea1o kh\\u00f4ng gian gi\\u1eefa c\\u00e1c ph\\u1ea7n t\\u1eed\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(988, 26, 'Thuộc tính nào xác định chiều cao của dòng văn bản?', '{\"option_1\":\"line-height\",\"option_2\":\"font-height\",\"option_3\":\"height\",\"option_4\":\"text-size\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(989, 26, 'Cú pháp nào đúng để tạo viền cho phần tử?', '{\"option_1\":\"border: 1px solid black;\",\"option_2\":\"border: 1 solid black;\",\"option_3\":\"border: 1px black solid;\",\"option_4\":\"border: solid 1px black;\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(990, 26, 'Thuộc tính list-style-type được dùng để thay đổi gì?', '{\"option_1\":\"Ki\\u1ec3u d\\u1ea5u ch\\u1ea5m \\u0111\\u1ea7u d\\u00f2ng c\\u1ee7a danh s\\u00e1ch\",\"option_2\":\"Ki\\u1ec3u c\\u1ee7a ti\\u00eau \\u0111\\u1ec1\",\"option_3\":\"Ki\\u1ec3u \\u0111\\u1ecbnh d\\u1ea1ng li\\u00ean k\\u1ebft\",\"option_4\":\"Ki\\u1ec3u n\\u1ec1n c\\u1ee7a danh s\\u00e1ch\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(991, 26, 'Thuộc tính nào để điều chỉnh độ rộng biên của phần tử?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border-width\",\"option_4\":\"outline-width\",\"correct_option\":3}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(992, 26, 'Thuộc tính nào kiểm soát hướng dòng trong bố cục Flexbox?', '{\"option_1\":\"flex-direction\",\"option_2\":\"justify-content\",\"option_3\":\"flex-wrap\",\"option_4\":\"flex-flow\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(993, 26, 'Giá trị nào của thuộc tính visibility sẽ ẩn phần tử nhưng vẫn giữ không gian cho nó?', '{\"option_1\":\"hidden\",\"option_2\":\"collapse\",\"option_3\":\"none\",\"option_4\":\"visible\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(994, 26, 'Thuộc tính nào dùng để căn chỉnh văn bản theo chiều dọc trong một ô của bảng?', '{\"option_1\":\"vertical-align\",\"option_2\":\"text-align\",\"option_3\":\"line-height\",\"option_4\":\"align-self\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(995, 26, 'Thuộc tính nào để xóa bỏ khoảng trắng giữa các phần tử trong Flexbox?', '{\"option_1\":\"justify-content: space-between;\",\"option_2\":\"gap: 0;\",\"option_3\":\"margin: 0;\",\"option_4\":\"align-content: space-between;\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(996, 26, 'Thuộc tính nào xác định ảnh nền có lặp lại hay không?', '{\"option_1\":\"background-repeat\",\"option_2\":\"background-attachment\",\"option_3\":\"background-position\",\"option_4\":\"background-size\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(997, 26, 'Giá trị nào của thuộc tính display làm cho phần tử hiển thị như một hàng (inline)?', '{\"option_1\":\"inline\",\"option_2\":\"block\",\"option_3\":\"flex\",\"option_4\":\"grid\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(998, 26, 'Thuộc tính nào dùng để làm một phần tử cố định tại một vị trí trong suốt quá trình cuộn trang?', '{\"option_1\":\"position: fixed;\",\"option_2\":\"position: absolute;\",\"option_3\":\"position: sticky;\",\"option_4\":\"position: relative;\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(999, 26, 'Giá trị nào của thuộc tính background-size làm cho ảnh nền bao phủ toàn bộ phần tử?', '{\"option_1\":\"cover\",\"option_2\":\"contain\",\"option_3\":\"auto\",\"option_4\":\"fill\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1000, 26, 'Thuộc tính nào xác định độ mờ của phần tử?', '{\"option_1\":\"opacity\",\"option_2\":\"visibility\",\"option_3\":\"display\",\"option_4\":\"transparency\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1001, 26, 'Giá trị nào của display ẩn phần tử nhưng vẫn giữ không gian cho nó?', '{\"option_1\":\"none\",\"option_2\":\"block\",\"option_3\":\"visibility: hidden;\",\"option_4\":\"inline-block\",\"correct_option\":3}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1002, 26, 'Thuộc tính nào điều chỉnh độ lớn của phần tử dựa trên nội dung bên trong nó?', '{\"option_1\":\"flex-grow\",\"option_2\":\"flex-basis\",\"option_3\":\"flex-shrink\",\"option_4\":\"flex-wrap\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1003, 26, 'Thuộc tính nào dùng để thay đổi phông chữ trên toàn bộ trang?', '{\"option_1\":\"font-family\",\"option_2\":\"font-size\",\"option_3\":\"font-weight\",\"option_4\":\"font-style\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1004, 26, 'Thuộc tính nào điều khiển sự chồng lấp của các phần tử?', '{\"option_1\":\"z-index\",\"option_2\":\"stack-index\",\"option_3\":\"index\",\"option_4\":\"layer-index\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1005, 26, 'Thuộc tính nào điều chỉnh khoảng cách giữa các chữ cái?', '{\"option_1\":\"letter-spacing\",\"option_2\":\"text-spacing\",\"option_3\":\"word-spacing\",\"option_4\":\"character-spacing\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1006, 26, 'Giá trị nào của position làm phần tử di chuyển theo vị trí tương đối so với chính nó?', '{\"option_1\":\"relative\",\"option_2\":\"absolute\",\"option_3\":\"fixed\",\"option_4\":\"sticky\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1007, 26, 'Lựa chọn nào đúng để làm cho một phần tử di chuyển khi cuộn đến phần đó?', '{\"option_1\":\"scroll-behavior: smooth;\",\"option_2\":\"scroll-action: auto;\",\"option_3\":\"transition: scroll;\",\"option_4\":\"animation: scroll;\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1008, 26, 'Giá trị nào của thuộc tính flex-direction sắp xếp các phần tử từ phải sang trái?', '{\"option_1\":\"row-reverse\",\"option_2\":\"column\",\"option_3\":\"row\",\"option_4\":\"column-reverse\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1009, 26, 'Thuộc tính nào dùng để thay đổi khoảng cách giữa các dòng văn bản?', '{\"option_1\":\"line-height\",\"option_2\":\"letter-spacing\",\"option_3\":\"text-align\",\"option_4\":\"vertical-align\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1010, 26, 'Thuộc tính outline khác gì với border?', '{\"option_1\":\"Outline kh\\u00f4ng chi\\u1ebfm kh\\u00f4ng gian trong b\\u1ed1 c\\u1ee5c\",\"option_2\":\"Border c\\u00f3 th\\u1ec3 c\\u00f3 m\\u00e0u s\\u1eafc\",\"option_3\":\"Outline c\\u00f3 th\\u1ec3 c\\u00f3 g\\u00f3c bo tr\\u00f2n\",\"option_4\":\"Border kh\\u00f4ng th\\u1ec3 b\\u1ecb x\\u00f3a b\\u1ecf\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1011, 26, 'Lựa chọn nào đúng để căn chỉnh hình ảnh trong phần tử?', '{\"option_1\":\"object-fit\",\"option_2\":\"img-align\",\"option_3\":\"image-align\",\"option_4\":\"image-fit\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1012, 26, 'Thuộc tính box-sizing: border-box; làm gì?', '{\"option_1\":\"Bao g\\u1ed3m padding v\\u00e0 border trong chi\\u1ec1u r\\u1ed9ng v\\u00e0 chi\\u1ec1u cao c\\u1ee7a ph\\u1ea7n t\\u1eed\",\"option_2\":\"Lo\\u1ea1i b\\u1ecf padding v\\u00e0 margin kh\\u1ecfi t\\u00ednh to\\u00e1n k\\u00edch th\\u01b0\\u1edbc\",\"option_3\":\"Th\\u00eam kh\\u00f4ng gian cho c\\u00e1c ph\\u1ea7n t\\u1eed b\\u00ean trong\",\"option_4\":\"\\u0110i\\u1ec1u ch\\u1ec9nh k\\u00edch th\\u01b0\\u1edbc ph\\u1ea7n t\\u1eed d\\u1ef1a tr\\u00ean n\\u1ed9i dung\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1013, 26, 'Thuộc tính nào kiểm soát việc phần tử tự động điều chỉnh kích thước để phù hợp với màn hình?', '{\"option_1\":\"width: auto;\",\"option_2\":\"max-width: 100%;\",\"option_3\":\"width: fit-content;\",\"option_4\":\"flex-wrap: nowrap;\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1014, 26, 'Lựa chọn nào để tạo hiệu ứng cho phần tử khi nó xuất hiện trong khung nhìn (viewport)?', '{\"option_1\":\"Intersection Observer API\",\"option_2\":\"@media queries\",\"option_3\":\"keyframes\",\"option_4\":\"transition\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1015, 26, 'Thuộc tính nào để làm một hình ảnh bao phủ toàn bộ phần tử mà không bị kéo dãn?', '{\"option_1\":\"background-size: cover;\",\"option_2\":\"object-fit: cover;\",\"option_3\":\"img-fit: contain;\",\"option_4\":\"width: 100%;\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1016, 26, 'Thuộc tính nào để tạo khoảng cách giữa các từ trong văn bản?', '{\"option_1\":\"word-spacing\",\"option_2\":\"letter-spacing\",\"option_3\":\"text-align\",\"option_4\":\"line-height\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1017, 26, 'Giá trị nào của background-attachment làm ảnh nền cố định khi cuộn trang?', '{\"option_1\":\"fixed\",\"option_2\":\"scroll\",\"option_3\":\"local\",\"option_4\":\"inherit\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1018, 26, 'Lựa chọn nào để làm một phần tử tràn ra ngoài khung nhìn của cha nó?', '{\"option_1\":\"overflow: visible;\",\"option_2\":\"overflow: hidden;\",\"option_3\":\"overflow: scroll;\",\"option_4\":\"overflow: auto;\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1019, 26, 'Giá trị nào của thuộc tính flex-grow làm cho phần tử phát triển đầy đủ không gian còn lại?', '{\"option_1\":\"1\",\"option_2\":\"0\",\"option_3\":\"2\",\"option_4\":\"auto\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1020, 26, 'Thuộc tính nào để làm cho một phần tử chỉ xuất hiện trong một khung nhìn cụ thể?', '{\"option_1\":\"clip-path\",\"option_2\":\"visibility\",\"option_3\":\"opacity\",\"option_4\":\"z-index\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1021, 26, 'Giá trị nào của thuộc tính white-space làm văn bản hiển thị trong một dòng duy nhất, ngay cả khi nó vượt quá kích thước phần tử?', '{\"option_1\":\"nowrap\",\"option_2\":\"pre\",\"option_3\":\"break-all\",\"option_4\":\"normal\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1022, 26, 'Thuộc tính nào để kiểm soát cách văn bản hiển thị khi vượt quá chiều dài phần tử?', '{\"option_1\":\"text-overflow\",\"option_2\":\"overflow-wrap\",\"option_3\":\"word-break\",\"option_4\":\"text-align\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1023, 26, 'Thuộc tính nào làm cho phần tử không bị ảnh hưởng bởi những phần tử khác khi hover?', '{\"option_1\":\"pointer-events: none;\",\"option_2\":\"cursor: none;\",\"option_3\":\"hover-effect: none;\",\"option_4\":\"event-listeners: off;\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1024, 26, 'Giá trị nào của thuộc tính justify-content sẽ phân phối đều các phần tử với khoảng cách bằng nhau và một phần tử ở đầu và cuối?', '{\"option_1\":\"space-between\",\"option_2\":\"space-around\",\"option_3\":\"space-evenly\",\"option_4\":\"start-end\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1025, 26, 'Thuộc tính nào giúp tạo ra không gian giữa nội dung phần tử và viền ngoài của nó?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border-spacing\",\"option_4\":\"outline-width\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1026, 26, 'Thuộc tính visibility: collapse; có tác dụng gì đối với các hàng trong bảng?', '{\"option_1\":\"\\u1ea8n m\\u1ed9t h\\u00e0ng nh\\u01b0ng v\\u1eabn gi\\u1eef kh\\u00f4ng gian\",\"option_2\":\"X\\u00f3a b\\u1ecf m\\u1ed9t h\\u00e0ng kh\\u1ecfi b\\u1ea3ng v\\u00e0 b\\u1ecf kh\\u00f4ng gian\",\"option_3\":\"Thu nh\\u1ecf h\\u00e0ng nh\\u01b0ng v\\u1eabn gi\\u1eef n\\u1ed9i dung\",\"option_4\":\"Gi\\u1eef h\\u00e0ng nh\\u01b0ng \\u1ea9n v\\u0103n b\\u1ea3n\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1027, 26, 'Thuộc tính nào để căn chỉnh các mục trong Flexbox dọc theo trục phụ?', '{\"option_1\":\"align-items\",\"option_2\":\"justify-content\",\"option_3\":\"flex-grow\",\"option_4\":\"align-self\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1028, 26, 'Thuộc tính nào để tạo khoảng cách giữa phần tử con và viền trong Grid Layout?', '{\"option_1\":\"grid-gap\",\"option_2\":\"padding\",\"option_3\":\"margin\",\"option_4\":\"border-spacing\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1029, 26, 'Lựa chọn nào để làm một phần tử xuất hiện trên phần tử khác mà không thay đổi bố cục?', '{\"option_1\":\"z-index: 10;\",\"option_2\":\"position: relative;\",\"option_3\":\"display: inline-block;\",\"option_4\":\"float: right;\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1030, 26, 'Thuộc tính nào để làm cho một hình ảnh tự động điều chỉnh theo tỷ lệ của phần tử chứa?', '{\"option_1\":\"object-fit: contain;\",\"option_2\":\"img-size: contain;\",\"option_3\":\"background-size: cover;\",\"option_4\":\"width: auto;\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1031, 26, 'Lựa chọn nào để thay đổi độ rộng của một phần tử khi kích thước cửa sổ trình duyệt thay đổi?', '{\"option_1\":\"width: 100%;\",\"option_2\":\"width: auto;\",\"option_3\":\"max-width: 100%;\",\"option_4\":\"flex-grow: 1;\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1032, 26, 'Thuộc tính nào dùng để kiểm soát kiểu đầu dòng trong danh sách?', '{\"option_1\":\"list-style\",\"option_2\":\"bullet-type\",\"option_3\":\"marker-type\",\"option_4\":\"text-indent\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1033, 26, 'Thuộc tính nào để tạo khoảng cách giữa các phần tử trong bố cục Flexbox?', '{\"option_1\":\"gap\",\"option_2\":\"margin\",\"option_3\":\"padding\",\"option_4\":\"align-content\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1034, 26, 'Thuộc tính nào để điều chỉnh tốc độ của một animation?', '{\"option_1\":\"animation-timing-function\",\"option_2\":\"animation-speed\",\"option_3\":\"transition-speed\",\"option_4\":\"keyframe-timing\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1035, 26, 'Thuộc tính nào để hiển thị nội dung quá khổ bên trong phần tử dưới dạng dấu chấm lửng (ellipsis)?', '{\"option_1\":\"text-overflow: ellipsis;\",\"option_2\":\"overflow: ellipsis;\",\"option_3\":\"text-wrap: ellipsis;\",\"option_4\":\"word-overflow: ellipsis;\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1036, 26, 'Lựa chọn nào đúng để căn giữa một phần tử theo chiều dọc trong bố cục Grid Layout?', '{\"option_1\":\"align-items: center;\",\"option_2\":\"justify-content: center;\",\"option_3\":\"grid-align: center;\",\"option_4\":\"align-content: middle;\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1037, 26, 'Giá trị nào của thuộc tính font-weight làm văn bản hiển thị đậm hơn?', '{\"option_1\":\"bold\",\"option_2\":\"100\",\"option_3\":\"normal\",\"option_4\":\"light\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1038, 26, 'Thuộc tính nào để tạo khoảng cách giữa các cột trong bố cục Grid Layout?', '{\"option_1\":\"column-gap\",\"option_2\":\"grid-gap\",\"option_3\":\"grid-column\",\"option_4\":\"padding\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1039, 27, 'JavaScript là một loại ngôn ngữ gì?', '{\"option_1\":\"L\\u1eadp tr\\u00ecnh song song\",\"option_2\":\"L\\u1eadp tr\\u00ecnh h\\u00e0m\",\"option_3\":\"L\\u1eadp tr\\u00ecnh h\\u01b0\\u1edbng \\u0111\\u1ed1i t\\u01b0\\u1ee3ng\",\"option_4\":\"C\\u1ea3 ba lo\\u1ea1i tr\\u00ean\",\"correct_option\":4}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1040, 27, 'Cú pháp nào để khai báo một biến trong JavaScript?', '{\"option_1\":\"int x = 10;\",\"option_2\":\"var x = 10;\",\"option_3\":\"let x == 10;\",\"option_4\":\"x = 10;\",\"correct_option\":2}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1041, 27, 'Trong JavaScript, toán tử nào được sử dụng để gán giá trị?', '{\"option_1\":\"==\",\"option_2\":\"=\",\"option_3\":\"===\",\"option_4\":\"!=\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1042, 27, 'Để kiểm tra kiểu dữ liệu của một biến, sử dụng hàm nào sau đây?', '{\"option_1\":\"typeof\",\"option_2\":\"getType\",\"option_3\":\"isType\",\"option_4\":\"checkType\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1043, 27, 'Giá trị của null trong JavaScript thuộc kiểu dữ liệu nào?', '{\"option_1\":\"Object\",\"option_2\":\"Undefined\",\"option_3\":\"Number\",\"option_4\":\"Boolean\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1044, 27, 'Phương thức nào được sử dụng để thêm một phần tử vào cuối mảng?', '{\"option_1\":\"add()\",\"option_2\":\"push()\",\"option_3\":\"insert()\",\"option_4\":\"append()\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1045, 27, 'JavaScript có thể được sử dụng để thực hiện những hành động nào?', '{\"option_1\":\"Thay \\u0111\\u1ed5i n\\u1ed9i dung c\\u1ee7a HTML\",\"option_2\":\"Thay \\u0111\\u1ed5i ki\\u1ec3u d\\u00e1ng CSS\",\"option_3\":\"Th\\u1ef1c hi\\u1ec7n t\\u00ednh to\\u00e1n logic\",\"option_4\":\"T\\u1ea5t c\\u1ea3 c\\u00e1c l\\u1ef1a ch\\u1ecdn tr\\u00ean\",\"correct_option\":4}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1046, 27, 'Toán tử nào kiểm tra cả giá trị và kiểu dữ liệu của hai biến?', '{\"option_1\":\"==\",\"option_2\":\"===\",\"option_3\":\"!==\",\"option_4\":\">=\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1047, 27, 'Vòng lặp nào được sử dụng để lặp qua các thuộc tính của một đối tượng?', '{\"option_1\":\"for\",\"option_2\":\"forEach\",\"option_3\":\"for..in\",\"option_4\":\"while\",\"correct_option\":3}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1048, 27, 'Phương thức nào trả về chuỗi ký tự viết thường từ một chuỗi?', '{\"option_1\":\"toLowerCase()\",\"option_2\":\"toLower()\",\"option_3\":\"lower()\",\"option_4\":\"downcase()\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1049, 27, 'Kết quả của biểu thức \"5\" + 5 trong JavaScript là gì?', '{\"option_1\":\"10\",\"option_2\":\"\\\"55\\\"\",\"option_3\":\"Error\",\"option_4\":\"5\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23');
INSERT INTO `quizs` (`id`, `subject_id`, `question`, `options`, `mark`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1050, 27, 'Cú pháp để định nghĩa hàm trong JavaScript là gì?', '{\"option_1\":\"function myFunction() {}\",\"option_2\":\"func myFunction() {}\",\"option_3\":\"function = myFunction() {}\",\"option_4\":\"function: myFunction() {}\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1051, 27, 'NaN trong JavaScript có nghĩa là gì?', '{\"option_1\":\"Kh\\u00f4ng ph\\u1ea3i l\\u00e0 m\\u1ed9t s\\u1ed1\",\"option_2\":\"L\\u1ed7i c\\u00fa ph\\u00e1p\",\"option_3\":\"M\\u1ed9t s\\u1ed1 \\u00e2m\",\"option_4\":\"M\\u1ed9t chu\\u1ed7i r\\u1ed7ng\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1052, 27, 'Để kiểm tra xem một mảng có chứa một giá trị cụ thể hay không, sử dụng phương thức nào?', '{\"option_1\":\"contains()\",\"option_2\":\"has()\",\"option_3\":\"includes()\",\"option_4\":\"find()\",\"correct_option\":3}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1053, 27, 'Sự kiện nào xảy ra khi người dùng nhấn vào một phần tử HTML?', '{\"option_1\":\"onhover\",\"option_2\":\"onclick\",\"option_3\":\"onpress\",\"option_4\":\"onmouseover\",\"correct_option\":2}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1054, 27, 'Giá trị của biến chưa được gán trong JavaScript là gì?', '{\"option_1\":\"undefined\",\"option_2\":\"null\",\"option_3\":\"0\",\"option_4\":\"NaN\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1055, 27, 'Để ngăn chặn một sự kiện xảy ra, ta sử dụng phương thức nào?', '{\"option_1\":\"prevent()\",\"option_2\":\"stop()\",\"option_3\":\"stopPropagation()\",\"option_4\":\"preventDefault()\",\"correct_option\":4}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1056, 27, 'Kết quả của biểu thức typeof null trong JavaScript là gì?', '{\"option_1\":\"\",\"option_2\":\"object\",\"option_3\":\"undefined\",\"option_4\":\"number\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1057, 27, 'Phương thức nào được sử dụng để chuyển đổi chuỗi thành số trong JavaScript?', '{\"option_1\":\"parseString()\",\"option_2\":\"toNumber()\",\"option_3\":\"Number()\",\"option_4\":\"convert()\",\"correct_option\":3}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1058, 27, 'Giá trị của biểu thức 3 + 4 + \"5\" trong JavaScript là gì?', '{\"option_1\":\"\\\"75\\\"\",\"option_2\":\"75\",\"option_3\":\"\\\"345\\\"\",\"option_4\":\"\\\"75\\\"\",\"correct_option\":4}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1059, 27, 'Câu lệnh nào tạo một mảng mới với các phần tử thỏa mãn điều kiện từ mảng ban đầu?', '{\"option_1\":\"map()\",\"option_2\":\"forEach()\",\"option_3\":\"filter()\",\"option_4\":\"reduce()\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1060, 27, 'Để thêm một thuộc tính mới vào đối tượng trong JavaScript, sử dụng cú pháp nào?', '{\"option_1\":\"obj.addProperty(\\\"newProp\\\", value);\",\"option_2\":\"obj.newProp = value;\",\"option_3\":\"obj.add(\\\"newProp\\\", value);\",\"option_4\":\"obj[\\\"newProp\\\"] = value;\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1061, 27, 'Phương thức nào loại bỏ phần tử đầu tiên của mảng?', '{\"option_1\":\"pop()\",\"option_2\":\"remove()\",\"option_3\":\"shift()\",\"option_4\":\"splice()\",\"correct_option\":3}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1062, 27, 'Vòng lặp nào được sử dụng để lặp qua tất cả các phần tử của một mảng?', '{\"option_1\":\"for..in\",\"option_2\":\"for..of\",\"option_3\":\"forEach\",\"option_4\":\"while\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1063, 27, 'Từ khóa nào dùng để dừng một vòng lặp sớm?', '{\"option_1\":\"continue\",\"option_2\":\"return\",\"option_3\":\"break\",\"option_4\":\"stop\",\"correct_option\":3}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1064, 27, 'Để hợp nhất hai hoặc nhiều mảng trong JavaScript, phương thức nào được sử dụng?', '{\"option_1\":\"merge()\",\"option_2\":\"concat()\",\"option_3\":\"join()\",\"option_4\":\"combine()\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1065, 27, 'Kết quả của biểu thức typeof [] trong JavaScript là gì?', '{\"option_1\":\"array\",\"option_2\":\"object\",\"option_3\":\"list\",\"option_4\":\"undefined\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1066, 27, 'Trong JavaScript, từ khóa nào được sử dụng để tạo một đối tượng mới kế thừa từ một đối tượng hiện có?', '{\"option_1\":\"prototype\",\"option_2\":\"extend\",\"option_3\":\"inherit\",\"option_4\":\"constructor\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1067, 27, 'Để xác định chiều dài của một mảng, sử dụng thuộc tính nào?', '{\"option_1\":\"size\",\"option_2\":\"length\",\"option_3\":\"count\",\"option_4\":\"total\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1068, 27, 'Hàm nào trong JavaScript được sử dụng để thực thi một đoạn mã sau một khoảng thời gian nhất định?', '{\"option_1\":\"setTimeout()\",\"option_2\":\"setInterval()\",\"option_3\":\"executeLater()\",\"option_4\":\"wait()\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1069, 27, 'Toán tử nào trả về giá trị true nếu cả hai điều kiện đều đúng?', '{\"option_1\":\"||\",\"option_2\":\"&&\",\"option_3\":\"!\",\"option_4\":\"==\",\"correct_option\":2}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1070, 27, 'Để chuyển đổi chuỗi \"123\" thành số nguyên, sử dụng hàm nào sau đây?', '{\"option_1\":\"parseInt()\",\"option_2\":\"parseFloat()\",\"option_3\":\"Number()\",\"option_4\":\"toInt()\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1071, 27, 'JavaScript chạy ở môi trường nào?', '{\"option_1\":\"Tr\\u00ecnh duy\\u1ec7t\",\"option_2\":\"M\\u00e1y ch\\u1ee7\",\"option_3\":\"C\\u1ea3 tr\\u00ecnh duy\\u1ec7t v\\u00e0 m\\u00e1y ch\\u1ee7\",\"option_4\":\"Ch\\u1ec9 tr\\u00ean m\\u00e1y ch\\u1ee7\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1072, 27, 'Để chuyển đổi chuỗi \"true\" thành giá trị boolean, sử dụng phương thức nào?', '{\"option_1\":\"Boolean()\",\"option_2\":\"toBoolean()\",\"option_3\":\"convertToBoolean()\",\"option_4\":\"parseBoolean()\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1073, 27, 'Để tạo một đối tượng ngày trong JavaScript, sử dụng từ khóa nào?', '{\"option_1\":\"new Date()\",\"option_2\":\"createDate()\",\"option_3\":\"makeDate()\",\"option_4\":\"getDate()\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1074, 27, 'Phương thức nào trả về giá trị tuyệt đối của một số trong JavaScript?', '{\"option_1\":\"abs()\",\"option_2\":\"Math.abs()\",\"option_3\":\"Math.absolute()\",\"option_4\":\"absolute()\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1075, 27, 'Để kiểm tra một biến có phải là một mảng hay không, phương thức nào sau đây được sử dụng?', '{\"option_1\":\"isArray()\",\"option_2\":\"Array.isArray()\",\"option_3\":\"typeof\",\"option_4\":\"checkArray()\",\"correct_option\":2}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1076, 27, 'Giá trị nào được trả về bởi NaN === NaN trong JavaScript?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1077, 27, 'Để tạo ra một chuỗi ký tự ngẫu nhiên từ một tập hợp các ký tự, phương thức nào được sử dụng?', '{\"option_1\":\"randomString()\",\"option_2\":\"Math.random().toString()\",\"option_3\":\"getRandomString()\",\"option_4\":\"randomize()\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1078, 27, 'Kết quả của 5 > 3 && 2 < 4 trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"null\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1079, 27, 'Để loại bỏ khoảng trắng ở đầu và cuối của một chuỗi, sử dụng phương thức nào?', '{\"option_1\":\"trim()\",\"option_2\":\"strip()\",\"option_3\":\"cut()\",\"option_4\":\"clean()\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1080, 27, 'Để dừng việc thực thi một hàm trong một khoảng thời gian liên tục, sử dụng phương thức nào?', '{\"option_1\":\"setTimeout()\",\"option_2\":\"setInterval()\",\"option_3\":\"stopInterval()\",\"option_4\":\"pauseExecution()\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1081, 27, 'Kết quả của typeof NaN trong JavaScript là gì?', '{\"option_1\":\"number\",\"option_2\":\"NaN\",\"option_3\":\"undefined\",\"option_4\":\"object\",\"correct_option\":1}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1082, 27, 'Để kết thúc một vòng lặp hiện tại và tiếp tục vòng lặp tiếp theo, sử dụng từ khóa nào?', '{\"option_1\":\"continue\",\"option_2\":\"break\",\"option_3\":\"return\",\"option_4\":\"stop\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1083, 27, 'Hàm nào trả về một số ngẫu nhiên từ 0 đến 1 trong JavaScript?', '{\"option_1\":\"Math.random()\",\"option_2\":\"Math.randomNumber()\",\"option_3\":\"getRandom()\",\"option_4\":\"randomize()\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1084, 27, 'Khi một biến được khai báo với từ khóa let, biến đó có phạm vi nào?', '{\"option_1\":\"To\\u00e0n c\\u1ea7u\",\"option_2\":\"Kh\\u1ed1i\",\"option_3\":\"H\\u00e0m\",\"option_4\":\"\\u0110\\u1ed1i t\\u01b0\\u1ee3ng\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1085, 27, 'Để kiểm tra xem một đối tượng có chứa một thuộc tính cụ thể hay không, sử dụng phương thức nào?', '{\"option_1\":\"hasProperty()\",\"option_2\":\"hasOwnProperty()\",\"option_3\":\"getProperty()\",\"option_4\":\"containsProperty()\",\"correct_option\":2}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1086, 27, 'Để chuyển đổi một đối tượng JavaScript thành chuỗi JSON, sử dụng phương thức nào?', '{\"option_1\":\"JSON.parse()\",\"option_2\":\"JSON.stringify()\",\"option_3\":\"toString()\",\"option_4\":\"convertToJson()\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1087, 27, 'Biến const có thể được tái gán giá trị không?', '{\"option_1\":\"C\\u00f3\",\"option_2\":\"Kh\\u00f4ng\",\"option_3\":\"Ch\\u1ec9 v\\u1edbi ki\\u1ec3u d\\u1eef li\\u1ec7u nguy\\u00ean th\\u1ee7y\",\"option_4\":\"Ch\\u1ec9 v\\u1edbi ki\\u1ec3u d\\u1eef li\\u1ec7u tham chi\\u1ebfu\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1088, 27, 'Khi một hàm không trả về giá trị nào, giá trị mặc định được trả về là gì?', '{\"option_1\":\"null\",\"option_2\":\"undefined\",\"option_3\":\"NaN\",\"option_4\":\"false\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1089, 27, 'Câu lệnh nào trong JavaScript được sử dụng để ném một lỗi?', '{\"option_1\":\"throw\",\"option_2\":\"catch\",\"option_3\":\"raise\",\"option_4\":\"error\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1090, 27, 'Giá trị của 10 % 3 trong JavaScript là gì?', '{\"option_1\":\"1\",\"option_2\":\"3\",\"option_3\":\"0\",\"option_4\":\"10\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1091, 27, 'Để lặp qua từng phần tử trong một mảng, sử dụng phương thức nào sau đây?', '{\"option_1\":\"forEach()\",\"option_2\":\"map()\",\"option_3\":\"filter()\",\"option_4\":\"find()\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1092, 27, 'Kết quả của biểu thức \"10\" - 5 trong JavaScript là gì?', '{\"option_1\":\"5\",\"option_2\":\"\\\"105\\\"\",\"option_3\":\"NaN\",\"option_4\":\"10\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1093, 27, 'Để kiểm tra một biến có phải là số hay không, sử dụng hàm nào?', '{\"option_1\":\"isNaN()\",\"option_2\":\"isNumber()\",\"option_3\":\"checkNumber()\",\"option_4\":\"typeof\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1094, 27, 'Để truy xuất tất cả các phím (keys) của một đối tượng, sử dụng phương thức nào?', '{\"option_1\":\"Object.values()\",\"option_2\":\"Object.keys()\",\"option_3\":\"Object.entries()\",\"option_4\":\"Object.getKeys()\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1095, 27, 'Để nối các phần tử của một mảng thành chuỗi với dấu phân cách, sử dụng phương thức nào?', '{\"option_1\":\"concat()\",\"option_2\":\"join()\",\"option_3\":\"split()\",\"option_4\":\"append()\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1096, 27, 'Biến let và var khác nhau ở điểm nào?', '{\"option_1\":\"Ph\\u1ea1m vi kh\\u1ed1i v\\u00e0 ph\\u1ea1m vi h\\u00e0m\",\"option_2\":\"Ph\\u1ea1m vi to\\u00e0n c\\u1ea7u\",\"option_3\":\"S\\u1ed1 l\\u1ea7n c\\u00f3 th\\u1ec3 khai b\\u00e1o\",\"option_4\":\"Kh\\u00f4ng c\\u00f3 s\\u1ef1 kh\\u00e1c bi\\u1ec7t\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1097, 27, 'Phương thức nào trong JavaScript được sử dụng để đếm số ký tự của một chuỗi?', '{\"option_1\":\"count()\",\"option_2\":\"length\",\"option_3\":\"size()\",\"option_4\":\"getCount()\",\"correct_option\":2}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1098, 27, 'Hàm nào trong JavaScript trả về một mảng mới từ một mảng ban đầu, sau khi lặp qua từng phần tử?', '{\"option_1\":\"map()\",\"option_2\":\"forEach()\",\"option_3\":\"filter()\",\"option_4\":\"reduce()\",\"correct_option\":1}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1099, 27, 'Phương thức nào loại bỏ khoảng trắng từ đầu và cuối của chuỗi trong JavaScript?', '{\"option_1\":\"trim()\",\"option_2\":\"strip()\",\"option_3\":\"slice()\",\"option_4\":\"clear()\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1100, 27, 'Kết quả của biểu thức Math.max(2, 5, 10) là gì?', '{\"option_1\":\"10\",\"option_2\":\"5\",\"option_3\":\"2\",\"option_4\":\"NaN\",\"correct_option\":1}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1101, 27, 'Để gán một giá trị mặc định cho một biến nếu giá trị hiện tại là null hoặc undefined, toán tử nào được sử dụng?', '{\"option_1\":\"??\",\"option_2\":\"||\",\"option_3\":\"&&\",\"option_4\":\"?\",\"correct_option\":1}', 4.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1102, 27, 'Trong JavaScript, một mảng có thể chứa các giá trị thuộc loại nào?', '{\"option_1\":\"Ch\\u1ec9 s\\u1ed1\",\"option_2\":\"Ch\\u1ec9 chu\\u1ed7i\",\"option_3\":\"C\\u1ea3 s\\u1ed1 v\\u00e0 chu\\u1ed7i\",\"option_4\":\"M\\u1ecdi lo\\u1ea1i gi\\u00e1 tr\\u1ecb\",\"correct_option\":4}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1103, 27, 'Hàm nào trả về vị trí của một chuỗi con trong một chuỗi lớn hơn?', '{\"option_1\":\"search()\",\"option_2\":\"find()\",\"option_3\":\"indexOf()\",\"option_4\":\"match()\",\"correct_option\":3}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1104, 27, 'Kết quả của typeof undefined trong JavaScript là gì?', '{\"option_1\":\"undefined\",\"option_2\":\"null\",\"option_3\":\"object\",\"option_4\":\"number\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1105, 27, 'Để kết hợp tất cả các giá trị trong một mảng thành một giá trị duy nhất, sử dụng phương thức nào?', '{\"option_1\":\"reduce()\",\"option_2\":\"concat()\",\"option_3\":\"filter()\",\"option_4\":\"combine()\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1106, 27, 'Để kết thúc việc thực thi một hàm và trả về giá trị, sử dụng từ khóa nào?', '{\"option_1\":\"break\",\"option_2\":\"return\",\"option_3\":\"stop\",\"option_4\":\"end\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1107, 27, 'Để thêm một phần tử vào đầu của mảng, phương thức nào được sử dụng?', '{\"option_1\":\"push()\",\"option_2\":\"shift()\",\"option_3\":\"unshift()\",\"option_4\":\"insert()\",\"correct_option\":3}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1108, 27, 'Trong JavaScript, kết quả của biểu thức \"Hello\".charAt(1) là gì?', '{\"option_1\":\"\\\"H\\\"\",\"option_2\":\"\\\"e\\\"\",\"option_3\":\"\\\"l\\\"\",\"option_4\":\"\\\"o\\\"\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1109, 27, 'Để kiểm tra xem một chuỗi có chứa chuỗi con hay không, phương thức nào được sử dụng?', '{\"option_1\":\"includes()\",\"option_2\":\"contains()\",\"option_3\":\"indexOf()\",\"option_4\":\"match()\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1110, 27, 'Toán tử nào trong JavaScript so sánh mà không kiểm tra kiểu dữ liệu?', '{\"option_1\":\"==\",\"option_2\":\"===\",\"option_3\":\"!=\",\"option_4\":\"!==\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1111, 27, 'Biểu thức nào trong JavaScript trả về true?', '{\"option_1\":\"\\\" \\\" == 0\",\"option_2\":\"\\\" \\\" === 0\",\"option_3\":\"null === undefined\",\"option_4\":\"null == undefined\",\"correct_option\":4}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1112, 27, 'Để kiểm tra xem một biến có phải là null hay không, sử dụng toán tử nào?', '{\"option_1\":\"==\",\"option_2\":\"===\",\"option_3\":\"!==\",\"option_4\":\"C\\u1ea3 b v\\u00e0 c\",\"correct_option\":4}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1113, 27, 'Kết quả của 2 ** 3 trong JavaScript là gì?', '{\"option_1\":\"6\",\"option_2\":\"8\",\"option_3\":\"9\",\"option_4\":\"10\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1114, 27, 'Để tách chuỗi thành mảng dựa trên một ký tự cụ thể, sử dụng phương thức nào?', '{\"option_1\":\"slice()\",\"option_2\":\"split()\",\"option_3\":\"splice()\",\"option_4\":\"trim()\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1115, 27, 'Để tạo một đối tượng mới từ một đối tượng mẫu (prototype), sử dụng từ khóa nào?', '{\"option_1\":\"Object.create()\",\"option_2\":\"new Object()\",\"option_3\":\"Object.build()\",\"option_4\":\"Object.construct()\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1116, 27, 'Kết quả của \"20\" + 5 trong JavaScript là gì?', '{\"option_1\":\"25\",\"option_2\":\"\\\"205\\\"\",\"option_3\":\"NaN\",\"option_4\":\"30\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1117, 27, 'Kết quả của biểu thức !!false trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"null\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1118, 27, 'Để lấy ra phần tử cuối cùng của một mảng, sử dụng phương thức nào?', '{\"option_1\":\"pop()\",\"option_2\":\"push()\",\"option_3\":\"shift()\",\"option_4\":\"unshift()\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1119, 27, 'Để nối hai chuỗi, sử dụng toán tử nào?', '{\"option_1\":\"+\",\"option_2\":\"*\",\"option_3\":\"&\",\"option_4\":\"&&\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1120, 27, 'Toán tử nào trả về true nếu hai giá trị khác nhau về cả giá trị và kiểu dữ liệu?', '{\"option_1\":\"!=\",\"option_2\":\"!==\",\"option_3\":\"==\",\"option_4\":\"=\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1121, 27, 'Để lấy giá trị của một thuộc tính trong đối tượng, sử dụng cú pháp nào?', '{\"option_1\":\"obj{key}\",\"option_2\":\"obj[key]\",\"option_3\":\"obj.key\",\"option_4\":\"C\\u1ea3 b v\\u00e0 c\",\"correct_option\":4}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1122, 27, 'Để kiểm tra xem một giá trị có phải là undefined hay không, sử dụng hàm nào?', '{\"option_1\":\"typeof\",\"option_2\":\"isUndefined()\",\"option_3\":\"checkUndefined()\",\"option_4\":\"isEmpty()\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1123, 27, 'Để thoát khỏi một hàm trước khi nó hoàn thành, sử dụng từ khóa nào?', '{\"option_1\":\"exit\",\"option_2\":\"return\",\"option_3\":\"stop\",\"option_4\":\"quit\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1124, 27, 'Toán tử nào kiểm tra nếu một biến có giá trị \"null\" hoặc \"undefined\"?', '{\"option_1\":\"??\",\"option_2\":\"??\",\"option_3\":\"||\",\"option_4\":\"&&\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1125, 27, 'Để truy cập vào phần tử đầu tiên của một mảng arr, cú pháp nào đúng?', '{\"option_1\":\"arr.first()\",\"option_2\":\"arr[1]\",\"option_3\":\"arr[0]\",\"option_4\":\"arr[-1]\",\"correct_option\":3}', 1, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1126, 27, 'Giá trị mặc định của undefined là gì?', '{\"option_1\":\"M\\u1ed9t \\u0111\\u1ed1i t\\u01b0\\u1ee3ng r\\u1ed7ng\",\"option_2\":\"null\",\"option_3\":\"M\\u1ed9t chu\\u1ed7i r\\u1ed7ng\",\"option_4\":\"Kh\\u00f4ng c\\u00f3 gi\\u00e1 tr\\u1ecb (undefined)\",\"correct_option\":4}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1127, 27, 'Để dừng việc lặp trong một vòng lặp forEach(), sử dụng từ khóa nào?', '{\"option_1\":\"break\",\"option_2\":\"continue\",\"option_3\":\"Kh\\u00f4ng th\\u1ec3 d\\u1eebng\",\"option_4\":\"stop\",\"correct_option\":3}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1128, 27, 'Kết quả của biểu thức true || false trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"null\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1129, 27, 'Toán tử == trong JavaScript so sánh giá trị mà không kiểm tra gì?', '{\"option_1\":\"Ki\\u1ec3u d\\u1eef li\\u1ec7u\",\"option_2\":\"Gi\\u00e1 tr\\u1ecb v\\u00e0 ki\\u1ec3u d\\u1eef li\\u1ec7u\",\"option_3\":\"To\\u00e1n t\\u1eed logic\",\"option_4\":\"C\\u1ea3 gi\\u00e1 tr\\u1ecb v\\u00e0 ki\\u1ec3u d\\u1eef li\\u1ec7u\",\"correct_option\":1}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1130, 27, 'Kết quả của 0.1 + 0.2 === 0.3 trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"NaN\",\"correct_option\":2}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1131, 27, 'Kết quả của biểu thức \"10\" * 2 là gì?', '{\"option_1\":\"\\\"102\\\"\",\"option_2\":\"20\",\"option_3\":\"NaN\",\"option_4\":\"10\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1132, 27, 'Giá trị nào trả về từ typeof []?', '{\"option_1\":\"array\",\"option_2\":\"object\",\"option_3\":\"list\",\"option_4\":\"undefined\",\"correct_option\":2}', 5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1133, 27, 'Hàm nào dưới đây trả về một chuỗi với tất cả ký tự viết hoa?', '{\"option_1\":\"upper()\",\"option_2\":\"toUpperCase()\",\"option_3\":\"toUpper()\",\"option_4\":\"capitalize()\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1134, 27, 'Giá trị của typeof NaN trong JavaScript là gì?', '{\"option_1\":\"number\",\"option_2\":\"NaN\",\"option_3\":\"undefined\",\"option_4\":\"object\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1135, 27, 'Để dừng việc thực thi một hàm trong khoảng thời gian nhất định, sử dụng hàm nào?', '{\"option_1\":\"setTimeout()\",\"option_2\":\"setInterval()\",\"option_3\":\"delay()\",\"option_4\":\"wait()\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1136, 27, 'Từ khóa nào trong JavaScript để khai báo một biến có giá trị không đổi?', '{\"option_1\":\"const\",\"option_2\":\"var\",\"option_3\":\"let\",\"option_4\":\"static\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1137, 27, 'Câu nào sau đây là đúng để gán giá trị mặc định khi biến có giá trị là null hoặc undefined?', '{\"option_1\":\"let a = b || \\\"default\\\";\",\"option_2\":\"let a = b && \\\"default\\\";\",\"option_3\":\"let a = b ?? \\\"default\\\";\",\"option_4\":\"let a = b ? \\\"default\\\" : b;\",\"correct_option\":3}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1138, 27, 'Phương thức nào trả về mảng chứa các giá trị của đối tượng?', '{\"option_1\":\"Object.keys()\",\"option_2\":\"Object.values()\",\"option_3\":\"Object.entries()\",\"option_4\":\"Object.getValues()\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1139, 27, 'Kết quả của \"5\" - 3 trong JavaScript là gì?', '{\"option_1\":\"\\\"53\\\"\",\"option_2\":\"2\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":2}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1140, 27, 'Cú pháp nào để thêm một phần tử vào đầu mảng?', '{\"option_1\":\"arr.shift()\",\"option_2\":\"arr.push()\",\"option_3\":\"arr.unshift()\",\"option_4\":\"arr.pop()\",\"correct_option\":3}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1141, 27, 'Kết quả của typeof function() {} trong JavaScript là gì?', '{\"option_1\":\"function\",\"option_2\":\"object\",\"option_3\":\"undefined\",\"option_4\":\"string\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1142, 27, 'Toán tử typeof trả về kiểu dữ liệu gì khi kiểm tra một mảng?', '{\"option_1\":\"array\",\"option_2\":\"object\",\"option_3\":\"list\",\"option_4\":\"undefined\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1143, 27, 'Hàm nào trả về một số ngẫu nhiên từ 0 đến 1?', '{\"option_1\":\"random()\",\"option_2\":\"Math.random()\",\"option_3\":\"getRandom()\",\"option_4\":\"randomize()\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1144, 27, 'Kết quả của !!0 trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"NaN\",\"correct_option\":2}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1145, 27, '=== trong JavaScript kiểm tra điều gì?', '{\"option_1\":\"Gi\\u00e1 tr\\u1ecb\",\"option_2\":\"Ki\\u1ec3u d\\u1eef li\\u1ec7u\",\"option_3\":\"Gi\\u00e1 tr\\u1ecb v\\u00e0 ki\\u1ec3u d\\u1eef li\\u1ec7u\",\"option_4\":\"Gi\\u00e1 tr\\u1ecb tham chi\\u1ebfu\",\"correct_option\":3}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1146, 27, 'Giá trị typeof null trả về là gì?', '{\"option_1\":\"null\",\"option_2\":\"object\",\"option_3\":\"undefined\",\"option_4\":\"string\",\"correct_option\":2}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1147, 27, 'Để tạo một đối tượng rỗng trong JavaScript, cú pháp nào đúng?', '{\"option_1\":\"var obj = [];\",\"option_2\":\"var obj = {};\",\"option_3\":\"var obj = Object.create();\",\"option_4\":\"var obj = new Object();\",\"correct_option\":2}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1148, 27, 'Kết quả của biểu thức \"10\" / 2 trong JavaScript là gì?', '{\"option_1\":\"5\",\"option_2\":\"\\\"102\\\"\",\"option_3\":\"NaN\",\"option_4\":\"\\\"5\\\"\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1149, 27, 'Hàm nào chuyển chuỗi thành mảng dựa trên ký tự phân cách?', '{\"option_1\":\"split()\",\"option_2\":\"join()\",\"option_3\":\"slice()\",\"option_4\":\"concat()\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1150, 27, 'Để gán một giá trị ngẫu nhiên cho một biến trong JavaScript, sử dụng phương thức nào?', '{\"option_1\":\"random()\",\"option_2\":\"Math.random()\",\"option_3\":\"getRandom()\",\"option_4\":\"randomize()\",\"correct_option\":2}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1151, 27, 'Kết quả của undefined == null là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"NaN\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1152, 27, 'Hàm nào dưới đây trả về chuỗi có tất cả các ký tự viết thường?', '{\"option_1\":\"toLowerCase()\",\"option_2\":\"lower()\",\"option_3\":\"downcase()\",\"option_4\":\"toLower()\",\"correct_option\":1}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1153, 27, 'Kết quả của 5 + null trong JavaScript là gì?', '{\"option_1\":\"NaN\",\"option_2\":\"5\",\"option_3\":\"null\",\"option_4\":\"0\",\"correct_option\":2}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1154, 27, 'Để kiểm tra xem một biến có phải là số hay không, sử dụng hàm nào?', '{\"option_1\":\"isNaN()\",\"option_2\":\"typeof\",\"option_3\":\"checkNumber()\",\"option_4\":\"isNumber()\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1155, 27, 'Phương thức nào trong JavaScript được sử dụng để đếm số ký tự của một chuỗi?', '{\"option_1\":\"count()\",\"option_2\":\"length\",\"option_3\":\"size()\",\"option_4\":\"getCount()\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1156, 27, 'Câu nào trong JavaScript dùng để thoát khỏi một vòng lặp?', '{\"option_1\":\"quit\",\"option_2\":\"exit\",\"option_3\":\"break\",\"option_4\":\"stop\",\"correct_option\":3}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1157, 27, 'Để xóa thuộc tính khỏi đối tượng, sử dụng từ khóa nào?', '{\"option_1\":\"delete\",\"option_2\":\"remove\",\"option_3\":\"pop\",\"option_4\":\"shift\",\"correct_option\":1}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1158, 27, 'Hàm nào trả về vị trí của một chuỗi con trong chuỗi chính?', '{\"option_1\":\"indexOf()\",\"option_2\":\"find()\",\"option_3\":\"includes()\",\"option_4\":\"match()\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1159, 27, 'Để kiểm tra một chuỗi có bắt đầu với chuỗi con cụ thể hay không, sử dụng phương thức nào?', '{\"option_1\":\"startsWith()\",\"option_2\":\"beginsWith()\",\"option_3\":\"indexOf()\",\"option_4\":\"includes()\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1160, 27, 'Để tạo ra một chuỗi ký tự ngẫu nhiên từ một tập hợp các ký tự, phương thức nào được sử dụng?', '{\"option_1\":\"randomString()\",\"option_2\":\"Math.random().toString()\",\"option_3\":\"getRandomString()\",\"option_4\":\"randomize()\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1161, 27, 'Hàm nào trong JavaScript trả về vị trí của phần tử cuối cùng xuất hiện trong mảng?', '{\"option_1\":\"findIndex()\",\"option_2\":\"indexOf()\",\"option_3\":\"lastIndexOf()\",\"option_4\":\"includes()\",\"correct_option\":3}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1162, 27, 'Toán tử nào được sử dụng để kiểm tra giá trị null hoặc undefined?', '{\"option_1\":\"??\",\"option_2\":\"||\",\"option_3\":\"&&\",\"option_4\":\"===\",\"correct_option\":1}', 2.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1163, 27, 'Để lấy giá trị của thuộc tính trong một đối tượng, cú pháp nào đúng?', '{\"option_1\":\"obj{key}\",\"option_2\":\"obj.key\",\"option_3\":\"obj[key]\",\"option_4\":\"C\\u1ea3 b v\\u00e0 c\",\"correct_option\":4}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1164, 27, 'Giá trị trả về của typeof [] trong JavaScript là gì?', '{\"option_1\":\"array\",\"option_2\":\"object\",\"option_3\":\"undefined\",\"option_4\":\"null\",\"correct_option\":2}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1165, 27, 'Để lặp qua các thuộc tính của một đối tượng, cú pháp nào đúng?', '{\"option_1\":\"for..of\",\"option_2\":\"for..in\",\"option_3\":\"forEach\",\"option_4\":\"while\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1166, 27, 'Hàm nào trả về giá trị Boolean của một biến?', '{\"option_1\":\"bool()\",\"option_2\":\"isTrue()\",\"option_3\":\"Boolean()\",\"option_4\":\"convertToBoolean()\",\"correct_option\":3}', 4.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1167, 27, 'Toán tử typeof trả về kiểu dữ liệu gì khi kiểm tra một hàm?', '{\"option_1\":\"function\",\"option_2\":\"object\",\"option_3\":\"undefined\",\"option_4\":\"string\",\"correct_option\":1}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1168, 27, 'Để gán giá trị mặc định khi biến không có giá trị xác định, toán tử nào được sử dụng?', '{\"option_1\":\"||\",\"option_2\":\"??\",\"option_3\":\"&&\",\"option_4\":\"===\",\"correct_option\":2}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1169, 27, 'Toán tử nào kiểm tra nếu một biến không bằng một giá trị và khác cả về kiểu dữ liệu?', '{\"option_1\":\"!=\",\"option_2\":\"!==\",\"option_3\":\"==\",\"option_4\":\"=\",\"correct_option\":2}', 1.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1170, 27, 'Kết quả của typeof null trong JavaScript là gì?', '{\"option_1\":\"object\",\"option_2\":\"null\",\"option_3\":\"undefined\",\"option_4\":\"string\",\"correct_option\":1}', 3.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1171, 27, 'Cú pháp nào để khai báo một hàm trong JavaScript?', '{\"option_1\":\"function myFunc() {}\",\"option_2\":\"var myFunc = function() {}\",\"option_3\":\"let myFunc = () => {};\",\"option_4\":\"T\\u1ea5t c\\u1ea3 c\\u00e1c c\\u00e2u tr\\u00ean\",\"correct_option\":4}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1172, 27, 'Phương thức nào được sử dụng để thêm một thuộc tính vào đối tượng trong JavaScript?', '{\"option_1\":\"obj.property = value;\",\"option_2\":\"obj.addProperty(\\\"property\\\", value);\",\"option_3\":\"obj.push(\\\"property\\\", value);\",\"option_4\":\"obj.append(\\\"property\\\", value);\",\"correct_option\":1}', 2, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1173, 27, 'Kết quả của biểu thức 5 + null là gì?', '{\"option_1\":\"5\",\"option_2\":\"null\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1174, 27, 'Hàm nào trong JavaScript được sử dụng để đếm số phần tử của một chuỗi?', '{\"option_1\":\"count()\",\"option_2\":\"length\",\"option_3\":\"size()\",\"option_4\":\"getCount()\",\"correct_option\":2}', 2.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1175, 27, 'Để kiểm tra một chuỗi có chứa chuỗi con hay không, sử dụng phương thức nào?', '{\"option_1\":\"includes()\",\"option_2\":\"contains()\",\"option_3\":\"indexOf()\",\"option_4\":\"match()\",\"correct_option\":1}', 0.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1176, 27, 'Kết quả của biểu thức true && false trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"undefined\",\"option_4\":\"null\",\"correct_option\":2}', 3.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1177, 27, 'Từ khóa nào trong JavaScript để khai báo một hằng số?', '{\"option_1\":\"const\",\"option_2\":\"let\",\"option_3\":\"var\",\"option_4\":\"final\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1178, 27, 'Kết quả của biểu thức \"5\" * \"2\" trong JavaScript là gì?', '{\"option_1\":\"\\\"52\\\"\",\"option_2\":\"10\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":2}', 3.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1179, 27, 'Hàm nào trong JavaScript được sử dụng để chuyển đổi số nguyên thành chuỗi?', '{\"option_1\":\"toString()\",\"option_2\":\"toInteger()\",\"option_3\":\"parseInt()\",\"option_4\":\"convertToString()\",\"correct_option\":1}', 4, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1180, 27, 'Kết quả của biểu thức \"10\" - \"3\" trong JavaScript là gì?', '{\"option_1\":\"7\",\"option_2\":\"\\\"7\\\"\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":1}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1181, 27, 'Hàm nào trả về phần tử cuối cùng của mảng?', '{\"option_1\":\"arr[arr.length-1]\",\"option_2\":\"arr[arr.length]\",\"option_3\":\"arr.shift()\",\"option_4\":\"arr.last()\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1182, 27, 'Kết quả của \"Hello\".charAt(0) trong JavaScript là gì?', '{\"option_1\":\"\\\"H\\\"\",\"option_2\":\"\\\"e\\\"\",\"option_3\":\"\\\"l\\\"\",\"option_4\":\"\\\"o\\\"\",\"correct_option\":1}', 2.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1183, 27, 'Để kết hợp tất cả các giá trị trong một mảng thành một giá trị duy nhất, sử dụng phương thức nào?', '{\"option_1\":\"reduce()\",\"option_2\":\"concat()\",\"option_3\":\"filter()\",\"option_4\":\"join()\",\"correct_option\":1}', 4.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1184, 27, 'Để lấy ra phần tử đầu tiên của một mảng, sử dụng phương thức nào?', '{\"option_1\":\"pop()\",\"option_2\":\"shift()\",\"option_3\":\"unshift()\",\"option_4\":\"push()\",\"correct_option\":2}', 1.25, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1185, 27, 'Kết quả của biểu thức \"10\" + 2 trong JavaScript là gì?', '{\"option_1\":\"\\\"102\\\"\",\"option_2\":\"12\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":1}', 0.75, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1186, 27, 'Kết quả của biểu thức 10 == \"10\" trong JavaScript là gì?', '{\"option_1\":\"true\",\"option_2\":\"false\",\"option_3\":\"NaN\",\"option_4\":\"undefined\",\"correct_option\":1}', 0.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1187, 27, 'Toán tử && trong JavaScript trả về true khi nào?', '{\"option_1\":\"Khi t\\u1ea5t c\\u1ea3 c\\u00e1c \\u0111i\\u1ec1u ki\\u1ec7n \\u0111\\u1ec1u \\u0111\\u00fang\",\"option_2\":\"Khi \\u00edt nh\\u1ea5t m\\u1ed9t \\u0111i\\u1ec1u ki\\u1ec7n \\u0111\\u00fang\",\"option_3\":\"Khi t\\u1ea5t c\\u1ea3 c\\u00e1c \\u0111i\\u1ec1u ki\\u1ec7n \\u0111\\u1ec1u sai\",\"option_4\":\"Khi kh\\u00f4ng c\\u00f3 \\u0111i\\u1ec1u ki\\u1ec7n n\\u00e0o\",\"correct_option\":1}', 1.5, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1188, 27, 'Để loại bỏ khoảng trắng từ đầu và cuối chuỗi, sử dụng phương thức nào?', '{\"option_1\":\"trim()\",\"option_2\":\"slice()\",\"option_3\":\"cut()\",\"option_4\":\"clean()\",\"correct_option\":1}', 3, 0, '2024-12-08 13:04:23', 0, '2024-12-08 13:04:23'),
(1189, 26, '<p>&lt;p&gt;&amp;lt;p&amp;gt;fdsafasfsaf&amp;lt;/p&amp;gt; fdsafdasfas&lt;/p&gt;fdasfasdfdas</p>', '{\"option_1\":\"<p>&lt;p&gt;&amp;lt;p&amp;gt;fdsafsafa&amp;lt;\\/p&amp;gt;&lt;\\/p&gt;<\\/p>\",\"option_2\":\"<p>&lt;p&gt;&amp;lt;p&amp;gt;fdsafdasf&amp;lt;\\/p&amp;gt;&lt;\\/p&gt;<\\/p>\",\"option_3\":\"<p>&lt;p&gt;&amp;lt;p&amp;gt;fdasfasf&amp;lt;\\/p&amp;gt;&lt;\\/p&gt;<\\/p>\",\"option_4\":\"<p>&lt;p&gt;&amp;lt;p&amp;gt;fdsafdsafasdfdas&amp;lt;\\/p&amp;gt;&lt;\\/p&gt;<\\/p>\",\"correct_option\":1}', 3, 0, '2024-12-08 13:08:56', 2, '2024-12-08 13:13:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_date` datetime NOT NULL DEFAULT current_timestamp(),
  `spend_time` int(11) NOT NULL COMMENT 'Thời gian dùng để làm bài',
  `exam_id` bigint(11) NOT NULL,
  `result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`result`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `meta` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `meta`) VALUES
(25, 'HTML', 'html'),
(26, 'CSS', 'css'),
(27, 'JAVASCRIPT', 'javascript');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teachings`
--

CREATE TABLE `teachings` (
  `id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `subject_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Mảng json chứa id các môn học' CHECK (json_valid(`subject_ids`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teachings`
--

INSERT INTO `teachings` (`id`, `name`, `teacher_id`, `subject_id`, `school_year`, `subject_ids`) VALUES
(7, 'Lớp thử nghiệm - 7', 66, 26, '2024-2025', ''),
(8, 'Lớp thử nghiệm - 8', 66, 25, '2024-2025', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_code` varchar(50) NOT NULL COMMENT 'Mã user',
  `password` varchar(250) NOT NULL,
  `fullname` varchar(30) NOT NULL,
  `phone` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `role` varchar(7) NOT NULL DEFAULT 'user',
  `teaching_id` bigint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `user_code`, `password`, `fullname`, `phone`, `email`, `role`, `teaching_id`) VALUES
(2, 'admin', 'FTB-2', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Administrator', '0911397764', 'redoprogrammer@gmail.com', 'admin', 7),
(50, 'dungdt', '2220700042', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'ĐIểU Thị Dung', '922343222', 'daothituoi@gmail.com', 'user', 7),
(51, 'duongttt', '2220700044', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Trịnh Thị Thùy Dương', '933222123', 'duongqua@gmail.com', 'user', 8),
(52, 'hantng', '2220700046', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Trịnh Ngọc Gia Hân', '911234543', 'tieulongnu@gmail.com', 'user', 7),
(53, 'hienttt', '22207000471', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'TrươNg Thị Thu HiềN', '942234932', 'doanchibinh@gmail.com', 'user', 8),
(54, 'hongnt', '2220700049', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Nông Thị Hồng', '953219832', 'vuongtrungduong@gmail.com', 'user', 7),
(55, 'huongttt', '2220700051', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Thạch Thị Thu Hương', '943218381', 'chaubathong@gmail.com', 'user', 8),
(56, 'linhbtk', '2220700053', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Bùi Thị Khánh Linh', '936839019', 'quachtinh@gmail.com', 'user', 7),
(57, 'loanltt', '2220700054', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Lê Thị Thu Loan', '912433999', 'hoangdung@gmail.com', 'user', 8),
(58, 'maintt', '2220700057', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Nguyễn Thị Tuyết Mai', '912433999', 'hoangdung@gmail.com', 'user', 7),
(64, 'fdsafdasfdsa', 'fdasfdasf', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Fdsafdasfdsa', '0911397764', 'fdsafjasdlkf@gmail.com', 'user', 7),
(65, 'hungtd', 'FTB2024', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'Trần Duy Hưng', '988333222', 'tranduyhung@gmail.com', 'user', 8),
(66, 'truongnh', '06049287', '$2y$10$RROejVtV4LqjJUgMnaWzfOBBKsE4EiuZ9HpKACW9OXou4MK9Sw61q', 'NguyễN HữU TrườNg', '0911397764', 'redoprogrammer1@gmail.com', 'teacher', 7);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subject_id` (`subject_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `teaching_id` (`teaching_id`);

--
-- Chỉ mục cho bảng `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_result_id` (`exam_result_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `question_blank_id` (`question_blank_id`);

--
-- Chỉ mục cho bảng `exam_configs`
--
ALTER TABLE `exam_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Chỉ mục cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `lession_id` (`lession_id`);

--
-- Chỉ mục cho bảng `lessions`
--
ALTER TABLE `lessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lession_id` (`lession_id`);

--
-- Chỉ mục cho bảng `question_blanks`
--
ALTER TABLE `question_blanks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Chỉ mục cho bảng `quizs`
--
ALTER TABLE `quizs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Chỉ mục cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Chỉ mục cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `teachings`
--
ALTER TABLE `teachings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_subject_year` (`teacher_id`,`subject_id`,`school_year`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teaching_id` (`teaching_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT cho bảng `exam_configs`
--
ALTER TABLE `exam_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `lessions`
--
ALTER TABLE `lessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `question_blanks`
--
ALTER TABLE `question_blanks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `quizs`
--
ALTER TABLE `quizs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1190;

--
-- AUTO_INCREMENT cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `teachings`
--
ALTER TABLE `teachings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`teaching_id`) REFERENCES `teachings` (`id`),
  ADD CONSTRAINT `fk_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Các ràng buộc cho bảng `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD CONSTRAINT `exam_answers_ibfk_1` FOREIGN KEY (`exam_result_id`) REFERENCES `exam_results` (`id`),
  ADD CONSTRAINT `exam_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `exam_answers_ibfk_3` FOREIGN KEY (`question_blank_id`) REFERENCES `question_blanks` (`id`);

--
-- Các ràng buộc cho bảng `exam_configs`
--
ALTER TABLE `exam_configs`
  ADD CONSTRAINT `exam_configs_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Các ràng buộc cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`lession_id`) REFERENCES `lessions` (`id`);

--
-- Các ràng buộc cho bảng `lessions`
--
ALTER TABLE `lessions`
  ADD CONSTRAINT `lessions_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`lession_id`) REFERENCES `lessions` (`id`);

--
-- Các ràng buộc cho bảng `question_blanks`
--
ALTER TABLE `question_blanks`
  ADD CONSTRAINT `question_blanks_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Các ràng buộc cho bảng `quizs`
--
ALTER TABLE `quizs`
  ADD CONSTRAINT `quizs_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Các ràng buộc cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`);

--
-- Các ràng buộc cho bảng `teachings`
--
ALTER TABLE `teachings`
  ADD CONSTRAINT `teaching_subject_fk` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teaching_teacher_fk` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teachings_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `teachings_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`teaching_id`) REFERENCES `teachings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
