<?PHP //$Id: backuplib.php,v 1.2 2003/09/14 15:47:36 stronk7 Exp $
    //This php script contains all the stuff to backup/restore
    //start mods

    //This is the "graphical" structure of the start mod:
    //
    //                       start
    //                     (CL,pk->id)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the backup procedure about this mod
    function start_backup_mods($bf,$preferences)
    {
        global $CFG;

        $status = true; 

        ////Iterate over start table
        if ($starts = get_records ("start","course", $preferences->backup_course,"id"))
        {
            foreach ($starts as $start)
            {
                if (backup_mod_selected($preferences,'start',$start->id))
                {
                    $status = start_backup_one_mod($bf,$preferences,$start);
                }
            }
        }
        return $status;
    }

    function start_backup_one_mod($bf,$preferences,$start)
    {
        global $CFG;
    
        if (is_numeric($start))
        {
            $start = get_record('start','id',$start);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$start->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"start"));
        fwrite ($bf,full_tag("NAME",4,false,$start->name));
        fwrite ($bf,full_tag("CONTENT",4,false,$start->content));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$start->timemodified));
        //End mod
        $status = fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }
   
    ////Return an array of info (name,value)
    function start_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null)
    {
        if (!empty($instances) && is_array($instances) && count($instances))
        {
            $info = array();
            foreach ($instances as $id => $instance)
            {
                $info += start_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }

         //First the course data
         $info[0][0] = get_string("modulenameplural","start");
         $info[0][1] = count_records("start", "course", "$course");
         return $info;
    } 

    ////Return an array of info (name,value)
    function start_check_backup_mods_instances($instance,$backup_unique_code)
    {
         //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        return $info;
    }

?>
