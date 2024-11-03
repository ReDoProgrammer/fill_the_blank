-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 06, 2024 lúc 08:51 PM
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
CREATE DEFINER=`root`@`localhost` FUNCTION `convertToSlug` (`str` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE result VARCHAR(255) DEFAULT '';

    -- Chuyển đổi chuỗi sang chữ thường
    SET str = LOWER(str);

    -- Xóa bỏ các dấu trong chuỗi
    SET str = REPLACE(str, 'à', 'a');
    SET str = REPLACE(str, 'á', 'a');
    SET str = REPLACE(str, 'ạ', 'a');
    SET str = REPLACE(str, 'ả', 'a');
    SET str = REPLACE(str, 'ã', 'a');
    SET str = REPLACE(str, 'â', 'a');
    SET str = REPLACE(str, 'ầ', 'a');
    SET str = REPLACE(str, 'ấ', 'a');
    SET str = REPLACE(str, 'ậ', 'a');
    SET str = REPLACE(str, 'ẩ', 'a');
    SET str = REPLACE(str, 'ẫ', 'a');
    SET str = REPLACE(str, 'ă', 'a');
    SET str = REPLACE(str, 'ằ', 'a');
    SET str = REPLACE(str, 'ắ', 'a');
    SET str = REPLACE(str, 'ặ', 'a');
    SET str = REPLACE(str, 'ẳ', 'a');
    SET str = REPLACE(str, 'ẵ', 'a');

    SET str = REPLACE(str, 'è', 'e');
    SET str = REPLACE(str, 'é', 'e');
    SET str = REPLACE(str, 'ẹ', 'e');
    SET str = REPLACE(str, 'ẻ', 'e');
    SET str = REPLACE(str, 'ẽ', 'e');
    SET str = REPLACE(str, 'ê', 'e');
    SET str = REPLACE(str, 'ề', 'e');
    SET str = REPLACE(str, 'ế', 'e');
    SET str = REPLACE(str, 'ệ', 'e');
    SET str = REPLACE(str, 'ể', 'e');
    SET str = REPLACE(str, 'ễ', 'e');

    SET str = REPLACE(str, 'ì', 'i');
    SET str = REPLACE(str, 'í', 'i');
    SET str = REPLACE(str, 'ị', 'i');
    SET str = REPLACE(str, 'ỉ', 'i');
    SET str = REPLACE(str, 'ĩ', 'i');

    SET str = REPLACE(str, 'ò', 'o');
    SET str = REPLACE(str, 'ó', 'o');
    SET str = REPLACE(str, 'ọ', 'o');
    SET str = REPLACE(str, 'ỏ', 'o');
    SET str = REPLACE(str, 'õ', 'o');
    SET str = REPLACE(str, 'ô', 'o');
    SET str = REPLACE(str, 'ồ', 'o');
    SET str = REPLACE(str, 'ố', 'o');
    SET str = REPLACE(str, 'ộ', 'o');
    SET str = REPLACE(str, 'ổ', 'o');
    SET str = REPLACE(str, 'ỗ', 'o');
    SET str = REPLACE(str, 'ơ', 'o');
    SET str = REPLACE(str, 'ờ', 'o');
    SET str = REPLACE(str, 'ớ', 'o');
    SET str = REPLACE(str, 'ợ', 'o');
    SET str = REPLACE(str, 'ở', 'o');
    SET str = REPLACE(str, 'ỡ', 'o');

    SET str = REPLACE(str, 'ù', 'u');
    SET str = REPLACE(str, 'ú', 'u');
    SET str = REPLACE(str, 'ụ', 'u');
    SET str = REPLACE(str, 'ủ', 'u');
    SET str = REPLACE(str, 'ũ', 'u');
    SET str = REPLACE(str, 'ư', 'u');
    SET str = REPLACE(str, 'ừ', 'u');
    SET str = REPLACE(str, 'ứ', 'u');
    SET str = REPLACE(str, 'ự', 'u');
    SET str = REPLACE(str, 'ử', 'u');
    SET str = REPLACE(str, 'ữ', 'u');

    SET str = REPLACE(str, 'ỳ', 'y');
    SET str = REPLACE(str, 'ý', 'y');
    SET str = REPLACE(str, 'ỵ', 'y');
    SET str = REPLACE(str, 'ỷ', 'y');
    SET str = REPLACE(str, 'ỹ', 'y');

    SET str = REPLACE(str, 'đ', 'd');

    -- Thay thế các ký tự đặc biệt và khoảng trắng bằng dấu "-"
    SET str = REGEXP_REPLACE(str, '[^a-z0-9]+', '-');

    -- Loại bỏ dấu "-" ở đầu và cuối chuỗi (nếu có)
    SET str = TRIM(BOTH '-' FROM str);

    RETURN str;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `number_of_questions` int(1) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL,
  `mode` int(1) NOT NULL DEFAULT 0 COMMENT 'Nếu lớn hơn 0 sẽ là id của config\r\nNếu 0 <=> random\r\nNếu -1 <=> tuỳ chỉnh',
  `thumbnail` varchar(200) NOT NULL,
  `begin_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `subject_id` int(11) NOT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exams`
--

INSERT INTO `exams` (`id`, `title`, `description`, `number_of_questions`, `duration`, `mode`, `thumbnail`, `begin_date`, `end_date`, `subject_id`, `questions`) VALUES
(1, 'fadf ákj ', '<p>fdsafdsa</p>', 30, 60, 0, '', '2024-10-06 22:45:00', '2024-10-29 23:45:00', 12, '[648,654,670,700,691,667,678,659,684,710,706,647,702,655,699,705,669,646,652,704,690,673,653,692,661,681,664,695,665,689]'),
(2, 'fdasfas', '<p>fdsafdasf</p>', 10, 15, 0, '', '2024-10-06 22:52:00', '2024-10-06 23:52:00', 7, '[592,589,584,595,596,634,610,620,637,597]'),
(3, '1 phút', '<p>dfsa</p>', 10, 1, 0, '', '2024-10-06 23:35:00', '2024-10-07 00:35:00', 12, '[655,671,685,707,668,686,669,702,653,656]'),
(4, '10 câu 1 phút test luôn tính năng tự động nộp bài', '<p><br></p>', 10, 1, 0, '/public/upload/images/417e93fe6b59aaf8e3fae0f72c53013c.jpg', '2024-10-07 00:06:00', '2024-10-31 01:06:00', 12, '[681,655,690,675,709,695,672,702,687,679]');

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
(1, 1, 2, 2, 'link'),
(2, 1, 2, 3, 'img'),
(3, 1, 2, 4, '</body>'),
(4, 1, 1, 1, 'gdsfgfsd');

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
(13, 'môn asp', 12, '[{\"quantity\":2,\"mark\":1},{\"quantity\":1,\"mark\":1.5},{\"quantity\":3,\"mark\":2},{\"quantity\":2,\"mark\":4},{\"quantity\":2,\"mark\":5}]');

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
(1, 2, 7, 21, '2024-10-06 17:44:57');

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
(1, 'Id', 'id', 7, '2024-07-24 13:39:14', '2024-07-26 08:57:38'),
(2, 'Label', 'label', 7, '2024-07-24 13:42:00', '2024-07-26 08:57:38'),
(3, 'gfsdgsdfgfdsgds', 'gfsdgsdfgfdsgds', 12, '2024-07-24 13:42:07', '2024-07-25 08:27:36'),
(4, 'gfdsgfsdgsd', 'gfdsgfsdgsd', 7, '2024-07-24 13:54:13', '2024-07-25 08:27:36'),
(5, 'Sử dụng Kotlin', 'su-dung-kotlin', 7, '2024-07-24 13:54:20', '2024-07-26 08:57:38'),
(6, 'Kết nối CSDL', 'ket-noi-csdl', 7, '2024-07-24 13:57:09', '2024-07-26 08:57:38'),
(7, 'GridLayout', 'gridlayout', 7, '2024-07-24 13:57:14', '2024-07-26 08:57:38'),
(8, 'XML', 'xml', 7, '2024-07-24 13:57:19', '2024-07-26 08:57:38'),
(9, 'Lập trình Android là gì?', 'lap-trinh-android-la-gi', 7, '2024-07-24 13:57:24', '2024-07-26 08:57:38'),
(10, 'Layout', 'layout', 7, '2024-07-24 13:57:27', '2024-07-26 08:57:38'),
(11, 'TextEdit', 'textedit', 7, '2024-07-24 13:57:31', '2024-07-26 08:57:38'),
(12, 'Hướng đối tượng OOP', 'huong-doi-tuong-oop', 7, '2024-07-24 13:57:36', '2024-07-26 08:57:38'),
(21, 'Asset trong Android', 'asset-trong-android', 7, '2024-07-26 08:50:54', '2024-07-26 08:56:51');

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
(1, 21, '<p>ffdsfdasfdsafdsaq ___</p>', '2024-10-06 15:55:42', '2024-10-06 15:55:42'),
(2, 21, '<p>Chèn các thẻ html cần thiết vào các đoạn trống sau:</p><p>&lt;html&gt;</p><p>&lt;body&gt;</p><p>		___ dùng để định liên kết file css ngoài</p><p>		___ dùng để liên kết hình ảnh</p><p>___</p>', '2024-10-06 17:42:33', '2024-10-06 17:42:33'),
(3, 21, '<p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">!DOCTYPE</span><span style=\"color: red;\">&nbsp;html</span><span style=\"color: mediumblue;\">&gt;</span></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">html</span><span style=\"color: mediumblue;\">&gt;</span></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">head</span><span style=\"color: mediumblue;\">&gt;</span></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">title</span><span style=\"color: mediumblue;\">&gt;</span><span style=\"color: rgb(0, 0, 0);\">Page Title___</span></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">/head</span><span style=\"color: mediumblue;\">&gt;</span></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">body</span><span style=\"color: mediumblue;\">&gt;</span></p><p><br></p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">h1</span><span style=\"color: mediumblue;\">&gt;</span><span style=\"color: rgb(0, 0, 0);\">This is a Heading</span><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">/h1</span><span style=\"color: mediumblue;\">&gt;</span></p><p><span style=\"color: mediumblue;\">&lt;___&gt;</span><span style=\"color: rgb(0, 0, 0);\">This is a paragraph.___</span></p><p><br></p><p>___</p><p><span style=\"color: mediumblue;\">&lt;</span><span style=\"color: brown;\">/html</span><span style=\"color: mediumblue;\">&gt;</span></p>', '2024-10-06 17:50:02', '2024-10-06 17:50:02');

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
(1, 1, 1, 'fdasfadsfas'),
(2, 2, 1, 'link'),
(3, 2, 2, 'img'),
(4, 2, 3, '</body>'),
(5, 3, 1, '</title>'),
(6, 3, 2, 'p'),
(7, 3, 3, '</p>'),
(8, 3, 4, '</body>');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quizs`
--

CREATE TABLE `quizs` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `question` varchar(255) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `mark` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quizs`
--

INSERT INTO `quizs` (`id`, `subject_id`, `question`, `options`, `mark`) VALUES
(579, 7, 'Thuộc tính nào dùng để thay đổi màu nền của phần tử?', '{\"option_1\":\"color\",\"option_2\":\"background-color\",\"option_3\":\"background\",\"option_4\":\"bgcolor\",\"correct_option\":2}', 2),
(580, 7, 'Thuộc tính CSS nào dùng để thay đổi kích thước chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"text-size\",\"option_3\":\"font-size\",\"option_4\":\"size\",\"correct_option\":3}', 2.25),
(581, 7, 'Cách nào để thêm CSS vào trang HTML?', '{\"option_1\":\"Ch\\u1ec9 d\\u00f9ng th\\u1ebb <style> trong <head>\",\"option_2\":\"Ch\\u1ec9 d\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i\",\"option_3\":\"D\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i, th\\u1ebb <style> ho\\u1eb7c thu\\u1ed9c t\\u00ednh style\",\"option_4\":\"Ch\\u1ec9 d\\u00f9ng thu\\u1ed9c t\\u00ednh style\",\"correct_option\":3}', 3.5),
(582, 7, 'Thuộc tính nào dùng để căn lề cho phần tử?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border\",\"option_4\":\"align\",\"correct_option\":2}', 2.25),
(583, 7, 'Thuộc tính nào dùng để đặt khoảng cách bên trong giữa nội dung và viền của phần tử?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"spacing\",\"correct_option\":2}', 5),
(584, 7, 'Cú pháp đúng để liên kết tệp CSS ngoài với HTML là gì?', '{\"option_1\":\"<link rel=\'stylesheet\' href=\'style.css\'>\",\"option_2\":\"<style src=\'style.css\'>\",\"option_3\":\"<stylesheet>style.css<\\/stylesheet>\",\"option_4\":\"<link src=\'style.css\'>\",\"correct_option\":1}', 1.5),
(585, 7, 'Thuộc tính CSS nào dùng để tạo bóng cho phần tử?', '{\"option_1\":\"text-shadow\",\"option_2\":\"box-shadow\",\"option_3\":\"shadow\",\"option_4\":\"filter-shadow\",\"correct_option\":2}', 1.25),
(586, 7, 'CSS viết tắt của cụm từ nào?', '{\"option_1\":\"Colorful Style Sheets\",\"option_2\":\"Computer Style Sheets\",\"option_3\":\"Cascading Style Sheets\",\"option_4\":\"Creative Style Sheets\",\"correct_option\":3}', 4.25),
(587, 7, 'Thuộc tính nào dùng để thay đổi kiểu chữ của văn bản?', '{\"option_1\":\"font-style\",\"option_2\":\"text-transform\",\"option_3\":\"text-decoration\",\"option_4\":\"font-weight\",\"correct_option\":1}', 3.75),
(588, 7, 'Thuộc tính CSS nào dùng để thay đổi chiều rộng của phần tử?', '{\"option_1\":\"height\",\"option_2\":\"padding\",\"option_3\":\"width\",\"option_4\":\"size\",\"correct_option\":3}', 0.5),
(589, 7, 'Cú pháp đúng để viết CSS nội tuyến là gì?', '{\"option_1\":\"<style>{color:red;}<\\/style>\",\"option_2\":\"<div style=\'color: red;\'>\",\"option_3\":\"<css=\'color: red;\'>\",\"option_4\":\"<style: \'color: red;\'>\",\"correct_option\":2}', 5),
(590, 7, 'Thuộc tính nào dùng để căn chỉnh văn bản theo chiều ngang?', '{\"option_1\":\"vertical-align\",\"option_2\":\"text-align\",\"option_3\":\"text-justify\",\"option_4\":\"line-height\",\"correct_option\":2}', 4),
(591, 7, 'Thuộc tính nào dùng để làm mờ một phần tử?', '{\"option_1\":\"opacity\",\"option_2\":\"blur\",\"option_3\":\"visibility\",\"option_4\":\"display\",\"correct_option\":1}', 4.5),
(592, 7, 'Cú pháp đúng để chọn tất cả phần tử <p> trong CSS là gì?', '{\"option_1\":\"p.all\",\"option_2\":\"*p\",\"option_3\":\"p\",\"option_4\":\".p\",\"correct_option\":3}', 4),
(593, 7, 'Đơn vị nào được dùng để định nghĩa kích thước linh hoạt dựa trên kích thước của phần tử gốc?', '{\"option_1\":\"em\",\"option_2\":\"px\",\"option_3\":\"rem\",\"option_4\":\"%\",\"correct_option\":1}', 3),
(594, 7, 'Thuộc tính CSS nào dùng để tạo khoảng cách giữa các dòng văn bản?', '{\"option_1\":\"letter-spacing\",\"option_2\":\"word-spacing\",\"option_3\":\"line-height\",\"option_4\":\"text-spacing\",\"correct_option\":3}', 1.25),
(595, 7, 'Thuộc tính nào dùng để ẩn phần tử nhưng vẫn giữ không gian của nó?', '{\"option_1\":\"display: none\",\"option_2\":\"visibility: hidden\",\"option_3\":\"opacity: 0\",\"option_4\":\"filter: hidden\",\"correct_option\":2}', 0.25),
(596, 7, 'Thứ tự đúng của mô hình hộp (box model) trong CSS là gì?', '{\"option_1\":\"Margin -> Padding -> Border -> Content\",\"option_2\":\"Padding -> Border -> Margin -> Content\",\"option_3\":\"Content -> Padding -> Border -> Margin\",\"option_4\":\"Content -> Border -> Padding -> Margin\",\"correct_option\":3}', 1),
(597, 7, 'Thuộc tính nào dùng để tạo nền cho phần tử?', '{\"option_1\":\"background\",\"option_2\":\"color\",\"option_3\":\"border\",\"option_4\":\"padding\",\"correct_option\":1}', 1.25),
(598, 7, 'Cú pháp đúng để thêm một lớp CSS là gì?', '{\"option_1\":\".classname\",\"option_2\":\"#classname\",\"option_3\":\"classname\",\"option_4\":\"class.classname\",\"correct_option\":1}', 3.25),
(599, 7, 'Thuộc tính nào dùng để thay đổi màu nền của phần tử?', '{\"option_1\":\"color\",\"option_2\":\"background-color\",\"option_3\":\"background\",\"option_4\":\"bgcolor\",\"correct_option\":2}', 2.25),
(600, 7, 'Thuộc tính CSS nào dùng để thay đổi kích thước chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"text-size\",\"option_3\":\"font-size\",\"option_4\":\"size\",\"correct_option\":3}', 2.25),
(601, 7, 'Cách nào để thêm CSS vào trang HTML?', '{\"option_1\":\"Ch\\u1ec9 d\\u00f9ng th\\u1ebb <style> trong <head>\",\"option_2\":\"Ch\\u1ec9 d\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i\",\"option_3\":\"D\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i, th\\u1ebb <style> ho\\u1eb7c thu\\u1ed9c t\\u00ednh style\",\"option_4\":\"Ch\\u1ec9 d\\u00f9ng thu\\u1ed9c t\\u00ednh style\",\"correct_option\":3}', 0.5),
(602, 7, 'Thuộc tính nào dùng để căn lề cho phần tử?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border\",\"option_4\":\"align\",\"correct_option\":2}', 3),
(603, 7, 'Thuộc tính nào dùng để đặt khoảng cách bên trong giữa nội dung và viền của phần tử?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"spacing\",\"correct_option\":2}', 0.75),
(604, 7, 'Cú pháp đúng để liên kết tệp CSS ngoài với HTML là gì?', '{\"option_1\":\"<link rel=\'stylesheet\' href=\'style.css\'>\",\"option_2\":\"<style src=\'style.css\'>\",\"option_3\":\"<stylesheet>style.css<\\/stylesheet>\",\"option_4\":\"<link src=\'style.css\'>\",\"correct_option\":1}', 2),
(605, 7, 'Thuộc tính CSS nào dùng để tạo bóng cho phần tử?', '{\"option_1\":\"text-shadow\",\"option_2\":\"box-shadow\",\"option_3\":\"shadow\",\"option_4\":\"filter-shadow\",\"correct_option\":2}', 0.75),
(606, 7, 'CSS viết tắt của cụm từ nào?', '{\"option_1\":\"Colorful Style Sheets\",\"option_2\":\"Computer Style Sheets\",\"option_3\":\"Cascading Style Sheets\",\"option_4\":\"Creative Style Sheets\",\"correct_option\":3}', 3),
(607, 7, 'Thuộc tính nào dùng để thay đổi kiểu chữ của văn bản?', '{\"option_1\":\"font-style\",\"option_2\":\"text-transform\",\"option_3\":\"text-decoration\",\"option_4\":\"font-weight\",\"correct_option\":1}', 2),
(608, 7, 'Thuộc tính CSS nào dùng để thay đổi chiều rộng của phần tử?', '{\"option_1\":\"height\",\"option_2\":\"padding\",\"option_3\":\"width\",\"option_4\":\"size\",\"correct_option\":3}', 2.75),
(609, 7, 'Cú pháp đúng để viết CSS nội tuyến là gì?', '{\"option_1\":\"<style>{color:red;}<\\/style>\",\"option_2\":\"<div style=\'color: red;\'>\",\"option_3\":\"<css=\'color: red;\'>\",\"option_4\":\"<style: \'color: red;\'>\",\"correct_option\":2}', 0.25),
(610, 7, 'Thuộc tính nào dùng để căn chỉnh văn bản theo chiều ngang?', '{\"option_1\":\"vertical-align\",\"option_2\":\"text-align\",\"option_3\":\"text-justify\",\"option_4\":\"line-height\",\"correct_option\":2}', 1.75),
(611, 7, 'Thuộc tính nào dùng để làm mờ một phần tử?', '{\"option_1\":\"opacity\",\"option_2\":\"blur\",\"option_3\":\"visibility\",\"option_4\":\"display\",\"correct_option\":1}', 1.5),
(612, 7, 'Cú pháp đúng để chọn tất cả phần tử <p> trong CSS là gì?', '{\"option_1\":\"p.all\",\"option_2\":\"*p\",\"option_3\":\"p\",\"option_4\":\".p\",\"correct_option\":3}', 3),
(613, 7, 'Đơn vị nào được dùng để định nghĩa kích thước linh hoạt dựa trên kích thước của phần tử gốc?', '{\"option_1\":\"em\",\"option_2\":\"px\",\"option_3\":\"rem\",\"option_4\":\"%\",\"correct_option\":1}', 0.75),
(614, 7, 'Thuộc tính CSS nào dùng để tạo khoảng cách giữa các dòng văn bản?', '{\"option_1\":\"letter-spacing\",\"option_2\":\"word-spacing\",\"option_3\":\"line-height\",\"option_4\":\"text-spacing\",\"correct_option\":3}', 1),
(615, 7, 'Thuộc tính nào dùng để ẩn phần tử nhưng vẫn giữ không gian của nó?', '{\"option_1\":\"display: none\",\"option_2\":\"visibility: hidden\",\"option_3\":\"opacity: 0\",\"option_4\":\"filter: hidden\",\"correct_option\":2}', 2.5),
(616, 7, 'Thứ tự đúng của mô hình hộp (box model) trong CSS là gì?', '{\"option_1\":\"Margin -> Padding -> Border -> Content\",\"option_2\":\"Padding -> Border -> Margin -> Content\",\"option_3\":\"Content -> Padding -> Border -> Margin\",\"option_4\":\"Content -> Border -> Padding -> Margin\",\"correct_option\":3}', 2.5),
(617, 7, 'Thuộc tính nào dùng để tạo nền cho phần tử?', '{\"option_1\":\"background\",\"option_2\":\"color\",\"option_3\":\"border\",\"option_4\":\"padding\",\"correct_option\":1}', 4.5),
(618, 7, 'Cú pháp đúng để thêm một lớp CSS là gì?', '{\"option_1\":\".classname\",\"option_2\":\"#classname\",\"option_3\":\"classname\",\"option_4\":\"class.classname\",\"correct_option\":1}', 0.25),
(619, 7, 'Thuộc tính nào dùng để thay đổi màu nền của phần tử?', '{\"option_1\":\"color\",\"option_2\":\"background-color\",\"option_3\":\"background\",\"option_4\":\"bgcolor\",\"correct_option\":2}', 4.75),
(620, 7, 'Thuộc tính CSS nào dùng để thay đổi kích thước chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"text-size\",\"option_3\":\"font-size\",\"option_4\":\"size\",\"correct_option\":3}', 2.5),
(621, 7, 'Cách nào để thêm CSS vào trang HTML?', '{\"option_1\":\"Ch\\u1ec9 d\\u00f9ng th\\u1ebb <style> trong <head>\",\"option_2\":\"Ch\\u1ec9 d\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i\",\"option_3\":\"D\\u00f9ng t\\u1ec7p CSS ngo\\u00e0i, th\\u1ebb <style> ho\\u1eb7c thu\\u1ed9c t\\u00ednh style\",\"option_4\":\"Ch\\u1ec9 d\\u00f9ng thu\\u1ed9c t\\u00ednh style\",\"correct_option\":3}', 4.25),
(622, 7, 'Thuộc tính nào dùng để căn lề cho phần tử?', '{\"option_1\":\"padding\",\"option_2\":\"margin\",\"option_3\":\"border\",\"option_4\":\"align\",\"correct_option\":2}', 4.25),
(623, 7, 'Thuộc tính nào dùng để đặt khoảng cách bên trong giữa nội dung và viền của phần tử?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"spacing\",\"correct_option\":2}', 3),
(624, 7, 'Cú pháp đúng để liên kết tệp CSS ngoài với HTML là gì?', '{\"option_1\":\"<link rel=\'stylesheet\' href=\'style.css\'>\",\"option_2\":\"<style src=\'style.css\'>\",\"option_3\":\"<stylesheet>style.css<\\/stylesheet>\",\"option_4\":\"<link src=\'style.css\'>\",\"correct_option\":1}', 4),
(625, 7, 'Thuộc tính CSS nào dùng để tạo bóng cho phần tử?', '{\"option_1\":\"text-shadow\",\"option_2\":\"box-shadow\",\"option_3\":\"shadow\",\"option_4\":\"filter-shadow\",\"correct_option\":2}', 4.5),
(626, 7, 'CSS viết tắt của cụm từ nào?', '{\"option_1\":\"Colorful Style Sheets\",\"option_2\":\"Computer Style Sheets\",\"option_3\":\"Cascading Style Sheets\",\"option_4\":\"Creative Style Sheets\",\"correct_option\":3}', 0.25),
(627, 7, 'Thuộc tính nào dùng để thay đổi kiểu chữ của văn bản?', '{\"option_1\":\"font-style\",\"option_2\":\"text-transform\",\"option_3\":\"text-decoration\",\"option_4\":\"font-weight\",\"correct_option\":1}', 1.25),
(628, 7, 'Thuộc tính CSS nào dùng để thay đổi chiều rộng của phần tử?', '{\"option_1\":\"height\",\"option_2\":\"padding\",\"option_3\":\"width\",\"option_4\":\"size\",\"correct_option\":3}', 3.25),
(629, 7, 'Thuộc tính nào dùng để thay đổi màu nền trong CSS?', '{\"option_1\":\"color\",\"option_2\":\"background-color\",\"option_3\":\"background\",\"option_4\":\"border-color\",\"correct_option\":2}', 3.75),
(630, 7, 'Thuộc tính nào dùng để thay đổi cỡ chữ trong CSS?', '{\"option_1\":\"font-weight\",\"option_2\":\"font-style\",\"option_3\":\"font-size\",\"option_4\":\"text-size\",\"correct_option\":3}', 3),
(631, 7, 'Thuộc tính nào dùng để căn chỉnh văn bản ở giữa trong CSS?', '{\"option_1\":\"vertical-align\",\"option_2\":\"text-align\",\"option_3\":\"align-content\",\"option_4\":\"align-items\",\"correct_option\":2}', 2.25),
(632, 7, 'Lựa chọn nào dưới đây là bộ chọn CSS hợp lệ?', '{\"option_1\":\"#id\",\"option_2\":\".class\",\"option_3\":\"*\",\"option_4\":\"T\\u1ea5t c\\u1ea3 c\\u00e1c l\\u1ef1a ch\\u1ecdn tr\\u00ean\",\"correct_option\":4}', 4.5),
(633, 7, 'Thuộc tính nào dùng để thay đổi độ trong suốt của phần tử?', '{\"option_1\":\"opacity\",\"option_2\":\"filter\",\"option_3\":\"visibility\",\"option_4\":\"display\",\"correct_option\":1}', 5),
(634, 7, 'Thuộc tính nào dùng để tạo khoảng cách bên trong phần tử (padding)?', '{\"option_1\":\"margin\",\"option_2\":\"padding\",\"option_3\":\"border\",\"option_4\":\"outline\",\"correct_option\":2}', 1.5),
(635, 7, 'Thuộc tính nào dùng để ẩn phần tử nhưng vẫn giữ không gian trên trang?', '{\"option_1\":\"display\",\"option_2\":\"visibility\",\"option_3\":\"opacity\",\"option_4\":\"z-index\",\"correct_option\":2}', 0.25),
(636, 7, 'Thuộc tính nào dùng để thay đổi loại phông chữ?', '{\"option_1\":\"font-style\",\"option_2\":\"font-family\",\"option_3\":\"font-weight\",\"option_4\":\"text-font\",\"correct_option\":2}', 1.5),
(637, 7, 'Thuộc tính nào dùng để tạo đường viền xung quanh phần tử?', '{\"option_1\":\"border\",\"option_2\":\"outline\",\"option_3\":\"margin\",\"option_4\":\"padding\",\"correct_option\":1}', 2),
(638, 7, 'Thuộc tính nào trong CSS dùng để thay đổi font chữ?', '{\"option_1\":\"font-family\",\"option_2\":\"font-style\",\"option_3\":\"text-font\",\"option_4\":\"font-weight\",\"correct_option\":1}', 2.25),
(639, 7, 'CSS có thể được nhúng trực tiếp vào HTML bằng thẻ nào?', '{\"option_1\":\"<script>\",\"option_2\":\"<link>\",\"option_3\":\"<style>\",\"option_4\":\"<css>\",\"correct_option\":3}', 5),
(640, 7, 'Thuộc tính nào dùng để đặt khoảng cách giữa các từ trong CSS?', '{\"option_1\":\"word-spacing\",\"option_2\":\"letter-spacing\",\"option_3\":\"line-height\",\"option_4\":\"text-spacing\",\"correct_option\":1}', 1.75),
(641, 7, 'Giá trị nào của thuộc tính display ẩn phần tử và không chiếm không gian trên trang?', '{\"option_1\":\"none\",\"option_2\":\"hidden\",\"option_3\":\"block\",\"option_4\":\"inline-block\",\"correct_option\":1}', 4.5),
(642, 7, 'Thuộc tính nào trong CSS dùng để thay đổi font chữ?', '{\"option_1\":\"font-family\",\"option_2\":\"font-style\",\"option_3\":\"text-font\",\"option_4\":\"font-weight\",\"correct_option\":1}', 0.25),
(643, 7, 'CSS có thể được nhúng trực tiếp vào HTML bằng thẻ nào?', '{\"option_1\":\"<script>\",\"option_2\":\"<link>\",\"option_3\":\"<style>\",\"option_4\":\"<css>\",\"correct_option\":3}', 3),
(644, 7, 'Thuộc tính nào dùng để đặt khoảng cách giữa các từ trong CSS?', '{\"option_1\":\"word-spacing\",\"option_2\":\"letter-spacing\",\"option_3\":\"line-height\",\"option_4\":\"text-spacing\",\"correct_option\":1}', 2.5),
(645, 7, 'Giá trị nào của thuộc tính display ẩn phần tử và không chiếm không gian trên trang?', '{\"option_1\":\"none\",\"option_2\":\"hidden\",\"option_3\":\"block\",\"option_4\":\"inline-block\",\"correct_option\":1}', 0.75),
(646, 12, 'HTML là viết tắt của từ gì?', '{\"option_1\":\"Hyper Trainer Marking Language\",\"option_2\":\"Hyper Text Markup Language\",\"option_3\":\"Hyper Text Marketing Language\",\"option_4\":\"Hyper Tech Markup Language\",\"correct_option\":2}', 4.25),
(647, 12, 'Thẻ <h1> dùng để làm gì trong HTML?', '{\"option_1\":\"\\u0110\\u1ecbnh d\\u1ea1ng \\u0111o\\u1ea1n v\\u0103n\",\"option_2\":\"Hi\\u1ec3n th\\u1ecb ti\\u00eau \\u0111\\u1ec1 c\\u1ea5p 1\",\"option_3\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_4\":\"T\\u1ea1o danh s\\u00e1ch kh\\u00f4ng th\\u1ee9 t\\u1ef1\",\"correct_option\":2}', 0.25),
(648, 12, 'Thẻ nào dùng để tạo liên kết trong HTML?', '{\"option_1\":\"<a>\",\"option_2\":\"<link>\",\"option_3\":\"<href>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 2),
(649, 12, 'Thẻ nào được sử dụng để tạo danh sách không thứ tự?', '{\"option_1\":\"<ul>\",\"option_2\":\"<li>\",\"option_3\":\"<ol>\",\"option_4\":\"<dl>\",\"correct_option\":1}', 3.75),
(650, 12, 'Đâu là thuộc tính của thẻ <img> dùng để hiển thị văn bản thay thế khi ảnh không hiển thị?', '{\"option_1\":\"title\",\"option_2\":\"alt\",\"option_3\":\"src\",\"option_4\":\"href\",\"correct_option\":2}', 2.75),
(651, 12, 'Thuộc tính nào dùng để định nghĩa đường dẫn của ảnh trong thẻ <img>?', '{\"option_1\":\"src\",\"option_2\":\"alt\",\"option_3\":\"href\",\"option_4\":\"link\",\"correct_option\":1}', 1),
(652, 12, 'Thẻ <br> dùng để làm gì trong HTML?', '{\"option_1\":\"T\\u1ea1o d\\u00f2ng ngang\",\"option_2\":\"T\\u1ea1o d\\u00f2ng m\\u1edbi\",\"option_3\":\"T\\u1ea1o m\\u1ed9t \\u0111o\\u1ea1n v\\u0103n\",\"option_4\":\"T\\u1ea1o m\\u1ed9t n\\u00fat b\\u1ea5m\",\"correct_option\":2}', 4.25),
(653, 12, 'Thẻ nào dùng để tạo bảng trong HTML?', '{\"option_1\":\"<table>\",\"option_2\":\"<tab>\",\"option_3\":\"<tr>\",\"option_4\":\"<td>\",\"correct_option\":1}', 0.5),
(654, 12, 'Thẻ <iframe> dùng để làm gì trong HTML?', '{\"option_1\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_2\":\"Ch\\u00e8n \\u0111o\\u1ea1n video\",\"option_3\":\"Ch\\u00e8n m\\u1ed9t trang web kh\\u00e1c v\\u00e0o trang hi\\u1ec7n t\\u1ea1i\",\"option_4\":\"T\\u1ea1o khung v\\u0103n b\\u1ea3n\",\"correct_option\":3}', 0.25),
(655, 12, 'Thuộc tính nào dùng để thay đổi màu nền của trang?', '{\"option_1\":\"background\",\"option_2\":\"bgcolor\",\"option_3\":\"color\",\"option_4\":\"background-color\",\"correct_option\":4}', 1),
(656, 12, 'Đâu là thẻ HTML5 để nhúng video vào trang web?', '{\"option_1\":\"<embed>\",\"option_2\":\"<video>\",\"option_3\":\"<source>\",\"option_4\":\"<audio>\",\"correct_option\":2}', 3.25),
(657, 12, 'Thẻ <head> chứa nội dung nào sau đây?', '{\"option_1\":\"Ti\\u00eau \\u0111\\u1ec1 trang (th\\u1ebb <title>)\",\"option_2\":\"N\\u1ed9i dung trang ch\\u00ednh\",\"option_3\":\"H\\u00ecnh \\u1ea3nh v\\u00e0 li\\u00ean k\\u1ebft\",\"option_4\":\"Danh s\\u00e1ch v\\u00e0 b\\u1ea3ng\",\"correct_option\":1}', 1.5),
(658, 12, 'Thẻ nào dùng để định nghĩa tiêu đề của trang web?', '{\"option_1\":\"<title>\",\"option_2\":\"<head>\",\"option_3\":\"<header>\",\"option_4\":\"<meta>\",\"correct_option\":1}', 1.25),
(659, 12, 'Đâu là thuộc tính của thẻ <a> để mở liên kết trong tab mới?', '{\"option_1\":\"href\",\"option_2\":\"target=\'_blank\'\",\"option_3\":\"rel=\'noopener\'\",\"option_4\":\"name=\'_new\'\",\"correct_option\":2}', 3.5),
(660, 12, 'Thuộc tính nào của thẻ <input> dùng để nhập mật khẩu?', '{\"option_1\":\"type=\'text\'\",\"option_2\":\"type=\'password\'\",\"option_3\":\"type=\'email\'\",\"option_4\":\"type=\'submit\'\",\"correct_option\":2}', 0.5),
(661, 12, 'Thẻ <meta> thường được sử dụng cho mục đích gì?', '{\"option_1\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_2\":\"Nh\\u00fang video\",\"option_3\":\"\\u0110\\u1ecbnh ngh\\u0129a th\\u00f4ng tin v\\u1ec1 t\\u00e0i li\\u1ec7u HTML\",\"option_4\":\"T\\u1ea1o li\\u00ean k\\u1ebft\",\"correct_option\":3}', 1.25),
(662, 12, 'Thẻ nào dùng để tạo dòng ngang trong HTML?', '{\"option_1\":\"<hr>\",\"option_2\":\"<br>\",\"option_3\":\"<line>\",\"option_4\":\"<div>\",\"correct_option\":1}', 3),
(663, 12, 'Thẻ nào được dùng để chèn âm thanh vào trang web?', '{\"option_1\":\"<audio>\",\"option_2\":\"<sound>\",\"option_3\":\"<voice>\",\"option_4\":\"<music>\",\"correct_option\":1}', 1.25),
(664, 12, 'Thuộc tính nào của thẻ <form> dùng để xác định nơi dữ liệu được gửi đi?', '{\"option_1\":\"action\",\"option_2\":\"method\",\"option_3\":\"enctype\",\"option_4\":\"target\",\"correct_option\":1}', 1.5),
(665, 12, 'Thẻ nào được dùng để nhúng một tệp JavaScript vào trang HTML?', '{\"option_1\":\"<link>\",\"option_2\":\"<script>\",\"option_3\":\"<style>\",\"option_4\":\"<meta>\",\"correct_option\":2}', 2.25),
(666, 12, 'HTML là viết tắt của từ gì?', '{\"option_1\":\"Hyper Trainer Marking Language\",\"option_2\":\"Hyper Text Markup Language\",\"option_3\":\"Hyper Text Marketing Language\",\"option_4\":\"Hyper Tech Markup Language\",\"correct_option\":2}', 2.5),
(667, 12, 'Thẻ <h1> dùng để làm gì trong HTML?', '{\"option_1\":\"\\u0110\\u1ecbnh d\\u1ea1ng \\u0111o\\u1ea1n v\\u0103n\",\"option_2\":\"Hi\\u1ec3n th\\u1ecb ti\\u00eau \\u0111\\u1ec1 c\\u1ea5p 1\",\"option_3\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_4\":\"T\\u1ea1o danh s\\u00e1ch kh\\u00f4ng th\\u1ee9 t\\u1ef1\",\"correct_option\":2}', 4.5),
(668, 12, 'Thẻ nào dùng để tạo liên kết trong HTML?', '{\"option_1\":\"<a>\",\"option_2\":\"<link>\",\"option_3\":\"<href>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 4),
(669, 12, 'Thẻ nào được sử dụng để tạo danh sách không thứ tự?', '{\"option_1\":\"<ul>\",\"option_2\":\"<li>\",\"option_3\":\"<ol>\",\"option_4\":\"<dl>\",\"correct_option\":1}', 3),
(670, 12, 'Đâu là thuộc tính của thẻ <img> dùng để hiển thị văn bản thay thế khi ảnh không hiển thị?', '{\"option_1\":\"title\",\"option_2\":\"alt\",\"option_3\":\"src\",\"option_4\":\"href\",\"correct_option\":2}', 4),
(671, 12, 'Thuộc tính nào dùng để định nghĩa đường dẫn của ảnh trong thẻ <img>?', '{\"option_1\":\"src\",\"option_2\":\"alt\",\"option_3\":\"href\",\"option_4\":\"link\",\"correct_option\":1}', 3.25),
(672, 12, 'Thẻ <br> dùng để làm gì trong HTML?', '{\"option_1\":\"T\\u1ea1o d\\u00f2ng ngang\",\"option_2\":\"T\\u1ea1o d\\u00f2ng m\\u1edbi\",\"option_3\":\"T\\u1ea1o m\\u1ed9t \\u0111o\\u1ea1n v\\u0103n\",\"option_4\":\"T\\u1ea1o m\\u1ed9t n\\u00fat b\\u1ea5m\",\"correct_option\":2}', 3),
(673, 12, 'Thẻ nào dùng để tạo bảng trong HTML?', '{\"option_1\":\"<table>\",\"option_2\":\"<tab>\",\"option_3\":\"<tr>\",\"option_4\":\"<td>\",\"correct_option\":1}', 3.75),
(674, 12, 'Thẻ <iframe> dùng để làm gì trong HTML?', '{\"option_1\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_2\":\"Ch\\u00e8n \\u0111o\\u1ea1n video\",\"option_3\":\"Ch\\u00e8n m\\u1ed9t trang web kh\\u00e1c v\\u00e0o trang hi\\u1ec7n t\\u1ea1i\",\"option_4\":\"T\\u1ea1o khung v\\u0103n b\\u1ea3n\",\"correct_option\":3}', 3.25),
(675, 12, 'Thuộc tính nào dùng để thay đổi màu nền của trang?', '{\"option_1\":\"background\",\"option_2\":\"bgcolor\",\"option_3\":\"color\",\"option_4\":\"background-color\",\"correct_option\":4}', 4.25),
(676, 12, 'Đâu là thẻ HTML5 để nhúng video vào trang web?', '{\"option_1\":\"<embed>\",\"option_2\":\"<video>\",\"option_3\":\"<source>\",\"option_4\":\"<audio>\",\"correct_option\":2}', 2),
(677, 12, 'Thẻ <head> chứa nội dung nào sau đây?', '{\"option_1\":\"Ti\\u00eau \\u0111\\u1ec1 trang (th\\u1ebb <title>)\",\"option_2\":\"N\\u1ed9i dung trang ch\\u00ednh\",\"option_3\":\"H\\u00ecnh \\u1ea3nh v\\u00e0 li\\u00ean k\\u1ebft\",\"option_4\":\"Danh s\\u00e1ch v\\u00e0 b\\u1ea3ng\",\"correct_option\":1}', 2.5),
(678, 12, 'Thẻ nào dùng để định nghĩa tiêu đề của trang web?', '{\"option_1\":\"<title>\",\"option_2\":\"<head>\",\"option_3\":\"<header>\",\"option_4\":\"<meta>\",\"correct_option\":1}', 0.25),
(679, 12, 'Đâu là thuộc tính của thẻ <a> để mở liên kết trong tab mới?', '{\"option_1\":\"href\",\"option_2\":\"target=\'_blank\'\",\"option_3\":\"rel=\'noopener\'\",\"option_4\":\"name=\'_new\'\",\"correct_option\":2}', 5),
(680, 12, 'Thuộc tính nào của thẻ <input> dùng để nhập mật khẩu?', '{\"option_1\":\"type=\'text\'\",\"option_2\":\"type=\'password\'\",\"option_3\":\"type=\'email\'\",\"option_4\":\"type=\'submit\'\",\"correct_option\":2}', 1.75),
(681, 12, 'Thẻ <meta> thường được sử dụng cho mục đích gì?', '{\"option_1\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_2\":\"Nh\\u00fang video\",\"option_3\":\"\\u0110\\u1ecbnh ngh\\u0129a th\\u00f4ng tin v\\u1ec1 t\\u00e0i li\\u1ec7u HTML\",\"option_4\":\"T\\u1ea1o li\\u00ean k\\u1ebft\",\"correct_option\":3}', 4),
(682, 12, 'Thẻ nào dùng để tạo dòng ngang trong HTML?', '{\"option_1\":\"<hr>\",\"option_2\":\"<br>\",\"option_3\":\"<line>\",\"option_4\":\"<div>\",\"correct_option\":1}', 0.25),
(683, 12, 'Thẻ nào được dùng để chèn âm thanh vào trang web?', '{\"option_1\":\"<audio>\",\"option_2\":\"<sound>\",\"option_3\":\"<voice>\",\"option_4\":\"<music>\",\"correct_option\":1}', 4),
(684, 12, 'Thuộc tính nào của thẻ <form> dùng để xác định nơi dữ liệu được gửi đi?', '{\"option_1\":\"action\",\"option_2\":\"method\",\"option_3\":\"enctype\",\"option_4\":\"target\",\"correct_option\":1}', 0.5),
(685, 12, 'Thẻ nào được dùng để nhúng một tệp JavaScript vào trang HTML?', '{\"option_1\":\"<link>\",\"option_2\":\"<script>\",\"option_3\":\"<style>\",\"option_4\":\"<meta>\",\"correct_option\":2}', 0.5),
(686, 12, 'HTML là viết tắt của từ gì?', '{\"option_1\":\"Hyper Trainer Marking Language\",\"option_2\":\"Hyper Text Markup Language\",\"option_3\":\"Hyper Text Marketing Language\",\"option_4\":\"Hyper Tech Markup Language\",\"correct_option\":2}', 0.25),
(687, 12, 'Thẻ <h1> dùng để làm gì trong HTML?', '{\"option_1\":\"\\u0110\\u1ecbnh d\\u1ea1ng \\u0111o\\u1ea1n v\\u0103n\",\"option_2\":\"Hi\\u1ec3n th\\u1ecb ti\\u00eau \\u0111\\u1ec1 c\\u1ea5p 1\",\"option_3\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_4\":\"T\\u1ea1o danh s\\u00e1ch kh\\u00f4ng th\\u1ee9 t\\u1ef1\",\"correct_option\":2}', 0.25),
(688, 12, 'Thẻ nào dùng để tạo liên kết trong HTML?', '{\"option_1\":\"<a>\",\"option_2\":\"<link>\",\"option_3\":\"<href>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 1.75),
(689, 12, 'Thẻ nào được sử dụng để tạo danh sách không thứ tự?', '{\"option_1\":\"<ul>\",\"option_2\":\"<li>\",\"option_3\":\"<ol>\",\"option_4\":\"<dl>\",\"correct_option\":1}', 2),
(690, 12, 'Đâu là thuộc tính của thẻ <img> dùng để hiển thị văn bản thay thế khi ảnh không hiển thị?', '{\"option_1\":\"title\",\"option_2\":\"alt\",\"option_3\":\"src\",\"option_4\":\"href\",\"correct_option\":2}', 4.75),
(691, 12, 'Thuộc tính nào dùng để định nghĩa đường dẫn của ảnh trong thẻ <img>?', '{\"option_1\":\"src\",\"option_2\":\"alt\",\"option_3\":\"href\",\"option_4\":\"link\",\"correct_option\":1}', 1.5),
(692, 12, 'Thẻ <br> dùng để làm gì trong HTML?', '{\"option_1\":\"T\\u1ea1o d\\u00f2ng ngang\",\"option_2\":\"T\\u1ea1o d\\u00f2ng m\\u1edbi\",\"option_3\":\"T\\u1ea1o m\\u1ed9t \\u0111o\\u1ea1n v\\u0103n\",\"option_4\":\"T\\u1ea1o m\\u1ed9t n\\u00fat b\\u1ea5m\",\"correct_option\":2}', 1),
(693, 12, 'Thẻ nào dùng để tạo bảng trong HTML?', '{\"option_1\":\"<table>\",\"option_2\":\"<tab>\",\"option_3\":\"<tr>\",\"option_4\":\"<td>\",\"correct_option\":1}', 4.25),
(694, 12, 'Thẻ <iframe> dùng để làm gì trong HTML?', '{\"option_1\":\"Ch\\u00e8n h\\u00ecnh \\u1ea3nh\",\"option_2\":\"Ch\\u00e8n \\u0111o\\u1ea1n video\",\"option_3\":\"Ch\\u00e8n m\\u1ed9t trang web kh\\u00e1c v\\u00e0o trang hi\\u1ec7n t\\u1ea1i\",\"option_4\":\"T\\u1ea1o khung v\\u0103n b\\u1ea3n\",\"correct_option\":3}', 1.75),
(695, 12, 'Thuộc tính nào dùng để thay đổi màu nền của trang?', '{\"option_1\":\"background\",\"option_2\":\"bgcolor\",\"option_3\":\"color\",\"option_4\":\"background-color\",\"correct_option\":4}', 3.75),
(696, 12, 'Đâu là thẻ HTML5 để nhúng video vào trang web?', '{\"option_1\":\"<embed>\",\"option_2\":\"<video>\",\"option_3\":\"<source>\",\"option_4\":\"<audio>\",\"correct_option\":2}', 2.25),
(697, 12, 'Thẻ nào dùng để tạo đoạn văn bản trong HTML?', '{\"option_1\":\"<p>\",\"option_2\":\"<div>\",\"option_3\":\"<span>\",\"option_4\":\"<section>\",\"correct_option\":1}', 0.5),
(698, 12, 'Thuộc tính nào dùng để thêm tiêu đề phụ cho thẻ <a>?', '{\"option_1\":\"target\",\"option_2\":\"href\",\"option_3\":\"title\",\"option_4\":\"alt\",\"correct_option\":3}', 0.5),
(699, 12, 'Thẻ nào dùng để chèn hình ảnh vào trong trang HTML?', '{\"option_1\":\"<img>\",\"option_2\":\"<picture>\",\"option_3\":\"<figure>\",\"option_4\":\"<icon>\",\"correct_option\":1}', 4.25),
(700, 12, 'Thẻ nào dùng để định nghĩa một liên kết trong HTML?', '{\"option_1\":\"<a>\",\"option_2\":\"<link>\",\"option_3\":\"<href>\",\"option_4\":\"<nav>\",\"correct_option\":1}', 4.75),
(701, 12, 'Thuộc tính nào dùng để mở liên kết trong cửa sổ mới?', '{\"option_1\":\"rel\",\"option_2\":\"target\",\"option_3\":\"href\",\"option_4\":\"src\",\"correct_option\":2}', 1.75),
(702, 12, 'Thẻ <title> của HTML được hiển thị ở đâu?', '{\"option_1\":\"Trong n\\u1ed9i dung trang\",\"option_2\":\"Trong ti\\u00eau \\u0111\\u1ec1 tr\\u00ecnh duy\\u1ec7t\",\"option_3\":\"Trong body\",\"option_4\":\"Trong th\\u1ebb meta\",\"correct_option\":2}', 2.25),
(703, 12, 'Thuộc tính nào dùng để thêm chú thích cho hình ảnh trong HTML?', '{\"option_1\":\"src\",\"option_2\":\"alt\",\"option_3\":\"href\",\"option_4\":\"title\",\"correct_option\":2}', 1.5),
(704, 12, 'Thẻ nào được dùng để tạo danh sách có thứ tự?', '{\"option_1\":\"<ul>\",\"option_2\":\"<ol>\",\"option_3\":\"<li>\",\"option_4\":\"<dl>\",\"correct_option\":2}', 4.75),
(705, 12, 'Thẻ nào được dùng để tạo form nhập liệu trong HTML?', '{\"option_1\":\"<input>\",\"option_2\":\"<form>\",\"option_3\":\"<button>\",\"option_4\":\"<textarea>\",\"correct_option\":2}', 2.5),
(706, 12, 'Thẻ nào dùng để tạo bảng trong HTML?', '{\"option_1\":\"<table>\",\"option_2\":\"<tr>\",\"option_3\":\"<td>\",\"option_4\":\"<th>\",\"correct_option\":1}', 2.75),
(707, 12, 'Thẻ nào dùng để nhúng tệp CSS ngoài vào trang HTML?', '{\"option_1\":\"<style>\",\"option_2\":\"<link>\",\"option_3\":\"<css>\",\"option_4\":\"<script>\",\"correct_option\":2}', 0.25),
(708, 12, 'Thuộc tính nào của thẻ <a> được sử dụng để xác định địa chỉ liên kết?', '{\"option_1\":\"href\",\"option_2\":\"link\",\"option_3\":\"src\",\"option_4\":\"target\",\"correct_option\":1}', 5),
(709, 12, 'Thẻ nào dùng để hiển thị nội dung in đậm?', '{\"option_1\":\"<strong>\",\"option_2\":\"<b>\",\"option_3\":\"<i>\",\"option_4\":\"C\\u1ea3 hai th\\u1ebb <strong> v\\u00e0 <b>\",\"correct_option\":4}', 3.25),
(710, 12, 'Thuộc tính nào của thẻ <form> được dùng để xác định phương thức gửi dữ liệu?', '{\"option_1\":\"method\",\"option_2\":\"action\",\"option_3\":\"target\",\"option_4\":\"enctype\",\"correct_option\":1}', 2.5),
(711, 12, 'Thẻ <meta> có tác dụng gì?', '{\"option_1\":\"\\u0110\\u1ecbnh ngh\\u0129a metadata c\\u1ee7a trang web\",\"option_2\":\"Ch\\u1ee9a ti\\u00eau \\u0111\\u1ec1 trang\",\"option_3\":\"Li\\u00ean k\\u1ebft t\\u1ec7p CSS ngo\\u00e0i\",\"option_4\":\"Ch\\u1ee9a n\\u1ed9i dung ch\\u00ednh c\\u1ee7a trang\",\"correct_option\":1}', 1.5);

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

--
-- Đang đổ dữ liệu cho bảng `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `quiz_date`, `spend_time`, `exam_id`, `result`) VALUES
(6, 2, '2024-10-06 23:44:29', 52, 3, '[{\"id\":\"653\",\"choice\":\"1\"},{\"id\":\"655\",\"choice\":\"1\"},{\"id\":\"656\",\"choice\":\"1\"},{\"id\":\"668\",\"choice\":\"3\"},{\"id\":\"669\",\"choice\":\"3\"},{\"id\":\"671\",\"choice\":\"1\"},{\"id\":\"685\",\"choice\":\"2\"},{\"id\":\"686\",\"choice\":\"2\"},{\"id\":\"702\",\"choice\":\"2\"},{\"id\":\"707\",\"choice\":\"2\"}]'),
(7, 2, '2024-10-06 23:46:05', 61, 3, '[{\"id\":\"653\",\"choice\":\"1\"},{\"id\":\"655\",\"choice\":\"4\"},{\"id\":\"656\",\"choice\":\"1\"},{\"id\":\"668\",\"choice\":\"-1\"},{\"id\":\"669\",\"choice\":\"-1\"},{\"id\":\"671\",\"choice\":\"-1\"},{\"id\":\"685\",\"choice\":\"-1\"},{\"id\":\"686\",\"choice\":\"-1\"},{\"id\":\"702\",\"choice\":\"-1\"},{\"id\":\"707\",\"choice\":\"-1\"}]'),
(8, 2, '2024-10-06 23:57:15', 61, 3, '[{\"id\":\"653\",\"choice\":\"-1\"},{\"id\":\"655\",\"choice\":\"-1\"},{\"id\":\"656\",\"choice\":\"-1\"},{\"id\":\"668\",\"choice\":\"-1\"},{\"id\":\"669\",\"choice\":\"-1\"},{\"id\":\"671\",\"choice\":\"-1\"},{\"id\":\"685\",\"choice\":\"-1\"},{\"id\":\"686\",\"choice\":\"-1\"},{\"id\":\"702\",\"choice\":\"-1\"},{\"id\":\"707\",\"choice\":\"-1\"}]'),
(9, 2, '2024-10-07 00:10:27', 61, 4, '[{\"id\":\"655\",\"choice\":\"4\"},{\"id\":\"672\",\"choice\":\"2\"},{\"id\":\"675\",\"choice\":\"4\"},{\"id\":\"679\",\"choice\":\"2\"},{\"id\":\"687\",\"choice\":\"1\"},{\"id\":\"681\",\"choice\":\"-1\"},{\"id\":\"690\",\"choice\":\"-1\"},{\"id\":\"695\",\"choice\":\"-1\"},{\"id\":\"702\",\"choice\":\"-1\"},{\"id\":\"709\",\"choice\":\"-1\"}]');

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
(2, 'CSS', 'css'),
(3, 'PHP', 'php'),
(4, 'SQL', 'sql'),
(5, 'JAVA', 'java'),
(6, 'PYTHON', 'python'),
(7, 'ANDROID', 'android'),
(8, 'IOS', 'ios'),
(9, 'NODE JS', 'node-js'),
(10, 'MONGODB', 'mongodb'),
(11, 'HTML', 'html'),
(12, 'ASP', 'asp'),
(13, 'RUBY ON RAILS', 'ruby-on-rails'),
(14, 'AUTOIT', 'autoit');

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
  `role` varchar(5) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `user_code`, `password`, `fullname`, `phone`, `email`, `role`) VALUES
(1, 'redo', 'FTB-1', '827ccb0eea8a706c4c34a16891f84e7b', 'Trường ReDo', '0911397764', 'redoprogrammer@gmail.com', 'user'),
(2, 'admin', 'FTB-2', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', '0911397764', 'redoprogrammer@gmail.com', 'admin'),
(31, 'hungtd', 'FTB2024', '827ccb0eea8a706c4c34a16891f84e7b', 'Trần Duy Hưng', '988333222', 'tranduyhung@gmail.com', 'user'),
(32, 'tuoidt', 'FTB2025', '827ccb0eea8a706c4c34a16891f84e7b', 'Đào Thị Tươi', '922343222', 'daothituoi@gmail.com', 'user'),
(33, 'quad', 'FTB2026', '827ccb0eea8a706c4c34a16891f84e7b', 'Dương Quá', '933222123', 'duongqua@gmail.com', 'user'),
(34, 'nutl', 'FTB2027', '827ccb0eea8a706c4c34a16891f84e7b', 'Tiểu Long Nữ', '911234543', 'tieulongnu@gmail.com', 'user'),
(35, 'binhdc', 'FTB2028', '827ccb0eea8a706c4c34a16891f84e7b', 'Doãn Chí Bình', '942234932', 'doanchibinh@gmail.com', 'user'),
(36, 'duongvt', 'FTB2029', '827ccb0eea8a706c4c34a16891f84e7b', 'Vương Trùng Dương', '953219832', 'vuongtrungduong@gmail.com', 'user'),
(37, 'thongcb', 'FTB2030', '827ccb0eea8a706c4c34a16891f84e7b', 'Châu Bá Thông', '943218381', 'chaubathong@gmail.com', 'user'),
(38, 'tinhq', 'FTB2031', '827ccb0eea8a706c4c34a16891f84e7b', 'Quách Tĩnh', '936839019', 'quachtinh@gmail.com', 'user'),
(39, 'dungh', 'FTB2032', '827ccb0eea8a706c4c34a16891f84e7b', 'Hoàng Dung', '912433999', 'hoangdung@gmail.com', 'user');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subject_id` (`subject_id`);

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
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `exam_configs`
--
ALTER TABLE `exam_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `lessions`
--
ALTER TABLE `lessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `question_blanks`
--
ALTER TABLE `question_blanks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `quizs`
--
ALTER TABLE `quizs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=712;

--
-- AUTO_INCREMENT cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `exams`
--
ALTER TABLE `exams`
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
