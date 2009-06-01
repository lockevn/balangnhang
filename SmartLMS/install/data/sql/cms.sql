-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 04 Lug, 2008 at 11:51 AM
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
  `last_modify` DATETIME NOT NULL ,
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