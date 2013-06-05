SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Dumping data for table `acos`
--

INSERT IGNORE INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, NULL, NULL, 'controllers', 1, 386),
(2, 1, NULL, NULL, 'Acl', 2, 25),
(3, 2, NULL, NULL, 'AclActions', 3, 16),
(4, 3, NULL, NULL, 'admin_index', 4, 5),
(5, 3, NULL, NULL, 'admin_add', 6, 7),
(6, 3, NULL, NULL, 'admin_edit', 8, 9),
(7, 3, NULL, NULL, 'admin_delete', 10, 11),
(8, 3, NULL, NULL, 'admin_move', 12, 13),
(9, 3, NULL, NULL, 'admin_generate', 14, 15),
(10, 2, NULL, NULL, 'AclPermissions', 17, 24),
(11, 10, NULL, NULL, 'admin_index', 18, 19),
(12, 10, NULL, NULL, 'admin_toggle', 20, 21),
(13, 10, NULL, NULL, 'admin_upgrade', 22, 23),
(14, 1, NULL, NULL, 'Blocks', 26, 55),
(15, 14, NULL, NULL, 'Blocks', 27, 44),
(16, 15, NULL, NULL, 'admin_toggle', 28, 29),
(17, 15, NULL, NULL, 'admin_index', 30, 31),
(18, 15, NULL, NULL, 'admin_add', 32, 33),
(19, 15, NULL, NULL, 'admin_edit', 34, 35),
(20, 15, NULL, NULL, 'admin_delete', 36, 37),
(21, 15, NULL, NULL, 'admin_moveup', 38, 39),
(22, 15, NULL, NULL, 'admin_movedown', 40, 41),
(23, 15, NULL, NULL, 'admin_process', 42, 43),
(24, 14, NULL, NULL, 'Regions', 45, 54),
(25, 24, NULL, NULL, 'admin_index', 46, 47),
(26, 24, NULL, NULL, 'admin_add', 48, 49),
(27, 24, NULL, NULL, 'admin_edit', 50, 51),
(28, 24, NULL, NULL, 'admin_delete', 52, 53),
(29, 1, NULL, NULL, 'Comments', 56, 73),
(30, 29, NULL, NULL, 'Comments', 57, 72),
(31, 30, NULL, NULL, 'admin_index', 58, 59),
(32, 30, NULL, NULL, 'admin_edit', 60, 61),
(33, 30, NULL, NULL, 'admin_delete', 62, 63),
(34, 30, NULL, NULL, 'admin_process', 64, 65),
(35, 30, NULL, NULL, 'index', 66, 67),
(36, 30, NULL, NULL, 'add', 68, 69),
(37, 30, NULL, NULL, 'delete', 70, 71),
(38, 1, NULL, NULL, 'Contacts', 74, 97),
(39, 38, NULL, NULL, 'Contacts', 75, 86),
(40, 39, NULL, NULL, 'admin_index', 76, 77),
(41, 39, NULL, NULL, 'admin_add', 78, 79),
(42, 39, NULL, NULL, 'admin_edit', 80, 81),
(43, 39, NULL, NULL, 'admin_delete', 82, 83),
(44, 39, NULL, NULL, 'view', 84, 85),
(45, 38, NULL, NULL, 'Messages', 87, 96),
(46, 45, NULL, NULL, 'admin_index', 88, 89),
(47, 45, NULL, NULL, 'admin_edit', 90, 91),
(48, 45, NULL, NULL, 'admin_delete', 92, 93),
(49, 45, NULL, NULL, 'admin_process', 94, 95),
(50, 1, NULL, NULL, 'Croogo', 98, 99),
(51, 1, NULL, NULL, 'Extensions', 100, 139),
(52, 51, NULL, NULL, 'ExtensionsLocales', 101, 112),
(53, 52, NULL, NULL, 'admin_index', 102, 103),
(54, 52, NULL, NULL, 'admin_activate', 104, 105),
(55, 52, NULL, NULL, 'admin_add', 106, 107),
(56, 52, NULL, NULL, 'admin_edit', 108, 109),
(57, 52, NULL, NULL, 'admin_delete', 110, 111),
(58, 51, NULL, NULL, 'ExtensionsPlugins', 113, 124),
(59, 58, NULL, NULL, 'admin_index', 114, 115),
(60, 58, NULL, NULL, 'admin_add', 116, 117),
(61, 58, NULL, NULL, 'admin_delete', 118, 119),
(62, 58, NULL, NULL, 'admin_toggle', 120, 121),
(63, 58, NULL, NULL, 'admin_migrate', 122, 123),
(64, 51, NULL, NULL, 'ExtensionsThemes', 125, 138),
(65, 64, NULL, NULL, 'admin_index', 126, 127),
(66, 64, NULL, NULL, 'admin_activate', 128, 129),
(67, 64, NULL, NULL, 'admin_add', 130, 131),
(68, 64, NULL, NULL, 'admin_editor', 132, 133),
(69, 64, NULL, NULL, 'admin_save', 134, 135),
(70, 64, NULL, NULL, 'admin_delete', 136, 137),
(71, 1, NULL, NULL, 'FileManager', 140, 175),
(72, 71, NULL, NULL, 'Attachments', 141, 152),
(73, 72, NULL, NULL, 'admin_index', 142, 143),
(74, 72, NULL, NULL, 'admin_add', 144, 145),
(75, 72, NULL, NULL, 'admin_edit', 146, 147),
(76, 72, NULL, NULL, 'admin_delete', 148, 149),
(77, 72, NULL, NULL, 'admin_browse', 150, 151),
(78, 71, NULL, NULL, 'FileManager', 153, 174),
(79, 78, NULL, NULL, 'admin_index', 154, 155),
(80, 78, NULL, NULL, 'admin_browse', 156, 157),
(81, 78, NULL, NULL, 'admin_editfile', 158, 159),
(82, 78, NULL, NULL, 'admin_upload', 160, 161),
(83, 78, NULL, NULL, 'admin_delete_file', 162, 163),
(84, 78, NULL, NULL, 'admin_delete_directory', 164, 165),
(85, 78, NULL, NULL, 'admin_rename', 166, 167),
(86, 78, NULL, NULL, 'admin_create_directory', 168, 169),
(87, 78, NULL, NULL, 'admin_create_file', 170, 171),
(88, 78, NULL, NULL, 'admin_chmod', 172, 173),
(89, 1, NULL, NULL, 'Install', 176, 189),
(90, 89, NULL, NULL, 'Install', 177, 188),
(91, 90, NULL, NULL, 'index', 178, 179),
(92, 90, NULL, NULL, 'database', 180, 181),
(93, 90, NULL, NULL, 'data', 182, 183),
(94, 90, NULL, NULL, 'adminuser', 184, 185),
(95, 90, NULL, NULL, 'finish', 186, 187),
(96, 1, NULL, NULL, 'Menus', 190, 219),
(97, 96, NULL, NULL, 'Links', 191, 208),
(98, 97, NULL, NULL, 'admin_toggle', 192, 193),
(99, 97, NULL, NULL, 'admin_index', 194, 195),
(100, 97, NULL, NULL, 'admin_add', 196, 197),
(101, 97, NULL, NULL, 'admin_edit', 198, 199),
(102, 97, NULL, NULL, 'admin_delete', 200, 201),
(103, 97, NULL, NULL, 'admin_moveup', 202, 203),
(104, 97, NULL, NULL, 'admin_movedown', 204, 205),
(105, 97, NULL, NULL, 'admin_process', 206, 207),
(106, 96, NULL, NULL, 'Menus', 209, 218),
(107, 106, NULL, NULL, 'admin_index', 210, 211),
(108, 106, NULL, NULL, 'admin_add', 212, 213),
(109, 106, NULL, NULL, 'admin_edit', 214, 215),
(110, 106, NULL, NULL, 'admin_delete', 216, 217),
(111, 1, NULL, NULL, 'Meta', 220, 221),
(112, 1, NULL, NULL, 'Migrations', 222, 223),
(113, 1, NULL, NULL, 'Nodes', 224, 257),
(114, 113, NULL, NULL, 'Nodes', 225, 256),
(115, 114, NULL, NULL, 'admin_toggle', 226, 227),
(116, 114, NULL, NULL, 'admin_index', 228, 229),
(117, 114, NULL, NULL, 'admin_create', 230, 231),
(118, 114, NULL, NULL, 'admin_add', 232, 233),
(119, 114, NULL, NULL, 'admin_edit', 234, 235),
(120, 114, NULL, NULL, 'admin_update_paths', 236, 237),
(121, 114, NULL, NULL, 'admin_delete', 238, 239),
(122, 114, NULL, NULL, 'admin_delete_meta', 240, 241),
(123, 114, NULL, NULL, 'admin_add_meta', 242, 243),
(124, 114, NULL, NULL, 'admin_process', 244, 245),
(125, 114, NULL, NULL, 'index', 246, 247),
(126, 114, NULL, NULL, 'term', 248, 249),
(127, 114, NULL, NULL, 'promoted', 250, 251),
(128, 114, NULL, NULL, 'search', 252, 253),
(129, 114, NULL, NULL, 'view', 254, 255),
(130, 1, NULL, NULL, 'Search', 258, 259),
(131, 1, NULL, NULL, 'Settings', 260, 297),
(132, 131, NULL, NULL, 'Languages', 261, 276),
(133, 132, NULL, NULL, 'admin_index', 262, 263),
(134, 132, NULL, NULL, 'admin_add', 264, 265),
(135, 132, NULL, NULL, 'admin_edit', 266, 267),
(136, 132, NULL, NULL, 'admin_delete', 268, 269),
(137, 132, NULL, NULL, 'admin_moveup', 270, 271),
(138, 132, NULL, NULL, 'admin_movedown', 272, 273),
(139, 132, NULL, NULL, 'admin_select', 274, 275),
(140, 131, NULL, NULL, 'Settings', 277, 296),
(141, 140, NULL, NULL, 'admin_dashboard', 278, 279),
(142, 140, NULL, NULL, 'admin_index', 280, 281),
(143, 140, NULL, NULL, 'admin_view', 282, 283),
(144, 140, NULL, NULL, 'admin_add', 284, 285),
(145, 140, NULL, NULL, 'admin_edit', 286, 287),
(146, 140, NULL, NULL, 'admin_delete', 288, 289),
(147, 140, NULL, NULL, 'admin_prefix', 290, 291),
(148, 140, NULL, NULL, 'admin_moveup', 292, 293),
(149, 140, NULL, NULL, 'admin_movedown', 294, 295),
(150, 1, NULL, NULL, 'Taxonomy', 298, 337),
(151, 150, NULL, NULL, 'Terms', 299, 312),
(152, 151, NULL, NULL, 'admin_index', 300, 301),
(153, 151, NULL, NULL, 'admin_add', 302, 303),
(154, 151, NULL, NULL, 'admin_edit', 304, 305),
(155, 151, NULL, NULL, 'admin_delete', 306, 307),
(156, 151, NULL, NULL, 'admin_moveup', 308, 309),
(157, 151, NULL, NULL, 'admin_movedown', 310, 311),
(158, 150, NULL, NULL, 'Types', 313, 322),
(159, 158, NULL, NULL, 'admin_index', 314, 315),
(160, 158, NULL, NULL, 'admin_add', 316, 317),
(161, 158, NULL, NULL, 'admin_edit', 318, 319),
(162, 158, NULL, NULL, 'admin_delete', 320, 321),
(163, 150, NULL, NULL, 'Vocabularies', 323, 336),
(164, 163, NULL, NULL, 'admin_index', 324, 325),
(165, 163, NULL, NULL, 'admin_add', 326, 327),
(166, 163, NULL, NULL, 'admin_edit', 328, 329),
(167, 163, NULL, NULL, 'admin_delete', 330, 331),
(168, 163, NULL, NULL, 'admin_moveup', 332, 333),
(169, 163, NULL, NULL, 'admin_movedown', 334, 335),
(170, 1, NULL, NULL, 'Ckeditor', 338, 339),
(171, 1, NULL, NULL, 'Users', 340, 385),
(172, 171, NULL, NULL, 'Roles', 341, 350),
(173, 172, NULL, NULL, 'admin_index', 342, 343),
(174, 172, NULL, NULL, 'admin_add', 344, 345),
(175, 172, NULL, NULL, 'admin_edit', 346, 347),
(176, 172, NULL, NULL, 'admin_delete', 348, 349),
(177, 171, NULL, NULL, 'Users', 351, 384),
(178, 177, NULL, NULL, 'admin_index', 352, 353),
(179, 177, NULL, NULL, 'admin_add', 354, 355),
(180, 177, NULL, NULL, 'admin_edit', 356, 357),
(181, 177, NULL, NULL, 'admin_reset_password', 358, 359),
(182, 177, NULL, NULL, 'admin_delete', 360, 361),
(183, 177, NULL, NULL, 'admin_login', 362, 363),
(184, 177, NULL, NULL, 'admin_logout', 364, 365),
(185, 177, NULL, NULL, 'index', 366, 367),
(186, 177, NULL, NULL, 'add', 368, 369),
(187, 177, NULL, NULL, 'activate', 370, 371),
(188, 177, NULL, NULL, 'edit', 372, 373),
(189, 177, NULL, NULL, 'forgot', 374, 375),
(190, 177, NULL, NULL, 'reset', 376, 377),
(191, 177, NULL, NULL, 'login', 378, 379),
(192, 177, NULL, NULL, 'logout', 380, 381),
(193, 177, NULL, NULL, 'view', 382, 383);

