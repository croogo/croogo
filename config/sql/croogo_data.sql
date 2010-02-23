SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, NULL, NULL, 'controllers', 1, 352),
(2, 1, NULL, NULL, 'Attachments', 2, 13),
(3, 2, NULL, NULL, 'admin_index', 3, 4),
(4, 2, NULL, NULL, 'admin_add', 5, 6),
(5, 2, NULL, NULL, 'admin_edit', 7, 8),
(6, 2, NULL, NULL, 'admin_delete', 9, 10),
(7, 2, NULL, NULL, 'admin_browse', 11, 12),
(8, 1, NULL, NULL, 'Blocks', 14, 29),
(9, 8, NULL, NULL, 'admin_index', 15, 16),
(10, 8, NULL, NULL, 'admin_add', 17, 18),
(11, 8, NULL, NULL, 'admin_edit', 19, 20),
(12, 8, NULL, NULL, 'admin_delete', 21, 22),
(13, 8, NULL, NULL, 'admin_moveup', 23, 24),
(14, 8, NULL, NULL, 'admin_movedown', 25, 26),
(15, 8, NULL, NULL, 'admin_process', 27, 28),
(16, 1, NULL, NULL, 'Comments', 30, 45),
(17, 16, NULL, NULL, 'admin_index', 31, 32),
(18, 16, NULL, NULL, 'admin_edit', 33, 34),
(19, 16, NULL, NULL, 'admin_delete', 35, 36),
(20, 16, NULL, NULL, 'admin_process', 37, 38),
(21, 16, NULL, NULL, 'index', 39, 40),
(22, 16, NULL, NULL, 'add', 41, 42),
(23, 16, NULL, NULL, 'delete', 43, 44),
(24, 1, NULL, NULL, 'Contacts', 46, 57),
(25, 24, NULL, NULL, 'admin_index', 47, 48),
(26, 24, NULL, NULL, 'admin_add', 49, 50),
(27, 24, NULL, NULL, 'admin_edit', 51, 52),
(28, 24, NULL, NULL, 'admin_delete', 53, 54),
(29, 24, NULL, NULL, 'view', 55, 56),
(30, 1, NULL, NULL, 'Filemanager', 58, 79),
(31, 30, NULL, NULL, 'admin_index', 59, 60),
(32, 30, NULL, NULL, 'admin_browse', 61, 62),
(33, 30, NULL, NULL, 'admin_editfile', 63, 64),
(34, 30, NULL, NULL, 'admin_upload', 65, 66),
(35, 30, NULL, NULL, 'admin_delete_file', 67, 68),
(36, 30, NULL, NULL, 'admin_delete_directory', 69, 70),
(37, 30, NULL, NULL, 'admin_rename', 71, 72),
(38, 30, NULL, NULL, 'admin_create_directory', 73, 74),
(39, 30, NULL, NULL, 'admin_create_file', 75, 76),
(40, 30, NULL, NULL, 'admin_chmod', 77, 78),
(41, 1, NULL, NULL, 'Languages', 80, 95),
(42, 41, NULL, NULL, 'admin_index', 81, 82),
(43, 41, NULL, NULL, 'admin_add', 83, 84),
(44, 41, NULL, NULL, 'admin_edit', 85, 86),
(45, 41, NULL, NULL, 'admin_delete', 87, 88),
(46, 41, NULL, NULL, 'admin_moveup', 89, 90),
(47, 41, NULL, NULL, 'admin_movedown', 91, 92),
(48, 41, NULL, NULL, 'admin_select', 93, 94),
(49, 1, NULL, NULL, 'Links', 96, 111),
(50, 49, NULL, NULL, 'admin_index', 97, 98),
(51, 49, NULL, NULL, 'admin_add', 99, 100),
(52, 49, NULL, NULL, 'admin_edit', 101, 102),
(53, 49, NULL, NULL, 'admin_delete', 103, 104),
(54, 49, NULL, NULL, 'admin_moveup', 105, 106),
(55, 49, NULL, NULL, 'admin_movedown', 107, 108),
(56, 49, NULL, NULL, 'admin_process', 109, 110),
(57, 1, NULL, NULL, 'Menus', 112, 121),
(58, 57, NULL, NULL, 'admin_index', 113, 114),
(59, 57, NULL, NULL, 'admin_add', 115, 116),
(60, 57, NULL, NULL, 'admin_edit', 117, 118),
(61, 57, NULL, NULL, 'admin_delete', 119, 120),
(62, 1, NULL, NULL, 'Messages', 122, 131),
(63, 62, NULL, NULL, 'admin_index', 123, 124),
(64, 62, NULL, NULL, 'admin_edit', 125, 126),
(65, 62, NULL, NULL, 'admin_delete', 127, 128),
(66, 62, NULL, NULL, 'admin_process', 129, 130),
(67, 1, NULL, NULL, 'Nodes', 132, 161),
(68, 67, NULL, NULL, 'admin_index', 133, 134),
(69, 67, NULL, NULL, 'admin_create', 135, 136),
(70, 67, NULL, NULL, 'admin_add', 137, 138),
(71, 67, NULL, NULL, 'admin_edit', 139, 140),
(72, 67, NULL, NULL, 'admin_update_paths', 141, 142),
(73, 67, NULL, NULL, 'admin_delete', 143, 144),
(74, 67, NULL, NULL, 'admin_delete_meta', 145, 146),
(75, 67, NULL, NULL, 'admin_add_meta', 147, 148),
(76, 67, NULL, NULL, 'admin_process', 149, 150),
(77, 67, NULL, NULL, 'index', 151, 152),
(78, 67, NULL, NULL, 'term', 153, 154),
(79, 67, NULL, NULL, 'promoted', 155, 156),
(80, 67, NULL, NULL, 'search', 157, 158),
(81, 67, NULL, NULL, 'view', 159, 160),
(82, 1, NULL, NULL, 'Regions', 162, 171),
(83, 82, NULL, NULL, 'admin_index', 163, 164),
(84, 82, NULL, NULL, 'admin_add', 165, 166),
(85, 82, NULL, NULL, 'admin_edit', 167, 168),
(86, 82, NULL, NULL, 'admin_delete', 169, 170),
(87, 1, NULL, NULL, 'Roles', 172, 181),
(88, 87, NULL, NULL, 'admin_index', 173, 174),
(89, 87, NULL, NULL, 'admin_add', 175, 176),
(90, 87, NULL, NULL, 'admin_edit', 177, 178),
(91, 87, NULL, NULL, 'admin_delete', 179, 180),
(92, 1, NULL, NULL, 'Settings', 182, 201),
(93, 92, NULL, NULL, 'admin_dashboard', 183, 184),
(94, 92, NULL, NULL, 'admin_index', 185, 186),
(95, 92, NULL, NULL, 'admin_view', 187, 188),
(96, 92, NULL, NULL, 'admin_add', 189, 190),
(97, 92, NULL, NULL, 'admin_edit', 191, 192),
(98, 92, NULL, NULL, 'admin_delete', 193, 194),
(99, 92, NULL, NULL, 'admin_prefix', 195, 196),
(100, 92, NULL, NULL, 'admin_moveup', 197, 198),
(101, 92, NULL, NULL, 'admin_movedown', 199, 200),
(102, 1, NULL, NULL, 'Terms', 202, 217),
(103, 102, NULL, NULL, 'admin_index', 203, 204),
(104, 102, NULL, NULL, 'admin_add', 205, 206),
(105, 102, NULL, NULL, 'admin_edit', 207, 208),
(106, 102, NULL, NULL, 'admin_delete', 209, 210),
(107, 102, NULL, NULL, 'admin_moveup', 211, 212),
(108, 102, NULL, NULL, 'admin_movedown', 213, 214),
(109, 102, NULL, NULL, 'admin_process', 215, 216),
(110, 1, NULL, NULL, 'Types', 218, 227),
(111, 110, NULL, NULL, 'admin_index', 219, 220),
(112, 110, NULL, NULL, 'admin_add', 221, 222),
(113, 110, NULL, NULL, 'admin_edit', 223, 224),
(114, 110, NULL, NULL, 'admin_delete', 225, 226),
(115, 1, NULL, NULL, 'Users', 228, 261),
(116, 115, NULL, NULL, 'admin_index', 229, 230),
(117, 115, NULL, NULL, 'admin_add', 231, 232),
(118, 115, NULL, NULL, 'admin_edit', 233, 234),
(119, 115, NULL, NULL, 'admin_reset_password', 235, 236),
(120, 115, NULL, NULL, 'admin_delete', 237, 238),
(121, 115, NULL, NULL, 'admin_login', 239, 240),
(122, 115, NULL, NULL, 'admin_logout', 241, 242),
(123, 115, NULL, NULL, 'index', 243, 244),
(124, 115, NULL, NULL, 'add', 245, 246),
(125, 115, NULL, NULL, 'activate', 247, 248),
(126, 115, NULL, NULL, 'edit', 249, 250),
(127, 115, NULL, NULL, 'forgot', 251, 252),
(128, 115, NULL, NULL, 'reset', 253, 254),
(129, 115, NULL, NULL, 'login', 255, 256),
(130, 115, NULL, NULL, 'logout', 257, 258),
(131, 115, NULL, NULL, 'view', 259, 260),
(132, 1, NULL, NULL, 'Vocabularies', 262, 271),
(133, 132, NULL, NULL, 'admin_index', 263, 264),
(134, 132, NULL, NULL, 'admin_add', 265, 266),
(135, 132, NULL, NULL, 'admin_edit', 267, 268),
(136, 132, NULL, NULL, 'admin_delete', 269, 270),
(137, 1, NULL, NULL, 'AclAcos', 272, 281),
(138, 137, NULL, NULL, 'admin_index', 273, 274),
(139, 137, NULL, NULL, 'admin_add', 275, 276),
(140, 137, NULL, NULL, 'admin_edit', 277, 278),
(141, 137, NULL, NULL, 'admin_delete', 279, 280),
(142, 1, NULL, NULL, 'AclActions', 282, 295),
(143, 142, NULL, NULL, 'admin_index', 283, 284),
(144, 142, NULL, NULL, 'admin_add', 285, 286),
(145, 142, NULL, NULL, 'admin_edit', 287, 288),
(146, 142, NULL, NULL, 'admin_delete', 289, 290),
(147, 142, NULL, NULL, 'admin_move', 291, 292),
(148, 142, NULL, NULL, 'admin_generate', 293, 294),
(149, 1, NULL, NULL, 'AclAros', 296, 305),
(150, 149, NULL, NULL, 'admin_index', 297, 298),
(151, 149, NULL, NULL, 'admin_add', 299, 300),
(152, 149, NULL, NULL, 'admin_edit', 301, 302),
(153, 149, NULL, NULL, 'admin_delete', 303, 304),
(154, 1, NULL, NULL, 'AclPermissions', 306, 311),
(155, 154, NULL, NULL, 'admin_index', 307, 308),
(156, 154, NULL, NULL, 'admin_toggle', 309, 310),
(159, 1, NULL, NULL, 'ExtensionsHooks', 312, 317),
(160, 159, NULL, NULL, 'admin_index', 313, 314),
(161, 159, NULL, NULL, 'admin_toggle', 315, 316),
(162, 1, NULL, NULL, 'ExtensionsLocales', 318, 329),
(163, 162, NULL, NULL, 'admin_index', 319, 320),
(164, 162, NULL, NULL, 'admin_activate', 321, 322),
(165, 162, NULL, NULL, 'admin_add', 323, 324),
(166, 162, NULL, NULL, 'admin_edit', 325, 326),
(167, 162, NULL, NULL, 'admin_delete', 327, 328),
(168, 1, NULL, NULL, 'ExtensionsPlugins', 330, 337),
(169, 168, NULL, NULL, 'admin_index', 331, 332),
(170, 168, NULL, NULL, 'admin_add', 333, 334),
(171, 168, NULL, NULL, 'admin_delete', 335, 336),
(172, 1, NULL, NULL, 'ExtensionsThemes', 338, 351),
(173, 172, NULL, NULL, 'admin_index', 339, 340),
(174, 172, NULL, NULL, 'admin_activate', 341, 342),
(175, 172, NULL, NULL, 'admin_add', 343, 344),
(176, 172, NULL, NULL, 'admin_editor', 345, 346),
(177, 172, NULL, NULL, 'admin_save', 347, 348),
(178, 172, NULL, NULL, 'admin_delete', 349, 350);

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
(1, 2, 23, '1', '1', '1', '1'),
(2, 2, 22, '1', '1', '1', '1'),
(3, 2, 21, '1', '1', '1', '1'),
(4, 3, 21, '1', '1', '1', '1'),
(5, 3, 22, '1', '1', '1', '1'),
(6, 2, 29, '1', '1', '1', '1'),
(7, 3, 29, '1', '1', '1', '1'),
(8, 2, 77, '1', '1', '1', '1'),
(9, 2, 78, '1', '1', '1', '1'),
(10, 2, 79, '1', '1', '1', '1'),
(11, 2, 80, '1', '1', '1', '1'),
(12, 2, 81, '1', '1', '1', '1'),
(13, 3, 77, '1', '1', '1', '1'),
(14, 3, 78, '1', '1', '1', '1'),
(15, 3, 79, '1', '1', '1', '1'),
(16, 3, 80, '1', '1', '1', '1'),
(17, 3, 81, '1', '1', '1', '1'),
(18, 2, 123, '1', '1', '1', '1'),
(19, 3, 124, '1', '1', '1', '1'),
(20, 3, 125, '1', '1', '1', '1'),
(21, 2, 126, '1', '1', '1', '1'),
(22, 3, 127, '1', '1', '1', '1'),
(23, 3, 128, '1', '1', '1', '1'),
(24, 3, 129, '1', '1', '1', '1'),
(25, 2, 130, '1', '1', '1', '1'),
(26, 2, 131, '1', '1', '1', '1'),
(27, 3, 131, '1', '1', '1', '1');

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`id`, `region_id`, `title`, `alias`, `body`, `show_title`, `class`, `status`, `weight`, `element`, `visibility_roles`, `visibility_paths`, `visibility_php`, `params`, `updated`, `created`) VALUES
(3, 4, 'About', 'about', 'This is the content of your block. Can be modified in admin panel.', 1, '', 1, 2, '', '', '', '', '', '2009-12-20 03:07:39', '2009-07-26 17:13:14'),
(8, 4, 'Search', 'search', '', 0, '', 1, 1, 'search', '', '', '', '', '2009-12-20 03:07:39', '2009-12-20 03:07:27'),
(5, 4, 'Meta', 'meta', '[menu:meta]', 1, '', 1, 6, '', '', '', '', '', '2009-12-22 05:17:39', '2009-09-12 06:36:22'),
(6, 4, 'Blogroll', 'blogroll', '[menu:blogroll]', 1, '', 1, 4, '', '', '', '', '', '2009-12-20 03:07:33', '2009-09-12 23:33:27'),
(7, 4, 'Categories', 'categories', '[vocabulary:categories type="blog"]', 1, '', 1, 3, '', '', '', '', '', '2009-12-20 03:07:36', '2009-10-03 16:52:50'),
(9, 4, 'Recent Posts', 'recent_posts', '[node:recent_posts conditions="Node.type:blog" order="Node.id DESC"]', 1, '', 1, 5, '', '', '', '', '', '2009-12-22 05:17:39', '2009-12-22 05:17:32');

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
(20, NULL, 1, 'About', 'about', '<p>This is an example of a Croogo page, you could edit this to put information about yourself or your site.</p>', '', 1, '', 0, 0, 0, '/about', '', 0, 1, 2, '', 'page', '2009-12-28 21:54:31', '2009-12-25 22:00:00'),
(21, NULL, 1, 'Hello World', 'hello-world', '<p>Welcome to Croogo. This is your first post. You can edit or delete it from the admin panel.</p>', '', 1, '', 2, 1, 1, '/blog/hello-world', '{"1":"uncategorized"}', 0, 1, 2, '', 'blog', '2009-12-28 21:55:09', '2009-12-25 11:00:00');

