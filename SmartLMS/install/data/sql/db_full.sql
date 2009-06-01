-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 11 Lug, 2008 at 11:28 AM
-- Versione MySQL: 5.0.51
-- Versione PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `docebo_3504`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area`
--

CREATE TABLE `cms_area` (
  `idArea` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `template` varchar(255) NOT NULL default '',
  `mr_title` varchar(255) NOT NULL default '',
  `browser_title` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `sitedesc` text NOT NULL,
  `link` varchar(255) NOT NULL default '',
  `home` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `langdef` tinyint(1) NOT NULL default '0',
  `show_in_menu` tinyint(1) NOT NULL default '1',
  `show_in_macromenu` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`idArea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block`
--

CREATE TABLE `cms_area_block` (
  `idBlock` int(11) NOT NULL auto_increment,
  `idSubdivision` int(11) NOT NULL default '0',
  `block_name` varchar(255) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `function` varchar(100) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block_filter`
--

CREATE TABLE `cms_area_block_filter` (
  `block_id` int(11) NOT NULL default '0',
  `block_type` varchar(255) NOT NULL default '',
  `id_type` varchar(60) NOT NULL default '',
  `id_val` int(11) NOT NULL default '0',
  PRIMARY KEY  (`block_id`,`block_type`,`id_type`,`id_val`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block_filter`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block_forum`
--

CREATE TABLE `cms_area_block_forum` (
  `idBlock` int(11) NOT NULL default '0',
  `idForum` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`idForum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block_forum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block_group`
--

CREATE TABLE `cms_area_block_group` (
  `idBlock` int(11) NOT NULL default '0',
  `idGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`idGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block_group`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block_items`
--

CREATE TABLE `cms_area_block_items` (
  `id` int(11) NOT NULL auto_increment,
  `idBlock` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_block_simpleprj`
--

CREATE TABLE `cms_area_block_simpleprj` (
  `block_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`block_id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_block_simpleprj`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_option`
--

CREATE TABLE `cms_area_option` (
  `idBlock` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_option`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_option_text`
--

CREATE TABLE `cms_area_option_text` (
  `idBlock` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `text` text,
  PRIMARY KEY  (`idBlock`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_option_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_perm`
--

CREATE TABLE `cms_area_perm` (
  `idArea` int(11) NOT NULL default '0',
  `idGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idArea`,`idGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_perm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_area_subdivision`
--

CREATE TABLE `cms_area_subdivision` (
  `idSubdivision` int(11) NOT NULL auto_increment,
  `idArea` int(11) NOT NULL default '0',
  `idParentSub` int(11) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `areaWidth` varchar(10) NOT NULL default '',
  `areaType` varchar(255) NOT NULL default '',
  `margin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idSubdivision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_area_subdivision`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_banner`
--

CREATE TABLE `cms_banner` (
  `banner_id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `idGroup` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `bdesc` varchar(255) NOT NULL default '',
  `kind` varchar(10) NOT NULL default '',
  `bancode` text NOT NULL,
  `banfile` varchar(255) NOT NULL default '',
  `banurl` varchar(255) NOT NULL default '',
  `ban_w` int(4) NOT NULL default '468',
  `ban_h` int(4) NOT NULL default '60',
  `ban_bg` varchar(7) NOT NULL default '#000000',
  `expimp` int(11) NOT NULL default '0',
  `expdate` datetime default NULL,
  `impression` int(11) NOT NULL default '0',
  `click` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`banner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_banner`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_banner_cat`
--

CREATE TABLE `cms_banner_cat` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `cat_name` varchar(255) NOT NULL default '',
  `cat_desc` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_banner_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_banner_raw_stat`
--

CREATE TABLE `cms_banner_raw_stat` (
  `rec_id` int(11) NOT NULL auto_increment,
  `rec_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `rec_type` varchar(10) NOT NULL default '',
  `banner_id` int(11) NOT NULL default '0',
  `idArea` int(11) NOT NULL default '0',
  `cat_id` int(11) NOT NULL default '0',
  `kind` varchar(10) NOT NULL default '',
  `rec_from` varchar(10) NOT NULL default '',
  `b_name` varchar(15) NOT NULL default '',
  `b_os` varchar(15) NOT NULL default '',
  `b_country` varchar(10) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rec_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_banner_raw_stat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_banner_rules`
--

CREATE TABLE `cms_banner_rules` (
  `banner_id` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `item_val` varchar(255) NOT NULL default '',
  `item_type` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`banner_id`,`item_id`,`item_val`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_banner_rules`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_blocktype`
--

CREATE TABLE `cms_blocktype` (
  `name` varchar(255) NOT NULL default '',
  `folder` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_blocktype`
--

INSERT INTO `cms_blocktype` (`name`, `folder`, `label`) VALUES
('alerts', '', '_BLK_ALERTS'),
('banners', '', '_BLK_BANNERS'),
('calendar', '', '_BLK_CALENDAR'),
('chat', '', '_BLK_CHAT'),
('chat_intelligere', 'chat', '_BLK_CHAT_INTELLIGERE'),
('chat_teleskill', 'chat', '_BLK_CHAT_TELESKILL'),
('content', '', '_BLK_CONTENT'),
('docs', '', '_BLK_DOCS'),
('docs_sel', 'docs', '_BLK_DOCS_SEL'),
('docs_sel_small', 'docs', '_BLK_DOCS_SEL_SMALL'),
('docs_small', 'docs', '_BLK_DOCS_SMALL'),
('faq', '', '_BLK_FAQ'),
('feedback', '', '_BLK_FEEDBACK'),
('feedreader', '', '_BLK_FEEDREADER'),
('forum', '', '_BLK_FORUM'),
('links', '', '_BLK_LINKS'),
('login', '', '_BLK_LOGIN'),
('media', '', '_BLK_MEDIA'),
('media_sel', 'media', '_BLK_MEDIA_SEL'),
('menu', '', '_BLK_MENU'),
('message', '', '_BLK_MESSAGE'),
('myfiles', '', '_BLK_MYFILES'),
('myfriends', '', '_BLK_MYFRIENDS'),
('mygroup', '', '_BLK_MYGROUP'),
('news', '', '_BLK_NEWS'),
('news_sel', 'news', '_BLK_NEWS_SEL'),
('news_sel_small', 'news', '_BLK_NEWS_SEL_SMALL'),
('news_small', 'news', '_BLK_NEWS_SMALL'),
('poll', '', '_BLK_POLL'),
('profile', '', '_BLK_PROFILE'),
('profile_search', '', '_BLK_PROFILE_SEARCH'),
('profile_search_teacher', 'profile_search', '_BLK_PROFILE_SEARCH_TEACHER'),
('simpleprj', '', '_BLK_SIMPLEPRJ'),
('submitnews', '', '_BLK_SUBMITNEWS'),
('subscription', '', '_BLK_SUBSCRIPTION'),
('text', '', '_BLK_TEXT'),
('wiki', '', '_BLK_WIKI'),
('scanmenu', '', '_BLK_SCANMENU');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_calendar`
--

CREATE TABLE `cms_calendar` (
  `calendar_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_calendar_item`
--

CREATE TABLE `cms_calendar_item` (
  `calendar_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`calendar_id`,`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_calendar_item`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_comment_ajax`
--

CREATE TABLE `cms_comment_ajax` (
  `id_comment` int(11) NOT NULL auto_increment,
  `resource_type` varchar(50) NOT NULL default '',
  `external_key` varchar(200) NOT NULL default '',
  `id_author` int(11) NOT NULL default '0',
  `posted_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `textof` text NOT NULL,
  `history_tree` varchar(255) NOT NULL default '',
  `id_parent` int(11) NOT NULL default '0',
  `moderated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_comment_ajax`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_content`
--

CREATE TABLE `cms_content` (
  `idContent` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('normal','block_text') NOT NULL default 'normal',
  `key1` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idContent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_content`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_content_attach`
--

CREATE TABLE `cms_content_attach` (
  `id` int(11) NOT NULL auto_increment,
  `idContent` int(11) NOT NULL default '0',
  `idAttach` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_content_attach`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_content_dir`
--

CREATE TABLE `cms_content_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_content_dir`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_content_titles`
--

CREATE TABLE `cms_content_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_content_titles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_docs`
--

CREATE TABLE `cms_docs` (
  `idDocs` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `auth_email` varchar(255) NOT NULL default '',
  `auth_url` varchar(255) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `click` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idDocs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_docs`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_docs_dir`
--

CREATE TABLE `cms_docs_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_docs_dir`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_docs_info`
--

CREATE TABLE `cms_docs_info` (
  `id` int(11) NOT NULL auto_increment,
  `idd` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `sdesc` varchar(255) NOT NULL default '',
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_docs_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_docs_titles`
--

CREATE TABLE `cms_docs_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_docs_titles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form`
--

CREATE TABLE `cms_form` (
  `idForm` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `fdesc` text NOT NULL,
  `email` text NOT NULL,
  `storeinfo` tinyint(1) NOT NULL default '0',
  `form_type` enum('normal','crm_contact') NOT NULL default 'normal',
  PRIMARY KEY  (`idForm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form_items`
--

CREATE TABLE `cms_form_items` (
  `id` int(11) NOT NULL auto_increment,
  `idForm` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `comp` tinyint(1) NOT NULL default '0',
  `ord` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form_map`
--

CREATE TABLE `cms_form_map` (
  `form_id` int(11) NOT NULL default '0',
  `field_id` int(11) NOT NULL default '0',
  `field_map_resource` enum('user','company','chistory') NOT NULL default 'user',
  `field_type` enum('predefined','custom') NOT NULL default 'predefined',
  `field_map_to` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`form_id`,`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form_map`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form_sendinfo`
--

CREATE TABLE `cms_form_sendinfo` (
  `send_id` int(11) NOT NULL auto_increment,
  `form_id` int(11) NOT NULL default '0',
  `form_type` enum('normal','crm_contact') NOT NULL default 'normal',
  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`send_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form_sendinfo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form_storage`
--

CREATE TABLE `cms_form_storage` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` varchar(100) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form_storage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_form_textarea`
--

CREATE TABLE `cms_form_textarea` (
  `idTextarea` int(11) NOT NULL auto_increment,
  `idCommon` int(11) NOT NULL default '0',
  `language` varchar(100) NOT NULL default '',
  `nameTextarea` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`idTextarea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_form_textarea`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forum`
--

CREATE TABLE `cms_forum` (
  `idForum` int(11) NOT NULL auto_increment,
  `idCourse` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `num_thread` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idForum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forummessage`
--

CREATE TABLE `cms_forummessage` (
  `idMessage` int(11) NOT NULL auto_increment,
  `idThread` int(11) NOT NULL default '0',
  `idCourse` int(11) default NULL,
  `answer_tree` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `generator` tinyint(1) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  `modified_by` int(11) NOT NULL default '0',
  `modified_by_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forummessage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forumthread`
--

CREATE TABLE `cms_forumthread` (
  `idThread` int(11) NOT NULL auto_increment,
  `idForum` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `author` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `num_view` int(5) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `erased` tinyint(1) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  `rilevantForum` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idThread`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forumthread`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forum_access`
--

CREATE TABLE `cms_forum_access` (
  `idForum` int(11) NOT NULL default '0',
  `idMember` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idForum`,`idMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forum_access`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forum_notifier`
--

CREATE TABLE `cms_forum_notifier` (
  `id_notify` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `notify_is_a` enum('forum','thread') NOT NULL default 'forum',
  PRIMARY KEY  (`id_notify`,`id_user`,`notify_is_a`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forum_notifier`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_forum_timing`
--

CREATE TABLE `cms_forum_timing` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_forum_timing`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_links`
--

CREATE TABLE `cms_links` (
  `idLinks` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `url` varchar(255) NOT NULL default '',
  `fpreview` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `auth_email` varchar(255) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `click` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idLinks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_links`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_links_dir`
--

CREATE TABLE `cms_links_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_links_dir`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_links_info`
--

CREATE TABLE `cms_links_info` (
  `id` int(11) NOT NULL auto_increment,
  `idl` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `sdesc` text NOT NULL,
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_links_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_links_titles`
--

CREATE TABLE `cms_links_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_links_titles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_media`
--

CREATE TABLE `cms_media` (
  `idMedia` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `fpreview` varchar(255) NOT NULL default '',
  `media_url` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `auth_email` varchar(255) NOT NULL default '',
  `auth_url` varchar(255) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `click` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idMedia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_media`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_media_dir`
--

CREATE TABLE `cms_media_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_media_dir`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_media_info`
--

CREATE TABLE `cms_media_info` (
  `id` int(11) NOT NULL auto_increment,
  `idm` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `sdesc` varchar(255) NOT NULL default '',
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_media_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_media_titles`
--

CREATE TABLE `cms_media_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_media_titles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_menu`
--

CREATE TABLE `cms_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_menu`
--

INSERT INTO `cms_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '', '', 1, 'true'),
(2, '_NEWS', '', 2, 'false'),
(3, '_CMS_CONTENT', '', 3, 'false'),
(4, '_CMS_BANNER', '', 4, 'false'),
(5, '_CMS_CONFIG', '', 5, 'false'),
(6, '_CMS_STATS', '', 6, 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_menu_under`
--

CREATE TABLE `cms_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_menu_under`
--

INSERT INTO `cms_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'manpage', '_MANPAGE', 'manpage', 'view', NULL, 1, 'class.manpage.php', 'Module_Manpage'),
(2, 2, 'news', '_NEWS', 'news', 'view', NULL, 2, 'class.news.php', 'Module_News'),
(3, 2, 'mantopic', '_MANTOPIC', 'mantopic', 'view', NULL, 3, 'class.mantopic.php', 'Module_Mantopic'),
(4, 3, 'content', '_CONTENT', 'content', 'view', NULL, 4, 'class.content.php', 'Module_Content'),
(5, 3, 'docs', '_DOCS', 'docs', 'view', NULL, 5, 'class.docs.php', 'Module_Docs'),
(6, 3, 'media', '_MEDIA', 'media', 'view', NULL, 6, 'class.media.php', 'Module_Media'),
(7, 3, 'links', '_LINKS', 'links', 'view', NULL, 7, 'class.links.php', 'Module_Links'),
(8, 4, 'banners', '_BANNER_CAT', 'viewcat', 'view', NULL, 8, '', ''),
(9, 4, 'banners', '_BANNER', 'banners', 'view', NULL, 9, 'class.banners.php', 'Module_Banners'),
(10, 5, 'forum', '_FORUM', 'forum', 'view', NULL, 10, 'class.forum.php', 'Module_Forum'),
(11, 5, 'poll', '_POLL', 'poll', 'view', NULL, 11, '', ''),
(12, 5, 'form', '_FORM', 'form', 'view', NULL, 12, 'class.form.php', 'Module_Form'),
(13, 6, 'stats', '_STATS_MAIN', 'stats', 'view', NULL, 14, 'class.stats.php', 'Module_Stats'),
(14, 6, 'stats', '_STATS_DETAILS', 'statsdetails', 'view', NULL, 15, '', ''),
(15, 6, 'stats', '_STATS_TEMPORAL', 'statstemporal', 'view', NULL, 16, '', ''),
(16, 2, 'feedreader', '_FEEDREADER', 'feedreader', 'view', 'framework', 9, 'class.feedreader.php', 'Module_FeedReader'),
(17, 3, 'faq', '_FAQ', 'main', 'view', NULL, 17, 'class.faq.php', 'Module_Faq'),
(18, 3, 'wiki', '_WIKI', 'main', 'view', NULL, 18, 'class.wiki.php', 'Module_Wiki'),
(20, 5, 'calendar', '_CALENDAR', 'main', 'view', NULL, 20, 'class.calendar.php', 'Module_Calendar'),
(21, 3, 'simpleprj', '_SIMPLEPRJ', 'main', 'view', NULL, 21, 'class.simpleprj.php', 'Module_SimplePrj');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_news`
--

CREATE TABLE `cms_news` (
  `idNews` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(255) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `reflink` varchar(255) NOT NULL default '',
  `source` varchar(255) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `usercontrib` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idNews`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_news`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_news_attach`
--

CREATE TABLE `cms_news_attach` (
  `id` int(11) NOT NULL auto_increment,
  `idNews` int(11) NOT NULL default '0',
  `idAttach` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_news_attach`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_news_dir`
--

CREATE TABLE `cms_news_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_news_dir`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_news_titles`
--

CREATE TABLE `cms_news_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_news_titles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_news_topic`
--

CREATE TABLE `cms_news_topic` (
  `idNews` int(11) NOT NULL default '0',
  `topic_id` int(11) NOT NULL default '0',
  `main` tinyint(1) NOT NULL default '0',
  `img_align` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`idNews`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_news_topic`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_poll`
--

CREATE TABLE `cms_poll` (
  `poll_id` int(11) NOT NULL auto_increment,
  `question` varchar(255) NOT NULL default '',
  `poll_desc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_poll`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_poll_answer`
--

CREATE TABLE `cms_poll_answer` (
  `answer_id` int(11) NOT NULL auto_increment,
  `answer_txt` varchar(255) NOT NULL default '',
  `poll_id` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_poll_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_poll_vote`
--

CREATE TABLE `cms_poll_vote` (
  `poll_id` int(11) NOT NULL default '0',
  `answer_id` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`,`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_poll_vote`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_setting`
--

CREATE TABLE `cms_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_setting`
--

INSERT INTO `cms_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`) VALUES
('anonymous_comment', 'off', 'enum', 3, 0, 6, 1, 0),
('cms_admin_mail', 'sample@localhost.com', 'string', 255, 0, 1, 1, 0),
('cms_nl_sendpause', '20', 'int', 3, 1, 1, 1, 0),
('cms_nl_sendpercycle', '200', 'int', 4, 1, 0, 1, 0),
('cms_previewimg_maxsize', '100', 'int', 4, 0, 5, 1, 0),
('cms_use_dropdown_menu', '1', 'check', 1, 0, 11, 1, 0),
('cms_version', '3.0', 'string', 4, 0, 10, 1, 1),
('defaultCmsLanguage', 'italian', 'language', 255, 0, 4, 1, 0),
('defaultCmsTemplate', 'standard', 'template', 255, 0, 3, 1, 0),
('default_banner_cat', '3', 'bancat_chooser', 11, 2, 2, 1, 0),
('forum_as_table', 'on', 'enum', 3, 3, 1, 1, 0),
('grpsel_type', 'group', 'grpsel_chooser', 20, 0, 9, 1, 0),
('last_auto_publish', '1185979282', 'int', 11, 0, 0, 1, 1),
('over_menu', '1', 'int', 1, 0, 0, 1, 1),
('pathforum', 'forum/', 'string', 255, 3, 2, 1, 0),
('reload_perm_after', '1185869333', 'int', 11, 0, 0, 1, 1),
('ttlSession', '4000', 'int', 6, 0, 2, 1, 0),
('url', 'http://localhost/docebo_35/doceboCms/', 'string', 255, 0, 0, 1, 0),
('use_mod_rewrite', 'off', 'enum', 3, 0, 8, 1, 0),
('visuItem', '20', 'int', 11, 2, 1, 1, 0),
('vop_show_banner', '1', 'check', 1, 2, 5, 1, 0),
('vop_show_date', '1', 'check', 1, 2, 3, 1, 0),
('vop_show_languages', '1', 'check', 1, 2, 6, 1, 0),
('vop_show_macroarea', '1', 'check', 1, 2, 7, 1, 0),
('vop_show_navigation', '1', 'check', 1, 2, 4, 1, 0),
('use_bbclone', '1', 'check', 1, 0, 12, 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_simpleprj`
--

CREATE TABLE `cms_simpleprj` (
  `project_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_simpleprj`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_simpleprj_file`
--

CREATE TABLE `cms_simpleprj_file` (
  `file_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_simpleprj_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_simpleprj_task`
--

CREATE TABLE `cms_simpleprj_task` (
  `task_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `complete` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_simpleprj_task`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_sysforum`
--

CREATE TABLE `cms_sysforum` (
  `idMessage` int(11) NOT NULL auto_increment,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) NOT NULL default '0',
  `key3` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_text`
--

CREATE TABLE `cms_text` (
  `idBlock` int(11) NOT NULL default '0',
  `language` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  PRIMARY KEY  (`idBlock`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_topic`
--

CREATE TABLE `cms_topic` (
  `id` int(11) NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `language` varchar(20) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_topic`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `cms_tree_perm`
--

CREATE TABLE `cms_tree_perm` (
  `type` varchar(10) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `node_id` int(11) NOT NULL default '0',
  `recursive` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`type`,`user_id`,`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `cms_tree_perm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_booking`
--

CREATE TABLE `conference_booking` (
  `booking_id` int(11) NOT NULL auto_increment,
  `room_id` int(11) NOT NULL default '0',
  `platform` varchar(255) NOT NULL default '',
  `module` varchar(100) NOT NULL default '',
  `user_idst` int(11) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`booking_id`)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_booking`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_chatperm`
--

CREATE TABLE `conference_chatperm` (
  `room_id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `user_idst` int(11) NOT NULL default '0',
  `perm` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`room_id`,`module`,`user_idst`,`perm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_chatperm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_chat_msg`
--

CREATE TABLE `conference_chat_msg` (
  `msg_id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_room` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `send_to` int(11) default NULL,
  `sent_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_chat_msg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_dimdim`
--

CREATE TABLE `conference_dimdim` (
  `id` bigint(20) NOT NULL auto_increment,
  `idConference` bigint(20) NOT NULL,
  `confkey` varchar(255) default NULL,
  `emailuser` varchar(255) default NULL,
  `displayname` varchar(255) default NULL,
  `timezone` varchar(255) default NULL,
  `audiovideosettings` int(11) default NULL,
  `maxmikes` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idConference` (`idConference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_dimdim`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_room`
--

CREATE TABLE `conference_inte_room` (
  `id_room` int(11) NOT NULL auto_increment,
  `ext_key` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `room_parent` int(11) NOT NULL default '0',
  `room_path` text NOT NULL,
  `type` enum('public','private') NOT NULL default 'public',
  `max_user` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `blocked` tinyint(1) NOT NULL default '0',
  `room_perm` int(8) NOT NULL default '0',
  `zone` varchar(20) NOT NULL default '',
  `bookable` tinyint(1) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `logo` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_token`
--

CREATE TABLE `conference_inte_token` (
  `id_user` int(11) NOT NULL default '0',
  `token` varchar(64) NOT NULL default '',
  `role` varchar(20) NOT NULL default 'guest',
  PRIMARY KEY  (`id_user`,`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_token`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_inte_user`
--

CREATE TABLE `conference_inte_user` (
  `id_user` int(11) NOT NULL auto_increment,
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `role` tinyint(1) NOT NULL default '0',
  `in_room` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`in_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_inte_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_menu`
--

CREATE TABLE `conference_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_menu`
--

INSERT INTO `conference_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_MAIN_CONFERENCE_MANAGMENT', '', 1, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_menu_under`
--

CREATE TABLE `conference_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_menu_under`
--

INSERT INTO `conference_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(2, 1, 'room', '_ROOM', 'room', 'view', NULL, 2, 'class.room.php', 'Module_Room');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_room`
--

CREATE TABLE `conference_room` (
	`id`					BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	`idCal` 				BIGINT NOT NULL,
	`idCourse`				BIGINT NOT NULL,
	`idSt`					BIGINT NOT NULL,
	`name`					VARCHAR(255),
	`room_type`				VARCHAR(255),
	`starttime`				BIGINT,
	`endtime`				BIGINT,
	`meetinghours`			INT,
	`maxparticipants`		INT,
	INDEX (`idCourse`)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_admin`
--

CREATE TABLE `conference_rules_admin` (
  `server_status` enum('yes','no') NOT NULL default 'yes',
  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
  PRIMARY KEY  (`server_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_admin`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_room`
--

CREATE TABLE `conference_rules_room` (
  `id_room` int(11) NOT NULL auto_increment,
  `enable_recording_function` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_advice_insert` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_chat_recording` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_private_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_public_subroom` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_drawboard_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_audio` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_webcam` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_stream_watch` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_strem_write` enum('admin','alluser','noone') NOT NULL default 'noone',
  `enable_remote_desktop` enum('admin','alluser','noone') NOT NULL default 'noone',
  `room_name` varchar(255) NOT NULL default '',
  `room_type` enum('course','private','public') NOT NULL default 'course',
  `id_source` int(11) NOT NULL default '0',
  `room_parent` int(11) NOT NULL default '0',
  `advice_one` text,
  `advice_two` text,
  `advice_three` text,
  `room_logo` varchar(255) default NULL,
  `room_sponsor` varchar(255) default NULL,
  PRIMARY KEY  (`id_room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_root`
--

CREATE TABLE `conference_rules_root` (
  `system_type` enum('p2p','server') NOT NULL default 'p2p',
  `server_ip` varchar(255) default NULL,
  `server_port` int(5) unsigned default NULL,
  `server_path` varchar(255) default NULL,
  `max_user_at_time` int(11) unsigned NOT NULL default '0',
  `max_room_at_time` int(11) unsigned NOT NULL default '0',
  `max_subroom_for_room` int(11) unsigned NOT NULL default '0',
  `enable_drawboard` enum('yes','no') NOT NULL default 'no',
  `enable_livestream` enum('yes','no') NOT NULL default 'no',
  `enable_remote_desktop` enum('yes','no') NOT NULL default 'no',
  `enable_webcam` enum('yes','no') NOT NULL default 'no',
  `enable_audio` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`system_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_root`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_rules_user`
--

CREATE TABLE `conference_rules_user` (
  `id_user` int(11) NOT NULL auto_increment,
  `last_hit` int(11) NOT NULL default '0',
  `id_room` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `user_ip` varchar(15) NOT NULL default '',
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `level` int(11) NOT NULL default '0',
  `auto_reload` tinyint(1) NOT NULL default '0',
  `banned_until` datetime default NULL,
  `chat_record` enum('yes','no') NOT NULL default 'no',
  `advice_insert` enum('yes','no') NOT NULL default 'no',
  `write_in_chat` enum('yes','no') NOT NULL default 'no',
  `request_to_chat` enum('yes','no') NOT NULL default 'no',
  `create_public_subroom` enum('yes','no') NOT NULL default 'no',
  `enable_webcam` enum('yes','no') NOT NULL default 'no',
  `enable_audio` enum('yes','no') NOT NULL default 'no',
  `enable_drawboard_watch` enum('yes','no') NOT NULL default 'no',
  `enable_drawboard_draw` enum('yes','no') NOT NULL default 'no',
  `enable_livestream_watch` enum('yes','no') NOT NULL default 'no',
  `enable_livestream_publish` enum('yes','no') NOT NULL default 'no',
  `accept_private_message` enum('yes','no') NOT NULL default 'no',
  `picture` varchar(255) default NULL,
  PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_rules_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_setting`
--

CREATE TABLE `conference_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_setting`
--

INSERT INTO `conference_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('code_teleskill', '', 'string', 255, 2, 3, 1, 0, ''),
('conference_creation_limit_per_user', '1', 'string', 255, 0, 0, 1, 0, ''),
('defaultTemplate', 'standard', 'string', 255, 0, 1, 1, 1, ''),
('dimdim_max_mikes', '2', 'string', 255, 6, 7, 1, 0, ''),
('dimdim_max_participant', '15', 'string', 255, 6, 6, 1, 0, ''),
('dimdim_max_room', '1', 'string', 255, 6, 5, 1, 0, ''),
('dimdim_port', '80', 'string', 255, 6, 2, 1, 0, ''),
('dimdim_server', 'www1.dimdim.com', 'string', 255, 6, 1, 1, 0, ''),
('intelligere_application_code', '', 'string', 255, 4, 5, 1, 0, ''),
('intelligere_max_participant', '', 'string', 255, 4, 7, 1, 0, ''),
('intelligere_max_room', '', 'string', 255, 4, 6, 1, 0, ''),
('intelligere_remote_desktop_server_type', '', 'string', 255, 4, 3, 1, 0, ''),
('intelligere_streaming_server_type', '', 'string', 255, 4, 2, 1, 0, ''),
('intelligere_user', '', 'string', 255, 4, 4, 1, 0, ''),
('org_name_teleskill', 'Docebo', 'string', 255, 2, 4, 1, 0, ''),
('teleskill_max_participant', '', 'string', 255, 2, 6, 1, 0, ''),
('teleskill_max_room', '', 'string', 255, 2, 5, 1, 0, ''),
('url_checkin_teleskill', 'http://ews.teleskill.it/ews/server.asp', 'string', 255, 2, 1, 1, 0, ''),
('url_videoconference_intelligere', '', 'string', 255, 4, 1, 1, 0, ''),
('url_videoconference_teleskill', '', 'string', 255, 2, 2, 1, 0, ''),
('dimdim_user', '', 'string', 255, 6, 2, 1, 0, ''),
('dimdim_password', '', 'string', 255, 6, 2, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `conference_teleskill`
--

CREATE TABLE conference_teleskill (
	`id`				BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	`idConference`		BIGINT NOT NULL,
	`roomid`			BIGINT NOT NULL,
	INDEX (idConference)
) TYPE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_teleskill`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `conference_teleskill_room`
--

CREATE TABLE `conference_teleskill_room` (
  `roomid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `zone` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `bookable` tinyint(1) NOT NULL default '0',
  `capacity` int(11) default NULL,
  PRIMARY KEY  (`roomid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `conference_teleskill_room`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_admin_course`
--

CREATE TABLE `core_admin_course` (
  `idst_user` int(11) NOT NULL default '0',
  `type_of_entry` varchar(50) NOT NULL default '',
  `id_entry` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idst_user`,`type_of_entry`,`id_entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_admin_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_admin_tree`
--

CREATE TABLE `core_admin_tree` (
  `idst` varchar(11) NOT NULL default '',
  `idstAdmin` varchar(11) NOT NULL default '',
  PRIMARY KEY  (`idst`,`idstAdmin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_admin_tree`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_calendar`
--

CREATE TABLE `core_calendar` (
  `id` bigint(20) NOT NULL auto_increment,
  `class` varchar(30) default NULL,
  `create_date` datetime default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `title` varchar(255) default NULL,
  `description` text,
  `private` varchar(2) default NULL,
  `category` varchar(255) default NULL,
  `type` bigint(20) default NULL,
  `visibility_rules` tinytext,
  `_owner` int(11) default NULL,
  `_day` smallint(2) default NULL,
  `_month` smallint(2) default NULL,
  `_year` smallint(4) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company`
--

CREATE TABLE `core_company` (
  `company_id` int(11) NOT NULL auto_increment,
  `code` varchar(255) default NULL,
  `name` varchar(255) NOT NULL default '',
  `ctype_id` int(11) NOT NULL default '0',
  `cstatus_id` int(11) NOT NULL default '0',
  `address` text NOT NULL,
  `tel` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `vat_number` varchar(255) NOT NULL default '',
  `restricted_access` tinyint(1) NOT NULL default '0',
  `is_used` tinyint(1) NOT NULL default '0',
  `imported_from_connection` varchar(255) default NULL,
  PRIMARY KEY  (`company_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_companystatus`
--

CREATE TABLE `core_companystatus` (
  `cstatus_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cstatus_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_companystatus`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_companytype`
--

CREATE TABLE `core_companytype` (
  `ctype_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ctype_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_companytype`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_field`
--

CREATE TABLE `core_company_field` (
  `idst` varchar(14) NOT NULL default '0',
  `id_field` int(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_fieldentry`
--

CREATE TABLE `core_company_fieldentry` (
  `id_common` int(11) NOT NULL default '0',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_fieldentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_company_user`
--

CREATE TABLE `core_company_user` (
  `company_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_company_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_connection`
--

CREATE TABLE `core_connection` (
  `name` varchar(50) NOT NULL default '',
  `description` varchar(255) default NULL,
  `type` varchar(50) NOT NULL default '',
  `params` text,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_connection`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_connector`
--

CREATE TABLE `core_connector` (
  `type` varchar(25) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `class` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_connector`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_country`
--

CREATE TABLE `core_country` (
  `id_country` int(11) NOT NULL auto_increment,
  `name_country` varchar(64) NOT NULL default '',
  `iso_code_country` varchar(3) NOT NULL default '',
  `id_zone` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_country`),
  KEY `IDX_COUNTRIES_NAME` (`name_country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_country`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_deleted_user`
--

CREATE TABLE `core_deleted_user` (
  `id_deletion` int(11) NOT NULL auto_increment,
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `signature` text NOT NULL,
  `level` int(11) NOT NULL default '0',
  `lastenter` datetime NOT NULL default '0000-00-00 00:00:00',
  `valid` tinyint(1) NOT NULL default '0',
  `pwd_expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `register_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deletion_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_by` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_deletion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_deleted_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_event`
--

CREATE TABLE `core_event` (
  `idEvent` int(11) NOT NULL auto_increment,
  `idClass` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `priority` smallint(1) unsigned NOT NULL default '1289',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idEvent`),
  KEY `idClass` (`idClass`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event`
--

INSERT INTO `core_event` (`idEvent`, `idClass`, `module`, `section`, `priority`, `description`) VALUES
(1, 40, 'configuration', 'edit', 1, 'Config for user_manager updated'),
(2, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(3, 40, 'configuration', 'edit', 1, 'Config for 8 updated'),
(4, 40, 'configuration', 'edit', 1, 'Config for suiteman updated'),
(5, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(6, 40, 'configuration', 'edit', 1, 'Config for 1 updated'),
(7, 40, 'configuration', 'edit', 1, 'Config for 0 updated'),
(8, 40, 'configuration', 'edit', 1, 'Config for 3 updated'),
(9, 40, 'configuration', 'edit', 1, 'Config for 4 updated'),
(10, 40, 'configuration', 'edit', 1, 'Config for 5 updated'),
(11, 40, 'configuration', 'edit', 1, 'Config for 7 updated'),
(12, 40, 'configuration', 'edit', 1, 'Config for 0 updated');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_class`
--

CREATE TABLE `core_event_class` (
  `idClass` int(11) NOT NULL auto_increment,
  `class` varchar(50) NOT NULL default '',
  `platform` varchar(50) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`idClass`),
  UNIQUE KEY `class_2` (`class`),
  KEY `class` (`class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_class`
--

INSERT INTO `core_event_class` (`idClass`, `class`, `platform`, `description`) VALUES
(1, 'UserNew', 'framework', '_EVENT_USERNEW'),
(2, 'UserMod', 'framework', '_EVENT_USERMOD'),
(3, 'UserDel', 'framework', '_EVENT_USERDEL'),
(4, 'UserNewModerated', 'framework', '_EVENT_USERNEWMODERATED'),
(5, 'UserGroupModerated', 'framework', '_EVENT_USERGROUPMODERATED'),
(6, 'UserGroupInsert', 'framework', '_EVENT_USERGROUPINSERTED'),
(7, 'UserGroupRemove', 'framework', '_EVENT_USERGROUPREMOVED'),
(8, 'UserCourseInsertModerate', 'lms-a', '_EVENT_USERCOURSEINSERTMODEATE'),
(9, 'UserCourseInserted', 'lms-a', '_EVENT_USERCOURSEINSERTED'),
(10, 'UserCourseRemoved', 'lms-a', '_EVENT_USERCOURSEREMOVED'),
(11, 'UserCourseLevelChanged', 'lms-a', '_EVENT_USERCOURSELEVELCHANGED'),
(12, 'UserCourseEnded', 'lms-a', '_EVENT_USERCOURSEENDED'),
(13, 'CoursePorpModified', 'lms-a', '_EVENT_COURSEPROPMODIFIED'),
(14, 'AdviceNew', 'lms', '_EVENT_ADVICENEW'),
(15, 'MsgNewReceived', 'lms', '_EVENT_MSGNEWRECEIVED'),
(16, 'ForumNewCategory', 'lms', '_EVENT_FORUMNEWCATEGORY'),
(17, 'ForumNewThread', 'lms', '_EVENT_FORUMNEWTHERAD'),
(18, 'ForumNewResponse', 'lms', '_EVENT_FORUMNEWRESPONSE'),
(19, 'NewsCreated', 'cms-a', '_EVENT_NEWSCREATED'),
(20, 'MediaCreated', 'cms-a', '_EVENT_MEDIACREATED'),
(21, 'DocumentCreated', 'cms-a', '_EVENT_DOCUMENTCREATED'),
(22, 'ContentCreated', 'cms-a', '_EVENT_CONTENTCREATED'),
(23, 'PageCreated', 'cms-a', '_EVENT_PAGECREATED'),
(24, 'NewsModified', 'cms-a', '_EVENT_NEWSMODIFIED'),
(25, 'MediaModified', 'cms-a', '_EVENT_MEDIAMODIFIED'),
(26, 'DocumentModified', 'cms-a', '_EVENT_DOCUMENTMODIFIED'),
(27, 'ContentModified', 'cms-a', '_EVENT_CONTENTMODIFIED'),
(28, 'PageModified', 'cms-a', '_EVENT_PAGEMODIFIED'),
(29, 'FlowApprovation', 'cms-a', '_EVENT_FLOWAPPROVATION'),
(30, 'NewletterReceived', 'cms-a', '_EVENT_NEWSLETTERRECEIVED'),
(31, 'CmsForumNewCategory', 'cms', '_EVENT_CMSFORUMNEWCATEGORY'),
(32, 'CmsForumNewThread', 'cms', '_EVENT_CMSFORUMNEWTHREAD'),
(33, 'CmsForumNewResponse', 'cms', '_EVENT_CMSFORUMNEWRESPONSE'),
(38, 'UserApproved', 'framework', '_EVENT_USERAPPROVED'),
(39, 'UserCourseBuy', 'lms', '_EVENT_USERCOURSEBUY'),
(40, 'SettingUpdate', 'framework', '_CONFIG_SETTINGS_HAS_BEEN_UPDATED');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_consumer`
--

CREATE TABLE `core_event_consumer` (
  `idConsumer` int(11) NOT NULL auto_increment,
  `consumer_class` varchar(50) NOT NULL default '',
  `consumer_file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idConsumer`),
  UNIQUE KEY `consumer_class` (`consumer_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table of consumer with PHP classes and files';

--
-- Dump dei dati per la tabella `core_event_consumer`
--

INSERT INTO `core_event_consumer` (`idConsumer`, `consumer_class`, `consumer_file`) VALUES
(1, 'DoceboUserNotifier', '/lib/lib.usernotifier.php'),
(2, 'DoceboCourseNotifier', '/lib/lib.coursenotifier.php'),
(3, 'DoceboOrgchartNotifier', '/lib/lib.orgchartnotifier.php'),
(4, 'DoceboCompanyNotifier', '/lib/lib.companynotifier.php'),
(5, 'DoceboSettingNotifier', '/lib/lib.settingnotifier.php');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_consumer_class`
--

CREATE TABLE `core_event_consumer_class` (
  `idConsumer` int(11) NOT NULL default '0',
  `idClass` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idConsumer`,`idClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='n:m relation from consumers and event''s classes';

--
-- Dump dei dati per la tabella `core_event_consumer_class`
--

INSERT INTO `core_event_consumer_class` (`idConsumer`, `idClass`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(2, 3),
(3, 3),
(4, 3),
(5, 40);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_manager`
--

CREATE TABLE `core_event_manager` (
  `idEventMgr` int(11) NOT NULL auto_increment,
  `idClass` int(11) NOT NULL default '0',
  `permission` enum('not_used','mandatory','user_selectable') NOT NULL default 'not_used',
  `channel` set('email','sms') NOT NULL default 'email',
  `recipients` varchar(255) NOT NULL default '',
  `show_level` set('godadmin','admin','user') NOT NULL default '',
  PRIMARY KEY  (`idEventMgr`),
  UNIQUE KEY `idClass` (`idClass`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_manager`
--

INSERT INTO `core_event_manager` (`idEventMgr`, `idClass`, `permission`, `channel`, `recipients`, `show_level`) VALUES
(1, 1, 'user_selectable', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(2, 2, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(3, 3, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(4, 4, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(5, 5, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(6, 6, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(7, 7, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(8, 8, 'not_used', 'email', '_EVENT_RECIPIENTS_MODERATORS_GOD', 'godadmin,admin'),
(9, 9, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(10, 10, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(11, 11, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(12, 12, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER', 'godadmin,admin,user'),
(13, 13, 'not_used', 'email', '_EVENT_RECIPIENTS_TEACHER_GOD', 'godadmin,admin,user'),
(14, 14, 'not_used', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user'),
(15, 15, 'not_used', 'email', '_EVENT_RECIPIEMTS_COURSEUSERS', 'godadmin,admin,user'),
(16, 16, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(17, 17, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(18, 18, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(19, 19, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(20, 20, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(21, 21, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(22, 22, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(23, 23, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(24, 24, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(25, 25, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(26, 26, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(27, 27, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(28, 28, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(29, 29, 'not_used', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin'),
(30, 30, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(31, 31, 'not_used', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(32, 32, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERS', 'godadmin,admin,user'),
(33, 33, 'user_selectable', 'email', '_EVENT_RECIPIENTS_GODADMIN_FLOWMEMBERS', 'godadmin,admin,user'),
(34, 34, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(35, 35, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(36, 36, 'user_selectable', 'email', '_EVENT_RECIPIENTS_ALLKINDOFUSERSRELATEDTODOC', 'godadmin,admin,user'),
(37, 37, 'user_selectable', 'email', '_EVENT_RECIPIENTS_FLOWMANAGER_OPERATOR', 'godadmin,admin,user'),
(38, 38, 'not_used', 'email', '_EVENT_RECIPIENTS_USER', 'godadmin,admin,user'),
(39, 39, 'not_used', 'email', 'user', 'godadmin,admin,user');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_property`
--

CREATE TABLE `core_event_property` (
  `idEvent` int(11) NOT NULL default '0',
  `property_name` varchar(50) NOT NULL default '',
  `property_value` text NOT NULL,
  `property_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`idEvent`,`property_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_property`
--

INSERT INTO `core_event_property` (`idEvent`, `property_name`, `property_value`, `property_date`) VALUES
(1, 'field_saved', 'mail_sender - register_type - max_log_attempt - save_log_attempt - pass_min_char - pass_max_time_valid - hour_request_limit - privacy_policy - register_tree - field_tree', '2007-08-01'),
(1, '_event_log', 'processed - DoceboEventConsumer - 2007-08-01', '2007-08-01'),
(2, 'field_saved', 'url - default_language - defaultTemplate - ttlSession - sender_event - hteditor - htmledit_image_godadmin - htmledit_image_admin - htmledit_image_user - visuItem - visuUser - default_pubflow_method - user_quota', '2007-08-01'),
(2, '_event_log', 'processed - DoceboEventConsumer - 2007-08-01', '2007-08-01'),
(3, 'field_saved', 'google_stat_code', '2007-08-01'),
(4, 'field_saved', '', '2007-08-01'),
(5, 'field_saved', 'use_social_courselist - url - admin_mail - ttlSession - tracking - course_quota', '2007-08-01'),
(6, 'field_saved', 'visuItem - visu_course - defaultTemplate - activeNews - course_list_plan - course_block - max_pdp_answer - on_catalogue_empty - home_course_catalogue', '2007-08-01'),
(7, 'field_saved', 'url - default_language - defaultTemplate - ttlSession - sender_event - hteditor - htmledit_image_godadmin - htmledit_image_admin - htmledit_image_user - visuItem - visuUser - default_pubflow_method - user_quota', '2007-08-01'),
(8, 'field_saved', 'stop_concurrent_user', '2007-08-01'),
(9, 'field_saved', 'user_use_coursecatalogue - user_use_profile', '2007-08-01'),
(10, 'field_saved', 'first_coursecatalogue_tab', '2007-08-01'),
(11, 'field_saved', 'use_course_catalogue', '2007-08-01'),
(12, 'field_saved', 'url - cms_admin_mail - ttlSession - defaultCmsTemplate - defaultCmsLanguage - cms_previewimg_maxsize - anonymous_comment - use_mod_rewrite - grpsel_type - cms_use_dropdown_menu', '2007-08-01');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_event_user`
--

CREATE TABLE `core_event_user` (
  `idEventMgr` int(11) NOT NULL default '0',
  `idst` int(11) NOT NULL default '0',
  `channel` set('email','sms') NOT NULL default '',
  PRIMARY KEY  (`idEventMgr`,`idst`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_event_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_faq`
--

CREATE TABLE `core_faq` (
  `faq_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `answer` text NOT NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`faq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_faq`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_faq_cat`
--

CREATE TABLE `core_faq_cat` (
  `category_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_faq_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_feed_cache`
--

CREATE TABLE `core_feed_cache` (
  `feed_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `refresh_time` int(5) NOT NULL default '0',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `show_on_platform` text NOT NULL,
  `zone` varchar(255) NOT NULL default 'public',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`feed_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_feed_cache`
--

INSERT INTO `core_feed_cache` (`feed_id`, `title`, `url`, `image`, `content`, `active`, `refresh_time`, `last_update`, `show_on_platform`, `zone`, `ord`) VALUES
(1, 'Docebo.org Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=news', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 0),
(2, 'Bugs Feed', 'http://www.docebo.org/doceboCms/feed.php?alias=fixed_bugs&lang=english', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 1),
(3, 'Docebo Corporate Blog', 'http://feeds.feedburner.com/DoceboCorpBlog', '', '', 1, 1440, '0000-00-00 00:00:00', '', 'dashboard', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_feed_out`
--

CREATE TABLE `core_feed_out` (
  `feed_id` int(11) NOT NULL auto_increment,
  `alias` varchar(100) default NULL,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) default NULL,
  `platform` varchar(255) NOT NULL default '',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `regenerate` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_feed_out`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field`
--

CREATE TABLE `core_field` (
  `idField` int(11) NOT NULL auto_increment,
  `id_common` int(11) NOT NULL default '0',
  `type_field` varchar(255) NOT NULL default '',
  `lang_code` varchar(255) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `show_on_platform` varchar(255) NOT NULL default 'framework,',
  `use_multilang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idField`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_son`
--

CREATE TABLE `core_field_son` (
  `idSon` int(11) NOT NULL auto_increment,
  `idField` int(11) NOT NULL default '0',
  `id_common_son` int(11) NOT NULL default '0',
  `lang_code` varchar(50) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(11) NOT NULL,
  PRIMARY KEY  (`idSon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_son`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_template`
--

CREATE TABLE `core_field_template` (
  `id_common` int(11) NOT NULL default '0',
  `ref_id` int(11) NOT NULL default '0',
  `template_code` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_common`,`ref_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_template`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_type`
--

CREATE TABLE `core_field_type` (
  `type_field` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `type_category` varchar(255) NOT NULL default 'standard',
  PRIMARY KEY  (`type_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_type`
--

INSERT INTO `core_field_type` (`type_field`, `type_file`, `type_class`, `type_category`) VALUES
('codicefiscale', 'class.cf.php', 'Field_Cf', 'standard'),
('date', 'class.date.php', 'Field_Date', 'standard'),
('dropdown', 'class.dropdown.php', 'Field_Dropdown', 'standard'),
('freetext', 'class.freetext.php', 'Field_Freetext', 'standard'),
('gmail', 'class.gmail.php', 'CField_Gmail', 'contact'),
('icq', 'class.icq.php', 'CField_Icq', 'contact'),
('msn', 'class.msn.php', 'CField_Msn', 'contact'),
('skype', 'class.skype.php', 'CField_Skype', 'contact'),
('textfield', 'class.textfield.php', 'Field_Textfield', 'standard'),
('upload', 'class.upload.php', 'Field_Upload', 'standard'),
('yahoo', 'class.yahoo.php', 'CField_Yahoo', 'contact'),
('yesno', 'class.yesno.php', 'Field_Yesno', 'standard');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_field_userentry`
--

CREATE TABLE `core_field_userentry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_field_userentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group`
--

CREATE TABLE `core_group` (
  `idst` int(11) NOT NULL default '0',
  `groupid` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `hidden` enum('true','false') NOT NULL default 'false',
  `type` enum('free','moderate','private','invisible','course','company') NOT NULL default 'free',
  `show_on_platform` text NOT NULL,
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `groupid` (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group`
--

INSERT INTO `core_group` (`idst`, `groupid`, `description`, `hidden`, `type`, `show_on_platform`) VALUES
(1, '/oc_0', 'Root of organization chart', 'true', 'free', 'framework,'),
(2, '/ocd_0', 'Root of organization chart and descendants', 'true', 'free', 'framework,'),
(3, '/framework/level/godadmin', 'Group of godadmins', 'true', 'free', 'framework, '),
(4, '/framework/level/admin', 'Group of administrators', 'true', 'free', 'framework, '),
(5, '/framework/level/user', 'Group of normal users', 'true', 'free', 'framework,'),
(6, '/framework/orgchart/fields', 'Used for orgchart field assignement', 'true', 'free', 'framework,'),
(7, '/pubflow_step_1', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(8, '/pubflow_step_2', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(9, '/pubflow_step_3', 'Pubblication flow step group', 'true', 'free', 'framework,'),
(10, '/framework/company/fields', 'Used to associate a set of fields', 'true', 'free', ''),
(271, '/lms/custom/1/7', 'for custom lms menu', 'true', 'free', ''),
(272, '/lms/custom/1/6', 'for custom lms menu', 'true', 'free', ''),
(273, '/lms/custom/1/5', 'for custom lms menu', 'true', 'free', ''),
(274, '/lms/custom/1/4', 'for custom lms menu', 'true', 'free', ''),
(275, '/lms/custom/1/3', 'for custom lms menu', 'true', 'free', ''),
(276, '/lms/custom/1/2', 'for custom lms menu', 'true', 'free', ''),
(277, '/lms/custom/1/1', 'for custom lms menu', 'true', 'free', ''),
(1011, '/framework/level/publicadmin', 'Group of Public Admin', 'true', 'free', 'framework, ');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_fields`
--

CREATE TABLE `core_group_fields` (
  `idst` int(11) NOT NULL default '0',
  `id_field` int(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('noaccess','readonly','readwrite') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_fields`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_members`
--

CREATE TABLE `core_group_members` (
  `idst` int(11) NOT NULL default '0',
  `idstMember` int(11) NOT NULL default '0',
  `filter` varchar(50) NOT NULL default '',
  UNIQUE KEY `unique_relation` (`idst`,`idstMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_members`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_group_user_waiting`
--

CREATE TABLE `core_group_user_waiting` (
  `idst_group` int(11) NOT NULL default '0',
  `idst_user` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idst_group`,`idst_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_group_user_waiting`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_hteditor`
--

CREATE TABLE `core_hteditor` (
  `hteditor` varchar(255) NOT NULL default '',
  `hteditorname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`hteditor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_hteditor`
--

INSERT INTO `core_hteditor` (`hteditor`, `hteditorname`) VALUES
('tinymce', '_TINYMCE'),
('textarea', '_TEXTAREA'),
('yui', '_YUI');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_language`
--

CREATE TABLE `core_lang_language` (
  `lang_code` varchar(50) NOT NULL default '',
  `lang_description` varchar(255) NOT NULL default '',
  `lang_charset` varchar(20) NOT NULL default 'utf-8',
  `lang_browsercode` varchar(50) NOT NULL default '',
  `lang_direction` enum('ltr','rtl') NOT NULL default 'ltr',
  PRIMARY KEY  (`lang_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_language`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_text`
--

CREATE TABLE `core_lang_text` (
  `id_text` int(11) NOT NULL auto_increment,
  `text_key` varchar(50) NOT NULL default '',
  `text_module` varchar(50) NOT NULL default '',
  `text_platform` varchar(50) NOT NULL default '',
  `text_description` varchar(255) NOT NULL default '',
  `text_attributes` set('accessibility','sms','email') NOT NULL default '',
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `text_key` (`text_key`,`text_module`,`text_platform`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_lang_translation`
--

CREATE TABLE `core_lang_translation` (
  `id_translation` int(11) NOT NULL auto_increment,
  `translation_text` text,
  `lang_code` varchar(50) NOT NULL default '',
  `save_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_text` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_translation`),
  KEY `lang_code` (`lang_code`,`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_lang_translation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_menu`
--

CREATE TABLE `core_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_menu`
--

INSERT INTO `core_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '', '', 1, 'true'),
(2, '_USER_MANAGMENT', '', 2, 'false'),
(3, '_TRASV_MANAGMENT', '', 3, 'false'),
(4, '_PERMISSION', '', 4, 'false'),
(5, '_LANGUAGE', '', 5, 'false'),
(6, '', '', 6, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_menu_under`
--

CREATE TABLE `core_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_menu_under`
--

INSERT INTO `core_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'dashboard', '_DASHBOARD', 'dashboard', 'view', NULL, 1, 'class.dashboard.php', 'Module_Dashboard'),
(2, 2, 'directory', '_LISTUSER', 'org_chart', 'view_org_chart', NULL, 1, 'class.directory.php', 'Module_Directory'),
(3, 2, 'directory', '_LISTGROUP', 'listgroup', 'view_group', NULL, 2, 'class.directory.php', 'Module_Directory'),
(4, 2, 'field_manager', '_FIELD_MANAGER', 'field_list', 'view', NULL, 4, 'class.field_manager.php', 'Module_Field_Manager'),
(5, 3, 'configuration', '_CONFIGURATION', 'config', 'view', NULL, 1, 'class.configuration.php', 'Module_Configuration'),
(7, 3, 'event_manager', '_EVENTMANAGER', 'display', 'view_event_manager', NULL, 3, 'class.event_manager.php', 'Module_Event_Manager'),
(8, 3, 'iotask', '_IOTASK', 'iotask', 'view', NULL, 4, 'class.iotask.php', 'Module_IOTask'),
(9, 4, 'admin_manager', '_ADMIN_MANAGER', 'view', 'view', NULL, 1, 'class.admin_manager.php', 'Module_Admin_Manager'),
(10, 5, 'lang', '_LANG', 'lang', 'view', NULL, 1, 'class.lang.php', 'Module_Lang'),
(11, 5, 'lang', '_LANG_IMPORT_EXPORT', 'importexport', 'view', NULL, 2, 'class.lang.php', 'Module_Lang'),
(12, 5, 'regional_settings', '_REGSET', 'regset', 'view', NULL, 3, 'class.regional_settings.php', 'Module_Regional_settings'),
(13, 6, 'newsletter', '_NEWSLETTER', 'newsletter', 'view', NULL, 1, 'class.newsletter.php', 'Module_Newsletter'),
(14, 2, 'company', '_COMPANY', 'main', 'view', 'crm', 3, 'class.company.php', 'Module_Company'),
(15, 4, 'public_admin_manager', '_PUBLIC_ADMIN_MANAGER', 'view', 'view', NULL, 2, 'class.public_admin_manager.php', 'Module_Public_Admin_Manager');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_message`
--

CREATE TABLE `core_message` (
  `idMessage` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `sender` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `attach` varchar(255) NOT NULL default '',
  `priority` int(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_message`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_message_user`
--

CREATE TABLE `core_message_user` (
  `idMessage` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `read` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`,`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_message_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_newsletter`
--

CREATE TABLE `core_newsletter` (
  `id` int(11) NOT NULL auto_increment,
  `id_send` int(11) NOT NULL default '0',
  `sub` varchar(255) NOT NULL default '',
  `msg` text NOT NULL,
  `fromemail` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  `tot` int(11) NOT NULL default '0',
  `send_type` enum('email','sms') NOT NULL default 'email',
  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
  `file` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_newsletter`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_newsletter_sendto`
--

CREATE TABLE `core_newsletter_sendto` (
  `id_send` int(11) NOT NULL default '0',
  `idst` int(11) NOT NULL default '0',
  `stime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_send`,`idst`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_newsletter_sendto`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart`
--

CREATE TABLE `core_org_chart` (
  `id_dir` int(11) NOT NULL default '0',
  `lang_code` varchar(50) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_dir`,`lang_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_field`
--

CREATE TABLE `core_org_chart_field` (
  `idst` int(11) NOT NULL default '0',
  `id_field` varchar(11) NOT NULL default '0',
  `mandatory` enum('true','false') NOT NULL default 'false',
  `useraccess` enum('readonly','readwrite','noaccess') NOT NULL default 'readonly',
  PRIMARY KEY  (`idst`,`id_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_fieldentry`
--

CREATE TABLE `core_org_chart_fieldentry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_fieldentry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_tree`
--

CREATE TABLE `core_org_chart_tree` (
  `idOrg` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` text NOT NULL,
  `lev` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idOrg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_tree`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_org_chart_user`
--

CREATE TABLE `core_org_chart_user` (
  `id_org` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_org_chart_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_lang`
--

CREATE TABLE `core_pflow_lang` (
  `id` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `language` varchar(40) NOT NULL default '',
  `val_name` varchar(30) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`type`,`language`,`val_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_lang`
--

INSERT INTO `core_pflow_lang` (`id`, `type`, `language`, `val_name`, `value`) VALUES
(1, 'flow', 'english', 'description', ''),
(1, 'flow', 'english', 'label', 'Immediate publish'),
(1, 'flow', 'italian', 'description', ''),
(1, 'flow', 'italian', 'label', 'Pubblicazione immediata'),
(1, 'step', 'english', 'description', ''),
(1, 'step', 'english', 'label', 'Published'),
(1, 'step', 'italian', 'description', ''),
(1, 'step', 'italian', 'label', 'Pubblicato'),
(2, 'flow', 'english', 'description', ''),
(2, 'flow', 'english', 'label', 'Publish / Unpublish'),
(2, 'flow', 'italian', 'description', ''),
(2, 'flow', 'italian', 'label', 'Pubblica / spubblica'),
(2, 'step', 'english', 'description', ''),
(2, 'step', 'english', 'label', 'Unpublished'),
(2, 'step', 'italian', 'description', ''),
(2, 'step', 'italian', 'label', 'Non pubblicato'),
(7, 'step', 'italian', 'description', ''),
(7, 'step', 'italian', 'label', 'Step 2'),
(8, 'step', 'english', 'description', ''),
(8, 'step', 'english', 'label', 'Step 3'),
(8, 'step', 'italian', 'description', ''),
(8, 'step', 'italian', 'label', 'Step 3');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_list`
--

CREATE TABLE `core_pflow_list` (
  `flow_id` int(11) NOT NULL auto_increment,
  `flow_code` varchar(20) default NULL,
  `default` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`flow_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_list`
--

INSERT INTO `core_pflow_list` (`flow_id`, `flow_code`, `default`, `ord`) VALUES
(1, 'pub_onestate', 1, 1),
(2, 'pub_twostate', 1, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pflow_step`
--

CREATE TABLE `core_pflow_step` (
  `step_id` int(11) NOT NULL auto_increment,
  `flow_id` int(11) NOT NULL default '0',
  `st_id` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `is_published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`step_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pflow_step`
--

INSERT INTO `core_pflow_step` (`step_id`, `flow_id`, `st_id`, `ord`, `is_published`) VALUES
(1, 1, 118, 3, 1),
(2, 2, 119, 4, 0),
(3, 2, 120, 5, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_platform`
--

CREATE TABLE `core_platform` (
  `platform` varchar(255) NOT NULL default '',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `class_file_menu` varchar(255) NOT NULL default '',
  `class_name_menu` varchar(255) NOT NULL default '',
  `class_name_menu_managment` varchar(255) NOT NULL default '',
  `file_class_config` varchar(255) NOT NULL default '',
  `class_name_config` varchar(255) NOT NULL default '',
  `var_default_template` varchar(255) NOT NULL default '',
  `class_default_admin` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `is_active` enum('true','false') NOT NULL default 'true',
  `mandatory` enum('true','false') NOT NULL default 'true',
  `dependencies` text NOT NULL,
  `main` enum('true','false') NOT NULL default 'true',
  `hidden_in_config` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`platform`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_platform`
--

INSERT INTO `core_platform` (`platform`, `class_file`, `class_name`, `class_file_menu`, `class_name_menu`, `class_name_menu_managment`, `file_class_config`, `class_name_config`, `var_default_template`, `class_default_admin`, `sequence`, `is_active`, `mandatory`, `dependencies`, `main`, `hidden_in_config`) VALUES
('cms', '', '', 'class.admin_menu_cms.php', 'Admin_Cms', 'Admin_Managment_Cms', 'class.conf_cms.php', 'Config_Cms', 'defaultCmsTemplate', 'CmsAdminModule', 5, 'true', 'false', '', 'false', 'false'),
('crm', '', '', 'class.admin_menu_crm.php', 'Admin_Crm', 'Admin_Managment_Crm', 'class.conf_crm.php', 'Config_Crm', 'defaultTemplate', 'CrmAdminModule', 6, 'false', 'false', '', 'false', 'true'),
('ecom', '', '', 'class.admin_menu_ecom.php', 'Admin_Ecom', 'Admin_Managment_Ecom', 'class.conf_ecom.php', 'Config_Ecom', 'defaultTemplate', 'EcomAdminModule', 7, 'false', 'false', '', 'false', 'false'),
('framework', '', '', 'class.admin_menu_fw.php', 'Admin_Framework', 'Admin_Managment_Framework', 'class.conf_fw.php', 'Config_Framework', 'defaultTemplate', 'Module', 1, 'true', 'true', '', 'false', 'false'),
('lms', '', '', 'class.admin_menu_lms.php', 'Admin_Lms', 'Admin_Managment_Lms', 'class.conf_lms.php', 'Config_Lms', 'defaultTemplate', 'LmsAdminModule', 2, 'true', 'false', '', 'true', 'false'),
('scs', '', '', 'class.admin_menu_scs.php', 'Admin_Scs', '', 'class.conf_scs.php', 'Config_Scs', 'defaultTemplate', 'ScsAdminModule', 3, 'true', 'false', '', 'false', 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_pwd_recover`
--

CREATE TABLE `core_pwd_recover` (
  `idst_user` int(11) NOT NULL default '0',
  `random_code` varchar(255) NOT NULL default '',
  `request_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idst_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_pwd_recover`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_reg_list`
--

CREATE TABLE `core_reg_list` (
  `region_id` varchar(100) NOT NULL default '',
  `lang_code` varchar(50) NOT NULL default '',
  `region_desc` varchar(255) NOT NULL default '',
  `default_region` tinyint(1) NOT NULL default '0',
  `browsercode` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_reg_list`
--

INSERT INTO `core_reg_list` (`region_id`, `lang_code`, `region_desc`, `default_region`, `browsercode`) VALUES
('england', 'english', 'england, usa, ...', 0, 'en-EN, en-US'),
('italy', 'italian', 'Italia', 1, 'it');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_reg_setting`
--

CREATE TABLE `core_reg_setting` (
  `region_id` varchar(100) NOT NULL default '',
  `val_name` varchar(100) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`region_id`,`val_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_reg_setting`
--

INSERT INTO `core_reg_setting` (`region_id`, `val_name`, `value`) VALUES
('england', 'custom_date_format', ''),
('england', 'custom_time_format', ''),
('england', 'date_format', 'd_m_Y'),
('england', 'date_sep', '/'),
('england', 'time_format', 'H_i'),
('italy', 'custom_date_format', ''),
('italy', 'custom_time_format', ''),
('italy', 'date_format', 'd_m_Y'),
('italy', 'date_sep', '-'),
('italy', 'time_format', 'H_i');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_resource`
--

CREATE TABLE `core_resource` (
  `resource_code` varchar(60) NOT NULL default '',
  `platform` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`resource_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_resource`
--

INSERT INTO `core_resource` (`resource_code`, `platform`) VALUES
('classroom', 'lms'),
('course', 'lms'),
('course_edition', 'lms'),
('user', 'framework');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_resource_timetable`
--

CREATE TABLE `core_resource_timetable` (
  `id` int(11) NOT NULL auto_increment,
  `resource` varchar(60) NOT NULL default '',
  `resource_id` int(11) NOT NULL default '0',
  `consumer` varchar(60) NOT NULL default '',
  `consumer_id` int(11) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_resource_timetable`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_rest_authentication`
--

CREATE TABLE `core_rest_authentication` (
  `id_user` int(11) NOT NULL,
  `user_level` int(11) NOT NULL,
  `token` varchar(255) collate utf8_unicode_ci NOT NULL,
  `generation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_enter_date` datetime default NULL,
  `expiry_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dump dei dati per la tabella `core_rest_authentication`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_revision`
--

CREATE TABLE `core_revision` (
  `type` enum('wiki','faq') NOT NULL default 'faq',
  `parent_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `sub_key` varchar(80) NOT NULL default '0',
  `author` int(11) NOT NULL default '0',
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` longtext NOT NULL,
  PRIMARY KEY  (`type`,`parent_id`,`version`,`sub_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_revision`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_role`
--

CREATE TABLE `core_role` (
  `idst` int(11) NOT NULL default '0',
  `roleid` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`idst`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

--
-- Dump dei dati per la tabella `core_role`
--

INSERT INTO `core_role` (`idst`, `roleid`, `description`) VALUES
(12, '/framework/admin/configuration/view', 'Configuration module'),
(13, '/framework/admin/directory/approve_waiting_user', 'Approve waiting user'),
(14, '/framework/admin/directory/associate_group', 'Associate users to groups'),
(15, '/framework/admin/directory/creategroup', 'Group insert'),
(16, '/framework/admin/directory/createuser_org_chart', 'User insert'),
(17, '/framework/admin/directory/delgroup', 'Group remove'),
(18, '/framework/admin/directory/deluser_org_chart', 'User remove'),
(19, '/framework/admin/directory/editgroup', 'Edit group'),
(20, '/framework/admin/directory/edituser_org_chart', 'Edit user'),
(21, '/framework/admin/directory/view', 'View user managment'),
(22, '/framework/admin/directory/view_group', 'View groups list'),
(23, '/framework/admin/directory/view_org_chart', 'You can see the organization_chart module'),
(24, '/framework/admin/directory/view_user', 'View users list'),
(25, '/framework/admin/event_manager/view_event_manager', 'Show event managment'),
(26, '/framework/admin/field_manager/add', 'You can add custom field'),
(27, '/framework/admin/field_manager/del', 'You can add remove field'),
(28, '/framework/admin/field_manager/mod', 'You can add modify field'),
(29, '/framework/admin/field_manager/view', 'You can see the custom field'),
(30, '/framework/admin/lang/importexport', 'Import language'),
(31, '/framework/admin/lang/view', 'You can use the language module'),
(32, '/framework/admin/lang/view_org_chart', 'You can see the org chart'),
(33, '/framework/admin/newsletter/view', ''),
(34, '/framework/admin/publication_flow/view', 'You can see the pubblication flow'),
(35, '/framework/admin/regional_settings/view', 'You can see the regional setting'),
(36, '/framework/admin/feedreader/view', ''),
(37, '/framework/admin/dashboard/view', ''),
(38, '/framework/admin/dashboard/view_event_manager', ''),
(39, '/framework/admin/iotask/view', 'Can operate on inport export tasks'),
(40, '/framework/admin/admin_manager/view', 'Can operate with admin configuration'),
(45, '/lms/admin/eportfolio/mod', ''),
(46, '/lms/admin/catalogue/associate', ''),
(47, '/lms/admin/catalogue/mod', 'You can operate with the catalogues'),
(48, '/lms/admin/catalogue/view', 'You can operate see the catalogues'),
(49, '/lms/admin/course/add', 'You can add courses'),
(50, '/lms/admin/course/del', 'You can remove courses'),
(51, '/lms/admin/course/mod', 'You can modify courses'),
(52, '/lms/admin/course/moderate', 'Modertae user tocourse subscription'),
(53, '/lms/admin/course/subscribe', 'You can subscribe users to courses'),
(54, '/lms/admin/course/view', 'You can see the courses'),
(55, '/lms/admin/coursepath/mod', 'You can operate with the bounded courses'),
(56, '/lms/admin/coursepath/moderate', ''),
(57, '/lms/admin/coursepath/subscribe', ''),
(58, '/lms/admin/coursepath/view', 'You can see the bounded courses'),
(59, '/lms/admin/manmenu/mod', 'You can modify the custom menu for courses'),
(60, '/lms/admin/manmenu/view', 'You can see the custom menu for courses'),
(61, '/lms/admin/news/mod', 'You can modify the news'),
(62, '/lms/admin/news/view', 'You can see the news'),
(63, '/lms/admin/questcategory/mod', ''),
(64, '/lms/admin/questcategory/view', ''),
(65, '/lms/admin/report/view', ''),
(66, '/lms/admin/webpages/mod', 'You can modify the webpages'),
(67, '/lms/admin/webpages/view', 'You can see the news'),
(68, '/lms/admin/eportfolio/view', 'You can view the eportfolio manager'),
(69, '/lms/admin/preassessment/view', ''),
(70, '/lms/admin/preassessment/mod', ''),
(71, '/lms/admin/preassessment/subscribe', ''),
(72, '/lms/admin/classroom/view', ''),
(73, '/lms/admin/classroom/mod', ''),
(74, '/lms/admin/classlocation/view', ''),
(75, '/lms/admin/classevent/view', ''),
(76, '/lms/course/private/course/view_info', ''),
(77, '/lms/course/private/course/mod', ''),
(78, '/lms/course/private/advice/view', ''),
(79, '/lms/course/private/advice/mod', ''),
(80, '/lms/course/private/groups/view', ''),
(81, '/lms/course/private/groups/mod', ''),
(82, '/lms/course/private/groups/subscribe', ''),
(83, '/lms/course/private/manmenu/view', ''),
(84, '/lms/course/private/manmenu/mod', ''),
(85, '/lms/course/private/organization/view', ''),
(86, '/lms/course/private/chat/view', ''),
(87, '/lms/course/private/notes/view', ''),
(88, '/lms/course/private/forum/view', ''),
(89, '/lms/course/private/forum/write', ''),
(90, '/lms/course/private/forum/upload', ''),
(91, '/lms/course/private/forum/add', ''),
(92, '/lms/course/private/forum/mod', ''),
(93, '/lms/course/private/forum/del', ''),
(94, '/lms/course/private/forum/moderate', ''),
(95, '/lms/course/private/forum/sema', ''),
(96, '/lms/course/private/teleskill/view', ''),
(97, '/lms/course/private/teleskill/moderator', ''),
(98, '/lms/course/private/project/view', ''),
(99, '/lms/course/private/project/add', ''),
(100, '/lms/course/private/project/mod', ''),
(101, '/lms/course/private/project/del', ''),
(102, '/lms/course/private/gradebook/view', ''),
(103, '/lms/course/private/storage/view', ''),
(104, '/lms/course/private/storage/home', ''),
(105, '/lms/course/private/storage/lesson', ''),
(106, '/lms/course/private/storage/public', ''),
(107, '/lms/course/private/coursereport/view', ''),
(108, '/lms/course/private/coursereport/mod', ''),
(109, '/lms/course/private/statistic/view', ''),
(110, '/lms/course/private/stats/view_user', ''),
(111, '/lms/course/private/stats/view_course', ''),
(112, '/lms/course/private/htmlfront/view', ''),
(113, '/lms/course/private/htmlfront/mod', ''),
(114, '/lms/course/private/conference/view', ''),
(115, '/lms/course/private/conference/mod', ''),
(119, '/lms/course/private/wiki/view', ''),
(120, '/lms/course/private/wiki/mod', ''),
(121, '/lms/course/private/newsletter/view', ''),
(122, '/lms/course/public/message/view', ''),
(123, '/lms/course/public/message/send_all', ''),
(124, '/lms/course/public/myfriends/view', ''),
(125, '/lms/course/public/course/view', 'Logged people can see list of subscribed course'),
(126, '/lms/course/public/coursecatalogue/view', 'Logged people can see the course catalogue'),
(127, '/lms/course/public/mygroup/view', 'View my group management'),
(128, '/lms/course/public/profile/mod', 'User can modify the own profile'),
(129, '/lms/course/public/profile/view', 'User can see the own profile'),
(130, '/lms/course/public/userevent/view', 'Show the module for my alert management'),
(131, '/lms/course/public/mycertificate/view', ''),
(132, '/lms/course/public/eportfolio/view', ''),
(133, '/lms/course/public/myfiles/view', ''),
(134, '/lms/course/public/tprofile/view', NULL),
(135, '/scs/admin/admin_configuration/view', 'You can modify the confiuration'),
(136, '/scs/admin/room/mod', 'You can operate with room'),
(137, '/scs/admin/room/view', 'You can see the rooms'),
(138, '/crm/admin/company/view', ''),
(139, '/crm/admin/ticketstatus/view', ''),
(140, '/crm/admin/companytype/view', ''),
(141, '/crm/admin/companystatus/view', ''),
(142, '/crm/admin/contactreason/view', ''),
(143, '/crm/admin/company/add', ''),
(144, '/crm/admin/company/mod', ''),
(145, '/crm/admin/company/del', ''),
(146, '/crm/admin/storedform/view', ''),
(147, '/crm/module/company/view', ''),
(148, '/crm/module/abook/view', ''),
(149, '/crm/module/project/view', ''),
(150, '/crm/module/todo/view', ''),
(151, '/crm/module/ticket/view', ''),
(152, '/crm/module/contacthistory/view', ''),
(153, '/crm/module/task/view', ''),
(154, '/ecom/admin/bought/view', ''),
(155, '/ecom/admin/payaccount/view', ''),
(156, '/ecom/admin/taxzone/view', ''),
(157, '/ecom/admin/taxcountry/view', ''),
(158, '/ecom/admin/taxcatgod/view', ''),
(159, '/ecom/admin/taxrate/view', ''),
(160, '/ecom/admin/transaction/view', ''),
(161, '/ecom/admin/reservation/view', ''),
(162, '/ecom/admin/taxzone/mod', ''),
(163, '/ecom/admin/taxcatgod/mod', ''),
(164, '/ecom/admin/payaccount/mod', ''),
(165, '/ecom/admin/taxcountry/mod', ''),
(166, '/ecom/admin/taxrate/mod', ''),
(167, '/ecom/admin/transaction/mod', ''),
(168, '/ecom/admin/reservation/mod', ''),
(169, '/ecom/admin/bought/mod', ''),
(170, '/cms/admin/banners/cat_view', ''),
(171, '/cms/admin/banners/view', ''),
(172, '/cms/admin/docs/view', ''),
(173, '/cms/admin/form/view', ''),
(174, '/cms/admin/links/view', ''),
(175, '/cms/admin/manpage/view', 'Page management'),
(176, '/cms/admin/banners/add', ''),
(177, '/cms/admin/banners/del', ''),
(178, '/cms/admin/banners/mod', ''),
(179, '/cms/admin/content/add', ''),
(180, '/cms/admin/content/del', ''),
(181, '/cms/admin/content/mod', ''),
(182, '/cms/admin/content/view', ''),
(183, '/cms/admin/docs/add', ''),
(184, '/cms/admin/docs/del', ''),
(185, '/cms/admin/docs/mod', ''),
(186, '/cms/admin/form/add', ''),
(187, '/cms/admin/form/del', ''),
(188, '/cms/admin/form/mod', ''),
(189, '/cms/admin/forum/add', ''),
(190, '/cms/admin/forum/del', ''),
(191, '/cms/admin/forum/mod', ''),
(192, '/cms/admin/forum/view', ''),
(193, '/cms/admin/links/add', ''),
(194, '/cms/admin/links/del', ''),
(195, '/cms/admin/links/mod', ''),
(196, '/cms/admin/manpage/add', ''),
(197, '/cms/admin/manpage/del', ''),
(198, '/cms/admin/manpage/mod', ''),
(199, '/cms/admin/faq/view', ''),
(200, '/cms/admin/mantopic/add', ''),
(201, '/cms/admin/mantopic/del', ''),
(202, '/cms/admin/mantopic/mod', ''),
(203, '/cms/admin/mantopic/view', 'You can see the CMS topic management module'),
(204, '/cms/admin/media/add', ''),
(205, '/cms/admin/media/del', ''),
(206, '/cms/admin/media/mod', ''),
(207, '/cms/admin/media/view', 'Multimedia manager'),
(208, '/cms/admin/news/add', ''),
(209, '/cms/admin/news/del', ''),
(210, '/cms/admin/news/mod', ''),
(211, '/cms/admin/news/view', 'News management'),
(212, '/cms/admin/poll/add', ''),
(213, '/cms/admin/poll/del', ''),
(214, '/cms/admin/poll/mod', ''),
(215, '/cms/admin/poll/view', ''),
(216, '/cms/admin/stats/view', ''),
(217, '/cms/admin/wiki/view', ''),
(278, '/lms/course/private/light_repo/view', ''),
(279, '/lms/course/private/light_repo/mod', ''),
(430, '/lms/admin/certificate/view', 'Certificate admin view'),
(431, '/lms/admin/certificate/mod', 'Certificate admin mod'),
(453, '/lms/admin/report_certificate/view', 'Certificate report admin view'),
(456, '/lms/course/private/calendar/view', ''),
(457, '/lms/course/private/calendar/personal', ''),
(458, '/lms/course/private/calendar/mod', ''),
(459, '/lms/course/private/wiki/edit', ''),
(460, '/lms/course/private/wiki/admin', ''),
(578, '/cms/admin/faq/add', ''),
(579, '/cms/admin/faq/mod', ''),
(580, '/cms/admin/faq/del', ''),
(581, '/cms/admin/wiki/add', ''),
(582, '/cms/admin/wiki/mod', ''),
(583, '/cms/admin/wiki/del', ''),
(584, '/framework/admin/bugtracker/view', ''),
(585, '/cms/admin/calendar/view', ''),
(586, '/cms/admin/calendar/add', ''),
(587, '/cms/admin/calendar/mod', ''),
(588, '/cms/admin/calendar/del', ''),
(592, '/lms/admin/classlocation/mod', ''),
(593, '/lms/admin/classevent/mod', ''),
(594, '/lms/admin/reservation/view', ''),
(595, '/lms/admin/reservation/mod', ''),
(893, '/crm/admin/companytype/add', ''),
(894, '/crm/admin/companytype/mod', ''),
(895, '/crm/admin/companytype/del', ''),
(896, '/crm/admin/companystatus/mod', ''),
(897, '/crm/admin/ticketstatus/add', ''),
(898, '/crm/admin/ticketstatus/mod', ''),
(899, '/crm/admin/ticketstatus/del', ''),
(900, '/crm/admin/contactreason/add', ''),
(901, '/crm/admin/contactreason/mod', ''),
(902, '/crm/admin/contactreason/del', ''),
(994, '/cms/admin/simpleprj/view', ''),
(995, '/lms/admin/middlearea/view', ''),
(996, '/lms/admin/internal_news/view', ''),
(997, '/lms/admin/internal_news/mod', ''),
(998, '/lms/course/public/public_forum/view', ''),
(999, '/lms/course/public/public_forum/write', ''),
(1000, '/lms/course/public/public_forum/upload', ''),
(1001, '/lms/course/public/public_forum/add', ''),
(1002, '/lms/course/public/public_forum/mod', ''),
(1003, '/lms/course/public/public_forum/del', ''),
(1004, '/lms/course/public/public_forum/moderate', ''),
(1005, '/lms/course/public/course_autoregistration/view', ''),
(1012, '/lms/course/public/mycompetences/view', NULL),
(1013, '/framework/admin/public_admin_manager/view', NULL),
(1014, '/lms/course/public/public_user_admin/view_org_chart', NULL),
(1015, '/lms/course/public/public_user_admin/createuser_org_chart', NULL),
(1016, '/lms/course/public/public_user_admin/edituser_org_chart', NULL),
(1017, '/lms/course/public/public_user_admin/deluser_org_chart', NULL),
(1018, '/lms/course/public/public_user_admin/approve_waiting_user', NULL),
(1019, '/lms/course/public/public_user_admin/view_user', NULL),
(1020, '/lms/course/public/public_course_admin/view', NULL),
(1021, '/lms/course/public/public_course_admin/add', NULL),
(1022, '/lms/course/public/public_course_admin/mod', NULL),
(1023, '/lms/course/public/public_course_admin/del', NULL),
(1024, '/lms/course/public/public_course_admin/subscribe', NULL),
(1025, '/lms/course/public/public_course_admin/moderate', NULL),
(1026, '/lms/course/public/public_subscribe_admin/view_org_chart', NULL),
(1027, '/lms/course/public/public_subscribe_admin/createuser_org_chart', NULL),
(1028, '/lms/course/public/public_subscribe_admin/edituser_org_chart', NULL),
(1029, '/lms/course/public/public_subscribe_admin/deluser_org_chart', NULL),
(1030, '/lms/course/public/public_subscribe_admin/approve_waiting_user', NULL),
(1031, '/lms/course/public/public_report_admin/view', NULL),
(1032, '/lms/course/public/public_newsletter_admin/view', NULL),
(1033, '/lms/admin/meta_certificate/view', NULL),
(1034, '/lms/admin/meta_certificate/mod', NULL),
(1035, '/lms/admin/competences/view', NULL),
(1036, '/lms/admin/competences/mod', NULL),
(1037, '/lms/admin/report/view', NULL),
(1038, '/lms/admin/report/mod', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_role_members`
--

CREATE TABLE `core_role_members` (
  `idst` int(11) NOT NULL default '0',
  `idstMember` int(11) NOT NULL default '0',
  UNIQUE KEY `idst` (`idst`,`idstMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_role_members`
--

INSERT INTO `core_role_members` (`idst`, `idstMember`) VALUES
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(26, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 3),
(32, 3),
(33, 3),
(34, 3),
(35, 3),
(36, 3),
(37, 3),
(37, 4),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(47, 3),
(49, 3),
(50, 3),
(51, 3),
(52, 3),
(53, 3),
(54, 3),
(55, 3),
(56, 3),
(57, 3),
(59, 3),
(60, 3),
(61, 3),
(62, 3),
(63, 3),
(64, 3),
(65, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 3),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(76, 271),
(76, 272),
(76, 273),
(76, 274),
(76, 275),
(76, 276),
(76, 277),
(77, 3),
(77, 271),
(77, 272),
(77, 273),
(77, 274),
(78, 3),
(78, 271),
(78, 272),
(78, 273),
(78, 274),
(78, 275),
(78, 276),
(78, 277),
(79, 3),
(79, 271),
(79, 272),
(79, 273),
(79, 274),
(80, 3),
(80, 271),
(80, 272),
(80, 273),
(80, 274),
(81, 3),
(81, 271),
(81, 272),
(82, 3),
(82, 271),
(82, 272),
(82, 273),
(82, 274),
(83, 3),
(83, 271),
(83, 272),
(84, 3),
(84, 271),
(84, 272),
(85, 3),
(85, 271),
(85, 272),
(85, 273),
(85, 274),
(85, 275),
(85, 276),
(85, 277),
(86, 3),
(86, 271),
(86, 272),
(86, 273),
(86, 274),
(86, 275),
(87, 3),
(87, 271),
(87, 272),
(87, 273),
(87, 274),
(87, 275),
(87, 276),
(87, 277),
(88, 3),
(88, 271),
(88, 272),
(88, 273),
(88, 274),
(88, 275),
(88, 276),
(88, 277),
(89, 3),
(89, 271),
(89, 272),
(89, 273),
(89, 274),
(89, 275),
(90, 3),
(90, 271),
(90, 272),
(90, 273),
(90, 274),
(90, 275),
(91, 3),
(91, 271),
(91, 272),
(92, 3),
(92, 271),
(92, 272),
(93, 3),
(93, 271),
(93, 272),
(94, 3),
(94, 271),
(94, 272),
(94, 273),
(94, 274),
(95, 3),
(96, 3),
(96, 271),
(96, 272),
(96, 273),
(96, 274),
(96, 275),
(96, 276),
(96, 277),
(97, 3),
(97, 271),
(97, 272),
(98, 3),
(98, 271),
(98, 272),
(98, 273),
(98, 274),
(98, 275),
(98, 276),
(98, 277),
(99, 3),
(99, 271),
(99, 272),
(100, 3),
(100, 271),
(100, 272),
(101, 3),
(101, 271),
(101, 272),
(102, 3),
(102, 275),
(103, 3),
(103, 271),
(103, 272),
(104, 3),
(104, 271),
(104, 272),
(105, 3),
(105, 271),
(105, 272),
(106, 3),
(106, 271),
(106, 272),
(107, 3),
(107, 271),
(107, 272),
(108, 3),
(108, 271),
(108, 272),
(109, 3),
(109, 271),
(109, 272),
(110, 3),
(110, 271),
(110, 272),
(111, 3),
(111, 271),
(111, 272),
(112, 3),
(112, 271),
(112, 272),
(112, 273),
(112, 274),
(112, 275),
(112, 276),
(112, 277),
(113, 3),
(113, 271),
(113, 272),
(113, 273),
(113, 274),
(114, 271),
(114, 272),
(114, 273),
(114, 274),
(114, 275),
(114, 276),
(114, 277),
(115, 3),
(115, 271),
(115, 272),
(115, 273),
(115, 274),
(119, 3),
(119, 271),
(119, 272),
(119, 273),
(119, 274),
(119, 275),
(119, 276),
(119, 277),
(120, 3),
(121, 3),
(121, 271),
(121, 272),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 3),
(136, 3),
(137, 3),
(138, 3),
(139, 3),
(140, 3),
(141, 3),
(142, 3),
(143, 3),
(144, 3),
(145, 3),
(146, 3),
(147, 3),
(148, 3),
(149, 3),
(150, 3),
(151, 3),
(152, 3),
(153, 3),
(154, 3),
(155, 3),
(156, 3),
(157, 3),
(158, 3),
(159, 3),
(160, 3),
(161, 3),
(162, 3),
(163, 3),
(164, 3),
(165, 3),
(166, 3),
(167, 3),
(168, 3),
(169, 3),
(170, 3),
(171, 3),
(172, 3),
(173, 3),
(174, 3),
(175, 3),
(176, 3),
(177, 3),
(178, 3),
(179, 3),
(180, 3),
(181, 3),
(182, 3),
(183, 3),
(184, 3),
(185, 3),
(186, 3),
(187, 3),
(188, 3),
(189, 3),
(190, 3),
(191, 3),
(192, 3),
(193, 3),
(194, 3),
(195, 3),
(196, 3),
(197, 3),
(198, 3),
(199, 3),
(200, 3),
(201, 3),
(202, 3),
(203, 3),
(204, 3),
(205, 3),
(206, 3),
(207, 3),
(208, 3),
(209, 3),
(210, 3),
(211, 3),
(212, 3),
(213, 3),
(214, 3),
(215, 3),
(216, 3),
(217, 3),
(278, 271),
(278, 272),
(278, 273),
(278, 274),
(278, 275),
(278, 276),
(278, 277),
(279, 271),
(279, 272),
(279, 273),
(279, 274),
(430, 3),
(431, 3),
(453, 3),
(456, 271),
(456, 272),
(456, 273),
(456, 274),
(456, 275),
(456, 276),
(456, 277),
(457, 271),
(457, 272),
(457, 273),
(457, 274),
(457, 275),
(458, 271),
(458, 272),
(458, 273),
(458, 274),
(459, 271),
(459, 272),
(459, 273),
(459, 274),
(459, 275),
(460, 271),
(460, 272),
(585, 3),
(586, 3),
(587, 3),
(588, 3),
(594, 3),
(595, 3),
(994, 3),
(995, 3),
(996, 3),
(997, 3),
(998, 3),
(999, 3),
(1000, 3),
(1001, 3),
(1002, 3),
(1003, 3),
(1004, 3),
(1005, 3),
(1012, 1),
(1013, 3),
(1014, 3),
(1015, 3),
(1016, 3),
(1017, 3),
(1018, 3),
(1019, 3),
(1020, 3),
(1021, 3),
(1022, 3),
(1023, 3),
(1024, 3),
(1025, 3),
(1026, 3),
(1027, 3),
(1028, 3),
(1029, 3),
(1030, 3),
(1031, 3),
(1032, 3),
(1033, 3),
(1034, 3),
(1035, 3),
(1036, 3),
(1037, 3),
(1038, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting`
--

CREATE TABLE `core_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `pack` varchar(255) NOT NULL default 'main',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting`
--

INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('accessibility', 'off', 'enum', 255, 'main', 0, 16, 1, 0, ''),
('common_admin_session', 'on', 'enum', 3, 'main', 4, 1, 1, 0, ''),
('company_idref_code', 'code', 'string', 255, 'main', 0, 0, 1, 1, ''),
('core_version', '3.6.0.3', 'string', 255, 'main', 0, 3, 1, 1, ''),
('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, ''),
('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, ''),
('default_pubflow_method', 'advanced', 'pubflow_method_chooser', 8, 'main', 0, 13, 1, 0, ''),
('do_debug', 'off', 'enum', 3, 'main', 0, 17, 1, 0, ''),
('field_tree', '0', 'field_tree', 255, 'log_option', 0, 11, 1, 0, ''),
('google_stat_code', '', 'textarea', 65535, 'main', 8, 1, 1, 0, ''),
('google_stat_in_cms', '0', 'check', 1, 'main', 8, 2, 1, 0, ''),
('google_stat_in_lms', '0', 'check', 1, 'main', 8, 3, 1, 0, ''),
('hour_request_limit', '24', 'int', 2, 'log_option', 0, 8, 0, 0, ''),
('hteditor', 'tinymce', 'hteditor', 255, 'main', 0, 7, 1, 0, ''),
('htmledit_image_admin', '1', 'check', 255, 'main', 0, 9, 1, 0, ''),
('htmledit_image_godadmin', '1', 'check', 255, 'main', 0, 8, 1, 0, ''),
('htmledit_image_user', '1', 'check', 255, 'main', 0, 10, 1, 0, ''),
('lang_edit', 'off', 'enum', 3, 'main', 0, 14, 1, 0, ''),
('lastfirst_mandatory', 'off', 'enum', 3, 'log_option', 0, 2, 2, 0, ''),
('layout', 'over', 'layout_chooser', 255, 'main', 0, 6, 1, 1, ''),
('ldap_port', '389', 'string', 5, 'main', 5, 3, 1, 0, ''),
('ldap_server', '192.168.0.1', 'string', 255, 'main', 5, 2, 1, 0, ''),
('ldap_used', 'off', 'enum', 3, 'main', 5, 1, 1, 0, ''),
('ldap_user_string', '$user@domain2.domain1', 'string', 255, 'main', 5, 4, 1, 0, ''),
('mail_sender', 'sample@localhost.com', 'string', 255, 'log_option', 0, 0, 0, 0, ''),
('max_log_attempt', '0', 'int', 3, 'log_option', 0, 2, 0, 0, ''),
('nl_sendpause', '20', 'int', 3, 'main', 2, 1, 1, 0, ''),
('nl_sendpercycle', '200', 'int', 4, 'main', 2, 0, 1, 0, ''),
('pass_alfanumeric', 'off', 'enum', 3, 'log_option', 0, 6, 0, 0, ''),
('pass_change_first_login', 'off', 'enum', 3, 'log_option', 0, 13, 1, 0, ''),
('pass_max_time_valid', '0', 'int', 4, 'log_option', 0, 7, 1, 0, ''),
('pass_min_char', '5', 'int', 2, 'log_option', 0, 5, 0, 0, ''),
('pathfield', 'field/', 'string', 255, 'main', 1, 2, 1, 0, ''),
('pathphoto', 'photo/', 'string', 255, 'main', 1, 2, 1, 0, ''),
('phantom', '', 'security_check', 255, 'main', 4, 3, 0, 0, ''),
('privacy_policy', 'on', 'enum', 3, 'log_option', 0, 9, 0, 0, ''),
('register_in_company', 'off', 'enum', 3, 'log_option', 0, 12, 1, 0, ''),
('register_tree', 'off', 'register_tree', 255, 'log_option', 0, 10, 1, 0, ''),
('register_type', 'self', 'register_type', 10, 'log_option', 0, 1, 0, 0, ''),
('save_log_attempt', 'after_max', 'save_log_attempt', 255, 'log_option', 0, 4, 0, 0, ''),
('sender_event', 'sample@localhost.com', 'string', 255, 'main', 0, 4, 1, 0, ''),
('session_ip_control', 'on', 'enum', 3, 'main', 4, 1, 1, 0, ''),
('session_ip_filter', '', 'textarea', 65535, 'main', 4, 2, 1, 0, ''),
('sms_cell_num_field', '1', 'field_select', 5, 'main', 3, 6, 1, 0, ''),
('sms_credit', '0', 'string', 20, 'main', 3, 10, 1, 1, ''),
('sms_gateway', 'smsmarket', 'string', 50, 'main', 3, 3, 1, 1, ''),
('sms_gateway_host', '193.254.241.47', 'string', 15, 'main', 3, 8, 1, 0, ''),
('sms_gateway_id', '3', 'sel_sms_gateway', 1, 'main', 3, 7, 1, 0, ''),
('sms_gateway_pass', '', 'string', 255, 'main', 3, 5, 1, 0, ''),
('sms_gateway_port', '26', 'int', 5, 'main', 3, 9, 1, 0, ''),
('sms_gateway_user', '', 'string', 50, 'main', 3, 4, 1, 0, ''),
('sms_international_prefix', '+39', 'string', 3, 'main', 3, 1, 1, 0, ''),
('sms_sent_from', '0', 'string', 25, 'main', 3, 2, 1, 0, ''),
('sserver_host', '', 'string', 255, 'main', 7, 1, 1, 0, ''),
('sserver_user', '', 'string', 255, 'main', 7, 2, 1, 0, ''),
('templ_use_field', '0', 'id_field', 11, 'main', 0, 0, 1, 1, ''),
('title_organigram_chart', 'root', 'string', 255, 'main', 0, 5, 1, 1, ''),
('ttlSession', '4000', 'int', 5, 'main', 0, 4, 1, 0, ''),
('url', 'http://127.0.0.1/doceboCore/', 'string', 255, 'main', 0, 1, 1, 0, ''),
('user_quota', '50', 'string', 255, 'main', 0, 15, 1, 0, ''),
('use_accesskey', 'off', 'enum', 3, 'main', 0, 15, 1, 0, ''),
('use_admin', '1', 'menuvoice', 1, 'main', 6, 5, 1, 0, '/admin_manager/view'),
('use_advanced_form', 'off', 'enum', 3, 'log_option', 0, 12, 1, 0, ''),
('use_groups', '1', 'menuvoice', 1, 'main', 6, 2, 1, 0, '/directory/view_group'),
('use_org_chart', '1', 'check', 1, 'main', 6, 1, 1, 0, ''),
('use_org_chart_field', '0', 'check', 1, 'main', 6, 4, 1, 0, ''),
('use_org_chart_multiple_choice', '1', 'check', 1, 'main', 6, 6, 1, 0, ''),
('use_tag', 'off', 'enum', 3, 'main', 0, 18, 1, 0, ''),
('use_user_fields', '1', 'check', 1, 'main', 6, 3, 1, 0, ''),
('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, ''),
('visuUser', '20', 'int', 5, 'main', 0, 12, 1, 0, ''),
('welcome_use_feed', 'off', 'enum', 3, 'main', 0, 16, 1, 1, ''),
('register_deleted_user', 'off', 'enum', 3, 'log_option', 0, 14, 1, 0, ''),
('profile_only_pwd', 'on', 'enum', 3, 'log_option', 0, 15, 1, 0, ''),
('register_with_code', 'off', 'enum', 3, 'log_option', 0, 16, 1, 0, ''),
('rest_auth_code', '', 'string', 255, 'main', 10, 2, 1, 0, ''),
('rest_auth_lifetime', '60', 'int', 3, 'main', 10, 3, 1, 0, ''),
('rest_auth_method', '1', 'rest_auth_sel_method', 3, 'main', 10, 1, 1, 0, ''),
('rest_auth_update', 'off', 'enum', 3, 'main', 10, 4, 1, 0, ''),
('use_rest_api', 'off', 'enum', 3, 'main', 10, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting_list`
--

CREATE TABLE `core_setting_list` (
  `path_name` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `default_value` text NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `visible` tinyint(1) NOT NULL default '0',
  `load_at_startup` tinyint(1) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`path_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting_list`
--

INSERT INTO `core_setting_list` (`path_name`, `label`, `default_value`, `type`, `visible`, `load_at_startup`, `sequence`) VALUES
('admin_rules.direct_course_subscribe', '_DIRECT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 6),
('admin_rules.direct_user_insert', '_DIRECT_USER_INSERT', 'off', 'enum', 1, 1, 3),
('admin_rules.limit_course_subscribe', '_LIMIT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 4),
('admin_rules.limit_user_insert', '_LIMIT_USER_INSERT', 'off', 'enum', 1, 1, 1),
('admin_rules.max_course_subscribe', '_MAX_COURSE_SUBSCRIBE', '0', 'integer', 1, 1, 5),
('admin_rules.max_user_insert', '_MAX_USER_INSERT', '0', 'integer', 1, 1, 2),
('admin_rules.user_lang_assigned', '', '', 'string', 0, 1, 0),
('ui.directory.custom_columns', '_CUSTOM_COLUMS', '', 'hidden', 0, 1, 0),
('ui.language', '_LANGUAGE', '', 'language', 1, 1, 0),
('ui.template', '_TEMPLATE', '', 'template', 1, 1, 0),
('user_rules.field_policy', '', '', 'serialized', 0, 1, 0),
('user_rules.user_quota', '', '-1', 'int', 0, 1, 0),
('user_rules.user_quota_used', '', '0', 'int', 0, 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_setting_user`
--

CREATE TABLE `core_setting_user` (
  `path_name` varchar(255) NOT NULL default '',
  `id_user` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`path_name`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_setting_user`
--

INSERT INTO `core_setting_user` (`path_name`, `id_user`, `value`) VALUES
('ui.language', 12, 'italian');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_st`
--

CREATE TABLE `core_st` (
  `idst` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`idst`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Security Tokens';

--
-- Dump dei dati per la tabella `core_st`
--

INSERT INTO `core_st` (`idst`) VALUES
(1010),
(1011),
(1012),
(1013),
(1014),
(1015),
(1016),
(1017),
(1018),
(1019),
(1020),
(1021),
(1022),
(1023),
(1024),
(1025),
(1026),
(1027),
(1028),
(1029),
(1030),
(1031),
(1032),
(1033),
(1034),
(1035),
(1036),
(1037),
(1038);

-- --------------------------------------------------------

--
-- Struttura della tabella `core_sysforum`
--

CREATE TABLE `core_sysforum` (
  `idMessage` int(11) NOT NULL auto_increment,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) NOT NULL default '0',
  `key3` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag`
--

CREATE TABLE `core_tag` (
  `id_tag` int(11) NOT NULL auto_increment,
  `tag_name` varchar(255) NOT NULL,
  `id_parent` int(11) NOT NULL,
  PRIMARY KEY  (`id_tag`),
  KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag_relation`
--

CREATE TABLE `core_tag_relation` (
  `id_tag` int(11) NOT NULL,
  `id_resource` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `id_course` int(11) NOT NULL,
  PRIMARY KEY  (`id_tag`,`id_resource`,`resource_type`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag_relation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_tag_resource`
--

CREATE TABLE `core_tag_resource` (
  `id_resource` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sample_text` text NOT NULL,
  `permalink` text NOT NULL,
  PRIMARY KEY  (`id_resource`,`resource_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_tag_resource`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_task`
--

CREATE TABLE `core_task` (
  `name` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `conn_source` varchar(50) NOT NULL default '',
  `conn_destination` varchar(50) NOT NULL default '',
  `schedule_type` enum('at','any') NOT NULL default 'at',
  `schedule` varchar(50) NOT NULL default '',
  `import_type` varchar(50) NOT NULL default '',
  `map` text NOT NULL,
  `last_execution` datetime default NULL,
  `sequence` int(3) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_task`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user`
--

CREATE TABLE `core_user` (
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `pass` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `photo` varchar(255) NOT NULL default '',
  `avatar` varchar(255) NOT NULL default '',
  `signature` text NOT NULL,
  `level` int(11) NOT NULL default '0',
  `lastenter` datetime default NULL,
  `valid` tinyint(1) NOT NULL default '1',
  `pwd_expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `register_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user`
--

INSERT INTO `core_user` (`idst`, `userid`, `firstname`, `lastname`, `pass`, `email`, `photo`, `avatar`, `signature`, `level`, `lastenter`, `valid`, `pwd_expire_at`, `register_date`) VALUES
(11, '/Anonymous', '', '', '', '', '', '', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_file`
--

CREATE TABLE `core_user_file` (
  `id` int(11) NOT NULL auto_increment,
  `user_idst` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `media_url` varchar(255) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `uldate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_friend`
--

CREATE TABLE `core_user_friend` (
  `id_user` int(11) NOT NULL default '0',
  `id_friend` int(11) NOT NULL default '0',
  `waiting` int(1) NOT NULL default '0',
  `request_msg` text NOT NULL,
  PRIMARY KEY  (`id_user`,`id_friend`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_friend`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_log_attempt`
--

CREATE TABLE `core_user_log_attempt` (
  `id` int(11) NOT NULL auto_increment,
  `userid` varchar(255) NOT NULL default '',
  `attempt_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `attempt_number` int(5) NOT NULL default '0',
  `user_ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_log_attempt`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_myfiles`
--

CREATE TABLE `core_user_myfiles` (
  `id_file` int(11) NOT NULL auto_increment,
  `area` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `file_name` varchar(255) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  `file_policy` int(1) NOT NULL default '0',
  `size` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_myfiles`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_profileview`
--

CREATE TABLE `core_user_profileview` (
  `id_owner` int(11) NOT NULL default '0',
  `id_viewer` int(11) NOT NULL default '0',
  `date_view` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_owner`,`id_viewer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_profileview`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_user_temp`
--

CREATE TABLE `core_user_temp` (
  `idst` int(11) NOT NULL default '0',
  `userid` varchar(255) NOT NULL default '',
  `firstname` varchar(100) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `pass` varchar(50) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `language` varchar(50) NOT NULL default '',
  `request_on` datetime default '0000-00-00 00:00:00',
  `random_code` varchar(255) NOT NULL default '',
  `create_by_admin` int(11) NOT NULL default '0',
  `confirmed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idst`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_user_temp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki`
--

CREATE TABLE `core_wiki` (
  `wiki_id` int(11) NOT NULL auto_increment,
  `source_platform` varchar(255) NOT NULL default '',
  `public` tinyint(1) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `other_lang` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `page_count` int(11) NOT NULL default '0',
  `revision_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wiki_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_page`
--

CREATE TABLE `core_wiki_page` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_code` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `page_path` varchar(255) NOT NULL default '',
  `lev` int(3) NOT NULL default '0',
  `wiki_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1;

--
-- Dump dei dati per la tabella `core_wiki_page`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_page_info`
--

CREATE TABLE `core_wiki_page_info` (
  `page_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `wiki_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`page_id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki_page_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `core_wiki_revision`
--

CREATE TABLE `core_wiki_revision` (
  `wiki_id` int(11) NOT NULL default '0',
  `page_id` int(11) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '0',
  `author` int(11) NOT NULL default '0',
  `rev_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `content` longtext NOT NULL,
  PRIMARY KEY  (`wiki_id`,`page_id`,`version`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `core_wiki_revision`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_contact_history`
--

CREATE TABLE `crm_contact_history` (
  `contact_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `type` enum('form','phone','email','meeting') NOT NULL default 'form',
  `reason_id` int(11) NOT NULL default '0',
  `meeting_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` text NOT NULL,
  PRIMARY KEY  (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_history`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_contact_reason`
--

CREATE TABLE `crm_contact_reason` (
  `reason_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_reason`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_contact_user`
--

CREATE TABLE `crm_contact_user` (
  `contact_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`contact_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_contact_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_file`
--

CREATE TABLE `crm_file` (
  `file_id` int(11) NOT NULL auto_increment,
  `type` varchar(20) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_menu`
--

CREATE TABLE `crm_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_menu`
--

INSERT INTO `crm_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_GENERAL_CRM', 'crm.gif', 1, 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_menu_under`
--

CREATE TABLE `crm_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_menu_under`
--

INSERT INTO `crm_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'companytype', '_COMPANYTYPE', 'main', 'view', NULL, 1, 'class.companytype.php', 'Module_Companytype'),
(2, 1, 'companystatus', '_COMPANYSTATUS', 'main', 'view', NULL, 2, 'class.companystatus.php', 'Module_Companystatus'),
(3, 1, 'ticketstatus', '_TICKETSTATUS', 'main', 'view', NULL, 3, 'class.ticketstatus.php', 'Module_Ticketstatus'),
(4, 1, 'contactreason', '_CONTACT_REASON', 'main', 'view', NULL, 4, 'class.contactreason.php', 'Module_Contactreason');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_note`
--

CREATE TABLE `crm_note` (
  `note_id` int(11) NOT NULL auto_increment,
  `type` varchar(20) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note_author` int(11) NOT NULL default '0',
  `note_txt` text NOT NULL,
  PRIMARY KEY  (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1;

--
-- Dump dei dati per la tabella `crm_note`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_project`
--

CREATE TABLE `crm_project` (
  `prj_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `cost` varchar(30) NOT NULL default '',
  `gain` varchar(30) NOT NULL default '',
  `priority` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `progress` tinyint(3) NOT NULL default '0',
  `sign_date` date NOT NULL default '0000-00-00',
  `expire` date NOT NULL default '0000-00-00',
  `deadline` date NOT NULL default '0000-00-00',
  `ticket` int(6) NOT NULL default '0',
  PRIMARY KEY  (`prj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_project`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_publicmenu_under`
--

CREATE TABLE `crm_publicmenu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_publicmenu_under`
--

INSERT INTO `crm_publicmenu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'company', '_COMPANY', 'main', 'view', 1, 'class.company.php', 'Module_Company'),
(2, 1, 'abook', '_ADDRESS_BOOK', 'main', 'view', 2, 'class.abook.php', 'Module_Abook'),
(3, 1, 'task', '_TASKS', 'main', 'view', 3, 'class.task.php', 'Module_Task'),
(4, 1, 'todo', '_TODO', 'main', 'view', 4, 'class.todo.php', 'Module_Todo'),
(5, 1, 'ticket', '_TICKETS', 'main', 'view', 5, 'class.ticket.php', 'Module_Ticket'),
(6, 1, 'contacthistory', '_CONTACT_HISTORY', 'main', 'view', 6, 'class.contacthistory.php', 'Module_Contacthistory'),
(7, 1, 'storedform', '_FORM_CONTACT', 'main', 'view', 7, 'class.storedform.php', 'Module_Storedform');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_public_menu`
--

CREATE TABLE `crm_public_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_public_menu`
--

INSERT INTO `crm_public_menu` (`idMenu`, `name`, `image`, `sequence`) VALUES
(1, '_GENERAL_CRM', 'crm.gif', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_setting`
--

CREATE TABLE `crm_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `pack` varchar(255) NOT NULL default 'main',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_setting`
--

INSERT INTO `crm_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('crm_version', '3.0', 'string', 255, 'main', 0, 3, 1, 1, ''),
('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, ''),
('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, ''),
('ttlSession', '2000', 'int', 5, 'main', 0, 4, 1, 0, ''),
('url', 'http://localhost/docebo_35/doceboCrm/', 'string', 255, 'main', 0, 1, 1, 1, ''),
('use_simplified', 'on', 'enum', 3, 'main', 0, 1, 1, 0, ''),
('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `crm_sysforum`
--

CREATE TABLE `crm_sysforum` (
  `idMessage` int(11) NOT NULL auto_increment,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) NOT NULL default '0',
  `key3` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_task`
--

CREATE TABLE `crm_task` (
  `task_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `prj_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `expire` date NOT NULL default '0000-00-00',
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `progress` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_task`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket`
--

CREATE TABLE `crm_ticket` (
  `ticket_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `prj_id` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket_msg`
--

CREATE TABLE `crm_ticket_msg` (
  `message_id` int(11) NOT NULL auto_increment,
  `ticket_id` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `text_msg` text NOT NULL,
  `from_staff` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket_msg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_ticket_status`
--

CREATE TABLE `crm_ticket_status` (
  `status_id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL default '',
  `is_used` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_ticket_status`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `crm_todo`
--

CREATE TABLE `crm_todo` (
  `todo_id` int(11) NOT NULL auto_increment,
  `company_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `priority` tinyint(1) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `complete` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`todo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `crm_todo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_menu`
--

CREATE TABLE `ecom_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_menu`
--

INSERT INTO `ecom_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_ECOMMERCE_MANAGMENT', '', 1, 'false'),
(2, '', '', 2, 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_menu_under`
--

CREATE TABLE `ecom_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_menu_under`
--

INSERT INTO `ecom_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'payaccount', '_PAYACCOUNT', 'payaccount', 'view', NULL, 1, 'class.payaccount.php', 'EcomAdmin_PayAccount'),
(2, 1, 'taxzone', '_TAXZONE', 'taxzone', 'view', NULL, 2, 'class.taxzone.php', 'EcomAdmin_TaxZone'),
(3, 1, 'taxcountry', '_TAXCOUNTRY', 'taxcountry', 'view', NULL, 3, 'class.taxcountry.php', 'EcomAdmin_TaxCountry'),
(4, 1, 'taxcatgod', '_TAXCATGOD', 'taxcatgod', 'view-hidden', NULL, 4, 'class.taxcatgod.php', 'EcomAdmin_TaxCatGod'),
(5, 1, 'taxrate', '_TAXRATE', 'taxrate', 'view', NULL, 5, 'class.taxrate.php', 'EcomAdmin_TaxRate'),
(6, 2, 'transaction', '_TRANSACTION', 'transaction', 'view', NULL, 1, 'class.transaction.php', 'EcomAdmin_Transaction');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_paramset_fieldgrp`
--

CREATE TABLE `ecom_paramset_fieldgrp` (
  `fieldgrp_id` int(11) NOT NULL auto_increment,
  `set_id` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `is_main` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fieldgrp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_paramset_fieldgrp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_paramset_grpitem`
--

CREATE TABLE `ecom_paramset_grpitem` (
  `item_id` int(11) NOT NULL auto_increment,
  `fieldgrp_id` int(11) NOT NULL default '0',
  `set_id` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `compulsory` tinyint(1) NOT NULL default '0',
  `ord` int(3) NOT NULL default '0',
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_paramset_grpitem`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_payaccount`
--

CREATE TABLE `ecom_payaccount` (
  `account_name` varchar(100) NOT NULL default '',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `active` enum('true','false') NOT NULL default 'true',
  PRIMARY KEY  (`account_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_payaccount`
--

INSERT INTO `ecom_payaccount` (`account_name`, `class_file`, `class_name`, `active`) VALUES
('check', 'class.check.php', 'PayAccount_Check', 'false'),
('mark', 'class.mark.php', 'PayAccount_Mark', 'false'),
('money_order', 'class.money_order.php', 'PayAccount_MoneyOrder', 'false'),
('paypal', 'class.paypal.php', 'PayAccount_PayPal', 'false'),
('wire_transfer', 'class.wire_transfer.php', 'PayAccount_WireTransfer', 'true');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_payaccount_setting`
--

CREATE TABLE `ecom_payaccount_setting` (
  `account_name` varchar(100) NOT NULL default '',
  `param_name` varchar(100) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `pack` varchar(255) NOT NULL default 'main',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`,`account_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_payaccount_setting`
--

INSERT INTO `ecom_payaccount_setting` (`account_name`, `param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('wire_transfer', 'abi', '3200', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'address', 'via boccaccio 1', 'string', 255, 'main', 0, 0, 1, 0, ''),
('paypal', 'back_ko', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ko_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ok', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('paypal', 'back_ok_buyer', '', 'string', 255, 'main', 0, 0, 1, 1, ''),
('wire_transfer', 'bank_account', '44047438', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'cab', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'cin', 'ww', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'city', 'acquaviva delle fonti', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'company', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('paypal', 'email', 'info@smsmarket.it', 'string', 255, 'main', 0, 0, 1, 0, ''),
('wire_transfer', 'iban', 'boh', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'name', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('check', 'registered_person', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'surname', '', 'string', 255, 'main', 0, 0, 1, 0, ''),
('money_order', 'zip_code', '70021', 'string', 255, 'main', 0, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product`
--

CREATE TABLE `ecom_product` (
  `prd_id` int(11) NOT NULL auto_increment,
  `prd_code` varchar(255) NOT NULL default '',
  `price` varchar(20) NOT NULL default '',
  `param_set_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `can_add_to_cart` tinyint(1) NOT NULL default '0',
  `ord` int(11) default NULL,
  PRIMARY KEY  (`prd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat`
--

CREATE TABLE `ecom_product_cat` (
  `cat_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(3) NOT NULL default '0',
  `param_set_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat_info`
--

CREATE TABLE `ecom_product_cat_info` (
  `cat_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cat_id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_cat_item`
--

CREATE TABLE `ecom_product_cat_item` (
  `cat_id` int(11) NOT NULL default '0',
  `prd_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`,`prd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_cat_item`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_field`
--

CREATE TABLE `ecom_product_field` (
  `idField` int(11) NOT NULL auto_increment,
  `id_common` int(11) NOT NULL default '0',
  `type_field` varchar(255) NOT NULL default '',
  `lang_code` varchar(255) NOT NULL default '',
  `translation` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `show_on_platform` varchar(255) NOT NULL default 'framework,',
  `use_multilang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idField`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_field`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_field_entry`
--

CREATE TABLE `ecom_product_field_entry` (
  `id_common` varchar(11) NOT NULL default '',
  `id_common_son` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `user_entry` text NOT NULL,
  PRIMARY KEY  (`id_common`,`id_common_son`,`id_user`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_field_entry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_img`
--

CREATE TABLE `ecom_product_img` (
  `img_id` int(11) NOT NULL auto_increment,
  `prd_id` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`img_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_img`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_product_info`
--

CREATE TABLE `ecom_product_info` (
  `prd_id` int(11) NOT NULL default '0',
  `language` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`prd_id`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_product_info`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_reservation`
--

CREATE TABLE `ecom_reservation` (
  `reservation_id` int(11) NOT NULL auto_increment,
  `product_code` varchar(255) NOT NULL default '',
  `company_id` int(11) NOT NULL default '0',
  `user_id` int(255) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `type` enum('course','course_edition','other') NOT NULL default 'course',
  `price` varchar(255) NOT NULL default '',
  `reservation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`reservation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_reservation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_setting`
--

CREATE TABLE `ecom_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_setting`
--

INSERT INTO `ecom_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('admin_mail', 'sample@localhost.com', 'string', 255, 0, 2, 1, 0, ''),
('ecom_type', 'standard', 'ecommerce_type', 30, 0, 4, 1, 1, ''),
('ttlSession', '1000', 'int', 5, 0, 3, 1, 0, ''),
('url', 'http://localhost/docebo_35/doceboEcom/', 'string', 255, 0, 1, 1, 1, ''),
('company_details', '', 'textarea', 65535, 0, 5, 1, 0, ''),
('send_order_email', '', 'string', 255, 0, 6, 1, 0, ''),
('currency_label', '&euro;', 'string', 255, 0, 7, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_cat_god`
--

CREATE TABLE `ecom_tax_cat_god` (
  `id_cat_god` int(11) NOT NULL auto_increment,
  `name_cat_god` varchar(255) NOT NULL default '',
  `cat_code` enum('course') default NULL,
  PRIMARY KEY  (`id_cat_god`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_cat_god`
--

INSERT INTO `ecom_tax_cat_god` (`id_cat_god`, `name_cat_god`, `cat_code`) VALUES
(1, 'Online courses', 'course');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_rate`
--

CREATE TABLE `ecom_tax_rate` (
  `id_zone` int(11) NOT NULL default '0',
  `id_cat_god` int(11) NOT NULL default '0',
  `rate` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id_zone`,`id_cat_god`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_rate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_tax_zone`
--

CREATE TABLE `ecom_tax_zone` (
  `id_zone` int(11) NOT NULL auto_increment,
  `name_zone` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_tax_zone`
--

INSERT INTO `ecom_tax_zone` (`id_zone`, `name_zone`) VALUES
(1, '_EUROPE'),
(2, '_USA'),
(3, '_REST_OF_THE_WORLD'),
(4, '_TAX_FREE');

-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_transaction`
--

CREATE TABLE `ecom_transaction` (
  `id_trans` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `total_amount` float NOT NULL default '0',
  `transaction_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `order_status` enum('NOTPROC','PROC','PARTPROC','CANC') NOT NULL default 'NOTPROC',
  `payment_status` enum('NOTPAY','PAYED','PARTPAY','CANC') NOT NULL default 'NOTPAY',
  `order_notes` text NOT NULL,
  `payment_notes` text NOT NULL,
  `payment_type` varchar(255) NOT NULL default '0',
  `active_status` enum('none','partial','all') NOT NULL default 'none',
  PRIMARY KEY  (`id_trans`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_transaction`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `ecom_transaction_product`
--

CREATE TABLE `ecom_transaction_product` (
  `product_id` int(11) NOT NULL auto_increment,
  `id_trans` int(11) NOT NULL default '0',
  `id_prod` varchar(255) NOT NULL default '',
  `id_user` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `type` enum('course','course_edition','other') NOT NULL default 'course',
  `price` varchar(255) NOT NULL default '',
  `quantity` int(11) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`product_id`),
  UNIQUE KEY `id_trans` (`id_trans`,`id_prod`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ecom_transaction_product`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_advice`
--

CREATE TABLE `learning_advice` (
  `idAdvice` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `important` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idAdvice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_advice`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_adviceuser`
--

CREATE TABLE `learning_adviceuser` (
  `idAdvice` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `archivied` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idAdvice`,`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_adviceuser`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_assessment_rules`
--

CREATE TABLE `learning_assessment_rules` (
  `id_rule` int(11) NOT NULL auto_increment,
  `id_assessment` int(11) NOT NULL default '0',
  `rule_type` varchar(255) NOT NULL default '',
  `rule_setting` varchar(255) NOT NULL default '',
  `rule_effect` text NOT NULL,
  `rule_casualities` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_rule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_assessment_rules`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_assessment_user`
--

CREATE TABLE `learning_assessment_user` (
  `id_assessment` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `type_of` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_assessment`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_assessment_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_calendar`
--

CREATE TABLE `learning_calendar` (
  `id` bigint(20) NOT NULL default '0',
  `idCourse` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue`
--

CREATE TABLE `learning_catalogue` (
  `idCatalogue` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idCatalogue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue_entry`
--

CREATE TABLE `learning_catalogue_entry` (
  `idCatalogue` int(11) NOT NULL default '0',
  `idEntry` int(11) NOT NULL default '0',
  `type_of_entry` enum('course','coursepath') NOT NULL default 'course',
  PRIMARY KEY  (`idCatalogue`,`idEntry`,`type_of_entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue_entry`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_catalogue_member`
--

CREATE TABLE `learning_catalogue_member` (
  `idCatalogue` int(11) NOT NULL default '0',
  `idst_member` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCatalogue`,`idst_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_catalogue_member`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_category`
--

CREATE TABLE `learning_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `lev` int(11) NOT NULL default '0',
  `path` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate`
--

CREATE TABLE `learning_certificate` (
  `id_certificate` int(11) NOT NULL auto_increment,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `base_language` varchar(255) NOT NULL default '',
  `cert_structure` text NOT NULL,
  `orientation` enum('P','L') NOT NULL default 'P',
  `bgimage` varchar(255) NOT NULL,
  `meta` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_certificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_assign`
--

CREATE TABLE `learning_certificate_assign` (
  `id_certificate` int(11) NOT NULL default '0',
  `id_course` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `cert_file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_certificate`,`id_course`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_assign`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_course`
--

CREATE TABLE `learning_certificate_course` (
  `id_certificate` int(11) NOT NULL default '0',
  `id_course` int(11) NOT NULL default '0',
  `available_for_status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_certificate`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta`
--

CREATE TABLE `learning_certificate_meta` (
  `idMetaCertificate` int(11) NOT NULL auto_increment,
  `idCertificate` int(11) NOT NULL default '0',
  `title` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` longtext character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`idMetaCertificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta_assign`
--

CREATE TABLE `learning_certificate_meta_assign` (
  `idUser` int(11) NOT NULL default '0',
  `idMetaCertificate` int(11) NOT NULL default '0',
  `idCertificate` int(11) NOT NULL default '0',
  `on_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `cert_file` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`idUser`,`idMetaCertificate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta_assign`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_meta_course`
--

CREATE TABLE `learning_certificate_meta_course` (
  `id` int(11) NOT NULL auto_increment,
  `idMetaCertificate` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `idCourseEdition` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_meta_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_certificate_tags`
--

CREATE TABLE `learning_certificate_tags` (
  `file_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`file_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_certificate_tags`
--

INSERT INTO `learning_certificate_tags` (`file_name`, `class_name`) VALUES
('certificate.course.php', 'CertificateSubs_Course'),
('certificate.user.php', 'CertificateSubs_User'),
('certificate.userstat.php', 'CertificateSubs_UserStat'),
('certificate.misc.php', 'CertificateSubs_Misc');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_classroom`
--

CREATE TABLE `learning_classroom` (
  `idClassroom` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `location_id` int(11) NOT NULL default '0',
  `room` varchar(255) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `zip_code` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `fax` varchar(255) NOT NULL default '',
  `capacity` varchar(255) NOT NULL default '',
  `disposition` text NOT NULL,
  `instrument` text NOT NULL,
  `available_instrument` text NOT NULL,
  `note` text NOT NULL,
  `responsable` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idClassroom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_classroom`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_classroom_calendar`
--

CREATE TABLE `learning_classroom_calendar` (
  `id` int(11) NOT NULL auto_increment,
  `classroom_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `owner` int(11) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_classroom_calendar`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_class_location`
--

CREATE TABLE `learning_class_location` (
  `location_id` int(11) NOT NULL auto_increment,
  `location` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_class_location`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_comment_ajax`
--

CREATE TABLE `learning_comment_ajax` (
  `id_comment` int(11) NOT NULL auto_increment,
  `resource_type` varchar(50) NOT NULL default '',
  `external_key` varchar(200) NOT NULL default '',
  `id_author` int(11) NOT NULL default '0',
  `posted_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `textof` text NOT NULL,
  `history_tree` varchar(255) NOT NULL default '',
  `id_parent` int(11) NOT NULL default '0',
  `moderated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_comment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_comment_ajax`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_commontrack`
--

CREATE TABLE `learning_commontrack` (
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idTrack` int(11) NOT NULL default '0',
  `objectType` varchar(20) NOT NULL default '',
  `firstAttempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateAttempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`idTrack`,`objectType`),
  KEY `idReference` (`idReference`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_commontrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence`
--

CREATE TABLE `learning_competence` (
  `id_competence` int(10) unsigned NOT NULL auto_increment,
  `id_category` int(10) unsigned NOT NULL default '0',
  `type` enum('score','flag') NOT NULL default 'score',
  `score` float NOT NULL default '0',
  `competence_type` enum('skill','attitude','_unknown') NOT NULL default 'skill',
  `score_min` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_category`
--

CREATE TABLE `learning_competence_category` (
  `id_competence_category` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id_competence_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_category_text`
--

CREATE TABLE `learning_competence_category_text` (
  `id_category` int(10) unsigned NOT NULL default '0',
  `id_text` int(10) unsigned NOT NULL auto_increment,
  `lang_code` varchar(255) NOT NULL,
  `text_name` varchar(255) NOT NULL,
  `text_desc` text,
  PRIMARY KEY  (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_category_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_course`
--

CREATE TABLE `learning_competence_course` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_course` int(10) unsigned NOT NULL default '0',
  `score` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`,`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_required`
--

CREATE TABLE `learning_competence_required` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `idst` int(10) unsigned NOT NULL default '0',
  `type_of` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_competence`,`idst`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_required`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_text`
--

CREATE TABLE `learning_competence_text` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_text` int(10) unsigned NOT NULL auto_increment,
  `lang_code` varchar(255) NOT NULL,
  `text_name` varchar(255) NOT NULL,
  `text_desc` text,
  PRIMARY KEY  (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_text`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_track`
--

CREATE TABLE `learning_competence_track` (
  `id_track` int(10) unsigned NOT NULL auto_increment,
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_user` int(10) unsigned NOT NULL default '0',
  `source` varchar(255) NOT NULL default '',
  `date_assignment` datetime NOT NULL default '0000-00-00 00:00:00',
  `score` float NOT NULL default '0',
  PRIMARY KEY  (`id_track`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_competence_user`
--

CREATE TABLE `learning_competence_user` (
  `id_competence` int(10) unsigned NOT NULL default '0',
  `id_user` int(10) unsigned NOT NULL default '0',
  `score_init` float NOT NULL default '0',
  `score_got` float NOT NULL default '0',
  PRIMARY KEY  (`id_competence`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_competence_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course`
--

CREATE TABLE `learning_course` (
  `idCourse` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `code` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `lang_code` varchar(100) NOT NULL default '',
  `status` int(1) NOT NULL default '0',
  `level_show_user` int(11) NOT NULL default '0',
  `subscribe_method` tinyint(1) NOT NULL default '0',
  `linkSponsor` varchar(255) NOT NULL default '',
  `imgSponsor` varchar(255) NOT NULL default '',
  `img_course` varchar(255) NOT NULL default '',
  `img_material` varchar(255) NOT NULL default '',
  `img_othermaterial` varchar(255) NOT NULL default '',
  `course_demo` varchar(255) NOT NULL default '',
  `mediumTime` int(10) unsigned NOT NULL default '0',
  `permCloseLO` tinyint(1) NOT NULL default '0',
  `userStatusOp` int(11) NOT NULL default '0',
  `difficult` enum('veryeasy','easy','medium','difficult','verydifficult') NOT NULL default 'medium',
  `show_progress` tinyint(1) NOT NULL default '1',
  `show_time` tinyint(1) NOT NULL default '0',
  `show_extra_info` tinyint(1) NOT NULL default '0',
  `show_rules` tinyint(1) NOT NULL default '0',
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `hour_begin` varchar(5) NOT NULL default '',
  `hour_end` varchar(5) NOT NULL default '',
  `valid_time` int(10) NOT NULL default '0',
  `max_num_subscribe` int(11) NOT NULL default '0',
  `min_num_subscribe` int(11) NOT NULL default '0',
  `max_sms_budget` double NOT NULL default '0',
  `selling` tinyint(1) NOT NULL default '0',
  `prize` varchar(255) NOT NULL default '',
  `course_type` varchar(255) NOT NULL default 'elearning',
  `policy_point` varchar(255) NOT NULL default '',
  `point_to_all` int(10) NOT NULL default '0',
  `course_edition` tinyint(1) NOT NULL default '0',
  `classrooms` varchar(255) NOT NULL default '',
  `certificates` varchar(255) NOT NULL default '',
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `security_code` varchar(255) NOT NULL default '',
  `imported_from_connection` varchar(255) default NULL,
  `course_quota` varchar(255) NOT NULL default '-1',
  `used_space` varchar(255) NOT NULL default '0',
  `course_vote` double NOT NULL default '0',
  `allow_overbooking` tinyint(1) NOT NULL default '0',
  `can_subscribe` tinyint(1) NOT NULL default '0',
  `sub_start_date` datetime default NULL,
  `sub_end_date` datetime default NULL,
  `advance` varchar(255) NOT NULL default '',
  `show_who_online` tinyint(1) NOT NULL,
  `direct_play` tinyint(1) NOT NULL,
  `autoregistration_code` varchar(255) NOT NULL,
  `use_logo_in_courselist` tinyint(1) NOT NULL,
  `show_result` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`idCourse`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath`
--

CREATE TABLE `learning_coursepath` (
  `id_path` int(11) NOT NULL auto_increment,
  `path_code` varchar(255) NOT NULL default '',
  `path_name` varchar(255) NOT NULL default '',
  `path_descr` text NOT NULL,
  `subscribe_method` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_courses`
--

CREATE TABLE `learning_coursepath_courses` (
  `id_path` int(11) NOT NULL default '0',
  `id_item` int(11) NOT NULL default '0',
  `in_slot` int(11) NOT NULL default '0',
  `prerequisites` text NOT NULL,
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_path`,`id_item`,`in_slot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_courses`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_slot`
--

CREATE TABLE `learning_coursepath_slot` (
  `id_slot` int(11) NOT NULL auto_increment,
  `id_path` int(11) NOT NULL default '0',
  `min_selection` int(3) NOT NULL default '0',
  `max_selection` int(3) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_slot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_slot`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursepath_user`
--

CREATE TABLE `learning_coursepath_user` (
  `id_path` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `waiting` tinyint(1) NOT NULL default '0',
  `subscribed_by` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_path`,`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursepath_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursereport`
--

CREATE TABLE `learning_coursereport` (
  `id_report` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `max_score` float NOT NULL default '0',
  `required_score` float NOT NULL default '0',
  `weight` int(3) NOT NULL default '0',
  `show_to_user` enum('true','false') NOT NULL default 'true',
  `use_for_final` enum('true','false') NOT NULL default 'true',
  `sequence` int(3) NOT NULL default '0',
  `source_of` enum('test','activity','scorm','final_vote') NOT NULL default 'test',
  `id_source` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`id_report`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursereport`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_coursereport_score`
--

CREATE TABLE `learning_coursereport_score` (
  `id_report` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `score` double(5,2) NOT NULL default '0.00',
  `score_status` enum('valid','not_checked','not_passed','passed') NOT NULL default 'valid',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id_report`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_coursereport_score`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_courseuser`
--

CREATE TABLE `learning_courseuser` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `edition_id` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `date_inscr` datetime default NULL,
  `date_first_access` datetime default NULL,
  `date_complete` datetime default NULL,
  `status` int(1) NOT NULL default '0',
  `waiting` tinyint(1) NOT NULL default '0',
  `subscribed_by` int(11) NOT NULL default '0',
  `score_given` int(4) default NULL,
  `imported_from_connection` varchar(255) default NULL,
  `absent` tinyint(1) NOT NULL default '0',
  `cancelled_by` int(11) NOT NULL default '0',
  `new_forum_post` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idUser`,`idCourse`,`edition_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_courseuser`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_edition`
--

CREATE TABLE `learning_course_edition` (
  `idCourseEdition` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `code` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `status` int(1) NOT NULL default '0',
  `img_material` varchar(255) NOT NULL default '',
  `img_othermaterial` varchar(255) NOT NULL default '',
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `hour_begin` varchar(5) NOT NULL default '',
  `hour_end` varchar(5) NOT NULL default '',
  `classrooms` varchar(255) NOT NULL default '',
  `max_num_subscribe` int(11) NOT NULL default '0',
  `min_num_subscribe` int(11) NOT NULL default '0',
  `price` varchar(255) NOT NULL default '',
  `advance` varchar(255) NOT NULL default '',
  `edition_type` varchar(255) NOT NULL default 'elearning',
  `allow_overbooking` tinyint(1) NOT NULL default '0',
  `can_subscribe` tinyint(1) NOT NULL default '0',
  `sub_start_date` datetime default NULL,
  `sub_end_date` datetime default NULL,
  PRIMARY KEY  (`idCourseEdition`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_edition`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_file`
--

CREATE TABLE `learning_course_file` (
  `id_file` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_file`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_course_point`
--

CREATE TABLE `learning_course_point` (
  `idCourse` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `point` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_course_point`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio`
--

CREATE TABLE `learning_eportfolio` (
  `id_portfolio` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `custom_pdp_descr` text NOT NULL,
  `custom_competence_descr` text NOT NULL,
  PRIMARY KEY  (`id_portfolio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence`
--

CREATE TABLE `learning_eportfolio_competence` (
  `id_competence` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `min_score` int(5) NOT NULL default '0',
  `max_score` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `block_competence` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_competence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence_invite`
--

CREATE TABLE `learning_eportfolio_competence_invite` (
  `invited_user` int(11) NOT NULL default '0',
  `sender` int(11) NOT NULL default '0',
  `id_portfolio` int(11) NOT NULL default '0',
  `message_text` text NOT NULL,
  `refused` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`invited_user`,`sender`,`id_portfolio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence_invite`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_competence_score`
--

CREATE TABLE `learning_eportfolio_competence_score` (
  `id_portfolio` int(11) NOT NULL default '0',
  `id_competence` int(11) NOT NULL default '0',
  `estimated_user` int(11) NOT NULL default '0',
  `from_user` int(11) NOT NULL default '0',
  `score` int(11) NOT NULL default '0',
  `comment` text NOT NULL,
  `status` int(3) NOT NULL default '0',
  PRIMARY KEY  (`id_portfolio`,`id_competence`,`estimated_user`,`from_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_competence_score`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_curriculum`
--

CREATE TABLE `learning_eportfolio_curriculum` (
  `id_portfolio` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `curriculum_file` varchar(255) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id_portfolio`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_curriculum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_member`
--

CREATE TABLE `learning_eportfolio_member` (
  `id_portfolio` int(11) NOT NULL default '0',
  `idst_member` int(11) NOT NULL default '0',
  `user_is_admin` enum('false','true') NOT NULL default 'false',
  PRIMARY KEY  (`id_portfolio`,`idst_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_member`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_pdp`
--

CREATE TABLE `learning_eportfolio_pdp` (
  `id_pdp` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `allow_answer` enum('true','false') NOT NULL default 'true',
  `max_answer` int(11) NOT NULL default '0',
  `answer_mod_for_day` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_pdp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_pdp`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_pdp_answer`
--

CREATE TABLE `learning_eportfolio_pdp_answer` (
  `id_answer` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_pdp` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_pdp_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation`
--

CREATE TABLE `learning_eportfolio_presentation` (
  `id_presentation` int(11) NOT NULL auto_increment,
  `id_portfolio` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `owner` int(11) NOT NULL default '0',
  `show_pdp` tinyint(1) NOT NULL default '0',
  `show_competence` tinyint(1) NOT NULL default '0',
  `show_curriculum` tinyint(1) NOT NULL default '0',
  `pubblication_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_presentation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation_attach`
--

CREATE TABLE `learning_eportfolio_presentation_attach` (
  `id_presentation` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `id_file` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_presentation`,`id_user`,`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation_attach`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_eportfolio_presentation_invite`
--

CREATE TABLE `learning_eportfolio_presentation_invite` (
  `id_presentation` int(11) NOT NULL default '0',
  `recipient_mail` varchar(255) NOT NULL default '',
  `security_code` varchar(255) NOT NULL default '',
  `send_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_presentation`,`recipient_mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_eportfolio_presentation_invite`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_faq`
--

CREATE TABLE `learning_faq` (
  `idFaq` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `answer` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idFaq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_faq`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_faq_cat`
--

CREATE TABLE `learning_faq_cat` (
  `idCategory` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_faq_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum`
--

CREATE TABLE `learning_forum` (
  `idForum` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `num_thread` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idForum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forummessage`
--

CREATE TABLE `learning_forummessage` (
  `idMessage` int(11) NOT NULL auto_increment,
  `idThread` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `answer_tree` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `generator` tinyint(1) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  `modified_by` int(11) NOT NULL default '0',
  `modified_by_on` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forummessage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forumthread`
--

CREATE TABLE `learning_forumthread` (
  `idThread` int(11) NOT NULL auto_increment,
  `id_edition` int(11) NOT NULL default '0',
  `idForum` int(11) NOT NULL default '0',
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `author` int(11) NOT NULL default '0',
  `num_post` int(11) NOT NULL default '0',
  `num_view` int(5) NOT NULL default '0',
  `last_post` int(11) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `erased` tinyint(1) NOT NULL default '0',
  `emoticons` varchar(255) NOT NULL default '',
  `rilevantForum` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idThread`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forumthread`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_access`
--

CREATE TABLE `learning_forum_access` (
  `idForum` int(11) NOT NULL default '0',
  `idMember` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idForum`,`idMember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_access`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_notifier`
--

CREATE TABLE `learning_forum_notifier` (
  `id_notify` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `notify_is_a` enum('forum','thread') NOT NULL default 'forum',
  PRIMARY KEY  (`id_notify`,`id_user`,`notify_is_a`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_notifier`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_sema`
--

CREATE TABLE `learning_forum_sema` (
  `idc` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `iduser` int(11) NOT NULL default '0',
  `idmsg` int(11) NOT NULL default '0',
  `idsema` int(11) NOT NULL default '0',
  `idsemaitem` int(11) NOT NULL default '0',
  PRIMARY KEY  (`iduser`,`idmsg`,`idsema`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_sema`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_forum_timing`
--

CREATE TABLE `learning_forum_timing` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_forum_timing`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_glossary`
--

CREATE TABLE `learning_glossary` (
  `idGlossary` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idGlossary`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_glossary`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_glossaryterm`
--

CREATE TABLE `learning_glossaryterm` (
  `idTerm` int(11) NOT NULL auto_increment,
  `idGlossary` int(11) NOT NULL default '0',
  `term` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idTerm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_glossaryterm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_homerepo`
--

CREATE TABLE `learning_homerepo` (
  `idRepo` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` int(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  `idOwner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idRepo`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`),
  KEY `idOwner` (`idOwner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_homerepo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_htmlfront`
--

CREATE TABLE `learning_htmlfront` (
  `id_course` int(11) NOT NULL default '0',
  `textof` text NOT NULL,
  PRIMARY KEY  (`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_htmlfront`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_htmlpage`
--

CREATE TABLE `learning_htmlpage` (
  `idPage` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `textof` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idPage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_htmlpage`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_instmsg`
--

CREATE TABLE `learning_instmsg` (
  `id_msg` bigint(20) NOT NULL auto_increment,
  `id_sender` int(11) NOT NULL default '0',
  `id_receiver` int(11) NOT NULL default '0',
  `msg` text,
  `status` smallint(2) NOT NULL default '0',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_msg`),
  KEY `id_sender` (`id_sender`,`id_receiver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_instmsg`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo`
--

CREATE TABLE `learning_light_repo` (
  `id_repository` int(11) NOT NULL auto_increment,
  `id_course` int(11) NOT NULL default '0',
  `repo_title` varchar(255) NOT NULL default '',
  `repo_descr` text NOT NULL,
  PRIMARY KEY  (`id_repository`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo_files`
--

CREATE TABLE `learning_light_repo_files` (
  `id_file` int(11) NOT NULL auto_increment,
  `id_repository` int(11) NOT NULL default '0',
  `file_name` varchar(255) NOT NULL default '',
  `file_descr` text NOT NULL,
  `id_author` int(11) NOT NULL default '0',
  `post_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo_files`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_light_repo_user`
--

CREATE TABLE `learning_light_repo_user` (
  `id_repo` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `last_enter` datetime NOT NULL default '0000-00-00 00:00:00',
  `repo_lock` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_repo`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_light_repo_user`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_link`
--

CREATE TABLE `learning_link` (
  `idLink` int(11) NOT NULL auto_increment,
  `idCategory` int(11) NOT NULL default '0',
  `title` varchar(150) NOT NULL default '',
  `link_address` varchar(255) NOT NULL default '',
  `keyword` text NOT NULL,
  `description` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idLink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_link`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_link_cat`
--

CREATE TABLE `learning_link_cat` (
  `idCategory` int(11) NOT NULL auto_increment,
  `title` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_link_cat`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_lo_param`
--

CREATE TABLE `learning_lo_param` (
  `id` int(11) NOT NULL auto_increment,
  `idParam` int(11) NOT NULL default '0',
  `param_name` varchar(20) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idParam_name` (`idParam`,`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_lo_param`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_lo_types`
--

CREATE TABLE `learning_lo_types` (
  `objectType` varchar(20) NOT NULL default '',
  `className` varchar(20) NOT NULL default '',
  `fileName` varchar(50) NOT NULL default '',
  `classNameTrack` varchar(255) NOT NULL default '',
  `fileNameTrack` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`objectType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_lo_types`
--

INSERT INTO `learning_lo_types` (`objectType`, `className`, `fileName`, `classNameTrack`, `fileNameTrack`) VALUES
('faq', 'Learning_Faq', 'learning.faq.php', 'Track_Faq', 'track.faq.php'),
('glossary', 'Learning_Glossary', 'learning.glossary.php', 'Track_Glossary', 'track.glossary.php'),
('htmlpage', 'Learning_Htmlpage', 'learning.htmlpage.php', 'Track_Htmlpage', 'track.htmlpage.php'),
('item', 'Learning_Item', 'learning.item.php', 'Track_Item', 'track.item.php'),
('link', 'Learning_Link', 'learning.link.php', 'Track_Link', 'track.link.php'),
('poll', 'Learning_Poll', 'learning.poll.php', 'Track_Poll', 'track.poll.php'),
('scormorg', 'Learning_ScormOrg', 'learning.scorm.php', 'Track_Scormorg', 'track.scorm.php'),
('test', 'Learning_Test', 'learning.test.php', 'Track_Test', 'track.test.php');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_materials_lesson`
--

CREATE TABLE `learning_materials_lesson` (
  `idLesson` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `path` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idLesson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_materials_lesson`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_materials_track`
--

CREATE TABLE `learning_materials_track` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idResource` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_materials_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menu`
--

CREATE TABLE `learning_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  `collapse` enum('true','false') NOT NULL default 'false',
  PRIMARY KEY  (`idMenu`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menu`
--

INSERT INTO `learning_menu` (`idMenu`, `name`, `image`, `sequence`, `collapse`) VALUES
(1, '_MANAGEMENT_COURSE', '', 1, 'false'),
(2, '_EXTERNAL_CONTENT', '', 2, 'false'),
(10, '_MIDDLE_AREA', '', 3, 'false'),
(3, '', '', 6, 'true'),
(4, '', '', 7, 'true'),
(5, '', '', 8, 'true'),
(6, '_CLASSROOMS', '', 5, 'false'),
(7, '_MAN_CERTIFICATE', '', 4, 'false'),
(8, '_MANAGEMENT_RESERVATION', '', 9, 'false');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucourse_main`
--

CREATE TABLE `learning_menucourse_main` (
  `idMain` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idMain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucourse_main`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucourse_under`
--

CREATE TABLE `learning_menucourse_under` (
  `idCourse` int(11) NOT NULL default '0',
  `idModule` int(11) NOT NULL default '0',
  `idMain` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `my_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idCourse`,`idModule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucourse_under`
--

INSERT INTO `learning_menucourse_under` (`idCourse`, `idModule`, `idMain`, `sequence`, `my_name`) VALUES
(0, 1, 0, 1, ''),
(0, 2, 0, 2, ''),
(0, 3, 0, 3, ''),
(0, 4, 0, 4, ''),
(0, 5, 0, 5, ''),
(0, 6, 0, 6, ''),
(0, 7, 0, 7, ''),
(0, 8, 0, 8, ''),
(0, 9, 0, 9, ''),
(0, 32, 0, 10, ''),
(0, 33, 0, 11, ''),
(0, 34, 0, 12, ''),
(0, 35, 1, 1, ''),
(0, 36, 1, 1, ''),
(0, 37, 1, 1, ''),
(0, 38, 1, 1, ''),
(0, 39, 1, 1, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom`
--

CREATE TABLE `learning_menucustom` (
  `idCustom` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idCustom`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom`
--

INSERT INTO `learning_menucustom` (`idCustom`, `title`, `description`) VALUES
(1, 'Full menu', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom_main`
--

CREATE TABLE `learning_menucustom_main` (
  `idMain` int(11) NOT NULL auto_increment,
  `idCustom` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idMain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom_main`
--

INSERT INTO `learning_menucustom_main` (`idMain`, `idCustom`, `sequence`, `name`, `image`) VALUES
(1, 1, 1, 'Students area', 'room.gif'),
(2, 1, 2, 'Collaboration area', 'community.gif'),
(3, 1, 3, 'Teacher area', 'find.gif'),
(4, 1, 4, 'Stat area', 'report.gif');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menucustom_under`
--

CREATE TABLE `learning_menucustom_under` (
  `idCustom` int(11) NOT NULL default '0',
  `idModule` int(11) NOT NULL default '0',
  `idMain` int(11) NOT NULL default '0',
  `sequence` int(3) NOT NULL default '0',
  `my_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idCustom`,`idModule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menucustom_under`
--

INSERT INTO `learning_menucustom_under` (`idCustom`, `idModule`, `idMain`, `sequence`, `my_name`) VALUES
(0, 1, 0, 1, ''),
(0, 2, 0, 2, ''),
(0, 3, 0, 3, ''),
(0, 4, 0, 4, ''),
(0, 5, 0, 5, ''),
(0, 6, 0, 6, ''),
(0, 7, 0, 7, ''),
(0, 8, 0, 8, ''),
(0, 9, 0, 9, ''),
(0, 32, 0, 10, ''),
(0, 33, 0, 11, ''),
(1, 10, 1, 1, ''),
(1, 11, 1, 2, ''),
(1, 25, 1, 3, ''),
(1, 13, 1, 4, ''),
(1, 14, 1, 5, ''),
(1, 15, 1, 6, ''),
(1, 16, 1, 7, ''),
(1, 17, 1, 8, ''),
(1, 18, 1, 9, ''),
(1, 19, 2, 1, ''),
(1, 20, 2, 2, ''),
(1, 21, 2, 3, ''),
(1, 22, 2, 4, ''),
(1, 23, 2, 5, ''),
(1, 24, 2, 6, ''),
(1, 12, 3, 1, ''),
(1, 26, 3, 2, ''),
(1, 27, 3, 3, ''),
(1, 28, 3, 4, ''),
(1, 29, 4, 1, ''),
(1, 30, 4, 2, ''),
(1, 31, 4, 3, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_menu_under`
--

CREATE TABLE `learning_menu_under` (
  `idUnder` int(11) NOT NULL auto_increment,
  `idMenu` int(11) NOT NULL default '0',
  `module_name` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `associated_token` varchar(255) NOT NULL default '',
  `of_platform` varchar(255) default NULL,
  `sequence` int(3) NOT NULL default '0',
  `class_file` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idUnder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_menu_under`
--

INSERT INTO `learning_menu_under` (`idUnder`, `idMenu`, `module_name`, `default_name`, `default_op`, `associated_token`, `of_platform`, `sequence`, `class_file`, `class_name`) VALUES
(1, 1, 'course', '_COURSE', 'course_list', 'view', NULL, 1, 'class.course.php', 'Module_Course'),
(2, 1, 'manmenu', '_MAN_MENU', 'mancustom', 'view', NULL, 2, 'class.manmenu.php', 'Module_Manmenu'),
(3, 1, 'coursepath', '_COURSEPATH', 'pathlist', 'view', NULL, 3, 'class.coursepath.php', 'Module_Coursepath'),
(4, 1, 'catalogue', '_CATALOGUE', 'catlist', 'view', NULL, 4, 'class.catalogue.php', 'Module_Catalogue'),
(5, 2, 'webpages', '_WEBPAGES', 'webpages', 'view', NULL, 1, 'class.webpages.php', 'Module_Webpages'),
(6, 2, 'news', '_NEWS', 'news', 'view', NULL, 2, 'class.news.php', 'Module_News'),
(7, 3, 'questcategory', '_QUESTCATEGORY', 'questcategory', 'view', NULL, 1, 'class.questcategory.php', 'Module_Questcategory'),
(8, 4, 'eportfolio', '_EPORTFOLIO', 'eportfolio', 'view', NULL, 1, 'class.eportfolio.php', 'Module_Eportfolio'),
(9, 5, 'report', '_REPORT', 'reportlist', 'view', NULL, 1, 'class.report.php', 'Module_Report'),
(10, 1, 'preassessment', '_PREASSESSMENT', 'assesmentlist', 'view', NULL, 5, 'class.preassessment.php', 'Module_PreAssessment'),
(11, 6, 'classevent', '_CLASSEVENT', 'main', 'view', NULL, 3, 'class.classevent.php', 'Module_Classevent'),
(12, 6, 'classlocation', '_CLASSLOCATION', 'main', 'view', NULL, 2, 'class.classlocation.php', 'Module_Classlocation'),
(13, 6, 'classroom', '_CLASSROOM', 'classroom', 'view', NULL, 3, 'class.classroom.php', 'Module_Classroom'),
(14, 7, 'certificate', '_CERTIFICATE', 'certificate', 'view', NULL, 1, 'class.certificate.php', 'Module_Certificate'),
(15, 7, 'certificate', '_REPORT_CERTIFICATE', 'report_certificate', 'view', NULL, 2, 'class.certificate.php', 'Module_Certificate'),
(17, 8, 'reservation', '_EVENTS', 'view_event', 'view', NULL, 1, 'class.reservation.php', 'Module_Reservation'),
(18, 8, 'reservation', '_CATEGORY', 'view_category', 'view', NULL, 2, 'class.reservation.php', 'Module_Reservation'),
(20, 8, 'reservation', '_RESERVATION', 'view_registration', 'view', NULL, 4, 'class.reservation.php', 'Module_Reservation'),
(21, 10, 'middlearea', '_MIDDLE_AREA', 'view_area', 'view', NULL, 1, 'class.middlearea.php', 'Module_MiddleArea'),
(22, 10, 'internal_news', '_NEWS_INTERNAL', 'news', 'view', NULL, 2, 'class.internal_news.php', 'Module_Internal_News'),
(23, 7, 'meta_certificate', '_META_CERTIFICATE', 'meta_certificate', 'view', NULL, 3, 'class.meta_certificate.php', 'Module_Meta_Certificate'),
(24, 1, 'competences', '_COMPETENCES', 'main', 'view', NULL, 5, 'class.competences.php', 'Module_Competences');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_middlearea`
--

CREATE TABLE `learning_middlearea` (
  `obj_index` varchar(255) NOT NULL default '',
  `disabled` tinyint(1) NOT NULL default '0',
  `idst_list` text NOT NULL,
  PRIMARY KEY  (`obj_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_middlearea`
--

INSERT INTO `learning_middlearea` (`obj_index`, `disabled`, `idst_list`) VALUES
('mo_4', 1, 'a:0:{}'),
('mo_5', 1, 'a:0:{}'),
('mo_6', 1, 'a:0:{}'),
('mo_8', 1, 'a:0:{}'),
('mo_9', 1, 'a:0:{}'),
('public_forum', 1, 'a:0:{}'),
('course_autoregistration', 1, 'a:0:{}'),
('user_details_short', 1, 'a:0:{}'),
('search_form', 1, 'a:0:{}'),
('news', 1, 'a:0:{}');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_module`
--

CREATE TABLE `learning_module` (
  `idModule` int(11) NOT NULL auto_increment,
  `module_name` varchar(255) NOT NULL default '',
  `default_op` varchar(255) NOT NULL default '',
  `default_name` varchar(255) NOT NULL default '',
  `token_associated` varchar(100) NOT NULL default '',
  `file_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `module_info` text NOT NULL,
  PRIMARY KEY  (`idModule`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_module`
--

INSERT INTO `learning_module` (`idModule`, `module_name`, `default_op`, `default_name`, `token_associated`, `file_name`, `class_name`, `module_info`) VALUES
(1, 'course', 'mycourses', '_MYCOURSES', 'view', 'class.course.php', 'Module_Course', ''),
(2, 'coursecatalogue', 'courselist', '_CATALOGUE', 'view', 'class.coursecatalogue.php', 'Module_Coursecatalogue', ''),
(3, 'profile', 'profile', '_PROFILE', 'view', 'class.profile.php', 'Module_Profile', 'type=user'),
(4, 'myfiles', 'myfiles', '_MYFILES', 'view', 'class.myfiles.php', 'Module_MyFiles', 'type=user'),
(5, 'mygroup', 'group', '_MYGROUP', 'view', 'class.mygroup.php', 'Module_MyGroup', 'type=user'),
(6, 'myfriends', 'myfriends', '_MYFRIENDS', 'view', 'class.myfriends.php', 'Module_MyFriends', 'type=user'),
(7, 'mycertificate', 'mycertificate', '_MY_CERTIFICATE', 'view', 'class.mycertificate.php', 'Module_MyCertificate', 'type=user'),
(8, 'eportfolio', 'eportfolio', '_EPORTFOLIO', 'view', 'class.eportfolio.php', 'Module_EPortfolio', 'type=user'),
(9, 'userevent', 'user_display', '_MYEVENTS', 'view', 'class.userevent.php', 'Module_UserEvent', 'type=user'),
(10, 'course', 'infocourse', '_INFCOURSE', 'view_info', 'class.course.php', 'Module_Course', ''),
(11, 'advice', 'advice', '_ADVICE', 'view', 'class.advice.php', 'Module_Advice', ''),
(12, 'storage', 'display', '_STORAGE', 'view', 'class.storage.php', 'Module_Storage', ''),
(13, 'calendar', 'calendar', '_CALENDAR', 'view', 'class.calendar.php', 'Module_Calendar', ''),
(14, 'gradebook', 'showgrade', '_GRADEBOOK', 'view', 'class.gradebook.php', 'Module_Gradebook', ''),
(15, 'notes', 'notes', '_NOTES', 'view', 'class.notes.php', 'Module_Notes', ''),
(16, 'reservation', 'reservation', '_RESERVATION', 'view', 'class.reservation.php', 'Module_Reservation', ''),
(17, 'light_repo', 'repolist', '_LIGHT_REPO', 'view', 'class.light_repo.php', 'Module_LightRepo', ''),
(18, 'htmlfront', 'showhtml', '_HTMLFRONT', 'view', 'class.htmlfront.php', 'Module_Htmlfront', ''),
(19, 'forum', 'forum', '_FORUM', 'view', 'class.forum.php', 'Module_Forum', ''),
(20, 'wiki', 'main', '_WIKI', 'view', 'class.wiki.php', 'Module_Wiki', ''),
(21, 'chat', 'chat', '_CHAT', 'view', 'class.chat.php', 'Module_Chat', ''),
(22, 'conference', 'list', '_CONFERENCE', 'view', 'class.conference.php', 'Module_Conference', ''),
(23, 'project', 'project', '_PROJECT', 'view', 'class.project.php', 'Module_Project', ''),
(24, 'groups', 'groups', '_GROUPS', 'view', 'class.groups.php', 'Module_Groups', ''),
(25, 'organization', 'organization', '_ORGANIZATION', 'view', 'class.organization.php', 'Module_Organization', ''),
(26, 'coursereport', 'coursereport', '_COURSEREPORT', 'view', 'class.coursereport.php', 'Module_CourseReport', ''),
(27, 'newsletter', 'view', '_NEWSLETTER', 'view', 'class.newsletter.php', 'Module_Newsletter', ''),
(28, 'manmenu', 'manmenu', '_MAN_MENU', 'view', 'class.manmenu.php', 'Module_CourseManmenu', ''),
(29, 'statistic', 'statistic', '_STAT', 'view', 'class.statistic.php', 'Module_Statistic', ''),
(30, 'stats', 'statuser', '_STATUSER', 'view_user', 'class.stats.php', 'Module_Stats', ''),
(31, 'stats', 'statcourse', '_STATCOURSE', 'view_course', 'class.stats.php', 'Module_Stats', ''),
(32, 'public_forum', 'public_forum', '_PUBLIC_FORUM', 'view', 'class.public_forum.php', 'Module_Public_Forum', ''),
(33, 'course_autoregistration', 'course_autoregistration', '_COURSE_AUTOREGISTRATION', 'view', 'class.course_autoregistration.php', 'Module_Course_Autoregistration', ''),
(34, 'mycompetences', 'mycompetences', '_MYCOMPETENCES', 'view', 'class.mycompetences.php', 'Module_MyCompetences', 'type=user'),
(35, 'public_user_admin', 'public_user_admin', '_PUBLIC_USER_ADMIN', 'view_org_chart', 'class.public_user_admin.php', 'Module_Public_User_Admin', 'type=public_admin'),
(36, 'public_course_admin', 'public_course_admin', '_PUBLIC_COURSE_ADMIN', 'view', 'class.public_course_admin.php', 'Module_Public_Course_Admin', 'type=public_admin'),
(37, 'public_subscribe_admin', 'public_subscribe_admin', '_PUBLIC_SUBSCRIBE_ADMIN', 'view', 'class.public_subscribe_admin.php', 'Module_Public_Subscribe_Admin', 'type=public_admin'),
(38, 'public_report_admin', 'reportlist', '_PUBLIC_REPORT_ADMIN', 'view', 'class.public_report_admin.php', 'Module_Public_Report_Admin', 'type=public_admin'),
(39, 'public_newsletter_admin', 'newsletter', '_PUBLIC_NEWSLETTER_ADMIN', 'view', 'class.public_newsletter_admin.php', 'Module_Public_Newsletter_Admin', 'type=public_admin');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_news`
--

CREATE TABLE `learning_news` (
  `idNews` int(11) NOT NULL auto_increment,
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idNews`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_news`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_news_internal`
--

CREATE TABLE `learning_news_internal` (
  `idNews` int(11) NOT NULL auto_increment,
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) NOT NULL default '',
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `viewer` longtext NOT NULL,
  PRIMARY KEY  (`idNews`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_news_internal`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_notes`
--

CREATE TABLE `learning_notes` (
  `idNotes` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `data` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(150) NOT NULL default '',
  `textof` text NOT NULL,
  PRIMARY KEY  (`idNotes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_notes`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_organization`
--

CREATE TABLE `learning_organization` (
  `idOrg` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` int(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  `idCourse` int(11) NOT NULL default '0',
  `prerequisites` varchar(255) NOT NULL default '',
  `isTerminator` tinyint(4) NOT NULL default '0',
  `idParam` int(11) NOT NULL default '0',
  `visible` tinyint(4) NOT NULL default '1',
  `milestone` enum('start','end','-') NOT NULL default '-',
  PRIMARY KEY  (`idOrg`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_organization`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_organization_access`
--

CREATE TABLE `learning_organization_access` (
  `idOrgAccess` int(11) NOT NULL default '0',
  `kind` set('user','group') NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idOrgAccess`,`kind`,`value`),
  KEY `idObject` (`idOrgAccess`),
  KEY `kind` (`kind`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Access to items in area lesson (organization)';

--
-- Dump dei dati per la tabella `learning_organization_access`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel`
--

CREATE TABLE `learning_pagel` (
  `id` int(11) NOT NULL auto_increment,
  `idc` int(11) NOT NULL default '0',
  `iduser` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `idatvt` int(11) NOT NULL default '0',
  `idcatval` int(11) NOT NULL default '0',
  `idvote` int(11) NOT NULL default '0',
  `adate` date NOT NULL default '0000-00-00',
  `title` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_atvt`
--

CREATE TABLE `learning_pagel_atvt` (
  `id` int(11) NOT NULL auto_increment,
  `idc` int(11) NOT NULL default '0',
  `idprof` int(11) NOT NULL default '0',
  `dsc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_atvt`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_sema`
--

CREATE TABLE `learning_pagel_sema` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_sema`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_sema_items`
--

CREATE TABLE `learning_pagel_sema_items` (
  `id` int(11) NOT NULL auto_increment,
  `idref` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_sema_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_vote`
--

CREATE TABLE `learning_pagel_vote` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_vote`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pagel_vote_items`
--

CREATE TABLE `learning_pagel_vote_items` (
  `id` int(11) NOT NULL auto_increment,
  `idref` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `val` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pagel_vote_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_poll`
--

CREATE TABLE `learning_poll` (
  `id_poll` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`id_poll`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_poll`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquest`
--

CREATE TABLE `learning_pollquest` (
  `id_quest` int(11) NOT NULL auto_increment,
  `id_poll` int(11) NOT NULL default '0',
  `id_category` int(11) NOT NULL default '0',
  `type_quest` varchar(255) NOT NULL default '',
  `title_quest` text NOT NULL,
  `sequence` int(5) NOT NULL default '0',
  `page` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquestanswer`
--

CREATE TABLE `learning_pollquestanswer` (
  `id_answer` int(11) NOT NULL auto_increment,
  `id_quest` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  PRIMARY KEY  (`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquestanswer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_pollquest_extra`
--

CREATE TABLE `learning_pollquest_extra` (
  `id_quest` int(11) NOT NULL default '0',
  `id_answer` int(11) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`id_quest`,`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_pollquest_extra`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_polltrack`
--

CREATE TABLE `learning_polltrack` (
  `id_track` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default '0',
  `id_reference` int(11) NOT NULL default '0',
  `id_poll` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('valid','not_complete') NOT NULL default 'not_complete',
  PRIMARY KEY  (`id_track`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_polltrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_polltrack_answer`
--

CREATE TABLE `learning_polltrack_answer` (
  `id_track` int(11) NOT NULL default '0',
  `id_quest` int(11) NOT NULL default '0',
  `id_answer` int(11) NOT NULL default '0',
  `more_info` longtext NOT NULL,
  PRIMARY KEY  (`id_track`,`id_quest`,`id_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_polltrack_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj`
--

CREATE TABLE `learning_prj` (
  `id` int(11) NOT NULL auto_increment,
  `ptitle` varchar(255) NOT NULL default '',
  `pgroup` int(11) NOT NULL default '0',
  `pprog` tinyint(3) NOT NULL default '0',
  `psfiles` tinyint(1) NOT NULL default '0',
  `pstasks` tinyint(1) NOT NULL default '0',
  `psnews` tinyint(1) NOT NULL default '0',
  `pstodo` tinyint(1) NOT NULL default '0',
  `psmsg` tinyint(1) NOT NULL default '0',
  `cid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_files`
--

CREATE TABLE `learning_prj_files` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `ftitle` varchar(255) NOT NULL default '',
  `fver` varchar(255) NOT NULL default '',
  `fdesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_files`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_news`
--

CREATE TABLE `learning_prj_news` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `ntitle` varchar(255) NOT NULL default '',
  `ndate` date NOT NULL default '0000-00-00',
  `ntxt` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_news`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_tasks`
--

CREATE TABLE `learning_prj_tasks` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `tprog` tinyint(3) NOT NULL default '0',
  `tname` varchar(255) NOT NULL default '',
  `tdesc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_tasks`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_todo`
--

CREATE TABLE `learning_prj_todo` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `ttitle` varchar(255) NOT NULL default '',
  `ttxt` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_todo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_prj_users`
--

CREATE TABLE `learning_prj_users` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `flag` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_prj_users`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_category`
--

CREATE TABLE `learning_quest_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `textof` text NOT NULL,
  `author` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_type`
--

CREATE TABLE `learning_quest_type` (
  `type_quest` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`type_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_type`
--

INSERT INTO `learning_quest_type` (`type_quest`, `type_file`, `type_class`, `sequence`) VALUES
('associate', 'class.associate.php', 'Associate_Question', 8),
('break_page', 'class.break_page.php', 'BreakPage_Question', 10),
('choice', 'class.choice.php', 'Choice_Question', 1),
('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2),
('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3),
('hot_text', 'class.hot_text.php', 'HotText_Question', 6),
('inline_choice', 'class.inline_choice.php', 'InlineChoice_Question', 5),
('text_entry', 'class.text_entry.php', 'TextEntry_Question', 4),
('title', 'class.title.php', 'Title_Question', 9),
('upload', 'class.upload.php', 'Upload_Question', 7);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_quest_type_poll`
--

CREATE TABLE `learning_quest_type_poll` (
  `type_quest` varchar(255) NOT NULL default '',
  `type_file` varchar(255) NOT NULL default '',
  `type_class` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`type_quest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_quest_type_poll`
--

INSERT INTO `learning_quest_type_poll` (`type_quest`, `type_file`, `type_class`, `sequence`) VALUES
('break_page', 'class.break_page.php', 'BreakPage_Question', 5),
('choice', 'class.choice.php', 'Choice_Question', 1),
('choice_multiple', 'class.choice_multiple.php', 'ChoiceMultiple_Question', 2),
('extended_text', 'class.extended_text.php', 'ExtendedText_Question', 3),
('title', 'class.title.php', 'Title_Question', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_repo`
--

CREATE TABLE `learning_repo` (
  `idRepo` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `objectType` varchar(20) NOT NULL default '',
  `idResource` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idAuthor` varchar(11) NOT NULL default '0',
  `version` varchar(8) NOT NULL default '',
  `difficult` enum('_VERYEASY','_EASY','_MEDIUM','_DIFFICULT','_VERYDIFFICULT') NOT NULL default '_VERYEASY',
  `description` text NOT NULL,
  `language` varchar(50) NOT NULL default '',
  `resource` varchar(255) NOT NULL default '',
  `objective` text NOT NULL,
  `dateInsert` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idRepo`),
  KEY `idParent` (`idParent`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_repo`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report`
--

CREATE TABLE `learning_report` (
  `id_report` int(11) NOT NULL auto_increment,
  `report_name` varchar(255) NOT NULL default '',
  `class_name` varchar(255) NOT NULL default '',
  `file_name` varchar(255) NOT NULL default '',
  `use_user_selection` enum('true','false') NOT NULL default 'true',
  `enabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_report`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_report`
--

INSERT INTO `learning_report` (`id_report`, `report_name`, `class_name`, `file_name`, `use_user_selection`, `enabled`) VALUES
(1, 'general_report', 'Report_General', 'class.report_general.php', 'true', 0),
(2, 'user_report', 'Report_User', 'class.report_user.php', 'true', 1),
(3, 'competences_report', 'Report_Competences', 'class.report_competences.php', 'true', 0),
(4, 'courses_report', 'Report_Courses', 'class.report_courses.php', 'true', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_filter`
--

CREATE TABLE `learning_report_filter` (
  `id_filter` int(10) unsigned NOT NULL auto_increment,
  `id_report` int(10) unsigned NOT NULL default '0',
  `author` int(10) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `filter_name` varchar(255) NOT NULL default '',
  `filter_data` text NOT NULL,
  `is_public` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_filter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_report_filter`
--

INSERT INTO `learning_report_filter` (`id_filter`, `id_report`, `author`, `creation_date`, `filter_name`, `filter_data`, `is_public`) VALUES
(1, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_USER', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:22:"all users, all courses";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:7:"courses";s:14:"columns_filter";a:7:{s:21:"org_chart_subdivision";i:0;s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:11:"sub_filters";a:0:{}s:16:"filter_exclusive";s:1:"1";s:14:"showed_columns";a:14:{i:0;s:7:"_TH_CAT";i:1;s:8:"_TH_CODE";i:2;s:14:"_TH_COURSEPATH";i:3;s:16:"_TH_COURSESTATUS";i:4;s:25:"_TH_USER_INSCRIPTION_DATE";i:5;s:19:"_TH_USER_START_DATE";i:6;s:17:"_TH_USER_END_DATE";i:7;s:20:"_TH_LAST_ACCESS_DATE";i:8;s:15:"_TH_USER_STATUS";i:9;s:20:"_TH_USER_START_SCORE";i:10;s:20:"_TH_USER_FINAL_SCORE";i:11;s:21:"_TH_USER_COURSE_SCORE";i:12;s:23:"_TH_USER_NUMBER_SESSION";i:13;s:21:"_TH_USER_ELAPSED_TIME";}s:13:"custom_fields";a:5:{i:0;a:3:{s:2:"id";i:9;s:5:"label";s:22:"test-filesupplementare";s:8:"selected";b:0;}i:1;a:3:{s:2:"id";i:10;s:5:"label";s:23:"test-camposupplementare";s:8:"selected";b:0;}i:2;a:3:{s:2:"id";i:1;s:5:"label";s:11:"Descrizione";s:8:"selected";b:0;}i:3;a:3:{s:2:"id";i:3;s:5:"label";s:13:"File di Prova";s:8:"selected";b:0;}i:4;a:3:{s:2:"id";i:7;s:5:"label";s:12:"Contatto MSN";s:8:"selected";b:0;}}}}', 1),
(2, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_LO', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:31:"all users, all learning objects";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:2:"LO";s:14:"columns_filter";a:6:{s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:8:"lo_types";a:8:{s:3:"faq";s:3:"faq";s:8:"glossary";s:8:"glossary";s:8:"htmlpage";s:8:"htmlpage";s:4:"item";s:4:"item";s:4:"link";s:4:"link";s:4:"poll";s:4:"poll";s:8:"scormorg";s:8:"scormorg";s:4:"test";s:4:"test";}s:13:"lo_milestones";a:3:{i:0;s:7:"ml_none";i:1;s:8:"ml_start";i:2;s:6:"ml_end";}s:14:"showed_columns";a:10:{i:0;s:9:"user_name";i:1;s:11:"course_name";i:2;s:13:"course_status";i:3;s:7:"lo_type";i:4;s:7:"lo_name";i:5;s:12:"lo_milestone";i:6;s:12:"firstAttempt";i:7;s:11:"lastAttempt";i:8;s:9:"lo_status";i:9;s:8:"lo_score";}s:13:"custom_fields";a:5:{i:0;a:3:{s:2:"id";i:9;s:5:"label";s:22:"test-filesupplementare";s:8:"selected";b:0;}i:1;a:3:{s:2:"id";i:10;s:5:"label";s:23:"test-camposupplementare";s:8:"selected";b:0;}i:2;a:3:{s:2:"id";i:1;s:5:"label";s:11:"Descrizione";s:8:"selected";b:0;}i:3;a:3:{s:2:"id";i:3;s:5:"label";s:13:"File di Prova";s:8:"selected";b:0;}i:4;a:3:{s:2:"id";i:7;s:5:"label";s:12:"Contatto MSN";s:8:"selected";b:0;}}}}', 1),
(3, 2, 0, '2008-08-11 12:00:00', '_STD_REPORT_DELAY', 'a:5:{s:9:"id_report";s:1:"2";s:11:"report_name";s:28:"Delay analysis for all users";s:11:"rows_filter";a:2:{s:5:"users";a:0:{}s:9:"all_users";b:1;}s:23:"columns_filter_category";s:5:"delay";s:14:"columns_filter";a:8:{s:11:"report_type";s:16:"course_completed";s:21:"day_from_subscription";s:0:"";s:20:"day_until_course_end";s:0:"";s:21:"date_until_course_end";s:0:"";s:21:"org_chart_subdivision";i:0;s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}s:14:"showed_columns";a:7:{i:0;s:9:"_LASTNAME";i:1;s:5:"_NAME";i:2;s:7:"_STATUS";i:3;s:5:"_MAIL";i:4;s:11:"_DATE_INSCR";i:5;s:18:"_DATE_FIRST_ACCESS";i:6;s:22:"_DATE_COURSE_COMPLETED";}}}', 1),
(4, 4, 0, '2008-08-11 12:00:00', '_STD_REPORT_COURSE', 'a:5:{s:9:"id_report";s:1:"4";s:11:"report_name";s:54:"Statistics on all courses for all users (last 30 days)";s:11:"rows_filter";a:2:{s:11:"all_courses";b:1;s:16:"selected_courses";a:0:{}}s:23:"columns_filter_category";s:5:"users";s:14:"columns_filter";a:5:{s:9:"all_users";b:1;s:5:"users";a:0:{}s:9:"time_belt";a:3:{s:10:"time_range";s:2:"31";s:10:"start_date";s:0:"";s:8:"end_date";s:0:"";}s:21:"org_chart_subdivision";i:0;s:11:"showed_cols";a:21:{i:0;s:12:"_CODE_COURSE";i:1;s:12:"_NAME_COURSE";i:2;s:16:"_COURSE_CATEGORY";i:3;s:13:"_COURSESTATUS";i:4;s:9:"_LANGUAGE";i:5;s:10:"_DIFFICULT";i:6;s:11:"_DATE_BEGIN";i:7;s:9:"_DATE_END";i:8;s:11:"_TIME_BEGIN";i:9;s:9:"_TIME_END";i:10;s:19:"_MAX_NUM_SUBSCRIBED";i:11;s:19:"_MIN_NUM_SUBSCRIBED";i:12;s:6:"_PRICE";i:13;s:8:"_ADVANCE";i:14;s:12:"_COURSE_TYPE";i:15;s:22:"_AUTOREGISTRATION_CODE";i:16;s:6:"_INSCR";i:17;s:10:"_MUSTBEGIN";i:18;s:18:"_USER_STATUS_BEGIN";i:19;s:15:"_COMPLETECOURSE";i:20;s:14:"_TOTAL_SESSION";}}}', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_schedule`
--

CREATE TABLE `learning_report_schedule` (
  `id_report_schedule` int(11) unsigned NOT NULL auto_increment,
  `id_report_filter` int(11) unsigned NOT NULL,
  `id_creator` int(11) unsigned NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `period` varchar(255) collate utf8_bin NOT NULL,
  `time` time NOT NULL,
  `creation_date` datetime NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id_report_schedule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `learning_report_schedule`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_report_schedule_recipient`
--

CREATE TABLE `learning_report_schedule_recipient` (
  `id_report_schedule` int(11) unsigned NOT NULL,
  `id_user` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id_report_schedule`,`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `learning_report_schedule_recipient`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_category`
--

CREATE TABLE `learning_reservation_category` (
  `idCategory` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `maxSubscription` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idCategory`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_category`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_events`
--

CREATE TABLE `learning_reservation_events` (
  `idEvent` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `idLaboratory` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` longtext,
  `date` date NOT NULL default '0000-00-00',
  `maxUser` int(11) NOT NULL default '0',
  `deadLine` date NOT NULL default '0000-00-00',
  `fromTime` time NOT NULL default '00:00:00',
  `toTime` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`idEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_events`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_perm`
--

CREATE TABLE `learning_reservation_perm` (
  `event_id` int(11) NOT NULL,
  `user_idst` int(11) NOT NULL,
  `perm` varchar(255) NOT NULL,
  PRIMARY KEY  (`event_id`,`user_idst`,`perm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_perm`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_reservation_subscribed`
--

CREATE TABLE `learning_reservation_subscribed` (
  `idstUser` int(11) NOT NULL default '0',
  `idEvent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idstUser`,`idEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_reservation_subscribed`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_items`
--

CREATE TABLE `learning_scorm_items` (
  `idscorm_item` int(11) NOT NULL auto_increment,
  `idscorm_organization` int(11) NOT NULL default '0',
  `idscorm_parentitem` int(11) default NULL,
  `adlcp_prerequisites` varchar(200) default NULL,
  `adlcp_maxtimeallowed` varchar(24) default NULL,
  `adlcp_timelimitaction` varchar(24) default NULL,
  `adlcp_datafromlms` varchar(255) default NULL,
  `adlcp_masteryscore` varchar(200) default NULL,
  `item_identifier` varchar(255) default NULL,
  `identifierref` varchar(255) default NULL,
  `idscorm_resource` int(11) default NULL,
  `isvisible` set('true','false') default 'true',
  `parameters` varchar(100) default NULL,
  `title` varchar(100) NOT NULL default '',
  `nChild` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  `adlcp_completionthreshold` varchar(10) NOT NULL,
  PRIMARY KEY  (`idscorm_item`),
  UNIQUE KEY `idscorm_organization` (`idscorm_organization`,`item_identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_items`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_items_track`
--

CREATE TABLE `learning_scorm_items_track` (
  `idscorm_item_track` int(11) NOT NULL auto_increment,
  `idscorm_organization` int(11) NOT NULL default '0',
  `idscorm_item` int(11) default NULL,
  `idReference` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idscorm_tracking` int(11) default NULL,
  `status` varchar(16) NOT NULL default 'not attempted',
  `nChild` int(11) NOT NULL default '0',
  `nChildCompleted` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  `nDescendantCompleted` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idscorm_item_track`),
  KEY `idscorm_organization` (`idscorm_organization`),
  KEY `idscorm_item` (`idscorm_item`),
  KEY `idUser` (`idUser`),
  KEY `idscorm_tracking` (`idscorm_tracking`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Join table 3 factor';

--
-- Dump dei dati per la tabella `learning_scorm_items_track`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_organizations`
--

CREATE TABLE `learning_scorm_organizations` (
  `idscorm_organization` int(11) NOT NULL auto_increment,
  `org_identifier` varchar(255) NOT NULL default '',
  `idscorm_package` int(11) NOT NULL default '0',
  `title` varchar(100) default NULL,
  `nChild` int(11) NOT NULL default '0',
  `nDescendant` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idscorm_organization`),
  UNIQUE KEY `idsco_package_unique` (`org_identifier`,`idscorm_package`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_organizations`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_package`
--

CREATE TABLE `learning_scorm_package` (
  `idscorm_package` int(11) NOT NULL auto_increment,
  `idpackage` varchar(255) NOT NULL default '',
  `idProg` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `defaultOrg` varchar(255) NOT NULL default '',
  `idUser` int(11) NOT NULL default '0',
  `scormVersion` varchar(10) NOT NULL default '1.2',
  PRIMARY KEY  (`idscorm_package`),
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_package`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_resources`
--

CREATE TABLE `learning_scorm_resources` (
  `idscorm_resource` int(11) NOT NULL auto_increment,
  `idsco` varchar(255) NOT NULL default '',
  `idscorm_package` int(11) NOT NULL default '0',
  `scormtype` set('sco','asset') default NULL,
  `href` varchar(255) default NULL,
  PRIMARY KEY  (`idscorm_resource`),
  UNIQUE KEY `idsco_package_unique` (`idsco`,`idscorm_package`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_resources`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_tracking`
--

CREATE TABLE `learning_scorm_tracking` (
  `idscorm_tracking` int(11) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idscorm_item` int(11) NOT NULL default '0',
  `user_name` varchar(255) default NULL,
  `lesson_location` varchar(255) default NULL,
  `credit` varchar(24) default NULL,
  `lesson_status` varchar(24) default NULL,
  `entry` varchar(24) default NULL,
  `score_raw` float default NULL,
  `score_max` float default NULL,
  `score_min` float default NULL,
  `total_time` varchar(15) default '0000:00:00.00',
  `lesson_mode` varchar(24) default NULL,
  `exit` varchar(24) default NULL,
  `session_time` varchar(15) default NULL,
  `suspend_data` blob,
  `launch_data` blob,
  `comments` blob,
  `comments_from_lms` blob,
  `xmldata` longblob,
  PRIMARY KEY  (`idscorm_tracking`),
  UNIQUE KEY `Unique_tracking_usersco` (`idUser`,`idReference`,`idscorm_item`),
  KEY `idUser` (`idUser`),
  KEY `idscorm_resource` (`idReference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_tracking`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_scorm_tracking_history`
--

CREATE TABLE `learning_scorm_tracking_history` (
  `idscorm_tracking` int(11) NOT NULL,
  `date_action` datetime NOT NULL,
  `score_raw` float default NULL,
  `score_max` float default NULL,
  `session_time` varchar(15) default NULL,
  `lesson_status` varchar(24) NOT NULL,
  PRIMARY KEY  (`idscorm_tracking`,`date_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_scorm_tracking_history`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_setting`
--

CREATE TABLE `learning_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` text NOT NULL,
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_setting`
--

INSERT INTO `learning_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES
('activeCommerce', 'on', 'enum', 3, 0, 5, 1, 1, ''),
('activeNews', 'block', 'sel_news', 3, 1, 7, 1, 0, ''),
('admin_mail', 'marco@docebo.com', 'string', 255, 0, 2, 1, 0, ''),
('course_block', 'off', 'enum', 3, 1, 9, 1, 0, ''),
('course_list_plan', 'on', 'enum', 3, 1, 8, 1, 0, ''),
('course_quota', '50', 'string', 255, 0, 5, 1, 0, ''),
('defaultTemplate', 'standard', 'template', 255, 1, 3, 1, 0, ''),
('do_debug', 'on', 'enum', 3, 1, 0, 1, 1, ''),
('first_coursecatalogue_tab', 'category', 'first_coursecatalogue_tab', 255, 5, 2, 1, 0, ''),
('forum_as_table', 'off', 'enum', 3, 1, 6, 1, 0, ''),
('lms_version', '3.0', 'string', 255, 0, 0, 1, 1, ''),
('max_pdp_answer', '10', 'int', 6, 1, 10, 1, 0, ''),
('on_catalogue_empty', 'on', 'enum', 3, 1, 10, 1, 0, ''),
('pathchat', 'chat/', 'string', 255, 2, 1, 1, 0, ''),
('pathcourse', 'course/', 'string', 255, 2, 2, 1, 0, ''),
('pathforum', 'forum/', 'string', 255, 2, 3, 1, 0, ''),
('pathlesson', 'item/', 'string', 255, 2, 4, 1, 0, ''),
('pathmessage', 'message/', 'string', 255, 2, 5, 1, 0, ''),
('pathprj', 'project/', 'string', 255, 2, 0, 1, 1, ''),
('pathscorm', 'scorm/', 'string', 255, 2, 7, 1, 0, ''),
('pathsponsor', 'sponsor/', 'string', 255, 2, 8, 1, 0, ''),
('pathtest', 'test/', 'string', 255, 2, 9, 1, 0, ''),
('stop_concurrent_user', 'on', 'enum', 3, 3, 1, 1, 0, ''),
('tablist_coursecatalogue', 'a%3A6%3A%7Bs%3A4%3A%22time%22%3Bi%3A1%3Bs%3A8%3A%22category%22%3Bi%3A1%3Bs%3A3%3A%22all%22%3Bi%3A1%3Bs%3A9%3A%22mostscore%22%3Bi%3A1%3Bs%3A7%3A%22popular%22%3Bi%3A1%3Bs%3A6%3A%22recent%22%3Bi%3A1%3B%7D', 'tablist_coursecatalogue', 255, 5, 1, 1, 0, ''),
('tracking', 'on', 'enum', 3, 0, 4, 1, 0, ''),
('ttlSession', '4000', 'int', 5, 0, 3, 1, 0, ''),
('use_coursepath', '0', 'menuvoice', 1, 7, 1, 1, 0, '/coursepath/view'),
('use_course_catalogue', '0', 'menuvoice', 1, 7, 2, 1, 0, '/catalogue/view'),
('use_social_courselist', 'off', 'enum', 3, 0, 0, 1, 0, ''),
('visuItem', '20', 'int', 5, 1, 1, 1, 0, ''),
('visuNewsHomePage', '4', 'int', 5, 1, 0, 1, 1, ''),
('visu_course', '25', 'int', 5, 1, 2, 1, 0, ''),
('no_answer_in_poll', 'off', 'enum', 3, 1, 11, 1, 0, ''),
('no_answer_in_test', 'off', 'enum', 3, 1, 12, 1, 0, ''),
('catalogue_hide_ended', 'on', 'enum', 3, 0, 8, 1, 0, ''),
('tablist_mycourses', 'status,name,code', 'tablist_mycourses', 255, 0, 6, 1, 0, ''),
('url', 'http://localhost/docebo/doceboLms/', 'string', 255, 0, 1, 1, 0, ''),
('first_catalogue', 'off', 'enum', 3, 0, 9, 1, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `learning_statuschangelog`
--

CREATE TABLE `learning_statuschangelog` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `status_user` tinyint(1) NOT NULL default '0',
  `when_do` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`,`status_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_statuschangelog`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_sysforum`
--

CREATE TABLE `learning_sysforum` (
  `idMessage` int(11) NOT NULL auto_increment,
  `key1` varchar(255) NOT NULL default '',
  `key2` int(11) NOT NULL default '0',
  `key3` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  `posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` int(11) NOT NULL default '0',
  `attach` varchar(255) NOT NULL default '',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_sysforum`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_teacher_profile`
--

CREATE TABLE `learning_teacher_profile` (
  `id_user` int(11) NOT NULL default '0',
  `curriculum` text NOT NULL,
  `publications` text NOT NULL,
  PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_teacher_profile`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_test`
--

CREATE TABLE `learning_test` (
  `idTest` int(11) NOT NULL auto_increment,
  `author` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `point_type` tinyint(1) NOT NULL default '0',
  `point_required` double NOT NULL default '0',
  `display_type` tinyint(1) NOT NULL default '0',
  `order_type` tinyint(1) NOT NULL default '0',
  `shuffle_answer` tinyint(1) NOT NULL default '0',
  `question_random_number` int(4) NOT NULL default '0',
  `save_keep` tinyint(1) NOT NULL default '0',
  `mod_doanswer` tinyint(1) NOT NULL default '1',
  `can_travel` tinyint(1) NOT NULL default '1',
  `show_only_status` tinyint(1) NOT NULL default '0',
  `show_score` tinyint(1) NOT NULL default '1',
  `show_score_cat` tinyint(1) NOT NULL default '0',
  `show_doanswer` tinyint(1) NOT NULL default '0',
  `show_solution` tinyint(1) NOT NULL default '0',
  `time_dependent` tinyint(1) NOT NULL default '0',
  `time_assigned` int(6) NOT NULL default '0',
  `penality_test` tinyint(1) NOT NULL default '0',
  `penality_time_test` double NOT NULL default '0',
  `penality_quest` tinyint(1) NOT NULL default '0',
  `penality_time_quest` double NOT NULL default '0',
  `max_attempt` int(11) NOT NULL default '0',
  `hide_info` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idTest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_test`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquest`
--

CREATE TABLE `learning_testquest` (
  `idQuest` int(11) NOT NULL auto_increment,
  `idTest` int(11) NOT NULL default '0',
  `idCategory` int(11) NOT NULL default '0',
  `type_quest` varchar(255) NOT NULL default '',
  `title_quest` text NOT NULL,
  `difficult` int(1) NOT NULL default '3',
  `time_assigned` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `page` int(11) NOT NULL default '0',
  `shuffle` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idQuest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquestanswer`
--

CREATE TABLE `learning_testquestanswer` (
  `idAnswer` int(11) NOT NULL auto_increment,
  `idQuest` int(11) NOT NULL default '0',
  `sequence` int(11) NOT NULL default '0',
  `is_correct` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  `comment` text NOT NULL,
  `score_correct` double NOT NULL default '0',
  `score_incorrect` double NOT NULL default '0',
  PRIMARY KEY  (`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquestanswer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquestanswer_associate`
--

CREATE TABLE `learning_testquestanswer_associate` (
  `idAnswer` int(11) NOT NULL auto_increment,
  `idQuest` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  PRIMARY KEY  (`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquestanswer_associate`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testquest_extra`
--

CREATE TABLE `learning_testquest_extra` (
  `idQuest` int(11) NOT NULL default '0',
  `idAnswer` int(11) NOT NULL default '0',
  `extra_info` text NOT NULL,
  PRIMARY KEY  (`idQuest`,`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testquest_extra`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack`
--

CREATE TABLE `learning_testtrack` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idTest` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_attempt_mod` datetime default NULL,
  `date_end_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_page_seen` int(11) NOT NULL default '0',
  `last_page_saved` int(11) NOT NULL default '0',
  `number_of_save` int(11) NOT NULL default '0',
  `number_of_attempt` int(11) NOT NULL default '0',
  `score` double default NULL,
  `bonus_score` double NOT NULL default '0',
  `score_status` enum('valid','not_checked','not_passed','passed','not_complete','doing') NOT NULL default 'not_complete',
  `comment` text NOT NULL,
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_answer`
--

CREATE TABLE `learning_testtrack_answer` (
  `idTrack` int(11) NOT NULL default '0',
  `idQuest` int(11) NOT NULL default '0',
  `idAnswer` int(11) NOT NULL default '0',
  `score_assigned` double NOT NULL default '0',
  `more_info` longtext NOT NULL,
  `manual_assigned` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`idQuest`,`idAnswer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_answer`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_page`
--

CREATE TABLE `learning_testtrack_page` (
  `idTrack` int(11) NOT NULL default '0',
  `page` int(3) NOT NULL default '0',
  `display_from` datetime default NULL,
  `display_to` datetime default NULL,
  `accumulated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_page`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_quest`
--

CREATE TABLE `learning_testtrack_quest` (
  `idTrack` int(11) NOT NULL default '0',
  `idQuest` int(11) NOT NULL default '0',
  `page` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idTrack`,`idQuest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_quest`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_testtrack_times`
--

CREATE TABLE `learning_testtrack_times` (
  `idTrack` int(11) NOT NULL default '0',
  `idReference` int(11) NOT NULL default '0',
  `idTest` int(11) NOT NULL default '0',
  `date_attempt` datetime NOT NULL default '0000-00-00 00:00:00',
  `number_time` tinyint(4) NOT NULL default '0',
  `score` double NOT NULL default '0',
  `score_status` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`idTrack`,`number_time`,`idTest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_testtrack_times`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_trackingeneral`
--

CREATE TABLE `learning_trackingeneral` (
  `idTrack` int(11) NOT NULL auto_increment,
  `idEnter` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `function` varchar(250) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `timeof` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`idTrack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_trackingeneral`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_tracksession`
--

CREATE TABLE `learning_tracksession` (
  `idEnter` int(11) NOT NULL auto_increment,
  `idCourse` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `enterTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `numOp` int(5) NOT NULL default '0',
  `lastFunction` varchar(50) NOT NULL default '',
  `lastOp` varchar(5) NOT NULL default '',
  `lastTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip_address` varchar(40) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idEnter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_tracksession`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_webpages`
--

CREATE TABLE `learning_webpages` (
  `idPages` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `language` varchar(255) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `in_home` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idPages`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_webpages`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `learning_wiki_course`
--

CREATE TABLE `learning_wiki_course` (
  `course_id` int(11) NOT NULL default '0',
  `wiki_id` int(11) NOT NULL default '0',
  `is_owner` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`course_id`,`wiki_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `learning_wiki_course`
--