--
-- Dumping data for table `aros`
--

INSERT IGNORE INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, 2, 'Role', 1, 'Role-admin', 3, 4),
(2, 3, 'Role', 2, 'Role-registered', 2, 5),
(3, NULL, 'Role', 3, 'Role-public', 1, 6);

--
-- Dumping data for table `aros_acos`
--

INSERT IGNORE INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 3, 35, '1', '1', '1', '1'),
(2, 3, 36, '1', '1', '1', '1'),
(3, 2, 37, '1', '1', '1', '1'),
(4, 3, 44, '1', '1', '1', '1'),
(5, 3, 125, '1', '1', '1', '1'),
(6, 3, 126, '1', '1', '1', '1'),
(7, 3, 127, '1', '1', '1', '1'),
(8, 3, 128, '1', '1', '1', '1'),
(9, 3, 129, '1', '1', '1', '1'),
(10, 2, 185, '1', '1', '1', '1'),
(11, 3, 186, '1', '1', '1', '1'),
(12, 3, 187, '1', '1', '1', '1'),
(13, 2, 188, '1', '1', '1', '1'),
(14, 3, 189, '1', '1', '1', '1'),
(15, 3, 190, '1', '1', '1', '1'),
(16, 3, 191, '1', '1', '1', '1'),
(17, 2, 192, '1', '1', '1', '1'),
(18, 2, 193, '1', '1', '1', '1');

