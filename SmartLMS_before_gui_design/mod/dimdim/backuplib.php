<?php //$Id: backuplib.php,v 1.1 2008/10/24 11:47:53 dhara01 Exp $
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


    //This function executes all the backup procedure about this mod
    function dimdim_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over dimdim table
        $dimdims = get_records ("dimdim","course",$preferences->backup_course,"id");
        if ($dimdims) {
            foreach ($dimdims as $dimdim) {
                if (backup_mod_selected($preferences,'dimdim',$dimdim->id)) {
                    $status = dimdim_backup_one_mod($bf,$preferences,$dimdim);
                }
            }
        }
        return $status;
    }

    function dimdim_backup_one_mod($bf,$preferences,$dimdim) {

        global $CFG;

        if (is_numeric($dimdim)) {
            $dimdim = get_record('dimdim','id',$dimdim);
        }

        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print dimdim data
        fwrite ($bf,full_tag("ID",4,false,$dimdim->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"dimdim"));
        fwrite ($bf,full_tag("NAME",4,false,$dimdim->name));
        fwrite ($bf,full_tag("INTRO",4,false,$dimdim->intro));
        fwrite ($bf,full_tag("KEEPDAYS",4,false,$dimdim->keepdays));
        fwrite ($bf,full_tag("STUDENTLOGS",4,false,$dimdim->studentlogs));
        fwrite ($bf,full_tag("SCHEDULE",4,false,$dimdim->schedule));
        fwrite ($bf,full_tag("dimdimTIME",4,false,$dimdim->dimdimtime));
        fwrite ($bf,full_tag("confkey",4,false,$dimdim->confkey));
        fwrite ($bf,full_tag("emailuser",4,false,$dimdim->emailuser));
        fwrite ($bf,full_tag("displayname",4,false,$dimdim->displayname));
        fwrite ($bf,full_tag("startnow",4,false,$dimdim->startnow));
        fwrite ($bf,full_tag("attendees",4,false,$dimdim->attendees));
        fwrite ($bf,full_tag("timezone",4,false,$dimdim->timezone));
        fwrite ($bf,full_tag("timestr",4,false,$dimdim->timestr));
        fwrite ($bf,full_tag("lobby",4,false,$dimdim->lobby));
        fwrite ($bf,full_tag("networkprofile",4,false,$dimdim->networkprofile));
        fwrite ($bf,full_tag("meetinghours",4,false,$dimdim->meetinghours));
        fwrite ($bf,full_tag("meetingminutes",4,false,$dimdim->meetingminutes));
        fwrite ($bf,full_tag("maxparticipants",4,false,$dimdim->maxparticipants));
        fwrite ($bf,full_tag("timemodified",4,false,$dimdim->timemodified));
        fwrite ($bf,full_tag("audiovideosettings",4,false,$dimdim->audiovideosettings));
        fwrite ($bf,full_tag("maxmikes",4,false,$dimdim->maxmikes));
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }


    //Return an array of info (name,value)
    function dimdim_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += dimdim_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","dimdim");
        if ($ids = dimdim_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        return $info;
    }

    //Return an array of info (name,value)
    function dimdim_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function dimdim_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of dimdims
        $buscar="/(".$base."\/mod\/dimdim\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@dimdimINDEX*$2@$',$content);

        //Link to dimdim view by moduleid
        $buscar="/(".$base."\/mod\/dimdim\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@dimdimVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of dimdims id
    function dimdim_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT c.id, c.course
                                 FROM {$CFG->prefix}dimdim c
                                 WHERE c.course = '$course'");
    }

    //Returns an array of assignment_submissions id
    function dimdim_message_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT m.id , m.dimdimid
                                 FROM {$CFG->prefix}dimdim_messages m,
                                      {$CFG->prefix}dimdim c
                                 WHERE c.course = '$course' AND
                                       m.dimdimid = c.id");
    }

    //Returns an array of dimdim id
    function dimdim_message_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT m.id , m.dimdimid
                                 FROM {$CFG->prefix}dimdim_messages m
                                 WHERE m.dimdimid = $instanceid");
    }
?>
