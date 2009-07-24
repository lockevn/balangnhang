<?php // $Id: backuplib.php,v 1.2 2007/01/05 03:17:38 mark-nielsen Exp $
/**
 * Gallery Module Backup library
 *
 * @author Mark Nielsen
 * @version $Id: backuplib.php,v 1.2 2007/01/05 03:17:38 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/

global $CFG;

// this makes the GalleryCoreApi available in all the functions
require_once($CFG->dirroot.'/mod/gallery/lib.php');
gallery_init();
gallery_r();

/**
 * This function executes all the backup procedure about this mod
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_backup_mods($bf, $preferences) {

    global $CFG;

    $status = true;

    //Iterate over gallery table
    $instances = get_records('gallery', 'course', $preferences->backup_course, 'id');
    if ($instances) {
        foreach ($instances as $instance) {
            if (backup_mod_selected($preferences,'gallery',$instance->id)) {
                $status = gallery_backup_one_mod($bf,$preferences,$instance);
            }
        }
    }
    return $status;  
}

/**
 * Backup gallery table record contents (executed from {@link gallery_backup_mods()})
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @param object $instance moodle gallery instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_backup_one_mod($bf,$preferences,$instance) {

    global $CFG;

    // backup gallery table //
    if (is_numeric($instance)) {
        $instance = get_record('gallery','id',$instance);
    }

    $status = true;

    //Start mod
    fwrite ($bf,start_tag('MOD',3,true));
    //Print lesson data
    fwrite ($bf,full_tag('ID',4,false,$instance->id));
    fwrite ($bf,full_tag('MODTYPE',4,false,'gallery'));
    fwrite ($bf,full_tag('NAME',4,false,$instance->name));
    fwrite ($bf,full_tag('ALBUMID',4,false,$instance->albumid));
    fwrite ($bf,full_tag('PERMISSIONS',4,false,$instance->permissions));
    fwrite ($bf,full_tag('TIMECREATED',4,false,$instance->timecreated));
    fwrite ($bf,full_tag('TIMEMODIFIED',4,false,$instance->timemodified));
    
    $status = gallery_backup_albums($bf,$preferences,$instance);

    if ($status) {
        $status = fwrite ($bf,end_tag('MOD',3,true));
    }

    return $status;

}


/**
 * Backup the gallery albums associated with the module instance (executed from {@link gallery_backup_one_mod})
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @param object $instance moodle gallery instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_backup_albums($bf,$preferences,$instance) {
    fwrite ($bf,start_tag('ALBUMS',4,true));
    
    $status = gallery_backup_one_album($bf,$preferences,$instance);
    
    if ($status) {
        $status = fwrite ($bf,end_tag('ALBUMS',4,true));
    }
    
    return $status;
}

/**
 * Backup a single gallery album (executed from {@link gallery_backup_albums})
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @param object $instance moodle gallery instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_backup_one_album($bf,$preferences,$instance) {
    
    global $CFG;
    
    $status = true;
    //gallery_init();
    list($ret, $album) = GalleryCoreApi::loadEntitiesById($instance->albumid);
    gallery_r();
    
    if ($ret) {
        return false;
    }

    if (!$userid = gallery_map_to_moodle_user($album->getownerid())) {
        return false;
    }
    
    fwrite ($bf,start_tag('ALBUM',5,true));
    fwrite ($bf,full_tag('ID',6,false,$album->getid()));
    fwrite ($bf,full_tag('OWNERID',6,false,$userid));
    fwrite ($bf,full_tag('PARENT',6,false,$album->getparentid()));
    fwrite ($bf,full_tag('DIRECTORY',6,false,$album->getpathcomponent()));
    fwrite ($bf,full_tag('TITLE',6,false,$album->gettitle()));
    fwrite ($bf,full_tag('SUMMARY',6,false,$album->getsummary()));
    fwrite ($bf,full_tag('DESCRIPTION',6,false,$album->getdescription()));
    $status = fwrite ($bf,full_tag('KEYWORDS',6,false,$album->getkeywords()));    
    
    if ($status) {
        // the comments if needed
        if (backup_userdata_selected($preferences, 'gallery', $instance->id)) {
            $status = gallery_backup_item_comments($bf,$preferences,$instance->albumid, 6);
        }
        
        if ($status) {
            // create our backup directory to store the album folders and images
            $status = check_dir_exists($CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/mod_gallery_backup/', true);
            
            if ($status) {
                // create the album directory
                list($ret, $logicalpath) = $album->fetchlogicalpath();
                $status = check_dir_exists($CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/mod_gallery_backup'.$logicalpath, true);
                if ($status) {
                    // backup any images associated with the album
                    list($status, $subalbumids) = gallery_backup_album_images($bf,$preferences,$instance);
                }
            }
        }
        
        if ($status) {
            $status = fwrite ($bf,end_tag('ALBUM',5,true));

            if ($status) {
                // backup sub albums of this album
                $count = 0;
                foreach ($subalbumids as $subalbumid) {
                    $instance->albumid = $subalbumid;  // trick it >:)
                    if (!$status = gallery_backup_one_album($bf,$preferences,$instance)) {
                        break;
                    }
                    $count++;
                }
            }
        }
    }
    
    return $status;
    
}

/**
 * Backup images in an album (executed from {@link gallery_backup_one_album})
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @param object $instance moodle gallery instance
 * @return array array(boolean status, array of sub-album ids)
 * @author Mark Nielsen
 **/
