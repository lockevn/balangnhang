# This file contains a complete database schema for all the 
# tables used by this block, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

#
# Table structure for table `block_my_notes`
#

CREATE TABLE prefix_block_my_notes (
 id SERIAL PRIMARY KEY,
 userid INTEGER NOT NULL default '0',
 text text NOT NULL default '',
 priority enum('1','2','3') NOT NULL default '1',	
 last_update datetime NOT NULL;
);
