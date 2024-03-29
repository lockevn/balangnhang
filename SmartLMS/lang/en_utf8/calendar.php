<?PHP // $Id: calendar.php,v 1.10.4.1 2007/11/29 14:40:46 skodak Exp $ 
      // calendar.php - created with Moodle 1.7 beta + (2006101003)

$string['allday'] = 'All day';
$string['advancedoptions'] = 'Advanced options';
$string['calendar'] = 'Calendar';
$string['calendarheading'] = '$a Calendar';
$string['clickhide'] = 'click to hide';
$string['clickshow'] = 'click to show';
$string['commontasks'] = 'Options';
$string['confirmeventdelete'] = 'Are you sure you want to delete this event?';
$string['course'] = 'Course';
$string['courseevent'] = 'Course event';
$string['courseevents'] = 'Course events';
$string['courses'] = 'Courses';
$string['dayview'] = 'Day View';
$string['daywithnoevents'] = 'There are no events this day.';
$string['default'] = 'Default';
$string['deleteevent'] = 'Delete event';
$string['deleteevents'] = 'Delete events';
$string['detailedmonthview'] = 'Detailed Month View';
$string['durationminutes'] = 'Duration in minutes';
$string['durationnone'] = 'Without duration';
$string['durationuntil'] = 'Until';
$string['editevent'] = 'Editing event';
$string['errorbeforecoursestart'] = 'Cannot set event before course start date';
$string['errorinvaliddate'] = 'Invalid date';
$string['errorinvalidminutes'] = 'Specify duration in minutes by giving a number between 1 and 999.';
$string['errorinvalidrepeats'] = 'Specify the number of events by giving a number between 1 and 99.';
$string['errornodescription'] = 'Description is required';
$string['errornoeventname'] = 'Name is required';
$string['eventdate'] = 'Date';
$string['eventdescription'] = 'Description';
$string['eventduration'] = 'Duration';
$string['eventendtime'] = 'End time';
$string['eventinstanttime'] = 'Time';
$string['eventkind'] = 'Type of event';
$string['eventname'] = 'Name';
$string['eventnone'] = 'No events';
$string['eventrepeat'] = 'Repeats';
$string['eventsall'] = 'All events';
$string['eventsfor'] = '$a events';
$string['eventskey'] = 'Events Key';
$string['eventsrelatedtocourses'] = 'Events related to courses';
$string['eventstarttime'] = 'Start time';
$string['eventtime'] = 'Time';
$string['eventview'] = 'Event Details';
$string['expired'] = 'Expired';
$string['explain_lookahead'] = 'This sets the (maximum) number of days in the future that an event has to start in in order to be displayed as an upcoming event. Events that start beyond this will never be displayed as upcoming. Please note that <strong>there is no guarantee</strong> that all events starting in this time frame will be displayed; if there are too many (more than the \"Maximum upcoming events\" preference) then the most distant events will not be shown.';
$string['explain_maxevents'] = 'This sets the maximum number of upcoming events that can be displayed. If you pick a large number here it is possible that upcoming events displays will take up a lot of space on your screen.';
$string['explain_persistflt'] = 'If this is enabled, then Moodle will remember your last event filter settings and automatically restore them each time you login.';
$string['explain_startwday'] = 'Calendar weeks will be shown as starting on the day that you select here.';
$string['explain_timeformat'] = 'You can choose to see times in either 12 or 24 hour format. If you choose \"default\", then the format will be automatically chosen according to the language you use in the site.';
$string['explain_site_timeformat'] = 'You can choose to see times in either 12 or 24 hour format for the whole site. If you choose \"default\", then the format will be automatically chosen according to the language you use in the site. This setting can be overridden by user preferences.';
$string['export'] = 'Export';
$string['exportcalendar'] = 'Export calendar';
$string['exportbutton'] = 'Export';
$string['for'] = 'for';
$string['fri'] = 'Fri';
$string['friday'] = 'Friday';
$string['generateurlbutton'] = 'Get calendar URL';
$string['global'] = 'Global';
$string['globalevent'] = 'Global event';
$string['globalevents'] = 'Global events';
$string['gotocalendar'] = 'Go to calendar';
$string['group'] = 'Group';
$string['groupevent'] = 'Group event';
$string['groupevents'] = 'Group events';
$string['hidden'] = 'hidden';
$string['ical'] = 'iCal';
$string['iwanttoexport'] = 'Export';
$string['manyevents'] = '$a events';
$string['mon'] = 'Mon';
$string['monday'] = 'Monday';
$string['monthlyview'] = 'Monthly View';
$string['monthnext'] = 'Next month';
$string['monththis'] = 'This month';
$string['newevent'] = 'New Event';
$string['noupcomingevents'] = 'There are no upcoming events';
$string['oneevent'] = '1 event';
$string['pref_lookahead'] = 'Upcoming events look-ahead';
$string['pref_maxevents'] = 'Maximum upcoming events';
$string['pref_persistflt'] = 'Remember filter settings';
$string['pref_startwday'] = 'First day of week';
$string['pref_timeformat'] = 'Time display format';
$string['preferences'] = 'Preferences';
$string['preferences_available'] = 'Your personal preferences';
$string['quickdownloadcalendar'] = 'Quick download / subscribe to calendar';
$string['recentupcoming'] = 'Recent and next 60 days';
$string['repeateditall'] = 'Apply changes to all $a events in this repeat series';
$string['repeateditthis'] = 'Apply changes to this event only';
$string['repeatnone'] = 'No repeats';
$string['repeatweeksl'] = 'Repeat weekly, creating altogether';
$string['repeatweeksr'] = 'events';
$string['sat'] = 'Sat';
$string['saturday'] = 'Saturday';
$string['shown'] = 'shown';
$string['spanningevents'] = 'Events underway';
$string['sun'] = 'Sun';
$string['sunday'] = 'Sunday';
$string['thu'] = 'Thu';
$string['thursday'] = 'Thursday';
$string['timeformat_12'] = '12-hour (am/pm)';
$string['timeformat_24'] = '24-hour';
$string['today'] = 'Today';
$string['tomorrow'] = 'Tomorrow';
$string['tt_deleteevent'] = 'Delete event';
$string['tt_editevent'] = 'Edit event';
$string['tt_hidecourse'] = 'Course events are shown (click to hide)';
$string['tt_hideglobal'] = 'Global events are shown (click to hide)';
$string['tt_hidegroups'] = 'Group events are shown (click to hide)';
$string['tt_hideuser'] = 'User events are shown (click to hide)';
$string['tt_showcourse'] = 'Course events are hidden (click to show)';
$string['tt_showglobal'] = 'Global events are hidden (click to show)';
$string['tt_showgroups'] = 'Group events are hidden (click to show)';
$string['tt_showuser'] = 'User events are hidden (click to show)';
$string['tue'] = 'Tue';
$string['tuesday'] = 'Tuesday';
$string['typecourse'] = 'Course event';
$string['typegroup'] = 'Group event';
$string['typesite'] = 'Site event';
$string['typeuser'] = 'User event';
$string['upcomingevents'] = 'Upcoming Events';
$string['urlforical'] = 'URL for iCalendar export, for subscribing to calendar';
$string['user'] = 'User';
$string['userevent'] = 'User event';
$string['userevents'] = 'User events';
$string['wed'] = 'Wed';
$string['wednesday'] = 'Wednesday';
$string['weeknext'] = 'Next week';
$string['weekthis'] = 'This week';
$string['yesterday'] = 'Yesterday';
$string['youcandeleteallrepeats'] = 'This event is part of a repeating event series. You can delete this event only, or all $a events in the series at once.';
$string['schedule'] = 'Personal schedule';
$string['calendar_details'] = 'Calendar details';
?>