function gallery_backup_album_images($bf,$preferences,$instance) {
    
    global $CFG;

    $status      = true;
    $subalbumids = array();
    $backupdir   = $CFG->dataroot.'/temp/backup/'.$preferences->backup_unique_code.'/mod_gallery_backup';

/// get the album and its subitems
    list($ret, $album)      = GalleryCoreApi::loadEntitiesById($instance->albumid);
    list($ret, $subitemids) = GalleryCoreApi::fetchChildItemIds($album);
    gallery_r();
    
    fwrite ($bf,start_tag('IMAGES',6,true));
    
    foreach ($subitemids as $subitemid) {
        list($ret, $subitem) = GalleryCoreApi::loadEntitiesById($subitemid);
        
        if ($ret) {
            $status = false;
            break;
        }
        
        if ($subitem->getentitytype() == 'GalleryPhotoItem') {  // is there a constant I could use for GalleryPhotoItem?
            fwrite ($bf,start_tag('IMAGE',7,true));
            fwrite ($bf,full_tag('ID',8,false,$subitem->getId()));
            fwrite ($bf,full_tag('PARENT',8,false,$subitem->getparentid()));
            fwrite ($bf,full_tag('NAME',8,false,$subitem->getpathcomponent()));
            fwrite ($bf,full_tag('TITLE',8,false,$subitem->gettitle()));
            fwrite ($bf,full_tag('SUMMARY',8,false,$subitem->getsummary()));
            fwrite ($bf,full_tag('DESCRIPTION',8,false,$subitem->getdescription()));
            fwrite ($bf,full_tag('KEYWORDS',8,false,$subitem->getkeywords()));
            fwrite ($bf,full_tag('MIMETYPE',8,false,$subitem->getmimetype()));
        
        /// copy over image to backup dir
            
            list($ret, $fullpath)    = $subitem->fetchpath();
            list($ret, $logicalpath) = $subitem->fetchlogicalpath();
            
            // save the path so we can get it back in restore
            fwrite ($bf,full_tag('PATH',8,false,$logicalpath));
            
            $status = backup_copy_file($fullpath, $backupdir.$logicalpath);
        
        /// backup comments if needed
            if ($status and backup_userdata_selected($preferences, 'gallery', $instance->id)) {
                $status = gallery_backup_item_comments($bf,$preferences,$subitemid, 8);
            }
            
            if ($status) {
                $status = fwrite ($bf,end_tag('IMAGE',7,true));
            } else {
                break;
            }
                                        
        } else if ($subitem->getentitytype() == 'GalleryAlbumItem') {
            // add to album array
            $subalbumids[] = $subitemid;
        }
    }
    
    if ($status) {
        $status = fwrite ($bf,end_tag('IMAGES',6,true));
    }
    
    return array($status, $subalbumids);
}

