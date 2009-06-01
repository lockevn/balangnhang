<?php

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

    // redirector code
    $GLOBALS["page"]->add("<script type=\"text/javascript\">\n"
          ."<!--\n"
          ."function go(addr) {\n"
          ."  blank = window.open(this.document.URL,'blank');\n"
          ."  blank.document.open();\n"
          ."  blank.document.write(\n"
          ."     '<?xml version=\"1.0\" encoding=\"".$_['global_charset']."\"?>'\n"
          ."    +'<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" '\n"
          ."    +'\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">'\n"
          ."    +'<html xmlns=\"http://www.w3.org/1999/xhtml\">'\n"
          ."    +'<head>'\n"
          ."    +'<title>Redirect</title>'\n"
          ."    +'<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$_['global_charset']."\" />'\n"
          ."    +'<meta http-equiv=\"Refresh\" content=\"0; URL=http://'+unescape(addr)+'\" />'\n"
          ."    +'</head>'\n"
          ."    +'<body>'\n"
          ."    +'</body>'\n"
          ."    +'</html>'\n"
          ."  );\n"
          ."  blank.document.close();\n"
          ."}\n"
          ."-->\n"
          ."</script>\n"
          ."<style type=\"text/css\">\n"
          ."<!--\n"
    // Body styles
          ."  body {margin: 0px; padding: 0px; background-color: #edf0f9}\n"
    // redefined tags
          ."  p {font-family: Arial, Helvetica, sans-serif; color: #606680; font-size: ".$BBC_TEXT_SIZE."pt}\n"
          ."  td {font-family: Arial, Helvetica, sans-serif; color: #606680; font-size: ".$BBC_TEXT_SIZE."pt}\n"
          ."  input {border: 1px #606680 solid; background-color: #edf0f9; vertical-align: middle}\n"
          ."  select {border: 1px #606680 solid; background-color: #edf0f9; vertical-align: middle}\n"
    // Links styles
          ."  a {text-decoration: underline; color: #cc7286}\n"
          ."  a:hover {text-decoration: none; color: #606680}\n"
    // Navbar
          ."  a.navbar {font-family: Arial, Helvetica, sans-serif; font-size: ".$BBC_SUBTITLE_SIZE."pt; "
          ."text-decoration: none; padding: 3px; color: #606680}\n"
          ."  a.navbar:hover  {font-family: Arial, Helvetica, sans-serif; font-size: ".$BBC_SUBTITLE_SIZE."pt; "
          ."text-decoration: none; padding: 2px; border: 1px solid #606680; background-color: #edf0f9}\n"
          ."  .navbar {font-family: Arial, Helvetica, sans-serif; font-size: ".$BBC_SUBTITLE_SIZE."pt; "
          ."color: #98a3d1; font-weight: bold; margin: 0px; padding: 10px; vertical-align: middle}\n"
          ."  .navbar img {vertical-align: middle}\n"
    // Titlebar
          ."  .titlebar {color: #ffffff; font-weight: bold; font-size: ".$BBC_TITLE_SIZE."pt}\n"
    // Stats
          ."  .head {font-family: Arial, Helvetica, sans-serif; font-size: ".$BBC_TEXT_SIZE."pt; text-align: center; "
          ."font-weight: bold; padding: 3px; white-space: nowrap}\n"
          ."  .graph {font-family: Arial, Helvetica, sans-serif; color: #606680; font-size: ".$BBC_NUM_SIZE."pt; "
          ."padding: 3px}\n"
          ."  .capt {font-weight: bold; color: #ffffff; white-space: nowrap}\n"
    // boxes
          ."  .cntbox {background-color:#ffffff; border: 1px #606680 solid}\n"
          ."  .detbox {background-color:#ffffff; border: 1px #606680; border-style: solid none}\n"
          ."  .gridbox {margin: 0px; border: 1px #606680 solid}\n"
    // border madness
          ."  .brd {border-width: 1px; border-color: #606680}\n"
           // collapse where 1px borders are needed
          ."  .collapse {border-collapse: collapse}\n"
          ."  .rows {margin: 0px; border: 1px #ffffff solid}\n"
          ."  .sky {border-width: 1px; border-color: #e5f2f7}\n"
          ."  table {border-collapse: collapse}\n"
           // evil hack for Opera 7+
          ."  tab\\le {border-collapse: separate;}\n"
           // evil hack for IE5 Mac
          ."  /*\*//*/\n"
          ."  td table {width:97%; margin:0px 1px 0px 0px; padding:0px}\n"
          ."  /**/\n"
          ."//-->\n"
          ."</style>\n"
           // another evil IE hack which should never see the daylight :-)
          ."<!--[if IE]>\n"
          ."<style type=\"text/css\">\n"
          ."  table {border-collapse: collapse !important}\n"
          ."</style>\n"
          ."<![endif]-->\n"
          ."</head>\n"
          ."<body>\n"
    // BBClone copyright notice: Removal or modification of the copyright holder
    // will void any support by the BBClone team and may be a reason to deny
    // access to the BBClone site if detected.
          ."<!--\n"
          ."This is BBClone $BBC_VERSION\n"
          ."Homebase: http://bbclone.de/\n"
          ."Copyright: 2001-2005 The BBClone team\n"
          ."License:  GNU/GPL, version 2 or later\n"
          ."-->\n", "page_head");

?>


