SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(11, NULL, 'Node', 20, '', 5, 6),
(12, NULL, 'Node', 21, '', 7, 8),
(18, NULL, NULL, NULL, 'controllers', 13, 306),
(19, 18, NULL, NULL, 'Attachments', 14, 25),
(20, 19, NULL, NULL, 'admin_index', 15, 16),
(21, 19, NULL, NULL, 'admin_add', 17, 18),
(22, 19, NULL, NULL, 'admin_edit', 19, 20),
(23, 19, NULL, NULL, 'admin_delete', 21, 22),
(24, 19, NULL, NULL, 'admin_browse', 23, 24),
(25, 18, NULL, NULL, 'Blocks', 26, 41),
(26, 25, NULL, NULL, 'admin_index', 27, 28),
(27, 25, NULL, NULL, 'admin_add', 29, 30),
(28, 25, NULL, NULL, 'admin_edit', 31, 32),
(29, 25, NULL, NULL, 'admin_delete', 33, 34),
(30, 25, NULL, NULL, 'admin_moveup', 35, 36),
(31, 25, NULL, NULL, 'admin_movedown', 37, 38),
(32, 25, NULL, NULL, 'admin_process', 39, 40),
(33, 18, NULL, NULL, 'Comments', 42, 57),
(34, 33, NULL, NULL, 'admin_index', 43, 44),
(35, 33, NULL, NULL, 'admin_edit', 45, 46),
(36, 33, NULL, NULL, 'admin_delete', 47, 48),
(37, 33, NULL, NULL, 'admin_process', 49, 50),
(38, 33, NULL, NULL, 'add', 51, 52),
(39, 33, NULL, NULL, 'delete', 53, 54),
(40, 18, NULL, NULL, 'Contacts', 58, 69),
(41, 40, NULL, NULL, 'admin_index', 59, 60),
(42, 40, NULL, NULL, 'admin_add', 61, 62),
(43, 40, NULL, NULL, 'admin_edit', 63, 64),
(44, 40, NULL, NULL, 'admin_delete', 65, 66),
(45, 40, NULL, NULL, 'view', 67, 68),
(46, 18, NULL, NULL, 'Filemanager', 70, 91),
(47, 46, NULL, NULL, 'admin_index', 71, 72),
(48, 46, NULL, NULL, 'admin_browse', 73, 74),
(49, 46, NULL, NULL, 'admin_editfile', 75, 76),
(50, 46, NULL, NULL, 'admin_upload', 77, 78),
(51, 46, NULL, NULL, 'admin_delete_file', 79, 80),
(52, 46, NULL, NULL, 'admin_delete_directory', 81, 82),
(53, 46, NULL, NULL, 'admin_rename', 83, 84),
(54, 46, NULL, NULL, 'admin_create_directory', 85, 86),
(55, 46, NULL, NULL, 'admin_create_file', 87, 88),
(56, 46, NULL, NULL, 'admin_chmod', 89, 90),
(57, 18, NULL, NULL, 'Links', 92, 107),
(58, 57, NULL, NULL, 'admin_index', 93, 94),
(59, 57, NULL, NULL, 'admin_add', 95, 96),
(60, 57, NULL, NULL, 'admin_edit', 97, 98),
(61, 57, NULL, NULL, 'admin_delete', 99, 100),
(62, 57, NULL, NULL, 'admin_moveup', 101, 102),
(63, 57, NULL, NULL, 'admin_movedown', 103, 104),
(64, 57, NULL, NULL, 'admin_process', 105, 106),
(65, 18, NULL, NULL, 'Menus', 108, 117),
(66, 65, NULL, NULL, 'admin_index', 109, 110),
(67, 65, NULL, NULL, 'admin_add', 111, 112),
(68, 65, NULL, NULL, 'admin_edit', 113, 114),
(69, 65, NULL, NULL, 'admin_delete', 115, 116),
(70, 18, NULL, NULL, 'Messages', 118, 127),
(71, 70, NULL, NULL, 'admin_index', 119, 120),
(72, 70, NULL, NULL, 'admin_edit', 121, 122),
(73, 70, NULL, NULL, 'admin_delete', 123, 124),
(74, 18, NULL, NULL, 'Nodes', 128, 155),
(75, 74, NULL, NULL, 'admin_index', 129, 130),
(76, 74, NULL, NULL, 'admin_create', 131, 132),
(77, 74, NULL, NULL, 'admin_add', 133, 134),
(78, 74, NULL, NULL, 'admin_edit', 135, 136),
(79, 74, NULL, NULL, 'admin_update_paths', 137, 138),
(80, 74, NULL, NULL, 'admin_delete', 139, 140),
(81, 74, NULL, NULL, 'admin_delete_meta', 141, 142),
(82, 74, NULL, NULL, 'admin_add_meta', 143, 144),
(83, 74, NULL, NULL, 'admin_process', 145, 146),
(84, 74, NULL, NULL, 'index', 147, 148),
(85, 74, NULL, NULL, 'term', 149, 150),
(86, 74, NULL, NULL, 'promoted', 151, 152),
(87, 74, NULL, NULL, 'view', 153, 154),
(88, 18, NULL, NULL, 'Regions', 156, 165),
(89, 88, NULL, NULL, 'admin_index', 157, 158),
(90, 88, NULL, NULL, 'admin_add', 159, 160),
(91, 88, NULL, NULL, 'admin_edit', 161, 162),
(92, 88, NULL, NULL, 'admin_delete', 163, 164),
(93, 18, NULL, NULL, 'Roles', 166, 175),
(94, 93, NULL, NULL, 'admin_index', 167, 168),
(95, 93, NULL, NULL, 'admin_add', 169, 170),
(96, 93, NULL, NULL, 'admin_edit', 171, 172),
(97, 93, NULL, NULL, 'admin_delete', 173, 174),
(99, 18, NULL, NULL, 'Settings', 176, 195),
(100, 99, NULL, NULL, 'admin_dashboard', 177, 178),
(101, 99, NULL, NULL, 'admin_index', 179, 180),
(102, 99, NULL, NULL, 'admin_view', 181, 182),
(103, 99, NULL, NULL, 'admin_add', 183, 184),
(104, 99, NULL, NULL, 'admin_edit', 185, 186),
(105, 99, NULL, NULL, 'admin_delete', 187, 188),
(106, 99, NULL, NULL, 'admin_prefix', 189, 190),
(107, 99, NULL, NULL, 'admin_moveup', 191, 192),
(108, 99, NULL, NULL, 'admin_movedown', 193, 194),
(109, 18, NULL, NULL, 'Terms', 196, 211),
(110, 109, NULL, NULL, 'admin_index', 197, 198),
(111, 109, NULL, NULL, 'admin_add', 199, 200),
(112, 109, NULL, NULL, 'admin_edit', 201, 202),
(113, 109, NULL, NULL, 'admin_delete', 203, 204),
(114, 109, NULL, NULL, 'admin_moveup', 205, 206),
(115, 109, NULL, NULL, 'admin_movedown', 207, 208),
(116, 109, NULL, NULL, 'admin_process', 209, 210),
(121, 18, NULL, NULL, 'Types', 212, 221),
(122, 121, NULL, NULL, 'admin_index', 213, 214),
(123, 121, NULL, NULL, 'admin_add', 215, 216),
(124, 121, NULL, NULL, 'admin_edit', 217, 218),
(125, 121, NULL, NULL, 'admin_delete', 219, 220),
(126, 18, NULL, NULL, 'Users', 222, 255),
(127, 126, NULL, NULL, 'admin_index', 223, 224),
(128, 126, NULL, NULL, 'admin_add', 225, 226),
(129, 126, NULL, NULL, 'admin_edit', 227, 228),
(130, 126, NULL, NULL, 'admin_reset_password', 229, 230),
(131, 126, NULL, NULL, 'admin_delete', 231, 232),
(132, 126, NULL, NULL, 'admin_login', 233, 234),
(133, 126, NULL, NULL, 'admin_logout', 235, 236),
(134, 126, NULL, NULL, 'index', 237, 238),
(135, 126, NULL, NULL, 'login', 239, 240),
(136, 126, NULL, NULL, 'logout', 241, 242),
(137, 126, NULL, NULL, 'view', 243, 244),
(138, 18, NULL, NULL, 'Vocabularies', 256, 265),
(139, 138, NULL, NULL, 'admin_index', 257, 258),
(140, 138, NULL, NULL, 'admin_add', 259, 260),
(141, 138, NULL, NULL, 'admin_edit', 261, 262),
(142, 138, NULL, NULL, 'admin_delete', 263, 264),
(143, 18, NULL, NULL, 'AclAcos', 266, 275),
(144, 143, NULL, NULL, 'admin_index', 267, 268),
(145, 143, NULL, NULL, 'admin_add', 269, 270),
(146, 143, NULL, NULL, 'admin_edit', 271, 272),
(147, 143, NULL, NULL, 'admin_delete', 273, 274),
(148, 18, NULL, NULL, 'AclActions', 276, 289),
(149, 148, NULL, NULL, 'admin_index', 277, 278),
(150, 148, NULL, NULL, 'admin_add', 279, 280),
(151, 148, NULL, NULL, 'admin_edit', 281, 282),
(152, 148, NULL, NULL, 'admin_delete', 283, 284),
(153, 148, NULL, NULL, 'admin_move', 285, 286),
(154, 148, NULL, NULL, 'admin_generate', 287, 288),
(155, 18, NULL, NULL, 'AclAros', 290, 299),
(156, 155, NULL, NULL, 'admin_index', 291, 292),
(157, 155, NULL, NULL, 'admin_add', 293, 294),
(158, 155, NULL, NULL, 'admin_edit', 295, 296),
(159, 155, NULL, NULL, 'admin_delete', 297, 298),
(160, 18, NULL, NULL, 'AclPermissions', 300, 305),
(161, 160, NULL, NULL, 'admin_index', 301, 302),
(162, 160, NULL, NULL, 'admin_toggle', 303, 304),
(164, 126, NULL, NULL, 'add', 245, 246),
(165, 126, NULL, NULL, 'activate', 247, 248),
(166, 126, NULL, NULL, 'edit', 249, 250),
(167, 126, NULL, NULL, 'forgot', 251, 252),
(168, 126, NULL, NULL, 'reset', 253, 254),
(169, NULL, 'Node', 27, '', 307, 308),
(171, 70, NULL, NULL, 'admin_process', 125, 126),
(172, 33, NULL, NULL, 'index', 55, 56);

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Role', 1, '', 1, 4),
(2, NULL, 'Role', 2, '', 5, 6),
(3, NULL, 'Role', 3, '', 7, 8),
(5, 1, 'User', 1, '', 2, 3);

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(18, 3, 38, '1', '1', '1', '1'),
(19, 2, 39, '1', '1', '1', '1'),
(20, 2, 38, '1', '1', '1', '1'),
(21, 3, 45, '1', '1', '1', '1'),
(22, 2, 45, '1', '1', '1', '1'),
(23, 2, 84, '1', '1', '1', '1'),
(24, 2, 85, '1', '1', '1', '1'),
(25, 2, 86, '1', '1', '1', '1'),
(26, 2, 87, '1', '1', '1', '1'),
(27, 3, 87, '1', '1', '1', '1'),
(28, 3, 86, '1', '1', '1', '1'),
(29, 3, 85, '1', '1', '1', '1'),
(30, 3, 84, '1', '1', '1', '1'),
(31, 3, 135, '1', '1', '1', '1'),
(32, 2, 136, '1', '1', '1', '1'),
(33, 2, 134, '1', '1', '1', '1'),
(34, 2, 137, '1', '1', '1', '1'),
(35, 3, 137, '1', '1', '1', '1'),
(36, 3, 164, '1', '1', '1', '1'),
(37, 3, 165, '1', '1', '1', '1'),
(38, 2, 166, '1', '1', '1', '1'),
(39, 3, 167, '1', '1', '1', '1'),
(40, 2, 168, '0', '0', '0', '0'),
(41, 3, 168, '1', '1', '1', '1'),
(42, 2, 172, '1', '1', '1', '1'),
(43, 3, 172, '1', '1', '1', '1');

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`id`, `region_id`, `title`, `alias`, `body`, `show_title`, `class`, `status`, `weight`, `element`, `visibility_roles`, `visibility_paths`, `visibility_php`, `params`, `updated`, `created`) VALUES
(3, 4, 'About', 'about', 'This is the content of your block. Can be modified in admin panel.', 1, '', 1, 1, '', '', '', '', '', '2009-10-30 19:10:34', '2009-07-26 17:13:14'),
(5, 4, 'Meta', 'meta', '[menu:meta]', 1, '', 1, 4, '', '', '', '', '', '2009-10-06 21:13:27', '2009-09-12 06:36:22'),
(6, 4, 'Blogroll', 'blogroll', '[menu:blogroll]', 1, '', 1, 3, '', '', '', '', '', '2009-10-06 21:13:30', '2009-09-12 23:33:27'),
(7, 4, 'Categories', 'categories', '[vocabulary:categories type="blog"]', 1, '', 1, 2, '', '', '', '', '', '2009-10-06 21:13:30', '2009-10-03 16:52:50');

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `parent_id`, `node_id`, `user_id`, `name`, `email`, `website`, `ip`, `title`, `body`, `rating`, `status`, `notify`, `type`, `comment_type`, `lft`, `rght`, `updated`, `created`) VALUES
(13, NULL, 21, 0, 'Mr Croogo', 'email@example.com', 'http://www.croogo.org', '127.0.0.1', '', 'Hi, this is the first comment.', NULL, 1, 0, 'blog', 'comment', 1, 2, '2009-10-06 22:13:05', '2009-10-03 19:43:52');

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `title`, `alias`, `body`, `name`, `position`, `address`, `address2`, `state`, `country`, `postcode`, `phone`, `fax`, `email`, `message_status`, `message_archive`, `message_count`, `message_spam_protection`, `message_captcha`, `message_notify`, `status`, `updated`, `created`) VALUES
(1, 'Contact', 'contact', '', '', '', '', '', '', '', '', '', '', 'you@your-site.com', 1, 0, 0, 0, 0, 1, 1, '2009-10-07 22:07:49', '2009-09-16 01:45:17');

