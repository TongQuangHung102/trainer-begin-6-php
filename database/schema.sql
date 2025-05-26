-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 26, 2025 lúc 06:01 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `trainer_begin_6`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `RoleId` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`RoleId`, `RoleName`) VALUES
(4, 'admin'),
(3, 'admod'),
(1, 'guest'),
(2, 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Avatar` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Birthday` date DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `JoinedDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `RoleId` int(11) NOT NULL,
  `ReferrerId` int(11) DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `last_failed_login` datetime DEFAULT NULL,
  `lockout_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--
-- MẬT KHẨU MÃ HÓA: 123456
INSERT INTO `users` (`UserId`, `FullName`, `UserName`, `Email`, `Phone`, `Avatar`, `Password`, `Description`, `Birthday`, `Gender`, `JoinedDate`, `RoleId`, `ReferrerId`, `failed_login_attempts`, `last_failed_login`, `lockout_until`) VALUES
(1, 'hungggg', 'abcccc', 'hunga2@gmail.com', '0374004660', NULL, '$2y$10$lGsXafWE9VNNykL2Bp4HB.VsRF.dHkulg/PzvU9.uhyCinn2jMxP6', '', '0000-00-00', 'Nữ', '2025-05-21 02:11:19', 2, NULL, 1, '2025-05-26 09:45:02', NULL),
(3, 'hung', 'abcd', 'hung20@gmail.com', '0364004990', NULL, '$2y$10$d3B8zAjp5nsDroNwogq6PugPHeGP6VP3PQucM80H8DXcYI2sixhMO', NULL, '2025-05-21', NULL, '2025-05-21 02:14:53', 2, NULL, 0, NULL, NULL),
(4, 'ggg', 'abcddd', 'gun@gmail.com', '0339532922', NULL, '$2y$10$RfuBB2zvKvepoXU4h/htx.Zs73doM8dQf2F4iqxjdB7ObNEgsCUXi', NULL, NULL, NULL, '2025-05-21 02:51:18', 2, NULL, 0, NULL, NULL),
(6, 'h123', 'h123456', 'hungzz@gmail.com', '0974212357', NULL, '$2y$10$5.bage4EGVO7p9ysIgy4v.J/3HE2MuTav0NGviqcdDWhqJha88KBq', '', '0000-00-00', NULL, '2025-05-21 03:07:27', 2, NULL, 0, NULL, NULL),
(7, 'ss', 'hung', 'hungx@gmail.com', '0912121212', NULL, '$2y$10$PEJZlNcZxhwipTHmSQlgi.G52SPbGwrhLYa.pf.XVF50FXUXQK.pe', 'hung ne', '0000-00-00', NULL, '2025-05-21 03:11:21', 2, NULL, 0, NULL, NULL),
(8, 'hung', 'abcde', 'abcde@gmail.com', '0981238765', NULL, '$2y$10$vTzqwPHlh944KKTEIZHehuU0adqgnmoOHlSyhwqZIwDQAMd493RVO', NULL, NULL, NULL, '2025-05-21 03:24:01', 2, NULL, 0, NULL, NULL),
(9, 'vvvv', 'vvv', 'vvv@gmail.com', '0934444444', NULL, '$2y$10$zEAhd8Djk0N4PWno20rk9.W/StTqSFxiEbjcAoTCl8S5.E4UqEwU.', 'hhhh', '0000-00-00', NULL, '2025-05-21 04:20:26', 2, NULL, 0, NULL, NULL),
(11, 'admod2', 'admod2', 'admod2@gmail.com', '06666655555', NULL, '$2y$10$6z6hzDbx2noMng/.iQmcye2NkrSEzEou3EjJcRidZvVoyAq8EC9K6', NULL, NULL, NULL, '2025-05-21 07:21:26', 3, 1, 0, NULL, NULL),
(12, 'admod1', 'admod1', 'admod1@gmail.com', '0953888888', 'uploads/avatar_6833e4a690260.png', '$2y$10$Vr1FYJqf002pl/gsoHe19eH97yt3o4OiETW8W0ih1UUMl.tPYUgjW', '', '0000-00-00', NULL, '2025-05-21 07:38:20', 3, 3, 0, NULL, NULL),
(13, 'www', 'www', 'www@gmail.com', '0977777777', NULL, '$2y$10$gbrRaypt10iZbwffhSEAteg/J1BZSuQYU8FR2kQQj2xuOG7/XsUp.', '', '2025-05-22', NULL, '2025-05-21 07:48:06', 2, NULL, 0, NULL, NULL),
(14, 'admin', 'admin', 'admin@gmail.com', '0912368754', 'uploads/avatar_6833e79ddd845.jpg', '$2y$10$MyGIor8zCNCPn7VFPnjAJedxJ.JIZXoHPwgoZVgeR.rEtZ3rrQ/4K', '', '0000-00-00', NULL, '2025-05-21 08:04:08', 4, NULL, 0, NULL, NULL),
(15, 'jdpp', 'jdpppp', 'jdp@gmail.com', '0988888888', NULL, '$2y$10$LcdAfS2qikYInnB1VQesleyPX1ZKNDQJESOaEagMvjnYKZYLgSimy', 'xxxx', '2025-05-21', 'Nữ', '2025-05-21 09:00:30', 2, 9, 0, NULL, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`RoleId`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Phone` (`Phone`),
  ADD KEY `RoleId` (`RoleId`),
  ADD KEY `ReferrerId` (`ReferrerId`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleId`) REFERENCES `roles` (`RoleId`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`ReferrerId`) REFERENCES `users` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;