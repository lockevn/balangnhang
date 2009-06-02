<?php

class PageBuilder
{
    /**
    *@desc assocarray (key = pagename (mod name), value = assocarray (key = 'zonename', 'pageletname1,pageletname2'))
    */
    public static $PageMap = array(
        'search' => array(
            'ZONE_MainContent' => array('search')
        ),
        'register' => array(
            'ZONE_MainContent' => array('register')
        ),
        'login' => array(
            'ZONE_MainContent' => array('loginbox')
        ),

        'forget_password' => array(
            'ZONE_MainContent' => array('forget_password')
        ),


        'invite_step1' => array(
            'ZONE_MainContent' => array('invite_tab', 'invite_step1')
        ),
        'invite_step2' => array(
            'ZONE_MainContent' => array('invite_tab', 'invite_step2')
        ),
        'invite_step3' => array(
            'ZONE_MainContent' => array('invite_tab', 'invite_step3')
        ),
        'invite_step4' => array(
            'ZONE_MainContent' => array('invite_tab', 'invite_step4')
        ),


        'verify_email' => array(
            'ZONE_MainContent' => array('verify_email')
        ),

        'invite_to_group' => array(
            'ZONE_MainContent' => array('invite_to_group')
        ),


        'dashboard' => array(
            'ZONE_MainContent' => array('what_when_how', 
            'public_profile_mostactive','public_profile_highlight'
                ),                
            'ZONE_Right' => array('loginbox', 'public_msg_user_lasttest')
        ),


        'group_member' => array(
            'ZONE_MainContent' => array('group_namecard', 'action_with_group', 'group_tab',  'group_member')
        ),
        'group_msg' => array(
            'ZONE_MainContent' => array('group_namecard', 'action_with_group', 'group_tab',  'post_msg', 'msglist')
        ),


        'advanced_post' => array(
            'ZONE_MainContent' => array('advanced_post')
        ),


        'setting' => array(
            'ZONE_MainContent' => array('setting')
        ),



        'all_group' => array(
            'ZONE_MainContent' => array('all_group')
        ),

        'joint_group' => array(
            'ZONE_MainContent' => array('joint_group_list')
        ),



        'friend' => array(
            'ZONE_MainContent' => array('profile_friend_list')
        ),
        'followee' => array(
            'ZONE_MainContent' => array('profile_followee_list')
        ),



        'view_msg_tree' => array(
            'ZONE_MainContent' => array('post_msg', 'view_msg_tree')
        ),



        'pm_inbox' => array(
            'ZONE_MainContent' => array('pm_inbox'),
            'ZONE_Right' => array('friend_boxlist', 'followee_boxlist')
        ),
         'pm_sent' => array(
            'ZONE_MainContent' => array('pm_sent'),
            'ZONE_Right' => array('friend_boxlist', 'followee_boxlist')
        ),



        'profile_msg' => array(
            'ZONE_TopInfo' => array('invite_info'),
            'ZONE_Top' => array('action_with_profile','profile_namecard'),
            'ZONE_MainContent' => array('profile_tab', 'post_msg', 'msglist'),
            'ZONE_Left' => array('profile_stat','friend_boxlist','followee_boxlist','joint_group_boxlist'),
            'ZONE_Right' => array(),
            'ZONE_Bottom' => array('profile_activity')
        ),


        'profile_replies' => array(
            'ZONE_MainContent' => array('profile_tab', 'post_msg', 'msglist'),
            'ZONE_Left' => array('friend_boxlist','followee_boxlist','joint_group_boxlist'),
            'ZONE_Right' => array('profile_stat')
        ),

        'home' => array(
            'ZONE_MainContent' => array('action_with_profile','profile_tab', 'post_msg', 'msglist'),
            'ZONE_Left' => array('friend_boxlist','followee_boxlist','joint_group_boxlist'),
            'ZONE_Right' => array('profile_stat')
        ),

        'group_home' => array(
            'ZONE_MainContent' => array('post_msg', 'post_msg', 'msglist'),
            'ZONE_Left' => array('joint_group_boxlist')
        ),

        'public_msg' => array(
            'ZONE_MainContent' => array('post_msg', 'msglist')
        ),
    );


    public static $AllowedCustomModule = array(
        
    );
    
     



    public static function Render($mod, $tpl)
    {
        $moduleConfig = PageBuilder::$PageMap[$mod];
        foreach ((array)$moduleConfig as $zonename => $arrPagelet)
        {
            $zonecontent = '';
            foreach ((array)$arrPagelet as $pagelet)
            {
                require_once(ABSPATH."Pagelet/$pagelet.php");
                $zonecontent .= $$pagelet;
            }
            $tpl->assign($zonename, $zonecontent);
        }

        return true;
    }

}

?>