--
-- Dumping data for table `blocks`
--

INSERT IGNORE INTO `blocks` (`id`, `region_id`, `title`, `alias`, `body`, `show_title`, `class`, `status`, `weight`, `element`, `visibility_roles`, `visibility_paths`, `visibility_php`, `params`, `updated`, `created`) VALUES
(3, 4, 'About', 'about', 'This is the content of your block. Can be modified in admin panel.', 1, '', 1, 2, '', '', '', '', '', '2009-12-20 03:07:39', '2009-07-26 17:13:14'),
(8, 4, 'Search', 'search', '', 0, '', 1, 1, 'Nodes.search', '', '', '', '', '2009-12-20 03:07:39', '2009-12-20 03:07:27'),
(5, 4, 'Meta', 'meta', '[menu:meta]', 1, '', 1, 6, '', '', '', '', '', '2009-12-22 05:17:39', '2009-09-12 06:36:22'),
(6, 4, 'Blogroll', 'blogroll', '[menu:blogroll]', 1, '', 1, 4, '', '', '', '', '', '2009-12-20 03:07:33', '2009-09-12 23:33:27'),
(7, 4, 'Categories', 'categories', '[vocabulary:categories type="blog"]', 1, '', 1, 3, '', '', '', '', '', '2009-12-20 03:07:36', '2009-10-03 16:52:50'),
(9, 4, 'Recent Posts', 'recent_posts', '[node:recent_posts conditions="Node.type:blog" order="Node.id DESC" limit="5"]', 1, '', 1, 5, '', '', '', '', '', '2010-04-08 21:09:31', '2009-12-22 05:17:32');

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `parent_id`, `node_id`, `user_id`, `name`, `email`, `website`, `ip`, `title`, `body`, `rating`, `status`, `notify`, `type`, `comment_type`, `lft`, `rght`, `updated`, `created`) VALUES
(1, NULL, 1, 0, 'Mr Croogo', 'email@example.com', 'http://www.croogo.org', '127.0.0.1', '', 'Hi, this is the first comment.', NULL, 1, 0, 'blog', 'comment', 1, 2, '2009-12-25 12:00:00', '2009-12-25 12:00:00');