--
-- Dumping data for table `i18n`
--


--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `title`, `native`, `alias`, `status`, `weight`, `updated`, `created`) VALUES
(1, 'English', 'English', 'eng', 1, 1, '2009-11-02 21:37:38', '2009-11-02 20:52:00');

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`id`, `parent_id`, `menu_id`, `title`, `description`, `link`, `target`, `rel`, `status`, `lft`, `rght`, `visibility_roles`, `params`, `updated`, `created`) VALUES
(5, NULL, 4, 'About', '', 'controller:nodes/action:view/type:page/slug:about', '', '', 1, 3, 4, '', '', '2009-10-06 23:14:21', '2009-08-19 12:23:33'),
(6, NULL, 4, 'Contact', '', 'controller:contacts/action:view/contact', '', '', 1, 5, 6, '', '', '2009-10-06 23:14:45', '2009-08-19 12:34:56'),
(7, NULL, 3, 'Home', '', '/', '', '', 1, 5, 6, '', '', '2009-10-06 21:17:06', '2009-09-06 21:32:54'),
(8, NULL, 3, 'About', '', '/about', '', '', 1, 7, 10, '', '', '2009-09-12 03:45:53', '2009-09-06 21:34:57'),
(9, 8, 3, 'Child link', '', '#', '', '', 0, 8, 9, '', '', '2009-10-06 23:13:06', '2009-09-12 03:52:23'),
(10, NULL, 5, 'Site Admin', '', '/admin', '', '', 1, 1, 2, '', '', '2009-09-12 06:34:09', '2009-09-12 06:34:09'),
(11, NULL, 5, 'Log out', '', '/users/logout', '', '', 1, 7, 8, '["1","2"]', '', '2009-09-12 06:35:22', '2009-09-12 06:34:41'),
(12, NULL, 6, 'Croogo', '', 'http://www.croogo.org', '', '', 1, 3, 4, '', '', '2009-09-12 23:31:59', '2009-09-12 23:31:59'),
(14, NULL, 6, 'CakePHP', '', 'http://www.cakephp.org', '', '', 1, 1, 2, '', '', '2009-10-07 03:25:25', '2009-09-12 23:38:43'),
(15, NULL, 3, 'Contact', '', '/controller:contacts/action:view/contact', '', '', 1, 11, 12, '', '', '2009-09-16 07:54:13', '2009-09-16 07:53:33'),
(16, NULL, 5, 'Entries (RSS)', '', '/nodes/promoted.rss', '', '', 1, 3, 4, '', '', '2009-10-27 17:46:22', '2009-10-27 17:46:22'),
(17, NULL, 5, 'Comments (RSS)', '', '/comments.rss', '', '', 1, 5, 6, '', '', '2009-10-27 17:46:54', '2009-10-27 17:46:54');

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `title`, `alias`, `description`, `status`, `weight`, `link_count`, `params`, `updated`, `created`) VALUES
(3, 'Main Menu', 'main', '', 1, NULL, 4, '', '2009-08-19 12:21:06', '2009-07-22 01:49:53'),
(4, 'Footer', 'footer', '', 1, NULL, 2, '', '2009-08-19 12:22:42', '2009-08-19 12:22:42'),
(5, 'Meta', 'meta', '', 1, NULL, 4, '', '2009-09-12 06:33:29', '2009-09-12 06:33:29'),
(6, 'Blogroll', 'blogroll', '', 1, NULL, 2, '', '2009-09-12 23:30:24', '2009-09-12 23:30:24');