/**
 * Backup the comments of an item (executed from 
 * {@link gallery_backup_album_images} and {@link gallery_backup_one_album})
 *
 * @param resource_handle $bf The backup file
 * @param object $preferences backup preferences
 * @param object $instance moodle gallery instance
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_backup_item_comments($bf,$preferences,$itemid, $indent=6) {
    
    $status = true;
    
    // gallery comment module class
    GalleryCoreApi::requireOnce('modules/comment/classes/GalleryCommentHelper.class');
    
    list($ret, $comments) = GalleryCommentHelper::fetchComments($itemid);
    gallery_r();
    
    fwrite ($bf,start_tag('COMMENTS',$indent,true));
    $indent++;    
    foreach ($comments as $comment) {
        // map the gallery2 user to the moodle user
        if (!$userid = gallery_map_to_moodle_user($comment->getcommenterid())) {
            $status = false;
            break;
        }
        
        fwrite ($bf,start_tag('COMMENT',$indent,true));
        $indent++;
        fwrite ($bf,full_tag('PARENT',$indent,false,$comment->getparentid()));
        fwrite ($bf,full_tag('USERID',$indent,false,$userid));
        fwrite ($bf,full_tag('SUBJECT',$indent,false,$comment->getsubject()));
        fwrite ($bf,full_tag('COMMENT',$indent,false,$comment->getcomment()));
        fwrite ($bf,full_tag('DATE',$indent,false,$comment->getdate()));
        $indent--;
        fwrite ($bf,end_tag('COMMENT',$indent,true));
    }
    if ($status) {
        $indent--;
        $status = fwrite ($bf,end_tag('COMMENTS',$indent,true));
    }
    
    return $status;
}

/**
 * Return an array of info (name,value) for a course for the moodle gallery module
 *
 * @param int $course Id of the course being backed up
 * @param boolean $user_data Backup up user data Yes/No
 * @param string $backup_unique_code The unique backup code
 * @param array $instances An array of moodle gallery module instances (not complete instances I think...)
 * @return array Has counts of items to be backed up
 * @author Mark Nielsen
 **/
function gallery_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
    if (!empty($instances) && is_array($instances) && count($instances)) {
        $info = array();
        foreach ($instances as $id => $instance) {
            $info += gallery_check_backup_mods_instances($instance,$backup_unique_code);
        }
        return $info;
    }
    
    //First the course data
    $info[0][0] = get_string('modulenameplural','gallery');
    $info[0][1] = gallery_count_by_course($course);

    // this will get all the album, image, and comment counts
    $counts = gallery_item_count_by_course($course);

    // albums
    $info[1][0] = get_string('albums','gallery');
    $info[1][1] = $counts->albums;

    // images
    $info[2][0] = get_string('images','gallery');
    $info[2][1] = $counts->images;
    
    //Now, if requested, the user_data
    if ($user_data) {
        // comments
        $info[3][0] = get_string('comments','gallery');
        $info[3][1] = $counts->comments;
    } else {
        notify('no user data in gallery_check_backup_mods');
    }
    return $info;
}

/**
 * Return an array of info (name,value) for a single instance of the moodle gallery module
 *
 * @param string $backup_unique_code The unique backup code
 * @param object $instance An instance of moodle gallery module (not complete instances I think...)
 * @return array Has counts of items to be backed up
 * @author Mark Nielsen
 **/
function gallery_check_backup_mods_instances($instance,$backup_unique_code) {
    //First the course data
    $info[$instance->id.'0'][0] = '<strong>'.$instance->name.'</strong>';
    $info[$instance->id.'0'][1] = '';

    // this will keep track of a running total
    $counts = new stdClass;
    $counts->albums = 0;
    $counts->images = 0;
    $counts->comments = 0;
    
    if ($albumid = get_field('gallery', 'albumid', 'id', $instance->id)) {
        $counts = gallery_item_count($albumid, $counts);
    }
    // albums
    $info[$instance->id.'1'][0] = get_string('albums','gallery');
    $info[$instance->id.'1'][1] = $counts->albums;

    // images
    $info[$instance->id.'2'][0] = get_string('images','gallery');
    $info[$instance->id.'2'][1] = $counts->images;
    
    //Now, if requested, the user_data
    if ($instance->userdata) {
        // comments
        $info[$instance->id.'3'][0] = get_string('comments','gallery');
        $info[$instance->id.'3'][1] = $counts->comments;
    }
    
    return $info;
}

