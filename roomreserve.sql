-- phpMyAdmin SQL Dump
-- version 4.3.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- 생성 시간: 15-09-25 10:15
-- 서버 버전: 5.5.8
-- PHP 버전: 5.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 데이터베이스: `roomreserve`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `confroom`
--

CREATE TABLE IF NOT EXISTS `confroom` (
  `cr_no` mediumint(8) unsigned NOT NULL COMMENT '인덱스',
  `cr_name` varchar(100) NOT NULL COMMENT '회의실명',
  `cr_group` varchar(100) NOT NULL COMMENT '회의실그룹명',
  `cr_update_date` datetime NOT NULL COMMENT '작성일시',
  `cr_update_ip` varchar(30) NOT NULL COMMENT '작성자IP'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='회의실';

--
-- 테이블의 덤프 데이터 `confroom`
--

INSERT INTO `confroom` (`cr_no`, `cr_name`, `cr_group`, `cr_update_date`, `cr_update_ip`) VALUES
(1, '2층 주 회의실', '2층 회의실', '2015-08-17 00:00:00', '147.46.96.35'),
(2, '2층 소형 회의실', '2층 회의실', '2015-08-17 00:00:00', '147.46.96.35');

-- --------------------------------------------------------

--
-- 테이블 구조 `profinfo`
--

CREATE TABLE IF NOT EXISTS `profinfo` (
  `pi_no` mediumint(8) unsigned NOT NULL COMMENT '인덱스',
  `pi_name` varchar(30) NOT NULL COMMENT '교수님 성함',
  `pi_phone` varchar(30) NOT NULL COMMENT '교수님 구내전화(암호대신)',
  `pi_level` varchar(100) DEFAULT NULL COMMENT '사용권한',
  `pi_update_date` datetime DEFAULT NULL,
  `pi_update_ip` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='사용등록된 교수님 정보';

-- --------------------------------------------------------

--
-- 테이블 구조 `roomreserve`
--

CREATE TABLE IF NOT EXISTS `roomreserve` (
  `rr_no` mediumint(8) unsigned NOT NULL COMMENT '인덱스',
  `rr_name` varchar(30) NOT NULL COMMENT '예약자명(교수명)',
  `rr_phone` varchar(30) NOT NULL COMMENT '예약자번호(구내번호)',
  `rr_subject` varchar(200) NOT NULL COMMENT '회의주제',
  `rr_room` varchar(100) NOT NULL COMMENT '회의실',
  `rr_date` date NOT NULL COMMENT '회의일자',
  `rr_time_begin` smallint(4) unsigned zerofill NOT NULL COMMENT '시작시간',
  `rr_time_end` smallint(4) unsigned zerofill NOT NULL COMMENT '종료시간',
  `rr_repeat_rr_no` mediumint(8) unsigned DEFAULT NULL COMMENT '반복원점항목',
  `rr_repeat_week` varchar(60) DEFAULT NULL COMMENT '반복요일',
  `rr_repeat_begin` date DEFAULT NULL COMMENT '반복일자시작',
  `rr_repeat_end` date DEFAULT NULL COMMENT '반복일자종료',
  `rr_update_date` datetime NOT NULL COMMENT '작성일시',
  `rr_update_ip` varchar(60) NOT NULL COMMENT '작성자IP'
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='예약테이블';

--
-- 테이블의 덤프 데이터 `roomreserve`
--

INSERT INTO `roomreserve` (`rr_no`, `rr_name`, `rr_phone`, `rr_subject`, `rr_room`, `rr_date`, `rr_time_begin`, `rr_time_end`, `rr_repeat_rr_no`, `rr_repeat_week`, `rr_repeat_begin`, `rr_repeat_end`, `rr_update_date`, `rr_update_ip`) VALUES
(2, '윤형진', '3420', '의공 윤형진 교수', '2층 2303 회의실 1(원형)', '2015-08-17', 1100, 1500, NULL, NULL, NULL, NULL, '2015-08-17 00:00:00', '147.46.96.35'),
(3, '정진호', '2411', '피부과 회의', '2층 2303 회의실 1(원형)', '2015-08-17', 0730, 0830, NULL, NULL, NULL, NULL, '2015-08-17 00:00:00', '147.46.96.35'),
(4, '강창현', '2653', '흉부외과 회의', '2층 2303 회의실 1(원형)', '2015-08-17', 1000, 1100, NULL, NULL, NULL, NULL, '2015-08-17 00:00:00', '147.46.96.35'),
(9, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-16', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(10, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-18', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(11, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-21', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(12, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-23', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(13, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-25', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(14, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-28', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(15, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-09-30', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(16, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-02', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(17, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-05', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(18, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-07', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(19, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-09', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(20, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-12', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(21, '김영호', '3429', '테스트 회의', '2층 2303 회의실 1(원형)', '2015-10-14', 1700, 1800, 9, '월,수,금', '2015-09-15', '2015-10-15', '2015-09-15 17:36:52', '147.46.96.35'),
(22, '김영호', '3429', '회의', '2층 2303 회의실 1(원형)', '2015-09-16', 1400, 1500, NULL, NULL, NULL, NULL, '2015-09-16 10:02:53', '147.46.96.35'),
(24, '김영호', '3429', '의공학과 회의', '2층 2303 회의실 1(원형)', '2015-09-16', 1800, 1830, NULL, NULL, NULL, NULL, '2015-09-17 18:23:44', '147.46.96.35'),
(25, '김영호', '3429', '회의', '2층 2303 회의실 1(원형)', '2015-08-17', 1700, 1800, NULL, NULL, NULL, NULL, '2015-09-18 15:42:43', '223.62.172.59'),
(26, '회의1', '3429', 'ㅇㅇ교수 랩미팅', '2층 2303 회의실 1(원형)', '2015-09-22', 2300, 2400, 26, '화', '2015-09-18', '2015-10-18', '2015-09-18 15:46:32', '223.62.172.59'),
(27, '회의1', '3429', 'ㅇㅇ교수 랩미팅', '2층 2303 회의실 1(원형)', '2015-09-29', 2300, 2400, 26, '화', '2015-09-18', '2015-10-18', '2015-09-18 15:46:32', '223.62.172.59'),
(28, '회의1', '3429', 'ㅇㅇ교수 랩미팅', '2층 2303 회의실 1(원형)', '2015-10-06', 2300, 2400, 26, '화', '2015-09-18', '2015-10-18', '2015-09-18 15:46:32', '223.62.172.59'),
(29, '회의1', '3429', 'ㅇㅇ교수 랩미팅', '2층 2303 회의실 1(원형)', '2015-10-13', 2300, 2400, 26, '화', '2015-09-18', '2015-10-18', '2015-09-18 15:46:32', '223.62.172.59');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `confroom`
--
ALTER TABLE `confroom`
  ADD PRIMARY KEY (`cr_no`);

--
-- 테이블의 인덱스 `profinfo`
--
ALTER TABLE `profinfo`
  ADD PRIMARY KEY (`pi_no`);

--
-- 테이블의 인덱스 `roomreserve`
--
ALTER TABLE `roomreserve`
  ADD PRIMARY KEY (`rr_no`), ADD KEY `name_idx` (`rr_name`), ADD KEY `reserve_idx` (`rr_date`,`rr_time_begin`,`rr_time_end`), ADD KEY `rr_room` (`rr_room`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `confroom`
--
ALTER TABLE `confroom`
  MODIFY `cr_no` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',AUTO_INCREMENT=3;
--
-- 테이블의 AUTO_INCREMENT `profinfo`
--
ALTER TABLE `profinfo`
  MODIFY `pi_no` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스';
--
-- 테이블의 AUTO_INCREMENT `roomreserve`
--
ALTER TABLE `roomreserve`
  MODIFY `rr_no` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',AUTO_INCREMENT=30;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