--
-- Dumping data for table `messages`
--


--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `model`, `foreign_key`, `key`, `value`, `weight`) VALUES
(23, 'Node', 20, 'meta_keywords', 'key1, key2', NULL);

--
-- Dumping data for table `nodes`
--

INSERT INTO `nodes` (`id`, `parent_id`, `user_id`, `title`, `slug`, `body`, `excerpt`, `status`, `mime_type`, `comment_status`, `comment_count`, `promote`, `path`, `terms`, `sticky`, `lft`, `rght`, `visibility_roles`, `type`, `updated`, `created`) VALUES
(20, NULL, 1, 'About', 'about', '<p>This is an example of a Croogo page, you could edit this to put information about yourself or your site.</p>', '', 1, '', 0, 0, 0, '/page/about', '', 0, 1, 2, '', 'page', '2009-10-07 23:15:24', '2009-08-11 05:47:03'),
(21, NULL, 1, 'Hello World', 'hello-world', '<p>Welcome to Croogo. This is your first post. You can edit or delete it from the admin panel.</p>', '', 1, '', 2, 1, 1, '/blog/hello-world', '{"1":"uncategorized","5":"random"}', 0, 1, 2, '', 'blog', '2009-10-07 20:42:37', '2009-09-02 19:50:56');

--
-- Dumping data for table `nodes_terms`
--

