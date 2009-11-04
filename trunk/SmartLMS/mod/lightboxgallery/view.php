<?php

    require_once('../../config.php');
    require_once($CFG->libdir . '/filelib.php');
    require_once($CFG->libdir . '/rsslib.php');
    require_once('lib.php');

    $id = optional_param('id', 0, PARAM_INT);
    $l = optional_param('l' , 0, PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);
    $search = optional_param('search', '', PARAM_TEXT);
    $editing = optional_param('editing', 0, PARAM_BOOL);

    if ($id) {
        if (! $cm = get_coursemodule_from_id('lightboxgallery', $id)) {
            error('Course module ID was incorrect');
        }    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }    
        if (! $gallery = get_record('lightboxgallery', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }
    } else {
        if (! $gallery = get_record('lightboxgallery', 'id', $l)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $gallery->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('lightboxgallery', $gallery->id, $course->id)) {
            error('Course module ID was incorrect');
        }
    }

    if ($gallery->ispublic) {
        course_setup($course->id);
        $userid = (isloggedin() ? $USER->id : 0);
    } else {
        require_login($course->id);
        $userid = $USER->id;
    }
//echo '<pre>'; print_r($USER); exit;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if ($editing) {
        require_capability('mod/lightboxgallery:edit', $context);
    }

    lightboxgallery_config_defaults();

    add_to_log($course->id, 'lightboxgallery', 'view', 'view.php?id=' . $cm->id . '&page=' . $page, $gallery->id, $cm->id, $userid);

    require_js(array('scripts/prototype.js', 'scripts/scriptaculous.js', 'scripts/effects.js', 'scripts/lightbox.js', 'scripts/thumbglow.js'));

    $navigation = build_navigation('', $cm);

    $update = update_module_button($cm->id, $course->id, get_string('modulenameshort', 'lightboxgallery'));

    if (has_capability('mod/lightboxgallery:edit', $context)) {
        $options = array('id' => $cm->id, 'page' => $page, 'editing' => ($editing ? '0' : '1'));
        $update = print_single_button($CFG->wwwroot.'/mod/lightboxgallery/view.php', $options, get_string('turnediting' . ($editing ? 'off' : 'on')), 'get', '', true) . $update;
    }

    $meta = '<link rel="stylesheet" type="text/css" href="' . $CFG->wwwroot . '/mod/lightboxgallery/scripts/lightbox.css" />';

    $allowrssfeed = (lightboxgallery_rss_enabled() && $gallery->rss);

    if ($allowrssfeed) {
        $rsspath = rss_get_url($course->id, $userid, 'lightboxgallery', $gallery->id);
        $meta .= "\n" . '<link rel="alternate" href="' . $rsspath . '" type="application/rss+xml" title="' . format_string($gallery->name) . '" id="gallery" />';
    }

    print_header($course->shortname . ': ' . $gallery->name, $course->fullname, $navigation, '', $meta, true, $update, navmenu($course, $cm));

    $heading = get_string('displayinggallery', 'lightboxgallery', $gallery->name);

    if ($allowrssfeed) {
        $heading .= ' ' . rss_get_link($course->id, $userid, 'lightboxgallery', $gallery->id, get_string('rsssubscribe', 'lightboxgallery'));
    }
    
