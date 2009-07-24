<?php        /// Load up any required Javascript libraries


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

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if(!empty($CFG->aspellpath)) {      // Enable global access to spelling feature.
        echo '<script language="JavaScript" type="text/javascript" src="'.$CFG->httpswwwroot.'/lib/speller/spellChecker.js"></script>'."\n";
    }

    if ( !empty($CFG->editorsrc) ) {
        foreach ( $CFG->editorsrc as $scriptsource ) {
            echo '<script language="javascript" type="text/javascript" src="'. $scriptsource .'"></script>'."\n";
        }
    }

?>
<!--<style type="text/css">/*<![CDATA[*/ body{behavior:url(<?php echo $CFG->httpswwwroot ?>/lib/csshover.htc);} /*]]>*/</style>-->

<script language="JavaScript" type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/javascript-static.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/javascript-mod.php"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/overlib.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/cookies.js"></script>

<script language="JavaScript" type="text/javascript" defer="defer">

function dimdimopenpopup(url,name,options,fullscreen) {
  fullurl = "<?php echo $CFG->httpswwwroot ?>" + url;
  if (fullscreen) {
     windowobj.moveTo(0,0);
     windowobj.resizeTo(screen.availWidth,screen.availHeight);
  }
  windowobj.focus();
  return false;
}


</script>