--
-- Dumping data for table `nodes_terms`
--

INSERT INTO `nodes_terms` (`id`, `node_id`, `vocabulary_id`, `term_id`, `weight`) VALUES
(6407, 21, 0, 1, NULL);

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `title`, `alias`, `description`, `block_count`) VALUES
(3, 'none', '', '', 0),
(4, 'right', 'right', '', 6),
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
(9, 'Site.status', '1', '', '', 'checkbox', 1, 6, ''),
(12, 'Meta.robots', 'index, follow', '', '', '', 1, 7, ''),
(13, 'Meta.keywords', 'croogo, Croogo', '', '', 'textarea', 1, 8, ''),
(14, 'Meta.description', 'Croogo - A CakePHP powered Content Management System', '', '', 'textarea', 1, 9, ''),
(15, 'Meta.generator', 'Croogo - Content Management System', '', '', '', 0, 10, ''),
(16, 'Service.akismet_key', 'your-key', '', '', '', 1, 12, ''),
(17, 'Service.recaptcha_public_key', 'your-public-key', '', '', '', 1, 13, ''),
(18, 'Service.recaptcha_private_key', 'your-private-key', '', '', '', 1, 14, ''),
(19, 'Service.akismet_url', 'http://your-blog.com', '', '', '', 1, 11, ''),
(20, 'Site.theme', '', '', '', '', 0, 15, ''),
(21, 'Site.feed_url', '', '', '', '', 0, 16, ''),
(22, 'Reading.nodes_per_page', '5', '', '', '', 1, 17, ''),
(23, 'Writing.wysiwyg', '1', 'Enable WYSIWYG editor', '', 'checkbox', 1, 18, ''),
(24, 'Comment.level', '1', '', 'levels deep (threaded comments)', '', 1, 19, ''),
(25, 'Comment.feed_limit', '10', '', 'number of comments to show in feed', '', 1, 20, ''),
(26, 'Site.locale', 'eng', '', '', 'text', 0, 21, ''),
(27, 'Reading.date_time_format', 'D, M d Y H:i:s', '', '', '', 1, 22, ''),
(28, 'Comment.date_time_format', 'M d, Y', '', '', '', 1, 23, ''),
(29, 'Site.timezone', '0', '', 'zero (0) for GMT', '', 1, 5, '');

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

INSERT INTO `users` (`id`, `role_id`, `username`, `password`, `name`, `email`, `website`, `activation_key`, `image`, `bio`, `timezone`, `status`, `updated`, `created`) VALUES
(1, 1, 'admin', 'c054b152596745efa1d197b809fa7fc70ce586e5', 'Administrator', 'you@your-site.com', '/about', '', '', '', '0', 1, '2009-10-07 22:23:27', '2009-04-05 00:20:34');

--
-- Dumping data for table `vocabularies`
--

INSERT INTO `vocabularies` (`id`, `title`, `alias`, `description`, `required`, `multiple`, `tags`, `plugin`, `term_count`, `weight`, `updated`, `created`) VALUES
(1, 'Categories', 'categories', '', 0, 0, 0, '', 2, NULL, '2009-07-22 02:16:21', '2009-07-22 02:16:21'),
(2, 'Tags', 'tags', '', 0, 0, 0, '', 1, NULL, '2009-07-22 02:16:34', '2009-07-22 02:16:34');
