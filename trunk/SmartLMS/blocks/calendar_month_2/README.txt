This is a block that uses the YUI AJAX library to update the calendar block without reloading the full page.

It works if Javascript is disabled in the browser, it also tests whether the AJAXEnabled check is set for the site/current user and abides by that decision.

The code started off as the the standard calendar block and initially I intended to see if a few <code> if ($CFG->AjaxEnabled) </code> checks could be inserted to retain complete original functionality when Ajax is disabled within moodle but to function completely differently when it is whilst also re-using as many of the original functions as possible.

On the first test it all seemed to work fine (from the homepage) but I soon noticed that the mini.php script had no knowledge of from which course the page had been called.

Unfortunately to resolve this problem caused additional problems in other functions. All the functions that have had to be duplicated/modified are contained within lib/yuiCalLib.php and have the initial functions name with _yui appended

This block does not work with versions of moodle below 1.9, it also does not work with 1.9beta and may not work with versions of 1.9 released before June 2008

It has been tested on both 1.9 and 2.0

Latest versions of this block can be found at www.matc-online.co.uk as well as a live demo


