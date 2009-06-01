<?php
/* This file is part of BBClone (The PHP web counter on steroids)
 *
 * $Header: /cvs/bbclone/show_regression.php,v 1.4 2006/12/27 17:01:41 christoph Exp $
 *
 * Copyright (C) 2001-2007, the BBClone Team (see file doc/authors.txt
 * distributed with this library)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * See doc/copying.txt for details
 */
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

// Check for PHP 4.0.3 or older
if (!function_exists("array_sum")) exit("<hr /><b>Error:</b> PHP ".PHP_VERSION." is too old for BBClone.");
elseif (is_readable("constants.php")) require_once("constants.php");
else return;

$all = 0;
$browser_ok = 0;
$browser_failed = 0;
$os_ok = 0;
$os_failed = 0;
$robot_ok = 0;
$robot_failed = 0;

function print_header() {
  return '<tr><td class="head">User Agent</td><td class="head">Status</td></tr>'.chr(13);
}

function print_summary() {
  global $all, $browser_ok, $browser_failed, $os_ok, $os_failed, $robot_ok, $robot_failed;
  return '<tr style="background-color: #e5f2f7" onmouseover="this.style.backgroundColor=\'#ffffff\'" onmouseout="this.style.backgroundColor=\'#e0e5f2\'"><td class="rows">All:</td><td class="rows">'.$all.'</td></tr>
<tr style="background-color: #e5f2f7" onmouseover="this.style.backgroundColor=\'#ffffff\'" onmouseout="this.style.backgroundColor=\'#e0e5f2\'"><td class="rows">Browser:</td><td class="rows">'.$browser_ok.' passed, '.$browser_failed.' failed (='.number_format($browser_failed/$all*100, 1).'%)</td></tr>
<tr style="background-color: #e5f2f7" onmouseover="this.style.backgroundColor=\'#ffffff\'" onmouseout="this.style.backgroundColor=\'#e0e5f2\'"><td class="rows">OS:</td><td class="rows">'.$os_ok.' passed, '.$os_failed.' failed (='.number_format($os_failed/$all*100, 1).'%)</td></tr>
<tr style="background-color: #e5f2f7" onmouseover="this.style.backgroundColor=\'#ffffff\'" onmouseout="this.style.backgroundColor=\'#e0e5f2\'"><td class="rows">Robot:</td><td class="rows">'.$robot_ok.' passed, '.$robot_failed.' failed (='.number_format($robot_failed/$all*100, 1).'%)</td></tr>'.chr(13);
}


foreach (array($BBC_CONFIG_FILE, $BBC_LIB_PATH."selectlang.php", $BBC_LIB_PATH."regression.php", $BBC_LIB_PATH."html.php") as $i) {
  if (is_readable($i)) require_once($i);
  else exit(bbc_msg($i));
}

if (is_readable($BBC_LIB_PATH."browser.php")) require_once($BBC_LIB_PATH."browser.php");
else return bbc_msg($BBC_LIB_PATH."browser.php");

$regression_keys = array_keys($regression);

echo $bbc_html->html_begin()
    .$bbc_html->topbar()
    ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n"
    ."<tr>\n<td class=\"detbox\" align=\"center\" valign=\"middle\">\n"
    ."<table class=\"collapse\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n"
    .print_header();

$all = count($regression_keys);
for ($g=0; $g<count($regression_keys); $g++) {
  $user_agent = $regression_keys[$g];

  $expected_browser_name = $regression[$regression_keys[$g]]['browser'];
  $expected_os_name = $regression[$regression_keys[$g]]['os'];
  $expected_robot_name = $regression[$regression_keys[$g]]['robot'];

  foreach (array("robot", "browser", "os") as $i) {
    require_once($BBC_LIB_PATH.$i.".php");
    reset($$i);

    while (list(${$i."_name"}, ${$i."_elem"}) = each($$i)) {
      reset(${$i."_elem"}['rule']);

      while (list($pattern, $note) = each(${$i."_elem"}['rule'])) {
        // eregi() is intentionally used because some php installations don't
        // know the "i" switch of preg_match() and would generate phony compile
        // error messages
        if (!eregi($pattern, $user_agent)) continue;

        echo '<tr style="background-color: #e0e5f2" onmouseover="this.style.backgroundColor=\'#ffffff\'" onmouseout="this.style.backgroundColor=\'#e0e5f2\'"><td class="rows">'.$user_agent.'</td>';
        if (($i == "browser") && (strcmp($expected_browser_name, ${$i."_name"}) != 0)) {
          echo '<td class="rows"><span style="color:red;">Browser: Got \''.${$i."_name"}.'\', expected \''.$expected_browser_name."'.</span></td>\n";
          ${$i.'_failed'}++;
        } elseif (($i == "os") && (strcmp($expected_os_name, ${$i."_name"}) != 0)) {
          echo '<td class="rows"><span style="color:red;">OS: Got \''.${$i."_name"}.'\', expected \''.$expected_os_name."'.</span></td>\n";
          ${$i.'_failed'}++;
        } elseif (($i == "robot") && (strcmp($expected_robot_name, ${$i."_name"}) != 0)) {
          echo '<td class="rows"><span style="color:red;">Robot: Got \''.${$i."_name"}.'\', expected \''.$expected_robot_name."'.</span></td>\n";
          ${$i.'_failed'}++;
        } else {
          echo '<td class="rows"><span style="color:green;">OK</span></td>'.chr(13);
          ${$i.'_ok'}++;
        }
        echo '</tr>'.chr(13);
        flush();

        break 2;
      }
    }
    if (!empty($connect['robot'])) break;
  }
}

echo print_summary()."</table>\n"
    ."</td>\n</tr>\n</table>\n"
    .$bbc_html->copyright()
    .$bbc_html->topbar(0, 1)
    .$bbc_html->html_end();

?>