--
-- Dumping data for table `contacts`
--

INSERT IGNORE INTO `contacts` (`id`, `title`, `alias`, `body`, `name`, `position`, `address`, `address2`, `state`, `country`, `postcode`, `phone`, `fax`, `email`, `message_status`, `message_archive`, `message_count`, `message_spam_protection`, `message_captcha`, `message_notify`, `status`, `updated`, `created`) VALUES
(1, 'Contact', 'contact', '', '', '', '', '', '', '', '', '', '', 'you@your-site.com', 1, 0, 0, 0, 0, 1, 1, '2009-10-07 22:07:49', '2009-09-16 01:45:17');

--
-- Dumping data for table `i18n`
--


--
-- Dumping data for table `languages`
--

INSERT IGNORE INTO `languages` (`id`, `title`, `native`, `alias`, `status`, `weight`, `updated`, `created`) VALUES
(1, 'English', 'English', 'eng', 1, 1, '2009-11-02 21:37:38', '2009-11-02 20:52:00');

--
-- Dumping data for table `links`
--

INSERT IGNORE INTO `links` (`id`, `parent_id`, `menu_id`, `title`, `class`, `description`, `link`, `target`, `rel`, `status`, `lft`, `rght`, `visibility_roles`, `params`, `updated`, `created`) VALUES
(5, NULL, 4, 'About', 'about', '', 'plugin:nodes/controller:nodes/action:view/type:page/slug:about', '', '', 1, 3, 4, '', '', '2009-10-06 23:14:21', '2009-08-19 12:23:33'),
(6, NULL, 4, 'Contact', 'contact', '', 'plugin:contacts/controller:contacts/action:view/contact', '', '', 1, 5, 6, '', '', '2009-10-06 23:14:45', '2009-08-19 12:34:56'),
(7, NULL, 3, 'Home', 'home', '', '/', '', '', 1, 5, 6, '', '', '2009-10-06 21:17:06', '2009-09-06 21:32:54'),
(8, NULL, 3, 'About', 'about', '', '/about', '', '', 1, 7, 10, '', '', '2009-09-12 03:45:53', '2009-09-06 21:34:57'),
(9, 8, 3, 'Child link', 'child-link', '', '#', '', '', 0, 8, 9, '', '', '2009-10-06 23:13:06', '2009-09-12 03:52:23'),
(10, NULL, 5, 'Site Admin', 'site-admin', '', '/admin', '', '', 1, 1, 2, '', '', '2009-09-12 06:34:09', '2009-09-12 06:34:09'),
(11, NULL, 5, 'Log out', 'log-out', '', '/plugin:users/controller:users/action:logout', '', '', 1, 7, 8, '["1","2"]', '', '2009-09-12 06:35:22', '2009-09-12 06:34:41'),
(12, NULL, 6, 'Croogo', 'croogo', '', 'http://www.croogo.org', '', '', 1, 3, 4, '', '', '2009-09-12 23:31:59', '2009-09-12 23:31:59'),
(14, NULL, 6, 'CakePHP', 'cakephp', '', 'http://www.cakephp.org', '', '', 1, 1, 2, '', '', '2009-10-07 03:25:25', '2009-09-12 23:38:43'),
(15, NULL, 3, 'Contact', 'contact', '', '/plugin:contacts/controller:contacts/action:view/contact', '', '', 1, 11, 12, '', '', '2009-09-16 07:54:13', '2009-09-16 07:53:33'),
(16, NULL, 5, 'Entries (RSS)', 'entries-rss', '', '/promoted.rss', '', '', 1, 3, 4, '', '', '2009-10-27 17:46:22', '2009-10-27 17:46:22'),
(17, NULL, 5, 'Comments (RSS)', 'comments-rss', '', '/comments.rss', '', '', 1, 5, 6, '', '', '2009-10-27 17:46:54', '2009-10-27 17:46:54');

