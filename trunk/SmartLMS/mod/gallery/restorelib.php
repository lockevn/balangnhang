<?php // $Id: restorelib.php,v 1.2 2007/01/05 03:17:40 mark-nielsen Exp $
/**
 * Gallery Module Restore library
 *
 * @author Mark Nielsen
 * @version $Id: restorelib.php,v 1.2 2007/01/05 03:17:40 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/

global $CFG;

// this makes the GalleryCoreApi available in all the functions
require_once($CFG->dirroot.'/mod/gallery/lib.php');
gallery_init();
gallery_r();

/**
 * Restores the moodle gallery module intance.  Calls {@link gallery_restore_albums()}
 *
 * @param object $mod module object
 * @param object $restore restore object
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_restore_mods($mod,$restore) {
    global $CFG;

    $status = true;

    //Get record from backup_ids
    $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

    if ($data) {
        //Now get completed xmlized object
        $info = $data->info;
        //traverse_xmlize($info);                                                              //Debug
        //print_object ($GLOBALS['traverse_array']);                                           //Debug
        //$GLOBALS['traverse_array']="";                                                       //Debug
        
        // restore the gallery record
        $instance = new stdClass;
        $instance->course = $restore->course_id;
        $instance->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
        $instance->permissions = backup_todb($info['MOD']['#']['PERMISSIONS']['0']['#']);
        $instance->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
        $instance->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
        
        $mod->newid = insert_record('gallery', $instance);
        $mod->oldalbumid = backup_todb($info['MOD']['#']['ALBUMID']['0']['#']);  // Get the oldalbumid; used later
        
        //Do some output
        echo '<li>'.get_string('modulename','gallery').' "'.format_string(stripslashes($instance->name),true).'"</li>';
        backup_flush(300);
        
        if ($mod->newid) {
            //We have the newid, update backup_ids
            backup_putid($restore->backup_unique_code,$mod->modtype,
                         $mod->id, $mod->newid);
            
            // restore the gallery albums/images/comments
            $status = gallery_restore_albums($mod,$info['MOD']['#']['ALBUMS']['0']['#']['ALBUM'],$restore);
        }
    }
    
    gallery_r();
    return $status;
}

/**
 * Restores gallery albums and images.  Also calls {@link gallery_restore_item_comments()}
 * for each album and image to restore user comments.
 *
 * @param int the id of the moodle gallery module
 * @param array xmlized section with the albums (which includes images and comments)
 * @param object $restore the restore object
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_restore_albums($mod, $albums, $restore) {
    global $CFG;
    
    $status = true;
    
    // figure out if we should include user data
    $userdata = restore_userdata_selected($restore, 'gallery', $mod->id);
    
    // loop through all of the albums
    for($a=0; $a<sizeof($albums); $a++) {
        $album = $albums[$a];
        
        $albuminfo = new stdClass;
        
        $oldid = backup_todb($album['#']['ID']['0']['#']);
        
        // build up an album object
        $olduserid = backup_todb($album['#']['OWNERID']['0']['#']);
        if ($restore->users) {
            $newuserid = backup_getid($restore->backup_unique_code,'user', $olduserid);            
            if (!$albuminfo->ownerid = gallery_map_user_to_gallery($newuserid->new_id)) {
                return false;
            }
        }
        gallery_r();
        
        $albuminfo->parentid = backup_todb($album['#']['PARENT']['0']['#']);
        $albuminfo->directory = backup_todb($album['#']['DIRECTORY']['0']['#']);
        $albuminfo->title = backup_todb($album['#']['TITLE']['0']['#']);
        $albuminfo->summary = backup_todb($album['#']['SUMMARY']['0']['#']);
        $albuminfo->description = backup_todb($album['#']['DESCRIPTION']['0']['#']);
        $albuminfo->keywords = backup_todb($album['#']['KEYWORDS']['0']['#']);

        if ($oldid != $mod->oldalbumid) {
            // Not our linked album, so must be a sub-album; get new parentid
            $backupdata = backup_getid($restore->backup_unique_code,'gallery_entity',$albuminfo->parentid);
            
            if (!empty($backupdata->new_id)) {
                $albuminfo->parentid = $backupdata->new_id;
            } else {
                return false;
            }
        } else {
            // This is main album for this instance.  
            // Parent is 0 (aka root) and we use our moodle_modid directory
            $albuminfo->parentid = 0;
            $albuminfo->directory = 'moodle_'.$mod->newid;
        }
        
        // OLD BUG: GalleryCoreApi::releaseAllLocks();  // very important! Don't remove.  Sometimes locks are not removed *shrug*
        $albumid = gallery_create_album($albuminfo, $restore->course_id);

        //We have the newid, update backup_ids (restore logs will use it!!)
        backup_putid($restore->backup_unique_code,'gallery_entity', $oldid, $albumid);
        
        if ($oldid == $mod->oldalbumid) {
            // This is the main album, so link it to our gallery module instance
            $instance     = new stdClass;
            $instance->id = $mod->newid;
            $instance->albumid = $albumid;
            if (update_record('gallery', $instance)) {
                // notify('update success');                                  // DEBUG
            } else {
                // notify('update failed');                                   // DEBUG
                return false;
            }
        }

        if ($userdata and !empty($album['#']['COMMENTS']['0']['#']['COMMENT'])) {
            // restore the comments for this album
            $status = gallery_restore_item_comments($album['#']['COMMENTS']['0']['#']['COMMENT'], $restore, $albumid);
        }
        
        if ($status and !empty($album['#']['IMAGES']['0']['#']['IMAGE'])) {
            // restore images
            if (!$status = gallery_restore_images($mod, $restore, $album['#']['IMAGES']['0']['#']['IMAGE'], $albumid)) {
                break;
            }            
        }
    }
    // echo '<br>End of Album Status: '.$status.'<br>';  //DEBUG
    
    gallery_r();
    return $status;
}

/**
 * This function restores comments for albums and images
 *
 * @param array $comments xmlized section with the comments
 * @param object $restore the restore object
 * @param int $parentid The id of the comment's parent
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_restore_item_comments($comments, $restore, $parentid) {
    $status = true;
    
    if (empty($comments)) {
        // not everything has a comment
        return $status;
    }
    
    // get the comment class
    GalleryCoreApi::requireOnce('modules/comment/classes/GalleryComment.class');
    
    // iterate through the comments
    for($c=0; $c<sizeof($comments); $c++) {
        $comment = $comments[$c];
        
        // get a comment class
        list ($ret, $comm) = GalleryCoreApi::newFactoryInstance('GalleryEntity', 'GalleryComment');
        if ($ret) {
            $status = false;
            break;
        }
        
        /*  could use the following instead of passing the parentid to this function...
            seems easier to pass the id :\
        
        $originalparentid = backup_todb($comment['#']['PARENT']['0']['#']);
        echo $originalparentid;
        $parentid = backup_getid($restore->backup_unique_code,'gallery_entity', $originalparentid);
        print_object($parentid);*/
        
        // start the creation
        $ret = $comm->create($parentid);
        if ($ret) {
            //echo 'create comment failed<br>'; // DEBUG
            //echo $ret->getAsHtml();           // DEBUG
            $status = false;
            break;
        }
        
        $olduserid = backup_todb($comment['#']['USERID']['0']['#']);
        $newuserid = backup_getid($restore->backup_unique_code,'user', $olduserid);
        if (!$galleryuserid = gallery_map_user_to_gallery($newuserid->new_id)) {
            $status = false;
            break;
        }
        
        // add all the necessary info to the comment
        $comm->setCommenterId($galleryuserid);
        $comm->setHost(GalleryUtilities::getRemoteHostAddress());
        $comm->setSubject(backup_todb($comment['#']['SUBJECT']['0']['#']));
        $comm->setComment(backup_todb($comment['#']['COMMENT']['0']['#']));
        $comm->setDate(backup_todb($comment['#']['DATE']['0']['#']));

        $ret = $comm->save();
        
        if ($ret) {
            $status = false;
            break;
        }
    }

    gallery_r();
    return $status;
}  

