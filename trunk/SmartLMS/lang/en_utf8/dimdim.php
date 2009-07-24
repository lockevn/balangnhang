<?php

/*
 **************************************************************************
 *                                                                        *
 *               DDDDD   iii                 dd  iii                      *
 *               DD  DD      mm mm mmmm      dd      mm mm mmmm           *
 *               DD   DD iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *               DD   DD iii mmm  mm  mm dd  dd  iii mmm  mm  mm          *
 *               DDDDDD  iii mmm  mm  mm  ddddd  iii mmm  mm  mm          *
 *                                                                        *
 **************************************************************************
 **************************************************************************
 *                                                                        *
 * Part of the Dimdim V 1.5 alpha Codebase (http://www.dimdim.com)	      *
 *                                                                        *
 * Copyright (c) 2007 Dimdim Inc. All Rights Reserved.                    *
 *                                                                        *
 *                                                                        *
 * This code is licensed under the Dimdim License                         *
 * For details please visit http://www.dimdim.com/license                 *
 *                                                                        *
 **************************************************************************
 */


$string['beep'] = 'beep';
$string['dimdimintro'] = 'Meeting Agenda';
$string['details'] = 'Brief Notes';
$string['dimdimname'] = 'Name of this dimdim room';
$string['conferencename'] = 'Web Meeting Name';
$string['maxparticipants'] = 'Maximum Participants';
$string['dimdimreport'] = 'dimdim sessions';
$string['dimdimtime'] = 'Next dimdim time';
$string['startschedule'] = 'Start Time';
$string['configmethod'] = 'The normal dimdim method involves the clients regularly contacting the server for updates. It requires no configuration and works everywhere, but it can create a large load on the server with many dimdimters.  Using a server daemon requires shell access to Unix, but it results in a fast scalable dimdim environment.';
$string['configoldping'] = 'What is the maximum time that may pass before we detect that a user has disconnected (in seconds)? This is just an upper limit, as usually disconnects are detected very quickly. Lower values will be more demanding on your server. If you are using the normal method, <strong>never</strong> set this lower than 2 * dimdim_refresh_room.';
$string['configrefreshroom'] = 'How often should the dimdim room itself be refreshed? (in seconds).  Setting this low will make the dimdim room seem quicker, but it may place a higher load on your web server when many people are dimdimting';
$string['configrefreshuserlist'] = 'How often should the list of users be refreshed? (in seconds)';
$string['configserverhost'] = 'The Dimdim server hostname or IP to which it is configured';
$string['configserverip'] = 'The numerical IP address that matches the above hostname';
$string['configservermax'] = 'Max number of clients allowed';
$string['configserverport'] = 'The Dimdim server port to which it is configured';
$string['currentdimdims'] = 'Active dimdim sessions';
$string['currentusers'] = 'Current users';
$string['deletesession'] = 'Delete this session';
$string['deletesessionsure'] = 'Are you sure you want to delete this session?';
$string['donotusedimdimtime'] = 'Don\'t publish any dimdim times';
$string['enterdimdim'] = 'Click here to enter the xyz now';
$string['errornousers'] = 'Could not find any users!';
$string['explaingeneralconfig'] = 'These settings are <strong>always</strong> into effect';
$string['explainmethoddaemon'] = 'These settings matter <strong>only</strong> if you have selected \"dimdim server daemon\" for dimdim_method';
$string['explainmethodnormal'] = 'These settings matter <strong>only</strong> if you have selected \"Normal method\" for dimdim_method';
$string['generalconfig'] = 'General configuration';
$string['helpdimdimting'] = 'Help with dimdimting';
$string['idle'] = 'Idle';
$string['messagebeepseveryone'] = '$a beeps everyone!';
$string['messagebeepsyou'] = '$a has just beeped you!';
$string['messageenter'] = '$a has just entered this dimdim';
$string['messageexit'] = '$a has left this dimdim';
$string['messages'] = 'Messages';
$string['methodnormal'] = 'Normal method';
$string['methoddaemon'] = 'dimdim server daemon';
$string['modulename'] = 'Dimdim Web Meeting';
$string['modulenameplural'] = 'Dimdim Web Meetings';
$string['neverdeletemessages'] = 'Never delete messages';
$string['nextsession'] = 'Next scheduled session';
$string['noguests'] = 'The dimdim is not open to guests';
$string['nomessages'] = 'No messages yet';
$string['5'] = '5';
$string['10'] = '10';
$string['15'] = '15';
$string['20'] = '20';
$string['enable'] = 'Enable';
$string['disable'] = 'Disable';
$string['lobby'] = 'Waiting Area';
$string['enterprise_check_label'] = 'Do you have Dimdim account';
$string['invalid_schedule'] = 'Meeting can not be in past';
$string['enterprise_username_label'] = 'Dimdim account User name';
$string['enterprise_password_label'] = 'Dimdim account Password';
$string['maxmikes'] = 'Attendee Mikes';
$string['meetinghours'] = 'Meeting Duration in hours';
$string['1hour'] = '1';
$string['2hour'] = '2';
$string['3hour'] = '3';
$string['4hour'] = '4';
$string['5hour'] = '5';
$string['0mike'] = '0';
$string['1mike'] = '1';
$string['2mike'] = '2';
$string['3mike'] = '3';
$string['4mike'] = '4';
$string['5mike'] = '5';
$string['Audio Video'] = 'Audio Video';
$string['audio'] = 'Audio';
$string['audio-video'] = 'Audio-Video';
$string['Video-Chat'] =  'Video Chat';
$string['NoAudioVideo'] = 'No Audio-Video';
$string['Video-Only'] = 'Video Only';
$string['Network'] = 'Network';
$string['dial-up'] = 'dial-up';
$string['cable/dsl'] = 'cable/dsl';
$string['lan'] = 'lan';
$string['repeatdaily'] = 'At the same time every day';
$string['repeatnone'] = 'No repeats - use the specified time only ';
$string['repeattimes'] = 'Repeat';
$string['repeatweekly'] = 'At the same time every week';
$string['savemessages'] = 'Save past sessions';
$string['seesession'] = 'See this session';
$string['sessions'] = 'dimdim sessions';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Everyone can view past sessions';
$string['viewreport'] = 'View past dimdim sessions';
$string['privatechat'] = 'Private Chat';
$string['publicchat'] = 'Public Chat';
$string['screencast'] = 'Screencast';
$string['whiteboard'] = 'Whiteboard';
$string['participantlist'] = 'Participant List';
$string['interntoll'] = 'International Toll';
$string['moderatorpasscode'] = 'Moderator Passcode';
$string['attendeepasscode'] = 'Attendee Passcode';
$string['displaydialinfo'] = 'Display Dial-in info';
$string['meetingkey'] = 'Meeting Key';
$string['hostkey'] = 'Host Key';
$string['feedback'] = 'Feedback Email';
$string['collaburl'] = 'Collabration URL';
$string['assistantenabled'] = 'Assistant Enabled';
$string['assignmikeonjoin'] = 'Assign Mike to attendees on Join';
$string['handsfreeonload'] = 'Hands Free on Start';
$string['allowattendeeinvite'] = 'Allow Presenter to invite';
$string['featuredocshare'] = 'Document Sharing';
$string['featurecobshare'] = 'Cobrowsing';
$string['featurerecording'] = 'Recording';
?>