--
-- Dumping data for table `menus`
--

INSERT IGNORE INTO `menus` (`id`, `title`, `alias`, `description`, `status`, `weight`, `link_count`, `params`, `updated`, `created`) VALUES
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

INSERT IGNORE INTO `meta` (`id`, `model`, `foreign_key`, `key`, `value`, `weight`) VALUES
(1, 'Node', 1, 'meta_keywords', 'key1, key2', NULL);

--
-- Dumping data for table `nodes`
--

INSERT IGNORE INTO `nodes` (`id`, `parent_id`, `user_id`, `title`, `slug`, `body`, `excerpt`, `status`, `mime_type`, `comment_status`, `comment_count`, `promote`, `path`, `terms`, `sticky`, `lft`, `rght`, `visibility_roles`, `type`, `updated`, `created`) VALUES
(1, NULL, 1, 'Hello World', 'hello-world', '<p>Welcome to Croogo. This is your first post. You can edit or delete it from the admin panel.</p>', '', 1, '', 2, 1, 1, '/blog/hello-world', '{"1":"uncategorized"}', 0, 1, 2, '', 'blog', '2009-12-25 11:00:00', '2009-12-25 11:00:00'),
(2, NULL, 1, 'About', 'about', '<p>This is an example of a Croogo page, you could edit this to put information about yourself or your site.</p>', '', 1, '', 0, 0, 0, '/about', '', 0, 1, 2, '', 'page', '2009-12-25 22:00:00', '2009-12-25 22:00:00');

