-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 15, 2009 at 05:41 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `p_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default '',
  `foreign_key` int(10) unsigned default NULL,
  `alias` varchar(255) default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, '', NULL, 'controllers', 1, 234),
(8, NULL, 'Node', 19, '', 245, 246),
(7, NULL, 'Node', 18, '', 243, 244),
(5, NULL, 'Link', 2, '', 239, 240),
(6, NULL, 'Link', 3, '', 241, 242);

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) default NULL,
  `model` varchar(255) default '',
  `foreign_key` int(10) unsigned default NULL,
  `alias` varchar(255) default '',
  `lft` int(10) default NULL,
  `rght` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Role', 1, '', 1, 4),
(2, NULL, 'Role', 2, '', 5, 6),
(3, NULL, 'Role', 3, '', 7, 8),
(5, 1, 'User', 1, '', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL default '0',
  `_read` char(2) NOT NULL default '0',
  `_update` char(2) NOT NULL default '0',
  `_delete` char(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `aros_acos`
--


-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE IF NOT EXISTS `blocks` (
  `id` bigint(20) NOT NULL auto_increment,
  `region_id` bigint(20) default NULL,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) default NULL,
  `content` text NOT NULL,
  `php` tinyint(1) NOT NULL default '0',
  `show_title` tinyint(1) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  `weight` int(11) default NULL,
  `file` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `blocks`
--


-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(20) NOT NULL auto_increment,
  `parent_id` bigint(20) default NULL,
  `node_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL default '0',
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(200) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) default NULL,
  `status` tinyint(1) NOT NULL default '0',
  `type` varchar(100) NOT NULL default 'post',
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `i18n`
--

CREATE TABLE IF NOT EXISTS `i18n` (
  `id` int(10) NOT NULL auto_increment,
  `locale` varchar(6) NOT NULL,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) NOT NULL,
  `content` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `locale` (`locale`),
  KEY `model` (`model`),
  KEY `row_id` (`foreign_key`),
  KEY `field` (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `i18n`
--


-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) NOT NULL auto_increment,
  `parent_id` bigint(20) default NULL,
  `menu_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `rel` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  `params` text NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`id`, `parent_id`, `menu_id`, `title`, `description`, `link`, `target`, `rel`, `status`, `lft`, `rght`, `params`, `updated`, `created`) VALUES
(2, NULL, 1, 'Home', '', '/', '', '', 1, 1, 2, '', '2009-07-04 22:39:08', '2009-07-04 22:39:08'),
(3, NULL, 1, 'About', '', '{controller: ''pages'', action: ''view'', slug: ''about'' }', '', '', 1, 3, 4, '', '2009-07-04 23:17:04', '2009-07-04 23:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `weight` int(11) default NULL,
  `link_count` int(11) NOT NULL,
  `params` text NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `title`, `alias`, `description`, `status`, `weight`, `link_count`, `params`, `updated`, `created`) VALUES
(1, 'Main Menu', 'main_menu', '', 1, NULL, 2, '', '2009-07-04 03:33:15', '2009-07-04 03:33:15'),
(2, 'Footer Menu', 'footer_menu', '', 1, NULL, 0, '', '2009-07-04 03:56:49', '2009-07-04 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` bigint(20) NOT NULL auto_increment,
  `model` varchar(255) NOT NULL default 'Node',
  `foreign_key` bigint(20) default NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `model` (`model`,`foreign_key`,`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id`, `model`, `foreign_key`, `key`, `value`) VALUES
(17, 'Node', 18, 'meta_keywords', 'keywords'),
(18, 'Node', 18, 'meta_description', 'description'),
(19, 'Node', 19, 'meta_keywords', ''),
(20, 'Node', 19, 'meta_description', '');

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE IF NOT EXISTS `nodes` (
  `id` bigint(20) NOT NULL auto_increment,
  `parent_id` bigint(20) default NULL,
  `user_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `draft` tinyint(1) NOT NULL default '0',
  `mime_type` varchar(100) NOT NULL,
  `comment_status` tinyint(1) NOT NULL default '1',
  `comment_count` int(11) default NULL,
  `guid` varchar(255) NOT NULL,
  `sticky` tinyint(1) NOT NULL default '0',
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  `type` varchar(100) NOT NULL default 'post',
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `slug` (`slug`,`type`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `nodes`
--

INSERT INTO `nodes` (`id`, `parent_id`, `user_id`, `title`, `slug`, `content`, `excerpt`, `status`, `draft`, `mime_type`, `comment_status`, `comment_count`, `guid`, `sticky`, `lft`, `rght`, `type`, `updated`, `created`) VALUES
(18, NULL, 0, 'About', 'about', '<p>content here</p>', '', 1, 0, '', 0, NULL, '', 0, 1, 2, 'page', '2009-07-04 23:45:12', '2009-07-04 23:45:12'),
(19, NULL, 0, 'Contact', 'contact', '<p>contact me</p>', '', 0, 0, '', 0, NULL, '', 0, 3, 4, 'page', '2009-07-04 23:47:41', '2009-07-04 23:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `nodes_terms`
--

CREATE TABLE IF NOT EXISTS `nodes_terms` (
  `id` bigint(20) NOT NULL auto_increment,
  `node_id` int(10) NOT NULL default '0',
  `vocabulary_id` int(10) NOT NULL default '0',
  `term_id` int(10) NOT NULL default '0',
  `weight` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodes_terms`
--


-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE IF NOT EXISTS `regions` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `title`, `alias`) VALUES
(1, 'sidebar', 'sidebar'),
(2, 'none', '');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) default NULL,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `alias`, `created`, `updated`) VALUES
(1, 'Admin', 'admin', '2009-04-05 00:10:34', '2009-04-05 00:10:34'),
(2, 'Registered', 'registered', '2009-04-05 00:10:50', '2009-04-06 05:20:38'),
(3, 'Public', 'public', '2009-04-05 00:12:38', '2009-04-07 01:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) NOT NULL auto_increment,
  `key` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `input_type` varchar(255) NOT NULL default 'text',
  `weight` varchar(255) default NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `title`, `description`, `input_type`, `weight`, `params`) VALUES
(1, 'Site.title', 'croogo cms', '', '', '', '1', ''),
(3, 'Prefix.anything', 'whatever', '', '', '', '2', ''),
(4, 'Admin.settings', 'Admin,Site', '', '', '', '3', '');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE IF NOT EXISTS `terms` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) NOT NULL,
  `vocabulary_id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lft` int(11) NOT NULL,
  `rght` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `terms`
--


-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(10) NOT NULL auto_increment,
  `model` varchar(255) NOT NULL default 'Node',
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `plugin` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `types`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `activation_key` varchar(60) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `password`, `name`, `email`, `website`, `activation_key`, `image`, `status`, `updated`, `created`) VALUES
(1, 1, 'admin', 'c054b152596745efa1d197b809fa7fc70ce586e5', 'Administrator', 'fahad19@gmail.com', '', '', '', 1, '2009-06-23 20:50:41', '2009-04-05 00:20:34');

-- --------------------------------------------------------

--
-- Table structure for table `vocabularies`
--

CREATE TABLE IF NOT EXISTS `vocabularies` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `required` tinyint(1) NOT NULL default '0',
  `multiple` tinyint(1) NOT NULL default '0',
  `tags` tinyint(1) NOT NULL default '0',
  `plugin` varchar(255) NOT NULL,
  `term_count` int(10) NOT NULL default '0',
  `weight` int(11) default NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `vocabularies`
--