echo '
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="20px"></td>
                <td width="220px" valign="top">
                
                    <!----------------------------------------------------->
                    <div class="leftpanel">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr><td height="30px">
                                <div class="title">PERSONAL</div>
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_GT.JPG" /></a></div>
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_ST.JPG" /></a></div>
                            </td></tr>
                            <tr><td height="1px" bgcolor="#CCCCCC"></td></tr>
                            <tr><td align="center">
                                <div style="width:200px;">
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td height="30px" align="left">
                                                Hello <a href="" class="leftpaneltext">'. $USER->username .'</a>!
                                            </td>
                                            <td height="30px" align="right">
                                                <a class="leftpaneltext" '. $CFG->frametarget .'href="'. $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'">'. 
                                                    get_string('logout').
                                                '</a>
                                            </td>
                                        </tr>
                                        <tr><td height="30px" align="left">
                                            Tin nhắn:
                                        </td></tr>
                                        <tr><td height="30px" style="padding:0 10px 0 10px" align="left">
                                            Chưa đọc: <a href="" class="leftpaneltext">10</a>
                                        </td></tr>
                                        <tr><td height="30px" style="padding:0 10px 0 10px" align="left">
                                            Có: <a href="" class="leftpaneltext">10</a>
                                        </td></tr>
                                    </table>
                                </div>
                            </td></tr>
                        </table>
                    </div>
                    
                    <!----------------------------------------------------->                    
                    <div class="leftpanel">    
                    </div>            
                    
                </td>
                <td width="20px"></td>
                <td valign="top">
                
                    <!-------------------------------------------------------------------->
                    <div class="newsarea">
                        <table cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tr><td height="30px" colspan="3">
                                <div class="title">';
                                /** 
                                *   Title cho gallery
                                */
                                    echo strtoupper($heading);
                                    //print_heading($heading);
                                /**
                                *  End title
                                */
echo                            '</div>
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_GT.JPG" /></a></div>
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_LT.JPG" /></a></div>
                            </td></tr>
                            <tr>
                                <td colspan="3">';
    lightboxgallery_print_js_config($gallery->autoresize);

    $fobj = new object;
    $fobj->para = false;

    /**
    * Description of gallery
    */
    if ($gallery->description && !$editing) {
        print_simple_box(format_text($gallery->description, FORMAT_MOODLE, $fobj), 'center');
    }
    /**
    * end description
    */                                    