/**
 * Restores images to albums.
 *
 * @param object $mod The instance of the gallery module that is being restored
 * @param object $restore The restore object
 * @param array $images xmlized section with the images and their comments
 * @param int $albumid The id of the album to which the image is added
 * @return boolean Success/Fail
 * @author Mark Nielsen
 **/
function gallery_restore_images($mod, $restore, $images, $albumid) {
    global $CFG;
    
    $status = true;
    
    if (!sizeof($images)) {
        return $status; // no images to restore
    }
    
    // use this to determine if comments will be backed up or not
    $userdata = restore_userdata_selected($restore, 'gallery', $mod->id);
    
    // need to lock the album before images can be added
    list ($ret, $lockid) = GalleryCoreApi::acquireWriteLock($albumid);

/// Run through the images and add them to the album
    for ($i=0; $i<sizeof($images); $i++) {
        $image = $images[$i];
        
        // Debug ?
        /*
        echo '<br>';
        echo $CFG->dataroot.'/temp/backup/'.$restore->backup_unique_code.'/mod_gallery_backup'.$image['#']['PATH']['0']['#'] . '<br>';
        echo $image['#']['NAME']['0']['#'] . '<br>'; 
        echo $image['#']['TITLE']['0']['#'] . '<br>'; 
        echo $image['#']['SUMMARY']['0']['#'] . '<br>';
        echo $image['#']['DESCRIPTION']['0']['#'] . '<br>'; 
        echo $image['#']['MIMETYPE']['0']['#'] . '<br>'; 
        echo $albumid . '<br>'; 
        */
                        
        list($ret, $newimage) = GalleryCoreApi::addItemToAlbum( $CFG->dataroot.'/temp/backup/'.$restore->backup_unique_code.'/mod_gallery_backup'.$image['#']['PATH']['0']['#'],
                                                                backup_todb($image['#']['NAME']['0']['#']), 
                                                                backup_todb($image['#']['TITLE']['0']['#']), 
                                                                backup_todb($image['#']['SUMMARY']['0']['#']),
                                                                backup_todb($image['#']['DESCRIPTION']['0']['#']), 
                                                                backup_todb($image['#']['MIMETYPE']['0']['#']), 
                                                                $albumid, 
                                                                false);
        if ($ret) {
            //echo '<br> Image add failed <br>';    // DEBUG
            //echo $ret->getAsHtml();               // DEBUG
            $status = false;
        } else {        
            //We have the newid, update backup_ids (restore logs will use it!!)
            backup_putid($restore->backup_unique_code,'gallery_entity', backup_todb($image['#']['ID']['0']['#']), $newimage->getId());
        }
        
        if ($status and $userdata and !empty($image['#']['COMMENTS']['0']['#']['COMMENT'])) {
            // restore image comments
            $status = gallery_restore_item_comments($image['#']['COMMENTS']['0']['#']['COMMENT'], $restore, $newimage->getId());
        }
        
        if (!$status) {
            // echo '<br>End of image loop Status: '.$status.'<br>'; // DEBUG
            break;
        }
    }
    
    // release the lock on the album
    GalleryCoreApi::releaseLocks($lockid);
    
    return $status;
}      
?>