-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2023 at 12:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yktechnology`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblaccountstatment_saleperson`
--

CREATE TABLE `tblaccountstatment_saleperson` (
  `AccountID` int(11) NOT NULL,
  `Account_Date` date NOT NULL,
  `SaleManID` int(11) NOT NULL,
  `Discription` varchar(50) NOT NULL,
  `Depit` float NOT NULL,
  `Crieted` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `admin_ID` int(11) NOT NULL,
  `admin_FName` varchar(30) NOT NULL,
  `admin_LName` varchar(30) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `admin_password` varchar(150) NOT NULL,
  `admin_phoneNumber` varchar(15) NOT NULL,
  `admin_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`admin_ID`, `admin_FName`, `admin_LName`, `admin_email`, `admin_password`, `admin_phoneNumber`, `admin_active`) VALUES
(1, 'yehia', 'kobeyssy', 'yehiakobeyssy2018@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '680764789', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `Cat_ID` int(11) NOT NULL,
  `Category_Icon` varchar(150) NOT NULL,
  `Category_Name` varchar(30) NOT NULL,
  `Cat_Discription` longtext NOT NULL,
  `Cat_Active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblclients`
--

CREATE TABLE `tblclients` (
  `ClientID` int(11) NOT NULL,
  `Client_FName` varchar(40) NOT NULL,
  `Client_LName` varchar(40) NOT NULL,
  `Client_email` varchar(50) NOT NULL,
  `Client_Phonenumber` varchar(50) NOT NULL,
  `Client_companyName` varchar(35) NOT NULL,
  `Client_addresse` varchar(50) NOT NULL,
  `Client_city` varchar(20) NOT NULL,
  `Client_Region` varchar(20) NOT NULL,
  `Client_zipcode` varchar(10) NOT NULL,
  `Client_country` int(11) NOT NULL,
  `Client_Password` varchar(150) NOT NULL,
  `Client_Acctivationcode` varchar(150) NOT NULL,
  `promo_Code` varchar(15) NOT NULL,
  `client_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblclientservices`
--

CREATE TABLE `tblclientservices` (
  `ServicesID` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `Date_service` date NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `Price` float NOT NULL,
  `Dateend` date NOT NULL,
  `ServiceTitle` varchar(50) NOT NULL,
  `ServiceDomain` varchar(30) NOT NULL,
  `ServiceTransfer` tinyint(1) NOT NULL,
  `CodeTransfer` varchar(75) NOT NULL,
  `forwhat` varchar(100) NOT NULL,
  `Colors` varchar(100) NOT NULL,
  `Discription` longtext NOT NULL,
  `filename` varchar(50) NOT NULL,
  `ServiceDone` tinyint(1) NOT NULL,
  `serviceStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcountrys`
--

CREATE TABLE `tblcountrys` (
  `CountryID` int(11) NOT NULL,
  `CountryName` varchar(50) NOT NULL,
  `CountryTVA` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcountrys`
--

INSERT INTO `tblcountrys` (`CountryID`, `CountryName`, `CountryTVA`) VALUES
(2, 'Åland (Finland)', 0),
(3, 'Albania 123', 0),
(4, 'Algeria', 0),
(5, 'American Samoa (US)', 0),
(6, 'Andorra', 0),
(7, 'Angola', 0),
(8, 'Anguilla (BOT)', 0),
(9, 'Antigua and Barbuda', 0),
(10, 'Argentina', 0),
(11, 'Armenia', 0),
(12, 'Artsakh', 0),
(13, 'Aruba (Netherlands)', 0),
(14, 'Australia', 0),
(15, 'Austria', 0),
(16, 'Azerbaijan', 0),
(17, 'Bahamas', 0),
(18, 'Bahrain', 0),
(19, 'Bangladesh', 0),
(20, 'Barbados', 0),
(21, 'Belarus', 0),
(22, 'Belgium', 0),
(23, 'Belize', 0),
(24, 'Benin', 0),
(25, 'Bermuda (BOT)', 0),
(26, 'Bhutan', 0),
(27, 'Bolivia', 0),
(28, 'Bonaire (Netherlands)', 0),
(29, 'Bosnia and Herzegovina', 0),
(30, 'Botswana', 0),
(31, 'Brazil', 0),
(32, 'British Virgin Islands (BOT)', 0),
(33, 'Brunei', 0),
(34, 'Bulgaria', 0),
(35, 'Burkina Faso', 0),
(36, 'Burundi', 0),
(37, 'Cambodia', 0),
(38, 'Cameroon', 0),
(39, 'Canada', 0),
(40, 'Cape Verde', 0),
(41, 'Cayman Islands (BOT)', 0),
(42, 'Central African Republic', 0),
(43, 'Chad', 0),
(44, 'Chile', 0),
(45, 'China', 0),
(46, 'Christmas Island (Australia)', 0),
(47, 'Cocos (Keeling) Islands (Australia)', 0),
(48, 'Colombia', 0),
(49, 'Comoros', 0),
(50, 'Congo', 0),
(51, 'Cook Islands', 0),
(52, 'Costa Rica', 0),
(53, 'Croatia', 0),
(54, 'Cuba', 0),
(55, 'Curaçao (Netherlands)', 0),
(56, 'Cyprus', 0),
(57, 'Czech Republic', 0),
(58, 'Denmark', 0),
(59, 'Djibouti', 0),
(60, 'Dominica', 0),
(61, 'Dominican Republic', 0),
(62, 'DR Congo', 0),
(63, 'East Timor', 0),
(64, 'Ecuador', 0),
(65, 'Egypt', 0),
(66, 'El Salvador', 0),
(67, 'Equatorial Guinea', 0),
(68, 'Eritrea', 0),
(69, 'Estonia', 0),
(70, 'Eswatini', 0),
(71, 'Ethiopia', 0),
(72, 'Falkland Islands (BOT)', 0),
(73, 'Faroe Islands (Denmark)', 0),
(74, 'Fiji', 0),
(75, 'Finland', 0),
(76, 'France', 0),
(77, 'French Guiana (France)', 0),
(78, 'French Polynesia (France)', 0),
(79, 'Gabon', 0),
(80, 'Gambia', 0),
(81, 'Georgia', 0),
(82, 'Germany', 0),
(83, 'Ghana', 0),
(84, 'Gibraltar (BOT)', 0),
(85, 'Greece', 0),
(86, 'Greenland (Denmark)', 0),
(87, 'Grenada', 0),
(88, 'Guadeloupe (France)', 0),
(89, 'Guam (US)', 0),
(90, 'Guatemala', 0),
(91, 'Guernsey (Crown Dependency)', 0),
(92, 'Guinea', 0),
(93, 'Guinea-Bissau', 0),
(94, 'Guyana', 0),
(95, 'Haiti', 0),
(96, 'Honduras', 0),
(97, 'Hong Kong', 0),
(98, 'Hungary', 0),
(99, 'Iceland', 0),
(100, 'India', 0),
(101, 'Indonesia', 0),
(102, 'Iran', 0),
(103, 'Iraq', 0),
(104, 'Ireland', 0),
(105, 'Isle of Man (Crown Dependency)', 0),
(106, 'Israel', 0),
(107, 'Italy', 0),
(108, 'Ivory Coast', 0),
(109, 'Jamaica', 0),
(110, 'Japan', 0),
(111, 'Jersey (Crown Dependency)', 0),
(112, 'Jordan', 0),
(113, 'Kazakhstan', 0),
(114, 'Kenya', 0),
(115, 'Kiribati', 0),
(116, 'Kosovo', 0),
(117, 'Kuwait', 0),
(118, 'Kyrgyzstan', 0),
(119, 'Laos', 0),
(120, 'Latvia', 0),
(121, 'Lebanon', 11),
(122, 'Lesotho', 0),
(123, 'Liberia', 0),
(124, 'Libya', 0),
(125, 'Liechtenstein', 0),
(126, 'Lithuania', 0),
(127, 'Luxembourg', 0),
(128, 'Macau', 0),
(129, 'Madagascar', 0),
(130, 'Malawi', 0),
(131, 'Malaysia', 0),
(132, 'Maldives', 0),
(133, 'Mali', 0),
(134, 'Malta', 0),
(135, 'Marshall Islands', 0),
(136, 'Martinique (France)', 0),
(137, 'Mauritania', 0),
(138, 'Mauritius', 0),
(139, 'Mayotte (France)', 0),
(140, 'Mexico', 0),
(141, 'Micronesia', 0),
(142, 'Moldova', 0),
(143, 'Monaco', 0),
(144, 'Mongolia', 0),
(145, 'Montenegro', 0),
(146, 'Montserrat (BOT)', 0),
(147, 'Morocco', 0),
(148, 'Mozambique', 0),
(149, 'Myanmar', 0),
(150, 'Namibia', 0),
(151, 'Nauru', 0),
(152, 'Nepal', 0),
(153, 'Netherlands', 0),
(154, 'New Caledonia (France)', 0),
(155, 'New Zealand', 0),
(156, 'Nicaragua', 0),
(157, 'Niger', 0),
(158, 'Nigeria', 0),
(159, 'Niue', 0),
(160, 'Norfolk Island (Australia)', 0),
(161, 'North Korea', 0),
(162, 'North Macedonia', 0),
(163, 'Northern Cyprus', 0),
(164, 'Northern Mariana Islands (US)', 0),
(165, 'Norway', 0),
(166, 'Oman', 0),
(167, 'Pakistan', 0),
(168, 'Palau', 0),
(169, 'Palestine', 0),
(170, 'Panama', 0),
(171, 'Papua New Guinea', 0),
(172, 'Paraguay', 0),
(173, 'Peru', 0),
(174, 'Philippines', 0),
(175, 'Pitcairn Islands (BOT)', 0),
(176, 'Poland', 0),
(177, 'Portugal', 0),
(178, 'Puerto Rico (US)', 0),
(179, 'Qatar', 0),
(180, 'Réunion (France)', 0),
(181, 'Romania', 0),
(182, 'Russia', 0),
(183, 'Rwanda', 0),
(184, 'Saba (Netherlands)', 0),
(185, 'Saint Barthélemy (France)', 0),
(186, 'Saint Helena, Ascension and Tristan da Cunha (BOT)', 0),
(187, 'Saint Kitts and Nevis', 0),
(188, 'Saint Lucia', 0),
(189, 'Saint Martin (France)', 0),
(190, 'Saint Pierre and Miquelon (France)', 0),
(191, 'Saint Vincent and the Grenadines', 0),
(192, 'Samoa', 0),
(193, 'San Marino', 0),
(194, 'São Tomé and Príncipe', 0),
(195, 'Saudi Arabia', 0),
(196, 'Senegal', 0),
(197, 'Serbia', 0),
(198, 'Seychelles', 0),
(199, 'Sierra Leone', 0),
(200, 'Singapore', 0),
(201, 'Sint Eustatius (Netherlands)', 0),
(202, 'Sint Maarten (Netherlands)', 0),
(203, 'Slovakia', 0),
(204, 'Slovenia', 0),
(205, 'Solomon Islands', 0),
(206, 'Somalia', 0),
(207, 'South Africa', 0),
(208, 'South Korea', 0),
(209, 'South Sudan', 0),
(210, 'Spain', 21),
(211, 'Sri Lanka', 0),
(212, 'Sudan', 0),
(213, 'Suriname', 0),
(214, 'Svalbard and Jan Mayen (Norway)', 0),
(215, 'Sweden', 0),
(216, 'Switzerland', 0),
(217, 'Syria', 0),
(218, 'Taiwan', 0),
(219, 'Tajikistan', 0),
(220, 'Tanzania', 0),
(221, 'Thailand', 0),
(222, 'Togo', 0),
(223, 'Tokelau (NZ)', 0),
(224, 'Tonga', 0),
(225, 'Transnistria', 0),
(226, 'Trinidad and Tobago', 0),
(227, 'Tunisia', 0),
(228, 'Turkey', 0),
(229, 'Turkmenistan', 0),
(230, 'Turks and Caicos Islands (BOT)', 0),
(231, 'Tuvalu', 0),
(232, 'U.S. Virgin Islands (US)', 0),
(233, 'Uganda', 0),
(234, 'Ukraine', 0),
(235, 'United Arab Emirates', 5),
(236, 'United Kingdom', 0),
(237, 'United States', 0),
(238, 'Uruguay', 0),
(239, 'Uzbekistan', 0),
(240, 'Vanuatu', 0),
(241, 'Vatican City', 0),
(242, 'Venezuela', 0),
(243, 'Vietnam', 0),
(244, 'Wallis and Futuna (France)', 0),
(245, 'Western Sahara', 0),
(246, 'Yemen', 0),
(247, 'Zambia', 0),
(248, 'Zimbabwe', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbldeiteilticket`
--

CREATE TABLE `tbldeiteilticket` (
  `detailTicketID` int(11) NOT NULL,
  `TicketID` int(11) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Message` longtext NOT NULL,
  `Client_company` int(11) NOT NULL,
  `file` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbldetailinvoice`
--

CREATE TABLE `tbldetailinvoice` (
  `DeitailInvoiceID` int(11) NOT NULL,
  `Invoice` int(11) NOT NULL,
  `Service` int(11) NOT NULL,
  `Description` varchar(50) NOT NULL,
  `UnitPrice` float NOT NULL,
  `ClientServiceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbldomaintype`
--

CREATE TABLE `tbldomaintype` (
  `DomainTypeID` int(11) NOT NULL,
  `ServiceName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbldomeinclients`
--

CREATE TABLE `tbldomeinclients` (
  `DomeinID` int(11) NOT NULL,
  `DateBegin` date NOT NULL,
  `Client` int(11) NOT NULL,
  `ServiceType` int(11) NOT NULL,
  `DomeinName` varchar(50) NOT NULL,
  `RenewDate` date NOT NULL,
  `Price_Renew` float NOT NULL,
  `Note` varchar(75) NOT NULL,
  `Status` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblduration`
--

CREATE TABLE `tblduration` (
  `DurationID` int(11) NOT NULL,
  `DurationName` varchar(30) NOT NULL,
  `days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblduration`
--

INSERT INTO `tblduration` (`DurationID`, `DurationName`, `days`) VALUES
(1, 'One Time', 600),
(2, 'Monthly', 30),
(3, 'Annaually', 365);

-- --------------------------------------------------------

--
-- Table structure for table `tblexpensis`
--

CREATE TABLE `tblexpensis` (
  `ExpenisisID` int(11) NOT NULL,
  `ExpensisDate` date NOT NULL,
  `ExpenisType` int(11) NOT NULL,
  `Discription` varchar(75) NOT NULL,
  `Expensis_Amount` float NOT NULL,
  `Expensis_Note` varchar(255) NOT NULL,
  `attached` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblforms`
--

CREATE TABLE `tblforms` (
  `formID` int(11) NOT NULL,
  `formName` varchar(30) NOT NULL,
  `tblname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoice`
--

CREATE TABLE `tblinvoice` (
  `InvoiceID` int(11) NOT NULL,
  `InvoiceDate` date NOT NULL,
  `InvoiceTime` time NOT NULL,
  `ClientID` int(11) NOT NULL,
  `TotalAmount` float NOT NULL,
  `TotalTax` float NOT NULL,
  `Invoice_Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbllibrary`
--

CREATE TABLE `tbllibrary` (
  `imageID` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `Subject` varchar(50) NOT NULL,
  `discription` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblourworkers`
--

CREATE TABLE `tblourworkers` (
  `workerID` int(11) NOT NULL,
  `workerimg` varchar(100) NOT NULL,
  `workerName` varchar(30) NOT NULL,
  `workerDiscription` varchar(30) NOT NULL,
  `Workeremail` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpayments`
--

CREATE TABLE `tblpayments` (
  `paymentID` int(11) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `invoiceID` int(11) NOT NULL,
  `paymentMethod` int(11) NOT NULL,
  `NoofDocument` varchar(100) NOT NULL,
  `Payment_Amount` float NOT NULL,
  `Payment_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpayment_method`
--

CREATE TABLE `tblpayment_method` (
  `paymentmethodD` int(11) NOT NULL,
  `methot` varchar(30) NOT NULL,
  `note` longtext NOT NULL,
  `method_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayment_method`
--

INSERT INTO `tblpayment_method` (`paymentmethodD`, `methot`, `note`, `method_active`) VALUES
(1, 'PayPal ', '', 1),
(2, 'From Old Balance', '', 0),
(3, 'Bank Transfer', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblportfolio`
--

CREATE TABLE `tblportfolio` (
  `portfolio_ID` int(11) NOT NULL,
  `portfolio_Title` varchar(30) NOT NULL,
  `portfolio_Pic` varchar(100) NOT NULL,
  `Duration_working` varchar(30) NOT NULL,
  `Lan_use` varchar(255) NOT NULL,
  `Discription` longtext NOT NULL,
  `linkWebsite` varchar(50) NOT NULL,
  `portfolio_Active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblsalesperson`
--

CREATE TABLE `tblsalesperson` (
  `SalePersonID` int(11) NOT NULL,
  `Sale_FName` varchar(30) NOT NULL,
  `Sale_LName` varchar(30) NOT NULL,
  `email_Sale` varchar(50) NOT NULL,
  `password_sale` varchar(100) NOT NULL,
  `Country` int(11) NOT NULL,
  `City` varchar(20) NOT NULL,
  `Addresse` varchar(100) NOT NULL,
  `PromoCode` varchar(100) NOT NULL,
  `ComitionRate` float NOT NULL,
  `PaymentType` int(11) NOT NULL,
  `Note` longtext NOT NULL,
  `saleActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblservices`
--

CREATE TABLE `tblservices` (
  `ServiceID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Service_Name` varchar(35) NOT NULL,
  `Duration` int(11) NOT NULL,
  `Service_Price` float NOT NULL,
  `old_Price` float NOT NULL,
  `Form_use` int(11) NOT NULL,
  `Get_commission` tinyint(1) NOT NULL,
  `Service_show` tinyint(1) NOT NULL,
  `Active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblslideshow`
--

CREATE TABLE `tblslideshow` (
  `slideID` int(11) NOT NULL,
  `slideimg` varchar(100) NOT NULL,
  `slideactive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblspeafications`
--

CREATE TABLE `tblspeafications` (
  `SpeaficationsID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `Speafications` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstatusdomein`
--

CREATE TABLE `tblstatusdomein` (
  `StatusDomeinID` int(11) NOT NULL,
  `StatusDomein` varchar(30) NOT NULL,
  `StatusColor` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstatusdomein`
--

INSERT INTO `tblstatusdomein` (`StatusDomeinID`, `StatusDomein`, `StatusColor`) VALUES
(1, 'Active', ' #009933'),
(2, 'Expire soon', '#8B8000'),
(3, 'Expired', ' #ff8c00 '),
(4, 'Transferred ', '#2a2727'),
(5, 'Cancel', ' #8b0000');

-- --------------------------------------------------------

--
-- Table structure for table `tblstatusinvoice`
--

CREATE TABLE `tblstatusinvoice` (
  `StatusInvoiceID` int(11) NOT NULL,
  `StatusInvoice` varchar(30) NOT NULL,
  `StatusInvoiceColor` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstatusinvoice`
--

INSERT INTO `tblstatusinvoice` (`StatusInvoiceID`, `StatusInvoice`, `StatusInvoiceColor`) VALUES
(1, 'Due invoice', '#ff4500'),
(2, 'Paid', '#00B140'),
(3, 'Canceled', '#8b0000');

-- --------------------------------------------------------

--
-- Table structure for table `tblstatusservices`
--

CREATE TABLE `tblstatusservices` (
  `StatusSerID` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Status_Color` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstatusservices`
--

INSERT INTO `tblstatusservices` (`StatusSerID`, `Status`, `Status_Color`) VALUES
(1, 'Active', ' #009933'),
(2, 'Expirer soon', '#8B8000'),
(3, 'Expired', ' #ff8c00 '),
(4, 'Canceled', ' #8b0000');

-- --------------------------------------------------------

--
-- Table structure for table `tblstatusticket`
--

CREATE TABLE `tblstatusticket` (
  `StatusTicketID` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `fontColor` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstatusticket`
--

INSERT INTO `tblstatusticket` (`StatusTicketID`, `Status`, `fontColor`) VALUES
(1, 'Open', ' #009933'),
(2, 'Client Responded', '#ff4500'),
(3, 'Operator Responded', '#1c4e80'),
(4, 'Close', '#8b0000');

-- --------------------------------------------------------

--
-- Table structure for table `tbltaskadmin`
--

CREATE TABLE `tbltaskadmin` (
  `taskID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `priorityID` int(11) NOT NULL,
  `Datetask` date NOT NULL,
  `Task_subject` varchar(255) NOT NULL,
  `Discription` text DEFAULT NULL,
  `Datend` date DEFAULT NULL,
  `done` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltaskpriority`
--

CREATE TABLE `tbltaskpriority` (
  `priority_id` int(11) NOT NULL,
  `priority_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltaskpriority`
--

INSERT INTO `tbltaskpriority` (`priority_id`, `priority_name`) VALUES
(1, 'High Priority'),
(2, 'Critical'),
(3, 'Top Priority'),
(4, 'Urgent'),
(5, 'Important'),
(6, 'Medium Priority'),
(7, 'Low Priority'),
(8, 'Non-Essential');

-- --------------------------------------------------------

--
-- Table structure for table `tblticket`
--

CREATE TABLE `tblticket` (
  `ticketID` int(11) NOT NULL,
  `ticketDate` date NOT NULL,
  `ClientID` int(11) NOT NULL,
  `ticketSection` int(11) NOT NULL,
  `TicketBelong` int(11) NOT NULL,
  `ticketSubject` varchar(30) NOT NULL,
  `ticketStatus` int(11) NOT NULL,
  `ticketlastUpdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltypeexpensis`
--

CREATE TABLE `tbltypeexpensis` (
  `TypeexpensisID` int(11) NOT NULL,
  `Type_Expensis` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltypeexpensis`
--

INSERT INTO `tbltypeexpensis` (`TypeexpensisID`, `Type_Expensis`) VALUES
(1, 'Hosting'),
(2, 'Employee '),
(3, 'Sellers');

-- --------------------------------------------------------

--
-- Table structure for table `tbltypeoftickets`
--

CREATE TABLE `tbltypeoftickets` (
  `TypeTicketID` int(11) NOT NULL,
  `TypeTicket` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltypeoftickets`
--

INSERT INTO `tbltypeoftickets` (`TypeTicketID`, `TypeTicket`) VALUES
(1, 'New Requests'),
(2, 'Conform Payment'),
(3, 'Problems with Service'),
(4, 'Feedback'),
(5, 'UPDATE Service');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblaccountstatment_saleperson`
--
ALTER TABLE `tblaccountstatment_saleperson`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`admin_ID`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`Cat_ID`);

--
-- Indexes for table `tblclients`
--
ALTER TABLE `tblclients`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexes for table `tblclientservices`
--
ALTER TABLE `tblclientservices`
  ADD PRIMARY KEY (`ServicesID`);

--
-- Indexes for table `tblcountrys`
--
ALTER TABLE `tblcountrys`
  ADD PRIMARY KEY (`CountryID`);

--
-- Indexes for table `tbldeiteilticket`
--
ALTER TABLE `tbldeiteilticket`
  ADD PRIMARY KEY (`detailTicketID`);

--
-- Indexes for table `tbldetailinvoice`
--
ALTER TABLE `tbldetailinvoice`
  ADD PRIMARY KEY (`DeitailInvoiceID`);

--
-- Indexes for table `tbldomaintype`
--
ALTER TABLE `tbldomaintype`
  ADD PRIMARY KEY (`DomainTypeID`);

--
-- Indexes for table `tbldomeinclients`
--
ALTER TABLE `tbldomeinclients`
  ADD PRIMARY KEY (`DomeinID`);

--
-- Indexes for table `tblduration`
--
ALTER TABLE `tblduration`
  ADD PRIMARY KEY (`DurationID`);

--
-- Indexes for table `tblexpensis`
--
ALTER TABLE `tblexpensis`
  ADD PRIMARY KEY (`ExpenisisID`);

--
-- Indexes for table `tblforms`
--
ALTER TABLE `tblforms`
  ADD PRIMARY KEY (`formID`);

--
-- Indexes for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  ADD PRIMARY KEY (`InvoiceID`);

--
-- Indexes for table `tbllibrary`
--
ALTER TABLE `tbllibrary`
  ADD PRIMARY KEY (`imageID`);

--
-- Indexes for table `tblourworkers`
--
ALTER TABLE `tblourworkers`
  ADD PRIMARY KEY (`workerID`);

--
-- Indexes for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `tblpayment_method`
--
ALTER TABLE `tblpayment_method`
  ADD PRIMARY KEY (`paymentmethodD`);

--
-- Indexes for table `tblportfolio`
--
ALTER TABLE `tblportfolio`
  ADD PRIMARY KEY (`portfolio_ID`);

--
-- Indexes for table `tblsalesperson`
--
ALTER TABLE `tblsalesperson`
  ADD PRIMARY KEY (`SalePersonID`);

--
-- Indexes for table `tblservices`
--
ALTER TABLE `tblservices`
  ADD PRIMARY KEY (`ServiceID`);

--
-- Indexes for table `tblslideshow`
--
ALTER TABLE `tblslideshow`
  ADD PRIMARY KEY (`slideID`);

--
-- Indexes for table `tblspeafications`
--
ALTER TABLE `tblspeafications`
  ADD PRIMARY KEY (`SpeaficationsID`);

--
-- Indexes for table `tblstatusdomein`
--
ALTER TABLE `tblstatusdomein`
  ADD PRIMARY KEY (`StatusDomeinID`);

--
-- Indexes for table `tblstatusinvoice`
--
ALTER TABLE `tblstatusinvoice`
  ADD PRIMARY KEY (`StatusInvoiceID`);

--
-- Indexes for table `tblstatusservices`
--
ALTER TABLE `tblstatusservices`
  ADD PRIMARY KEY (`StatusSerID`);

--
-- Indexes for table `tblstatusticket`
--
ALTER TABLE `tblstatusticket`
  ADD PRIMARY KEY (`StatusTicketID`);

--
-- Indexes for table `tbltaskadmin`
--
ALTER TABLE `tbltaskadmin`
  ADD PRIMARY KEY (`taskID`);

--
-- Indexes for table `tbltaskpriority`
--
ALTER TABLE `tbltaskpriority`
  ADD PRIMARY KEY (`priority_id`);

--
-- Indexes for table `tblticket`
--
ALTER TABLE `tblticket`
  ADD PRIMARY KEY (`ticketID`);

--
-- Indexes for table `tbltypeexpensis`
--
ALTER TABLE `tbltypeexpensis`
  ADD PRIMARY KEY (`TypeexpensisID`);

--
-- Indexes for table `tbltypeoftickets`
--
ALTER TABLE `tbltypeoftickets`
  ADD PRIMARY KEY (`TypeTicketID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblaccountstatment_saleperson`
--
ALTER TABLE `tblaccountstatment_saleperson`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `Cat_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblclients`
--
ALTER TABLE `tblclients`
  MODIFY `ClientID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblclientservices`
--
ALTER TABLE `tblclientservices`
  MODIFY `ServicesID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcountrys`
--
ALTER TABLE `tblcountrys`
  MODIFY `CountryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `tbldeiteilticket`
--
ALTER TABLE `tbldeiteilticket`
  MODIFY `detailTicketID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbldetailinvoice`
--
ALTER TABLE `tbldetailinvoice`
  MODIFY `DeitailInvoiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbldomaintype`
--
ALTER TABLE `tbldomaintype`
  MODIFY `DomainTypeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbldomeinclients`
--
ALTER TABLE `tbldomeinclients`
  MODIFY `DomeinID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblduration`
--
ALTER TABLE `tblduration`
  MODIFY `DurationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblexpensis`
--
ALTER TABLE `tblexpensis`
  MODIFY `ExpenisisID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblforms`
--
ALTER TABLE `tblforms`
  MODIFY `formID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  MODIFY `InvoiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbllibrary`
--
ALTER TABLE `tbllibrary`
  MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblourworkers`
--
ALTER TABLE `tblourworkers`
  MODIFY `workerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblpayments`
--
ALTER TABLE `tblpayments`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblpayment_method`
--
ALTER TABLE `tblpayment_method`
  MODIFY `paymentmethodD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblportfolio`
--
ALTER TABLE `tblportfolio`
  MODIFY `portfolio_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsalesperson`
--
ALTER TABLE `tblsalesperson`
  MODIFY `SalePersonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblservices`
--
ALTER TABLE `tblservices`
  MODIFY `ServiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblslideshow`
--
ALTER TABLE `tblslideshow`
  MODIFY `slideID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblspeafications`
--
ALTER TABLE `tblspeafications`
  MODIFY `SpeaficationsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblstatusdomein`
--
ALTER TABLE `tblstatusdomein`
  MODIFY `StatusDomeinID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblstatusinvoice`
--
ALTER TABLE `tblstatusinvoice`
  MODIFY `StatusInvoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstatusservices`
--
ALTER TABLE `tblstatusservices`
  MODIFY `StatusSerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblstatusticket`
--
ALTER TABLE `tblstatusticket`
  MODIFY `StatusTicketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbltaskadmin`
--
ALTER TABLE `tbltaskadmin`
  MODIFY `taskID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbltaskpriority`
--
ALTER TABLE `tbltaskpriority`
  MODIFY `priority_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tblticket`
--
ALTER TABLE `tblticket`
  MODIFY `ticketID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbltypeexpensis`
--
ALTER TABLE `tbltypeexpensis`
  MODIFY `TypeexpensisID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbltypeoftickets`
--
ALTER TABLE `tbltypeoftickets`
  MODIFY `TypeTicketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
