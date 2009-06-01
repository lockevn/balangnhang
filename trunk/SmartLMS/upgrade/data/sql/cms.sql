-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generato il: 10 Dic, 2005 at 11:43 AM
-- Versione MySQL: 4.1.13
-- Versione PHP: 4.4.0
-- 
-- Database: `docebo30`
-- 

-- --------------------------------------------------------


-- 
-- Struttura della tabella `cms_area`
-- 

CREATE TABLE IF NOT EXISTS `cms_area` (
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
  PRIMARY KEY  (`idArea`)
) TYPE=MyISAM PACK_KEYS=0;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_block`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_block` (
  `idBlock` int(11) NOT NULL auto_increment,
  `idSubdivision` int(11) NOT NULL default '0',
  `block_name` varchar(255) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `function` varchar(100) NOT NULL default '',
  `sequence` int(5) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_block_forum`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_block_forum` (
  `idBlock` int(11) NOT NULL default '0',
  `idForum` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`idForum`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_block_group`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_block_group` (
  `idBlock` int(11) NOT NULL default '0',
  `idGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`idGroup`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_block_items`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_block_items` (
  `id` int(11) NOT NULL auto_increment,
  `idBlock` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_option`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_option` (
  `idBlock` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`idBlock`,`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_perm`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_perm` (
  `idArea` int(11) NOT NULL default '0',
  `idGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idArea`,`idGroup`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_area_subdivision`
-- 

CREATE TABLE IF NOT EXISTS `cms_area_subdivision` (
  `idSubdivision` int(11) NOT NULL auto_increment,
  `idArea` int(11) NOT NULL default '0',
  `idParentSub` int(11) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `areaWidth` varchar(10) NOT NULL default '',
  `areaType` varchar(255) NOT NULL default '',
  `margin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`idSubdivision`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_banner`
-- 

CREATE TABLE IF NOT EXISTS `cms_banner` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_banner_cat`
-- 

CREATE TABLE IF NOT EXISTS `cms_banner_cat` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `cat_name` varchar(255) NOT NULL default '',
  `cat_desc` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=0;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_banner_raw_stat`
-- 

CREATE TABLE IF NOT EXISTS `cms_banner_raw_stat` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_banner_rules`
-- 

CREATE TABLE IF NOT EXISTS `cms_banner_rules` (
  `banner_id` int(11) NOT NULL default '0',
  `item_id` int(11) NOT NULL default '0',
  `item_val` varchar(255) NOT NULL default '',
  `item_type` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`banner_id`,`item_id`,`item_val`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_content`
-- 

CREATE TABLE IF NOT EXISTS `cms_content` (
  `idContent` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('normal','block_text') NOT NULL default 'normal',
  `key1` int(11) default NULL,
  `title` varchar(100) NOT NULL default '',
  `long_desc` text NOT NULL,
  `language` varchar(100) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `publish` tinyint(1) NOT NULL default '0',
  `cancomment` tinyint(1) NOT NULL default '0',
  `pubdate` datetime default NULL,
  `expdate` datetime default NULL,
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idContent`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_content_attach`
-- 

CREATE TABLE IF NOT EXISTS `cms_content_attach` (
  `id` int(11) NOT NULL auto_increment,
  `idContent` int(11) NOT NULL default '0',
  `idAttach` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_content_dir`
-- 

CREATE TABLE IF NOT EXISTS `cms_content_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`),
  FULLTEXT KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_content_titles`
-- 

CREATE TABLE IF NOT EXISTS `cms_content_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_docs`
-- 

CREATE TABLE IF NOT EXISTS `cms_docs` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_docs_dir`
-- 

CREATE TABLE IF NOT EXISTS `cms_docs_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`),
  FULLTEXT KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_docs_info`
-- 

CREATE TABLE IF NOT EXISTS `cms_docs_info` (
  `id` int(11) NOT NULL auto_increment,
  `idd` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `sdesc` varchar(255) NOT NULL default '',
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_docs_titles`
-- 

CREATE TABLE IF NOT EXISTS `cms_docs_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_form`
-- 

CREATE TABLE IF NOT EXISTS `cms_form` (
  `idForm` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `fdesc` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`idForm`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_form_items`
-- 

CREATE TABLE IF NOT EXISTS `cms_form_items` (
  `id` int(11) NOT NULL auto_increment,
  `idForm` int(11) NOT NULL default '0',
  `idField` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `comp` tinyint(1) NOT NULL default '0',
  `ord` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_form_textarea`
-- 

CREATE TABLE IF NOT EXISTS `cms_form_textarea` (
  `idTextarea` int(11) NOT NULL auto_increment,
  `idCommon` int(11) NOT NULL default '0',
  `language` varchar(100) NOT NULL default '',
  `nameTextarea` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`idTextarea`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forum`
-- 

CREATE TABLE IF NOT EXISTS `cms_forum` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forum_access`
-- 

CREATE TABLE IF NOT EXISTS `cms_forum_access` (
  `idForum` int(11) NOT NULL default '0',
  `idMember` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idForum`,`idMember`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forum_notifier`
-- 

CREATE TABLE IF NOT EXISTS `cms_forum_notifier` (
  `id_notify` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `notify_is_a` enum('forum','thread') NOT NULL default 'forum',
  PRIMARY KEY  (`id_notify`,`id_user`,`notify_is_a`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forum_timing`
-- 

CREATE TABLE IF NOT EXISTS `cms_forum_timing` (
  `idUser` int(11) NOT NULL default '0',
  `idCourse` int(11) NOT NULL default '0',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idUser`,`idCourse`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forummessage`
-- 

CREATE TABLE IF NOT EXISTS `cms_forummessage` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_forumthread`
-- 

CREATE TABLE IF NOT EXISTS `cms_forumthread` (
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
  PRIMARY KEY  (`idThread`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_links`
-- 

CREATE TABLE IF NOT EXISTS `cms_links` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_links_dir`
-- 

CREATE TABLE IF NOT EXISTS `cms_links_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`),
  FULLTEXT KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_links_info`
-- 

CREATE TABLE IF NOT EXISTS `cms_links_info` (
  `id` int(11) NOT NULL auto_increment,
  `idl` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `sdesc` text NOT NULL,
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_links_titles`
-- 

CREATE TABLE IF NOT EXISTS `cms_links_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_media`
-- 

CREATE TABLE IF NOT EXISTS `cms_media` (
  `idMedia` int(11) NOT NULL auto_increment,
  `idFolder` int(11) NOT NULL default '0',
  `publish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `fname` varchar(255) NOT NULL default '',
  `real_fname` varchar(255) NOT NULL default '',
  `fpreview` varchar(255) NOT NULL default '',
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_media_dir`
-- 

CREATE TABLE IF NOT EXISTS `cms_media_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`),
  FULLTEXT KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_media_info`
-- 

CREATE TABLE IF NOT EXISTS `cms_media_info` (
  `id` int(11) NOT NULL auto_increment,
  `idm` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `sdesc` varchar(255) NOT NULL default '',
  `ldesc` text NOT NULL,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_media_titles`
-- 

CREATE TABLE IF NOT EXISTS `cms_media_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_news`
-- 

CREATE TABLE IF NOT EXISTS `cms_news` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_news_attach`
-- 

CREATE TABLE IF NOT EXISTS `cms_news_attach` (
  `id` int(11) NOT NULL auto_increment,
  `idNews` int(11) NOT NULL default '0',
  `idAttach` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_news_dir`
-- 

CREATE TABLE IF NOT EXISTS `cms_news_dir` (
  `id` int(11) NOT NULL auto_increment,
  `idParent` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `lev` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `idParent` (`idParent`),
  FULLTEXT KEY `path` (`path`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_news_titles`
-- 

CREATE TABLE IF NOT EXISTS `cms_news_titles` (
  `id` int(11) NOT NULL auto_increment,
  `iddir` int(11) NOT NULL default '0',
  `lang` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_news_topic`
-- 

CREATE TABLE IF NOT EXISTS `cms_news_topic` (
  `idNews` int(11) NOT NULL default '0',
  `topic_id` int(11) NOT NULL default '0',
  `main` tinyint(1) NOT NULL default '0',
  `img_align` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`idNews`,`topic_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_poll`
-- 

CREATE TABLE IF NOT EXISTS `cms_poll` (
  `poll_id` int(11) NOT NULL auto_increment,
  `question` varchar(255) NOT NULL default '',
  `poll_desc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`poll_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_poll_answer`
-- 

CREATE TABLE IF NOT EXISTS `cms_poll_answer` (
  `answer_id` int(11) NOT NULL auto_increment,
  `answer_txt` varchar(255) NOT NULL default '',
  `poll_id` int(11) NOT NULL default '0',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`answer_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_poll_vote`
-- 

CREATE TABLE IF NOT EXISTS `cms_poll_vote` (
  `poll_id` int(11) NOT NULL default '0',
  `answer_id` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`,`answer_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_sysforum`
-- 

CREATE TABLE IF NOT EXISTS `cms_sysforum` (
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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_text`
-- 

CREATE TABLE IF NOT EXISTS `cms_text` (
  `idBlock` int(11) NOT NULL default '0',
  `language` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `textof` text NOT NULL,
  PRIMARY KEY  (`idBlock`,`language`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_topic`
-- 

CREATE TABLE IF NOT EXISTS `cms_topic` (
  `id` int(11) NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `language` varchar(20) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;








-- 
-- Struttura della tabella `cms_blocktype`
-- 

CREATE TABLE IF NOT EXISTS `cms_blocktype` (
  `name` varchar(255) NOT NULL default '',
  `folder` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `cms_blocktype`
-- 

INSERT INTO `cms_blocktype` VALUES ('menu', '', '_BLK_MENU');
INSERT INTO `cms_blocktype` VALUES ('login', '', '_BLK_LOGIN');
INSERT INTO `cms_blocktype` VALUES ('news', '', '_BLK_NEWS');
INSERT INTO `cms_blocktype` VALUES ('text', '', '_BLK_TEXT');
INSERT INTO `cms_blocktype` VALUES ('news_sel', 'news', '_BLK_NEWS_SEL');
INSERT INTO `cms_blocktype` VALUES ('media', '', '_BLK_MEDIA');
INSERT INTO `cms_blocktype` VALUES ('media_sel', 'media', '_BLK_MEDIA_SEL');
INSERT INTO `cms_blocktype` VALUES ('links', '', '_BLK_LINKS');
INSERT INTO `cms_blocktype` VALUES ('docs', '', '_BLK_DOCS');
INSERT INTO `cms_blocktype` VALUES ('docs_sel', 'docs', '_BLK_DOCS_SEL');
INSERT INTO `cms_blocktype` VALUES ('content', '', '_BLK_CONTENT');
INSERT INTO `cms_blocktype` VALUES ('alerts', '', '_BLK_ALERTS');
INSERT INTO `cms_blocktype` VALUES ('guestbook', '', '_BLK_GUESTBOOK');
INSERT INTO `cms_blocktype` VALUES ('subscription', '', '_BLK_SUBSCRIPTION');
INSERT INTO `cms_blocktype` VALUES ('profile', '', '_BLK_PROFILE');
INSERT INTO `cms_blocktype` VALUES ('forum', '', '_BLK_FORUM');
INSERT INTO `cms_blocktype` VALUES ('banners', '', '_BLK_BANNERS');
INSERT INTO `cms_blocktype` VALUES ('feedback', '', '_BLK_FEEDBACK');
INSERT INTO `cms_blocktype` VALUES ('mygroup', '', '_BLK_MYGROUP');
INSERT INTO `cms_blocktype` VALUES ('chat', '', '_BLK_CHAT');
INSERT INTO `cms_blocktype` VALUES ('submitnews', '', '_BLK_SUBMITNEWS');
INSERT INTO `cms_blocktype` VALUES ('docs_small', 'docs', '_BLK_DOCS_SMALL');
INSERT INTO `cms_blocktype` VALUES ('docs_sel_small', 'docs', '_BLK_DOCS_SEL_SMALL');
INSERT INTO `cms_blocktype` VALUES ('news_small', 'news', '_BLK_NEWS_SMALL');
INSERT INTO `cms_blocktype` VALUES ('news_sel_small', 'news', '_BLK_NEWS_SEL_SMALL');
INSERT INTO `cms_blocktype` VALUES ('poll', '', '_BLK_POLL');
INSERT INTO `cms_blocktype` VALUES ('chat_teleskill', 'chat', '_BLK_CHAT_TELESKILL');

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_menu`
-- 

CREATE TABLE IF NOT EXISTS `cms_menu` (
  `idMenu` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sequence` int(3) NOT NULL default '0',
  PRIMARY KEY  (`idMenu`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=3 ;

-- 
-- Dump dei dati per la tabella `cms_menu`
-- 

INSERT INTO `cms_menu` VALUES (1, '_GENERAL_CMS', 'general.gif', 1);
INSERT INTO `cms_menu` VALUES (2, '_STAT_CMS', 'stat.gif', 2);

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_menu_under`
-- 

CREATE TABLE IF NOT EXISTS `cms_menu_under` (
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
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=24 ;

-- 
-- Dump dei dati per la tabella `cms_menu_under`
-- 

INSERT INTO `cms_menu_under` VALUES (9, 1, 'mantopic', '_MANTOPIC', 'mantopic', 'view', 1, 'class.mantopic.php', 'Module_Mantopic');
INSERT INTO `cms_menu_under` VALUES (10, 1, 'manpage', '_MANPAGE', 'manpage', 'view', 2, 'class.manpage.php', 'Module_Manpage');
INSERT INTO `cms_menu_under` VALUES (11, 1, 'news', '_NEWS', 'news', 'view', 3, 'class.news.php', 'Module_News');
INSERT INTO `cms_menu_under` VALUES (12, 1, 'media', '_MEDIA', 'media', 'view', 4, 'class.media.php', 'Module_Media');
INSERT INTO `cms_menu_under` VALUES (13, 1, 'forum', '_FORUM', 'forum', 'view', 5, 'class.forum.php', 'Module_Forum');
INSERT INTO `cms_menu_under` VALUES (14, 1, 'banners', '_BANNER_CAT', 'viewcat', 'view', 6, '', '');
INSERT INTO `cms_menu_under` VALUES (15, 1, 'banners', '_BANNER', 'banners', 'view', 7, 'class.banners.php', 'Module_Banners');
INSERT INTO `cms_menu_under` VALUES (16, 1, 'poll', '_POLL', 'poll', 'view', 8, '', '');
INSERT INTO `cms_menu_under` VALUES (17, 1, 'docs', '_DOCS', 'docs', 'view', 9, 'class.docs.php', 'Module_Docs');
INSERT INTO `cms_menu_under` VALUES (18, 1, 'links', '_LINKS', 'links', 'view', 10, 'class.links.php', 'Module_Links');
INSERT INTO `cms_menu_under` VALUES (19, 1, 'form', '_FORM', 'form', 'view', 11, 'class.form.php', 'Module_Form');
INSERT INTO `cms_menu_under` VALUES (20, 2, 'stats', '_STATS_MAIN', 'stats', 'view', 1, 'class.stats.php', 'Module_Stats');
INSERT INTO `cms_menu_under` VALUES (21, 2, 'stats', '_STATS_DETAILS', 'statsdetails', 'view', 2, '', '');
INSERT INTO `cms_menu_under` VALUES (22, 2, 'stats', '_STATS_TEMPORAL', 'statstemporal', 'view', 3, '', '');
INSERT INTO `cms_menu_under` VALUES (23, 1, 'content', '_CONTENT', 'content', 'view', 12, 'class.content.php', 'Module_Content');

-- --------------------------------------------------------

-- 
-- Struttura della tabella `cms_setting`
-- 

CREATE TABLE IF NOT EXISTS `cms_setting` (
  `param_name` varchar(255) NOT NULL default '',
  `param_value` varchar(255) NOT NULL default '',
  `value_type` varchar(255) NOT NULL default 'string',
  `max_size` int(3) NOT NULL default '255',
  `regroup` int(5) NOT NULL default '0',
  `sequence` int(5) NOT NULL default '0',
  `param_load` tinyint(1) NOT NULL default '1',
  `hide_in_modify` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`param_name`)
) TYPE=MyISAM;

-- 
-- Dump dei dati per la tabella `cms_setting`
-- 

INSERT INTO `cms_setting` VALUES ('url', '', 'string', 255, 0, 0, 1, 0);
INSERT INTO `cms_setting` VALUES ('ttlSession', '20000', 'int', 6, 0, 2, 1, 0);
INSERT INTO `cms_setting` VALUES ('defaultCmsTemplate', 'standard', 'template', 255, 0, 3, 1, 0);
INSERT INTO `cms_setting` VALUES ('defaultCmsLanguage', 'italian', 'language', 255, 0, 4, 1, 0);
INSERT INTO `cms_setting` VALUES ('over_menu', '1', 'int', 1, 0, 0, 1, 1);
INSERT INTO `cms_setting` VALUES ('cms_admin_mail', '', 'string', 255, 0, 1, 1, 0);
INSERT INTO `cms_setting` VALUES ('cms_previewimg_maxsize', '100', 'int', 4, 0, 5, 1, 0);
INSERT INTO `cms_setting` VALUES ('cms_nl_sendpercycle', '1', 'int', 4, 1, 0, 1, 0);
INSERT INTO `cms_setting` VALUES ('cms_nl_sendpause', '20', 'int', 3, 1, 1, 1, 0);
INSERT INTO `cms_setting` VALUES ('anonymous_comment', 'off', 'enum', 3, 0, 6, 1, 0);
INSERT INTO `cms_setting` VALUES ('cms_version', '3.0', 'string', 4, 0, 10, 1, 1);
INSERT INTO `cms_setting` VALUES ('use_mod_rewrite', 'on', 'enum', 3, 0, 8, 1, 0);
INSERT INTO `cms_setting` VALUES ('grpsel_type', 'group', 'grpsel_chooser', 20, 0, 9, 1, 0);
INSERT INTO `cms_setting` VALUES ('forum_as_table', 'on', 'enum', 3, 3, 1, 1, 0);
INSERT INTO `cms_setting` VALUES ('visuItem', '20', 'int', 11, 2, 1, 1, 0);
INSERT INTO `cms_setting` VALUES ('reload_perm_after', '1133973338', 'int', 11, 0, 0, 1, 1);
INSERT INTO `cms_setting` VALUES ('default_banner_cat', '3', 'bancat_chooser', 11, 2, 2, 1, 0);
INSERT INTO `cms_setting` VALUES ('vop_show_date', '1', 'check', 1, 2, 3, 1, 0);
INSERT INTO `cms_setting` VALUES ('vop_show_navigation', '1', 'check', 1, 2, 4, 1, 0);
INSERT INTO `cms_setting` VALUES ('vop_show_banner', '1', 'check', 1, 2, 5, 1, 0);
INSERT INTO `cms_setting` VALUES ('vop_show_languages', '1', 'check', 1, 2, 6, 1, 0);
INSERT INTO `cms_setting` VALUES ('vop_show_macroarea', '1', 'check', 1, 2, 7, 1, 0);
INSERT INTO `cms_setting` VALUES ('last_auto_publish', '1134056660', 'int', 11, 0, 0, 1, 1);
INSERT INTO `cms_setting` VALUES ('pathforum', 'forum/', 'string', 255, 3, 2, 1, 0);