/**
 * Return a content encoded to support interactivities linking. Every module
 * should have its own. They are called automatically from the backup procedure.
 *
 * @param string $content Content to be encoded
 * @param object $preferences ? Not sure if it is an object
 * @return void
 * @author Mark Nielsen
 * @todo Finish documenting this function
 **/
function gallery_encode_content_links ($content,$preferences) {

    global $CFG;

    $base = preg_quote($CFG->wwwroot,'/');

    //Link to the list of lessons
    $buscar="/(".$base."\/mod\/gallery\/index.php\?id\=)([0-9]+)/";
    $result= preg_replace($buscar,'$@GALLERYINDEX*$2@$',$content);

    //Link to lesson view by moduleid
    $buscar="/(".$base."\/mod\/gallery\/view.php\?id\=)([0-9]+)/";
    $result= preg_replace($buscar,'$@GALLERYVIEWBYID*$2@$',$result);

    return $result;
}

/**
 * Get the number of gallery instances in a course
 *
 * @param int $courseid The id of the course in question
 * @return int Number of gallery instances in the given course
 * @author Mark Nielsen
 **/
function gallery_count_by_course ($courseid) {

    global $CFG;

    return count(get_records('gallery', 'course', $courseid, '', 'id, course'));
}

/**
 * Returns an object with item counts for the gallery module for a whole course
 *
 * @param int $courseid The id of the course in question
 * @return object With the number of albums, images, and comments in the course
 * @author Mark Nielsen
 **/
function gallery_item_count_by_course($courseid) {
    $instances = get_records('gallery', 'course', $courseid);
    
    $counts = new stdClass;
    $counts->albums = 0;
    $counts->images = 0;
    $counts->comments = 0;
    
    foreach ($instances as $instance) {
        if ($instance->albumid) {
            $counts = gallery_item_count($instance->albumid, $counts);
        }
    }
    
    return $counts;
}

/**
 * Returns an object with item counts for gallery album
 *
 * @param int $albumid Id of the gallery album
 * @param object $counts Has the number of albums, images, and comments
 * @return object With the number of albums, images, and comments
 * @author Mark Nielsen
 **/
function gallery_item_count($albumid, $counts) {
    
    GalleryCoreApi::requireOnce('modules/comment/classes/GalleryCommentHelper.class');
    gallery_r();
    
    $counts->albums++;
    
    // comment count for album
    list($ret, $comments) = GalleryCommentHelper::fetchComments($albumid);
    $counts->comments += count($comments);
    
    // load the album and get the children
    list($ret, $album) = GalleryCoreApi::loadEntitiesById($albumid);
    list($ret, $subitemids) = GalleryCoreApi::fetchChildItemIds($album);
    
    foreach($subitemids as $subitemid) {
        list($ret, $subitem) = GalleryCoreApi::loadEntitiesById($subitemid);
        
        if ($subitem->getentitytype() == 'GalleryPhotoItem') { 
            $counts->images++;
            // comment count for the image
            list($ret, $comments) = GalleryCommentHelper::fetchComments($subitemid);
            $counts->comments += count($comments);
        } else if ($subitem->getentitytype() == 'GalleryAlbumItem') {
            $counts = gallery_item_count($subitemid, $counts);
        }
    }
    gallery_r();
    return $counts;
}

/**
 * Given a gallery user id, get the moodle user id
 *
 * @param int $galleryuserid Id of the gallery user
 * @return int The id of the moodle user
 * @author Mark Nielsen
 **/
function gallery_map_to_moodle_user($galleryuserid) {
    list($ret, $g2user) = GalleryCoreApi::loadEntitiesById($galleryuserid);
    if ($ret) {
        return false;
    }
    gallery_r();
    
    return get_field('user', 'id', 'username', $g2user->getusername());
}

?>