INSERT INTO `nodes_terms` (`id`, `node_id`, `vocabulary_id`, `term_id`, `weight`) VALUES
(6406, 21, 0, 5, NULL),
(6405, 21, 0, 1, NULL);

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `title`, `alias`, `description`, `block_count`) VALUES
(3, 'none', '', '', 0),
(4, 'right', 'right', '', 4),
(6, 'left', 'left', '', 0),
(7, 'header', 'header', '', 0),
(8, 'footer', 'footer', '', 0),
(9, 'region1', 'region1', '', 0),
(10, 'region2', 'region2', '', 0),
(11, 'region3', 'region3', '', 0),
(12, 'region4', 'region4', '', 0),
(13, 'region5', 'region5', '', 0),
(14, 'region6', 'region6', '', 0),
(15, 'region7', 'region7', '', 0),
(16, 'region8', 'region8', '', 0),
(17, 'region9', 'region9', '', 0);

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `alias`, `created`, `updated`) VALUES
(1, 'Admin', 'admin', '2009-04-05 00:10:34', '2009-04-05 00:10:34'),
(2, 'Registered', 'registered', '2009-04-05 00:10:50', '2009-04-06 05:20:38'),
(3, 'Public', 'public', '2009-04-05 00:12:38', '2009-04-07 01:41:45');

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `title`, `description`, `input_type`, `editable`, `weight`, `params`) VALUES
(5, 'Admin.settings', 'Site,Meta,Reading,Writing,Comment,Service', '', '', '', 1, 1, ''),
(6, 'Site.title', 'Croogo', '', '', '', 1, 2, ''),
(7, 'Site.tagline', 'A CakePHP powered Content Management System.', '', '', 'textarea', 1, 3, ''),
(8, 'Site.email', 'you@your-site.com', '', '', '', 1, 4, ''),
(9, 'Site.status', '1', '', '', 'checkbox', 1, 5, ''),
(12, 'Meta.robots', 'index, follow', '', '', '', 1, 6, ''),
(13, 'Meta.keywords', 'croogo, Croogo', '', '', 'textarea', 1, 7, ''),
(14, 'Meta.description', 'Croogo - A CakePHP powered Content Management System', '', '', 'textarea', 1, 8, ''),
(15, 'Meta.generator', 'Croogo - Content Management System', '', '', '', 0, 9, ''),
(16, 'Service.akismet_key', 'your-key', '', '', '', 1, 11, ''),
(17, 'Service.recaptcha_public_key', 'your-public-key', '', '', '', 1, 12, ''),
(18, 'Service.recaptcha_private_key', 'your-private-key', '', '', '', 1, 13, ''),
(19, 'Service.akismet_url', 'http://your-blog.com', '', '', '', 1, 10, ''),
(20, 'Site.theme', '', '', '', '', 0, 14, ''),
(21, 'Site.feed_url', '', '', '', '', 0, 15, ''),
(22, 'Reading.nodes_per_page', '5', '', '', '', 1, 16, ''),
(23, 'Writing.wysiwyg', '1', 'Enable WYSIWYG editor', '', 'checkbox', 1, 17, ''),
(24, 'Comment.level', '1', '', 'levels deep (threaded comments)', '', 1, 18, ''),
(25, 'Comment.feed_limit', '10', '', 'number of comments to show in feed', '', 1, 19, ''),
(26, 'Site.locale', 'eng', '', '', 'text', 0, 20, ''),
(27, 'Reading.date_time_format', 'D, M d Y', '', '', '', 1, 21, ''),
(28, 'Comment.date_time_format', 'M d, Y', '', '', '', 1, 22, '');

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `parent_id`, `vocabulary_id`, `title`, `slug`, `description`, `lft`, `rght`, `status`, `updated`, `created`) VALUES
(1, NULL, 1, 'Uncategorized', 'uncategorized', '', 1, 2, 1, '2009-07-22 03:38:43', '2009-07-22 03:34:56'),
(2, NULL, 1, 'Announcements', 'announcements', '', 3, 8, 1, '2009-07-22 03:45:37', '2009-07-22 03:45:37'),
(6, NULL, 2, 'mytag', 'mytag', '', 9, 10, 1, '2009-08-26 14:42:43', '2009-08-26 14:42:43'),
(7, NULL, 3, 'test term', 'test-term-1', '', 11, 12, 1, '2009-09-02 19:27:26', '2009-09-02 19:27:26');

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `title`, `alias`, `description`, `format_show_author`, `format_show_date`, `comment_status`, `comment_approve`, `comment_spam_protection`, `comment_captcha`, `params`, `plugin`, `updated`, `created`) VALUES
(1, 'Page', 'page', 'A page is a simple method for creating and displaying information that rarely changes, such as an "About us" section of a website. By default, a page entry does not allow visitor comments.', 0, 0, 0, 1, 0, 0, '', '', '2009-09-09 00:23:24', '2009-09-02 18:06:27'),
(2, 'Blog', 'blog', 'A blog entry is a single post to an online journal, or blog.', 1, 1, 2, 1, 0, 0, '', '', '2009-09-15 12:15:43', '2009-09-02 18:20:44'),
(4, 'Node', 'node', 'Default content type.', 1, 1, 2, 1, 0, 0, '', '', '2009-10-06 21:53:15', '2009-09-05 23:51:56');

--
-- Dumping data for table `types_vocabularies`
--

INSERT INTO `types_vocabularies` (`id`, `type_id`, `vocabulary_id`, `weight`) VALUES
(23, 2, 2, NULL),
(22, 2, 1, NULL),
(25, 4, 2, NULL),
(24, 4, 1, NULL);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `password`, `name`, `email`, `website`, `activation_key`, `image`, `status`, `updated`, `created`) VALUES
(1, 1, 'admin', 'c054b152596745efa1d197b809fa7fc70ce586e5', 'Administrator', 'you@your-site.com', '/about', '', '', 1, '2009-10-07 22:23:27', '2009-04-05 00:20:34');

--
-- Dumping data for table `vocabularies`
--

INSERT INTO `vocabularies` (`id`, `title`, `alias`, `description`, `required`, `multiple`, `tags`, `plugin`, `term_count`, `weight`, `updated`, `created`) VALUES
(1, 'Categories', 'categories', '', 0, 0, 0, '', 2, NULL, '2009-07-22 02:16:21', '2009-07-22 02:16:21'),
(2, 'Tags', 'tags', '', 0, 0, 0, '', 1, NULL, '2009-07-22 02:16:34', '2009-07-22 02:16:34');
