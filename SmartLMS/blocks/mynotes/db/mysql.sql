# This file contains a complete database schema for all the 
# tables used by this block, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

# --------------------------------------------------------

#
# Table structure for table `prefix_block_my_notes`
#
CREATE TABLE `mdl_block_mynotes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `priority` enum('1','2','3') NOT NULL default '1',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Personal notes';