echo                           '</td>
                            </tr>
                            <tr>
                                <td valign="top" width="5px"><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
                                <td valign="top" width="100%">
                                    <table cellpadding="10px" cellspacing="0" width="100%" style="background:url('. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px">
                                        <tr>
                                            <td>';
   /**
    *  Print images 
    */

    $dataroot = $CFG->dataroot . '/' . $course->id . '/' . $gallery->folder;
    $webroot = lightboxgallery_get_image_url($gallery->id);

    $allimages = lightboxgallery_directory_images($dataroot);
    $images = ($gallery->perpage == 0 ? $allimages : array_slice($allimages, $page * $gallery->perpage, $gallery->perpage));

    $captions = array();
    if ($cobjs = get_records_select('lightboxgallery_image_meta',  "metatype = 'caption' AND gallery = $gallery->id")) {
        foreach ($cobjs as $cobj) {
            $captions[$cobj->image] = s($cobj->description);
        }
    }

    if (count($images) > 0) {
        $edittypes = ($editing ? lightboxgallery_edit_types() : null);
        foreach ($images as $image) {
            $imageextra = '';
            $imageurl = $webroot.'/'.$image;
            $imagelocal = $dataroot.'/'.$image;
            $imagelabel = lightboxgallery_resize_label($image);
            if ($edittypes) {
                $imageextra = '<form action="'.$CFG->wwwroot.'/mod/lightboxgallery/imageedit.php" method="get">'.
                              '<fieldset class="invisiblefieldset">'.
                              '<input type="hidden" name="id" value="'.$gallery->id.'" />'.
                              '<input type="hidden" name="image" value="'.$image.'" />'.
                              '<input type="hidden" name="page" value="'.$page.'" />'.
                              '<select name="tab" class="lightbox-edit-select" onchange="submit();">'.
                              '<option>' . get_string('choose') . '...</option>';
                foreach ($edittypes as $editoption => $editdisplay) {
                    $imageextra .= '<option value="'.$editoption.'">'.$editdisplay.'</option>';
                }
                $imageextra .= '</select></fieldset></form>';
            } else if ($gallery->extinfo) {
                $iobj = lightboxgallery_image_info($imagelocal);
                $imageextra = sprintf('<br />%s<br />%s, %dx%d', $iobj->modified, $iobj->filesize, $iobj->imagesize[0], $iobj->imagesize[1]);
            }
            $imagetitle = (isset($captions[$image]) ? $captions[$image] : $image);
            echo('<div class="thumb">
                    <div class="image"><a class="overlay" href="'.$imageurl.'" rel="lightbox[gallery-' . $gallery->id . ']" title="'.$imagetitle.'">'.lightboxgallery_image_thumbnail($course->id, $gallery, $image).'</a></div>
                    <a class="courseBB" href="'.$imageurl.'" rel="lightbox[gallery-' . $gallery->id . ']">'.$imagelabel.$imageextra.'</a>
                  </div>');
        }
    } else {
        print_string('errornoimages', 'lightboxgallery');
    }

    
    /**
    *  End print images
    */                                            
echo '                                       
                                            </td>
                                        </tr>
                                    </table>
                                                                        
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr><td bgcolor="#CCCCCC" height="1px"></td></tr>
                                        <tr><td align="right" class="courseBB" height="25px">';
/**
* Pagination
*/
    if ($gallery->perpage) {
        print_paging_bar(count($allimages), $page, $gallery->perpage, $CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$cm->id.'&amp;' . ($editing ? 'editing=1&amp;' : ''));
    }
/**
* End pagination
* 
* 
*/
echo '                                        
                                        </td></tr>                                        
                                    </table>
                                    <table cellpadding="0" cellspacing="0">
                                        <tr><td height="10px"/></tr>
                                        <tr><td>';
/**
* Show tag and add comment
*     
*/

    $showtags = !in_array('tag', explode(',', get_config('lightboxgallery', 'disabledplugins')));

    if (!$editing && $showtags) {
        $sql = 'SELECT description
                FROM ' . $CFG->prefix . 'lightboxgallery_image_meta
                WHERE gallery = ' . $gallery->id . '
                AND metatype = \'tag\'
                GROUP BY description
                ORDER BY COUNT(description) DESC, description ASC';
        if ($tags = get_records_sql($sql, 0, 10)) {
            lightboxgallery_print_tags(get_string('tagspopular', 'lightboxgallery'), $tags, $course->id, $gallery->id);
        }
    }

    $options = array();

    if ($gallery->folder && has_capability('mod/lightboxgallery:addimage', $context)) {
        $options[] = '<a href="' . $CFG->wwwroot . '/mod/lightboxgallery/imageadd.php?id=' . $gallery->id . '">' . get_string('addimage', 'lightboxgallery') . '</a>';
    }

    if ($gallery->comments && has_capability('mod/lightboxgallery:addcomment', $context)) {
        $options[] = '<a  href="' . $CFG->wwwroot . '/mod/lightboxgallery/comment.php?id=' . $gallery->id . '"><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_Addcomment.jpg" /></a>';
        
    }

    if (count($options) > 0) {
        echo('<div style="text-align:center; font-size: 0.8em;">' . implode(' | ', $options) . '</div>');
    }

    if (!$editing && $gallery->comments && has_capability('mod/lightboxgallery:viewcomments', $context)) {
        if ($comments = get_records('lightboxgallery_comments', 'gallery', $gallery->id, 'timemodified ASC')) {
            foreach ($comments as $comment) {
                lightboxgallery_print_comment($comment, $context);
            }
        }
    }
/**
* End Show tag and add comment
*/                                        
echo '                                        
                                        <a href=""></a>
                                        </td></tr>
                                    </table>
                                </td>
                                <td valign="top" width="5px"><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BG1_R.jpg" /></td>
                            </tr>
                        </table>
                    </div>
                
                    <!-------------------------------------------------------------------->
                    <div class="newsarea">
                        <table cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tr><td height="30px" colspan="3">
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_GT.JPG" /></a></div>
                                <div class="titleicon"><a href=""><img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/BT_LT.JPG" /></a></div>
                            </td></tr>
                        </table>
                    </div>
                
                    
                </td>
                <td width="20px"></td>
            </tr>
        </table>
    ';

    print_footer($course);

?>

