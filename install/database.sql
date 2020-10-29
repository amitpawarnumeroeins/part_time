-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2020 at 09:06 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.2.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobs_app_buyer`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`, `email`, `image`) VALUES
(1, 'admin', 'admin', 'viaviwebtech@gmail.com', 'profile.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_apply`
--

CREATE TABLE `tbl_apply` (
  `ap_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `apply_date` varchar(11) CHARACTER SET utf8 NOT NULL,
  `seen` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `cid` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`cid`, `category_name`, `category_image`, `status`) VALUES
(1, 'Accounting', '16567_accounting.png', 1),
(7, 'Manufacturing', '98980_manufacturing.png', 1),
(8, 'Marketing', '73446_marketing.png', 1),
(10, 'Other', '73272_other.png', 1),
(11, 'Railway', '33773_railway.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_city`
--

CREATE TABLE `tbl_city` (
  `c_id` int(10) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_city`
--

INSERT INTO `tbl_city` (`c_id`, `city_name`, `status`) VALUES
(2, 'Surat', 1),
(3, 'Ahmedabad', 1),
(4, 'Delhi', 1),
(11, 'Amreli', 1),
(13, 'jaipur', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company`
--

CREATE TABLE `tbl_company` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `company_email` varchar(100) NOT NULL,
  `mobile_no` varchar(30) CHARACTER SET utf8 NOT NULL,
  `city` varchar(255) CHARACTER SET utf8 NOT NULL,
  `company_website` varchar(255) CHARACTER SET utf8 NOT NULL,
  `company_address` text CHARACTER SET utf8 NOT NULL,
  `company_desc` text NOT NULL,
  `company_logo` varchar(255) NOT NULL,
  `company_work_day` varchar(255) NOT NULL,
  `company_work_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jobs`
--

CREATE TABLE `tbl_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `cat_id` varchar(255) NOT NULL,
  `city_id` varchar(255) NOT NULL,
  `job_name` varchar(255) NOT NULL,
  `job_designation` varchar(255) NOT NULL,
  `job_desc` text NOT NULL,
  `job_salary` varchar(255) NOT NULL,
  `job_company_name` varchar(255) NOT NULL,
  `job_company_website` varchar(255) NOT NULL,
  `job_phone_number` varchar(255) NOT NULL,
  `job_mail` varchar(255) NOT NULL,
  `job_vacancy` varchar(255) NOT NULL,
  `job_address` varchar(500) NOT NULL,
  `job_qualification` text NOT NULL,
  `job_skill` text NOT NULL,
  `job_image` varchar(255) NOT NULL,
  `job_map_latitude` varchar(255) NOT NULL,
  `job_map_longitude` varchar(255) NOT NULL,
  `job_date` int(11) NOT NULL,
  `job_type` varchar(255) NOT NULL,
  `job_experince` varchar(255) NOT NULL,
  `job_work_day` text NOT NULL,
  `job_work_time` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_jobs`
--

INSERT INTO `tbl_jobs` (`id`, `user_id`, `cat_id`, `city_id`, `job_name`, `job_designation`, `job_desc`, `job_salary`, `job_company_name`, `job_company_website`, `job_phone_number`, `job_mail`, `job_vacancy`, `job_address`, `job_qualification`, `job_skill`, `job_image`, `job_map_latitude`, `job_map_longitude`, `job_date`, `job_type`, `job_experince`, `job_work_day`, `job_work_time`, `status`) VALUES
(1, 0, '1', '3', 'SEO Expert', 'Day', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pretium nunc non justo placerat pulvinar. Vestibulum ac ullamcorper sapien, nec scelerisque ipsum. Aliquam in tempus nulla. Curabitur ac pulvinar elit. Donec sed iaculis lorem. Duis at fermentum odio, ut mattis risus. Mauris molestie mi a dignissim eleifend. Nam sit amet facilisis odio, ac ornare quam. Donec a arcu at leo interdum viverra vulputate vitae odio. Aliquam erat volutpat. Vivamus aliquam interdum ex a condimentum. Sed mollis maximus cursus. Aenean vitae malesuada tellus. Aenean tristique porta massa. Fusce at nisl vitae dui consectetur pharetra. Praesent non ipsum dui.</p>\r\n', '20000', 'Mayan Design Studios', 'http://viaviweb.com/', '9874563210', 'info@viaviweb.com', '25', 'Lorem Ipsum Dolor Ahmedabad..', 'Bachelors', 'html,css,communication', '46905.jpg', '22.2736308', '70.7512554', 1583881200, 'Hourly', '3 years', 'Mon-Fri', '9Am-6Pm', 1),
(2, 0, '1', '11', 'Graphic Designer', 'designer', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pretium nunc non justo placerat pulvinar. Vestibulum ac ullamcorper sapien, nec scelerisque ipsum. Aliquam in tempus nulla. Curabitur ac pulvinar elit. Donec sed iaculis lorem. Duis at fermentum odio, ut mattis risus. Mauris molestie mi a dignissim eleifend. Nam sit amet facilisis odio, ac ornare quam. Donec a arcu at leo interdum viverra vulputate vitae odio. Aliquam erat volutpat. Vivamus aliquam interdum ex a condimentum. Sed mollis maximus cursus. Aenean vitae malesuada tellus. Aenean tristique porta massa. Fusce at nisl vitae dui consectetur pharetra. Praesent non ipsum dui.</p>\r\n', '20000', 'Media Wave', 'http://viaviweb.com/', '9874563210', 'info@viaviweb.com', '12', 'Lorem Ipsum Dolor Amreli..', 'Bachelors', 'adobe photoshop,html,css', '75844_img1.jpg', '22.2736308', '70.7512554', 1584313200, 'Half Time', '3 year', 'Mon-Fri', '9Am-6Pm', 1),
(4, 0, '8', '4', 'Wordpress Developer', 'Experienced Professional', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pretium nunc non justo placerat pulvinar. Vestibulum ac ullamcorper sapien, nec scelerisque ipsum. Aliquam in tempus nulla. Curabitur ac pulvinar elit. Donec sed iaculis lorem. Duis at fermentum odio, ut mattis risus. Mauris molestie mi a dignissim eleifend. Nam sit amet facilisis odio, ac ornare quam. Donec a arcu at leo interdum viverra vulputate vitae odio. Aliquam erat volutpat. Vivamus aliquam interdum ex a condimentum. Sed mollis maximus cursus. Aenean vitae malesuada tellus. Aenean tristique porta massa. Fusce at nisl vitae dui consectetur pharetra. Praesent non ipsum dui.</p>\r\n', '14000', 'Connect People', 'www.viaviweb.com', '9874563210', 'viaviwebtech@gmail.com', '4', 'Barrington, New Hampshire, Delhi', 'Certification Degree', 'wordpress,javascript,html,css', '47920_21126.jpg', '22.2736308', '70.7512554', 1585090800, 'Hourly', '5 Years', 'Mon-Fri', '9Am-5Pm', 1),
(5, 0, '10', '13', 'Php Developer Required', 'Devlopers', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse tellus libero, rutrum eleifend enim vitae, aliquam fermentum tortor. Morbi facilisis malesuada placerat. Aliquam ipsum metus, scelerisque sit amet libero ac, volutpat molestie lacus. Nam varius leo enim, sit amet aliquet odio varius sed. Vestibulum sodales egestas velit a convallis. Sed aliquam, diam et sagittis tincidunt, nisi nulla fermentum turpis, faucibus interdum lacus turpis ut ligula. Nulla sit amet laoreet enim. Sed lobortis suscipit ipsum eget convallis. Quisque sit amet nisi vel urna mattis lobortis. Suspendisse non pulvinar magna. Etiam tellus quam, sodales vel semper quis, laoreet eu elit. Maecenas aliquet convallis massa, ac pharetra tellus consequat a. In dictum est non ornare faucibus. Duis malesuada ipsum sed tincidunt aliquam. Cras ullamcorper velit nec tellus bibendum, id varius nunc commodo.</p>\r\n', '$ 35,000', 'It company', 'http://viaviweb.com/', '98745632', 'info@viaviweb.com', '3', 'labaster, Alabama, jaipur', 'Masters Degree', 'php,mysql,strong communication skill', '78859.jpg', '22.2736308', '70.7512554', 1585004400, 'Half Time', '5 Years', 'Mon - Sat', '9AM - 5PM', 1),
(6, 0, '11', '2', 'Medicine Supervisor', 'MS', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pretium nunc non justo placerat pulvinar. Vestibulum ac ullamcorper sapien, nec scelerisque ipsum. Aliquam in tempus nulla. Curabitur ac pulvinar elit. Donec sed iaculis lorem. Duis at fermentum odio, ut mattis risus. Mauris molestie mi a dignissim eleifend. Nam sit amet facilisis odio, ac ornare quam. Donec a arcu at leo interdum viverra vulputate vitae odio. Aliquam erat volutpat. Vivamus aliquam interdum ex a condimentum. Sed mollis maximus cursus. Aenean vitae malesuada tellus. Aenean tristique porta massa. Fusce at nisl vitae dui consectetur pharetra. Praesent non ipsum dui.</p>\r\n', '250000', 'Pharma Tech Inc', 'www.vivaviweb.com', '8745632108', 'info@viaviweb.com', '12', 'Dorado, Arkansas,Surat\r\n', 'MPhil/MS', 'Marketing,Communication Skill', '16527.jpg', '22.2736308', '70.7512554', 1585605600, 'Full Time', '3 Years', 'Mon - Fri', '9AM-6PM', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_recent`
--

CREATE TABLE `tbl_recent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` text NOT NULL,
  `recent_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_saved`
--

CREATE TABLE `tbl_saved` (
  `sa_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `created_at` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `envato_buyer_name` varchar(255) NOT NULL,
  `envato_purchase_code` varchar(255) NOT NULL,
  `envato_purchased_status` int(1) NOT NULL DEFAULT 0,
  `envato_ios_purchase_code` varchar(255) NOT NULL,
  `envato_ios_purchased_status` int(2) NOT NULL DEFAULT 0,
  `package_name` varchar(255) NOT NULL,
  `ios_bundle_identifier` varchar(255) NOT NULL,
  `envato_buyer_email` varchar(255) NOT NULL,
  `onesignal_app_id` varchar(500) NOT NULL,
  `onesignal_rest_key` varchar(500) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_logo` varchar(255) NOT NULL,
  `app_email` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `app_author` varchar(255) NOT NULL,
  `app_contact` varchar(255) NOT NULL,
  `app_website` varchar(255) NOT NULL,
  `app_description` text NOT NULL,
  `app_developed_by` varchar(255) NOT NULL,
  `app_from_email` varchar(255) NOT NULL,
  `app_privacy_policy` text NOT NULL,
  `update_status` varchar(10) NOT NULL DEFAULT 'false',
  `new_app_version` double NOT NULL DEFAULT 1,
  `app_update_desc` text NOT NULL,
  `app_link` text NOT NULL,
  `cancel_status` varchar(10) NOT NULL DEFAULT 'false',
  `api_latest_limit` int(3) NOT NULL,
  `api_page_limit` int(11) NOT NULL DEFAULT 5,
  `api_home_limit` int(11) NOT NULL DEFAULT 5,
  `api_cat_order_by` varchar(255) NOT NULL,
  `api_cat_post_order_by` varchar(255) NOT NULL,
  `publisher_id` text NOT NULL,
  `interstital_ad` text NOT NULL,
  `interstital_ad_id` text NOT NULL,
  `interstital_ad_click` varchar(255) NOT NULL,
  `banner_ad` text NOT NULL,
  `banner_ad_id` text NOT NULL,
  `publisher_id_ios` varchar(500) NOT NULL,
  `app_id_ios` varchar(500) NOT NULL,
  `interstital_ad_ios` varchar(500) NOT NULL,
  `interstital_ad_id_ios` varchar(500) NOT NULL,
  `interstital_ad_click_ios` varchar(500) NOT NULL,
  `banner_ad_ios` varchar(500) NOT NULL,
  `banner_ad_id_ios` varchar(500) NOT NULL,
  `banner_ad_type` varchar(30) NOT NULL DEFAULT 'admob',
  `banner_facebook_id` text NOT NULL,
  `interstital_ad_type` varchar(30) NOT NULL DEFAULT 'admob',
  `facebook_banner_ad` varchar(255) NOT NULL DEFAULT 'false',
  `facebook_banner_ad_id` varchar(255) NOT NULL DEFAULT '1',
  `facebook_interstital_ad` varchar(255) NOT NULL,
  `facebook_interstital_ad_id` varchar(255) NOT NULL,
  `facebook_interstital_ad_click` varchar(255) NOT NULL DEFAULT 'false',
  `interstital_facebook_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `envato_buyer_name`, `envato_purchase_code`, `envato_purchased_status`, `envato_ios_purchase_code`, `envato_ios_purchased_status`, `package_name`, `ios_bundle_identifier`, `envato_buyer_email`, `onesignal_app_id`, `onesignal_rest_key`, `app_name`, `app_logo`, `app_email`, `app_version`, `app_author`, `app_contact`, `app_website`, `app_description`, `app_developed_by`, `app_from_email`, `app_privacy_policy`, `update_status`, `new_app_version`, `app_update_desc`, `app_link`, `cancel_status`, `api_latest_limit`, `api_page_limit`, `api_home_limit`, `api_cat_order_by`, `api_cat_post_order_by`, `publisher_id`, `interstital_ad`, `interstital_ad_id`, `interstital_ad_click`, `banner_ad`, `banner_ad_id`, `publisher_id_ios`, `app_id_ios`, `interstital_ad_ios`, `interstital_ad_id_ios`, `interstital_ad_click_ios`, `banner_ad_ios`, `banner_ad_id_ios`, `banner_ad_type`, `banner_facebook_id`, `interstital_ad_type`, `facebook_banner_ad`, `facebook_banner_ad_id`, `facebook_interstital_ad`, `facebook_interstital_ad_id`, `facebook_interstital_ad_click`, `interstital_facebook_id`) VALUES
(1, '', '', 0, '', 0, 'com.app.jobs', 'com.viaviwebtech.JobApp', 'info@viaviweb.in', '', '', 'Job Apps', 'admin_logo.png', 'Viavi Webtech', '1.0.0', 'Viavi Webtech', 'Viavi Webtech', 'www.viaviweb.com', '<p>As Viavi Webtech is finest offshore IT company which has expertise in the below mentioned all technologies and our professional, dedicated approach towards our work has always satisfied our clients as well as users. We have reached to this level because of the dedication and hard work of our 10+ years experienced team as well as new ideas of freshers, they always provide the best solutions. Here are the promising services served by Viavi Webtech.</p>\r\n\r\n<p>Contact on Skype &amp; Email for more information.</p>\r\n\r\n<p><strong>Skype ID:</strong> support.viaviweb <strong>OR</strong> viaviwebtech<br />\r\n<strong>Email:</strong> info@viaviweb.com <strong>OR</strong> viaviwebtech@gmail.com<br />\r\n<strong>Website:</strong> <a href=\"http://www.viaviweb.com\">http://www.viaviweb.com</a><br />\r\n<br />\r\nOur Products : <em><strong><a href=\"https://codecanyon.net/user/viaviwebtech/portfolio?ref=viaviwebtech\">CODECANYON</a></strong></em></p>\r\n', 'Viavi Webtech', 'info@viaviweb.in', '<p><strong>We are committed to protecting your privacy</strong></p>\r\n\r\n<p>We collect the minimum amount of information about you that is commensurate with providing you with a satisfactory service. This policy indicates the type of processes that may result in data being collected about you. Your use of this website gives us the right to collect that information. </p>\r\n\r\n<p><strong>Information Collected</strong></p>\r\n\r\n<p>We may collect any or all of the information that you give us depending on the type of transaction you enter into, including your name, address, telephone number, and email address, together with data about your use of the website. Other information that may be needed from time to time to process a request may also be collected as indicated on the website.</p>\r\n\r\n<p><strong>Information Use</strong></p>\r\n\r\n<p>We use the information collected primarily to process the task for which you visited the website. Data collected in the UK is held in accordance with the Data Protection Act. All reasonable precautions are taken to prevent unauthorised access to this information. This safeguard may require you to provide additional forms of identity should you wish to obtain information about your account details.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Your Internet browser has the in-built facility for storing small files - \"cookies\" - that hold information which allows a website to recognise your account. Our website takes advantage of this facility to enhance your experience. You have the ability to prevent your computer from accepting cookies but, if you do, certain functionality on the website may be impaired.</p>\r\n\r\n<p><strong>Disclosing Information</strong></p>\r\n\r\n<p>We do not disclose any personal information obtained about you from this website to third parties unless you permit us to do so by ticking the relevant boxes in registration or competition forms. We may also use the information to keep in contact with you and inform you of developments associated with us. You will be given the opportunity to remove yourself from any mailing list or similar device. If at any time in the future we should wish to disclose information collected on this website to any third party, it would only be with your knowledge and consent. </p>\r\n\r\n<p>We may from time to time provide information of a general nature to third parties - for example, the number of individuals visiting our website or completing a registration form, but we will not use any information that could identify those individuals. </p>\r\n\r\n<p>In addition Dummy may work with third parties for the purpose of delivering targeted behavioural advertising to the Dummy website. Through the use of cookies, anonymous information about your use of our websites and other websites will be used to provide more relevant adverts about goods and services of interest to you. For more information on online behavioural advertising and about how to turn this feature off, please visit youronlinechoices.com/opt-out.</p>\r\n\r\n<p><strong>Changes to this Policy</strong></p>\r\n\r\n<p>Any changes to our Privacy Policy will be placed here and will supersede this version of our policy. We will take reasonable steps to draw your attention to any changes in our policy. However, to be on the safe side, we suggest that you read this document each time you use the website to ensure that it still meets with your approval.</p>\r\n\r\n<p><strong>Contacting Us</strong></p>\r\n\r\n<p>If you have any questions about our Privacy Policy, or if you want to know what information we have collected about you, please email us at hd@dummy.com. You can also correct any factual errors in that information or require us to remove your details form any list under our control.</p>\r\n', 'true', 1, 'kindly you can update new version app', 'https://play.google.com/store/apps/developer?id=Viaan+Parmar', 'true', 15, 5, 5, 'cid', 'DESC', 'pub-9456493320432553', 'true', 'ca-app-pub-3940256099942544/1033173712', '3', 'true', 'ca-app-pub-3940256099942544/6300978111', 'pub-8356404931736973', '', 'true', 'ca-app-pub-8356404931736973/3923571407', '5', 'true', 'ca-app-pub-8356404931736973/9778533319', 'admob', '', 'facebook', 'true', 'cca-app-pub-3940256099942544/63009781110', 'true', 'ca-app-pub-3940256099942544/10331737120', '5', 'ca-app-pub-8356404931736973/8732534868');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp_settings`
--

CREATE TABLE `tbl_smtp_settings` (
  `id` int(5) NOT NULL,
  `smtp_type` varchar(20) NOT NULL,
  `smtp_host` varchar(150) NOT NULL,
  `smtp_email` varchar(150) NOT NULL,
  `smtp_password` text NOT NULL,
  `smtp_secure` varchar(20) NOT NULL,
  `port_no` varchar(10) NOT NULL,
  `smtp_ghost` varchar(150) NOT NULL,
  `smtp_gemail` varchar(150) NOT NULL,
  `smtp_gpassword` text NOT NULL,
  `smtp_gsecure` varchar(20) NOT NULL,
  `gport_no` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_smtp_settings`
--

INSERT INTO `tbl_smtp_settings` (`id`, `smtp_type`, `smtp_host`, `smtp_email`, `smtp_password`, `smtp_secure`, `port_no`, `smtp_ghost`, `smtp_gemail`, `smtp_gpassword`, `smtp_gsecure`, `gport_no`) VALUES
(1, 'server', '', '', '', 'ssl', '465', 'smtp.gmail.com', '', '', 'ssl', '465');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `user_resume` varchar(255) DEFAULT NULL,
  `current_company_name` text DEFAULT NULL,
  `experiences` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `gender` varchar(11) DEFAULT NULL,
  `date_of_birth` int(50) DEFAULT NULL,
  `confirm_code` varchar(255) NOT NULL,
  `register_date` int(50) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `user_type`, `name`, `email`, `password`, `phone`, `city`, `address`, `user_image`, `user_resume`, `current_company_name`, `experiences`, `skills`, `gender`, `date_of_birth`, `confirm_code`, `register_date`, `status`) VALUES
(0, '1', 'Admin', 'admin@gmail.com', '25d55ad283aa400af464c76d713c07ad', '+911234567890', 'Bjelovar', 'Cestica', '', '', 'FieldGate properties', '2 Years', 'dfgg', 'Female', 1601330400, '', 1595973600, '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_apply`
--
ALTER TABLE `tbl_apply`
  ADD PRIMARY KEY (`ap_id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `tbl_city`
--
ALTER TABLE `tbl_city`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `tbl_company`
--
ALTER TABLE `tbl_company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_jobs`
--
ALTER TABLE `tbl_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_recent`
--
ALTER TABLE `tbl_recent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_saved`
--
ALTER TABLE `tbl_saved`
  ADD PRIMARY KEY (`sa_id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_apply`
--
ALTER TABLE `tbl_apply`
  MODIFY `ap_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_city`
--
ALTER TABLE `tbl_city`
  MODIFY `c_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_company`
--
ALTER TABLE `tbl_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_jobs`
--
ALTER TABLE `tbl_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_recent`
--
ALTER TABLE `tbl_recent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_saved`
--
ALTER TABLE `tbl_saved`
  MODIFY `sa_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