--
-- Dumping data for table `nodes_taxonomies`
--

INSERT INTO `nodes_taxonomies` (`id`, `node_id`, `taxonomy_id`) VALUES
(1, 1, 1);

--
-- Dumping data for table `regions`
--

INSERT IGNORE INTO `regions` (`id`, `title`, `alias`, `description`, `block_count`) VALUES
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

INSERT IGNORE INTO `roles` (`id`, `title`, `alias`, `created`, `updated`) VALUES
(1, 'Admin', 'admin', '2009-04-05 00:10:34', '2009-04-05 00:10:34'),
(2, 'Registered', 'registered', '2009-04-05 00:10:50', '2009-04-06 05:20:38'),
(3, 'Public', 'public', '2009-04-05 00:12:38', '2009-04-07 01:41:45');

--
-- Dumping data for table `settings`
--

INSERT IGNORE INTO `settings` (`id`, `key`, `value`, `title`, `description`, `input_type`, `editable`, `weight`, `params`) VALUES
(6, 'Site.title', 'Croogo', '', '', '', 1, 1, ''),
(7, 'Site.tagline', 'A CakePHP powered Content Management System.', '', '', 'textarea', 1, 2, ''),
(8, 'Site.email', 'you@your-site.com', '', '', '', 1, 3, ''),
(9, 'Site.status', '1', '', '', 'checkbox', 1, 6, ''),
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
(27, 'Reading.date_time_format', 'D, M d Y H:i:s', '', '', '', 1, 21, ''),
(28, 'Comment.date_time_format', 'M d, Y', '', '', '', 1, 22, ''),
(29, 'Site.timezone', '0', '', 'zero (0) for GMT', '', 1, 4, ''),
(32, 'Hook.bootstraps', 'Settings,Comments,Contacts,Nodes,Meta,Menus,Users,Blocks,Taxonomy,FileManager,Wysiwyg,Ckeditor', '', '', '', 0, 23, ''),
(33, 'Comment.email_notification', '1', 'Enable email notification', '', '', 1, 24, ''),
(34, 'Access Control.multiColumn', '0', 'Allow login by username or email', '', 'checkbox', 1, 25, ''),
(34, 'Access Control.multiRole', '0', 'Enable Multiple Roles', '', 'checkbox', 1, 25, ''),
(35, 'Access Control.rowLevel', '0', 'Row Level Access Control', '', 'checkbox', 1, 26, ''),
(36, 'Access Control.autoLoginDuration', '+1 week', '"Remember Me" Duration', 'Eg: +1 day, +1 week. Leave empty to disable.', 'text', 1, 27, ''),
(37, 'Access Control.models', '', 'Models with Row Level Acl', 'Select models to activate Row Level Access Control on', 'multiple', 1, 28, 'multiple=checkbox
options={"Nodes.Node": "Node", "Blocks.Block": "Block", "Menus.Menu": "Menu", "Menus.Link": "Link"}');

--
-- Dumping data for table `taxonomies`
--

INSERT IGNORE INTO `taxonomies` (`id`, `parent_id`, `term_id`, `vocabulary_id`, `lft`, `rght`) VALUES
(1, NULL, 1, 1, 1, 2),
(2, NULL, 2, 1, 3, 4),
(3, NULL, 3, 2, 1, 2);

--
-- Dumping data for table `terms`
--

INSERT IGNORE INTO `terms` (`id`, `title`, `slug`, `description`, `updated`, `created`) VALUES
(1, 'Uncategorized', 'uncategorized', '', '2009-07-22 03:38:43', '2009-07-22 03:34:56'),
(2, 'Announcements', 'announcements', '', '2010-05-16 23:57:06', '2009-07-22 03:45:37'),
(3, 'mytag', 'mytag', '', '2009-08-26 14:42:43', '2009-08-26 14:42:43');

--
-- Dumping data for table `types`
--

INSERT IGNORE INTO `types` (`id`, `title`, `alias`, `description`, `format_show_author`, `format_show_date`, `comment_status`, `comment_approve`, `comment_spam_protection`, `comment_captcha`, `params`, `plugin`, `updated`, `created`) VALUES
(1, 'Page', 'page', 'A page is a simple method for creating and displaying information that rarely changes, such as an "About us" section of a website. By default, a page entry does not allow visitor comments.', 0, 0, 0, 1, 0, 0, '', '', '2009-09-09 00:23:24', '2009-09-02 18:06:27'),
(2, 'Blog', 'blog', 'A blog entry is a single post to an online journal, or blog.', 1, 1, 2, 1, 0, 0, '', '', '2009-09-15 12:15:43', '2009-09-02 18:20:44'),
(4, 'Node', 'node', 'Default content type.', 1, 1, 2, 1, 0, 0, '', '', '2009-10-06 21:53:15', '2009-09-05 23:51:56');

--
-- Dumping data for table `types_vocabularies`
--

INSERT IGNORE INTO `types_vocabularies` (`id`, `type_id`, `vocabulary_id`, `weight`) VALUES
(31, 2, 2, NULL),
(30, 2, 1, NULL),
(25, 4, 2, NULL),
(24, 4, 1, NULL);

--
-- Dumping data for table `vocabularies`
--

INSERT IGNORE INTO `vocabularies` (`id`, `title`, `alias`, `description`, `required`, `multiple`, `tags`, `plugin`, `weight`, `updated`, `created`) VALUES
(1, 'Categories', 'categories', '', 0, 1, 0, '', 1, '2010-05-17 20:03:11', '2009-07-22 02:16:21'),
(2, 'Tags', 'tags', '', 0, 1, 0, '', 2, '2010-05-17 20:03:11', '2009-07-22 02:16:34');
