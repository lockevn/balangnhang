<?php //$Id: restorelib.php,v 1.1 2008/10/24 11:47:53 dhara01 Exp $
    //This php script contains all the stuff to backup/restore
    //dimdim mods

/*
 **************************************************************************
 *                                                                        *
 *               DDDDD   iii                 dd  iii                      *
 *               DD  DD      mm mm mmmm      dd      mm mm mmmm           *
 *               DD   DD iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *               DD   DD iii mmm  mm  mm dd  dd  iii mmm  mm  mm          *
 *               DDDDDD  iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *                                                                        *
 *																		  *
 *                  Visit us at http://www.dimdim.com                     *
 *                  The Friendly Open Source Web Meeting                  *
 **************************************************************************
 **************************************************************************
 * NOTICE OF COPYRIGHT													  *
 *																		  *
 * Copyright (C) 2007													  *
 *																		  *
 * This program is free software; you can redistribute it and/or modify   *
 * it under the terms of the GNU General Public License as published by   *
 * the Free Software Foundation; either version 2 of the License, or      *
 * (at your option) any later version.					                  *
 *                                                                        *
 * This program is distributed in the hope that it will be useful,        *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 * GNU General Public License for more details:                           *
 *                                                                        *
 *          http://www.gnu.org/copyleft/gpl.html                          *
 * 													                      *
 * 															              *
 *                                                                        *
 **************************************************************************
 */


    //This function executes all the restore procedure about this mod
    function dimdim_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;

            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
            // if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('dimdim', $restore, $info['MOD']['#'], array('dimdimTIME'));
            }
            //Now, build the dimdim record structure
            $dimdim->course = $restore->course_id;
            $dimdim->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $dimdim->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $dimdim->keepdays = backup_todb($info['MOD']['#']['KEEPDAYS']['0']['#']);
            $dimdim->studentlogs = backup_todb($info['MOD']['#']['STUDENTLOGS']['0']['#']);
            $dimdim->dimdimtime = backup_todb($info['MOD']['#']['dimdimTIME']['0']['#']);
            $dimdim->schedule = backup_todb($info['MOD']['#']['SCHEDULE']['0']['#']);
            $dimdim->confkey = backup_todb($info['MOD']['#']['confkey']['0']['#']);
            $dimdim->emailuser = backup_todb($info['MOD']['#']['emailuser']['0']['#']);
            $dimdim->displayname = backup_todb($info['MOD']['#']['displayname']['0']['#']);
            $dimdim->startnow = backup_todb($info['MOD']['#']['startnow']['0']['#']);
            $dimdim->attendees = backup_todb($info['MOD']['#']['attendees']['0']['#']);
            $dimdim->timezone = backup_todb($info['MOD']['#']['timezone']['0']['#']);
            $dimdim->timestr = backup_todb($info['MOD']['#']['timestr']['0']['#']);
            $dimdim->lobby = backup_todb($info['MOD']['#']['lobby']['0']['#']);
            $dimdim->networkprofile = backup_todb($info['MOD']['#']['networkprofile']['0']['#']);
            $dimdim->meetinghours = backup_todb($info['MOD']['#']['meetinghours']['0']['#']);
            $dimdim->meetingminutes = backup_todb($info['MOD']['#']['meetingminutes']['0']['#']);
            $dimdim->maxparticipants = backup_todb($info['MOD']['#']['maxparticipants']['0']['#']);
            $dimdim->timemodified = backup_todb($info['MOD']['#']['timemodified']['0']['#']);
            $dimdim->audiovideosettings = backup_todb($info['MOD']['#']['audiovideosettings']['0']['#']);
            $dimdim->maxmikes = backup_todb($info['MOD']['#']['maxmikes']['0']['#']);

            //The structure is equal to the db, so insert the dimdim
            $newid = insert_record ("dimdim",$dimdim);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","dimdim")." \"".format_string(stripslashes($dimdim->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'dimdim',$mod->id)) {
                    //Restore dimdim_messages
                    $status = false;
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //dimdim_decode_content_links_caller() function in each module
    //in the restore process
    function dimdim_decode_content_links ($content,$restore) {

        global $CFG;

        $result = $content;

        //Link to the list of dimdims

        $searchstring='/\$@(dimdimINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(dimdimINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/dimdim/index.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/dimdim/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to dimdim view by moduleid

        $searchstring='/\$@(dimdimVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(dimdimVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/dimdim/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/dimdim/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function dimdim_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($dimdims = get_records_sql ("SELECT c.id, c.intro
                                   FROM {$CFG->prefix}dimdim c
                                   WHERE c.course = $restore->course_id")) {
                                               //Iterate over each dimdim->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($dimdims as $dimdim) {
                //Increment counter
                $i++;
                $content = $dimdim->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $dimdim->intro = addslashes($result);
                    $status = update_record("dimdim",$dimdim);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function dimdim_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "talk":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
