<?php
/**
 * Language strings for new study calendar course format.
 *
 * @copyright &copy; 2006 The Open University
 * @author S.Marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

// Name strings used in other bits of Moodle
$string['formatstudycal']='Study calendar';
$string['namestudycal']='study week';

// Inside calendar weeks
$string['events']='Events';
$string['weeks']='$a weeks';
$string['days']='$a days';
$string['hours']='$a hours';
$string['mins']='$a mins';
$string['week']='1 week';
$string['day']='1 day';
$string['hour']='1 hour';
$string['min']='1 min';
$string['datetime']='$a->date, $a->time';
$string['duration']='$a->datetime; for $a->duration';

// Icon alt text
$string['combineweeks']='Combine weeks';
$string['splitweeks']='Split weeks';
$string['editweeksettings']='Edit week settings';

// Week editing
$string['hideweeknumber']='Hide week number';
$string['resetweeknumber']='Restart numbering from this week at: ';
$string['hidedate']='Hide week date';
$string['weektitle']='Optional title for week or block'; 

// Calendar editing
$string['hideweeknumbers']='Hide all week numbers';
$string['startdate']='Calendar start date (relative to course)';
$string['weekstoview']='Weeks to view';
$string['uploadcalendar']='Upload entire calendar';
$string['uploadexplanation']='
You can upload a .csv or .(x)htm(l) file containing the calendar information in a specific format.<br />
<strong>This will entirely erase the structure of your current calendar.</strong><br />Activities will
not be deleted but labels that were uploaded in the first place will go.
'; 
$string['upload']='Upload';

// Upload edit form
$string['checkupload']='Check uploaded calendar';
$string['deleteheader']='Exclude this header and all its data entries';
$string['checkuploadexplanation']='
<p>Please check the calendar and make any changes before confirming the upload.</p>
<ul>
<li> Entries related to <strong>TMA dates</strong> should be removed entirely (use the checkbox) because we will
add these automatically for most courses.</li>
<li> Each entry displays as the header followed by text. Sometimes the <strong>header is unnecessary</strong>,
for example if the heading is \'CDs\' and each entry begins with \'CD\'. In this case simply edit the header so
that it is completely blank.</li>
<li> Headers should be <strong>short</strong>; delete any unnecessary text.</li> 
<li> Headers should follow standard capitalisation, so <strong>Course text</strong> not <strong>Course Text</strong>.</li>
<li> Any footnotes indicated by asterisks on specific entries should be converted where possible to text on the 
entries themselves, or deleted.</li>
</ul>';
$string['week']='Week';
$string['thisweek']='This week: ';
$string['entry']='Entry';
$string['header']='Header $a'; 

// Top of calendar
$string['studycalendar']='Study calendar';
$string['calendarlocked']='Calendar structure is locked';
$string['calendarunlocked']='Calendar structure is unlocked';
$string['editcalendarsettings']='Edit calendar settings';

// Bottom of calendar
$string['showallweeks']='Show entire calendar';
$string['showcurrentweeks']='Show current weeks';

// Progress viewing
$string['viewprogress']='View progress:';
$string['course']='Course';
$string['group']='Group';
$string['viewprogressfor']='Progress for $a';
$string['forcourse']='entire course';
$string['forgroup']='group $a';

// Checkboxes
$string['checkboxnojs']='<small>Because Javascript is not enabled in your browser, the progress tick boxes beside each item are unavailable.</small>';
$string['checkboxexplanation']='
You can use the tick boxes beside each item to record your progress through the course.<br /><small>This is optional. Tutors and other staff can see what you\'ve ticked.</small>';

// Capabilities
$string['studycal:lock']='Lock study calendar structure';
$string['studycal:manage']='Manage study calendar structure';
$string['studycal:upload']='Upload study calendar';
$string['studycal:trackprogress']='Track own calendar progress';
$string['studycal:viewgroupprogress']='View calendar progress within group';
$string['studycal:viewallprogress']='View calendar progress within course';

$string['strftimedateshortest'] = '%%d %%b';
$string['view'] = 'View';
$string['viewmultiplecalendars'] = 'View combined study calendar';
$string['viewcoursecalendars'] = 'View events calendar';
$string['viewcalendars'] = 'View calendars';
$string['selectcalendars'] = 'Select calendars';
$string['showselected'] = 'Show selected';
$string['toomanycourses'] = 'You have selected too many courses - this page can only display $a at a time.
Please choose up to 3 courses.';
$string['invalidcoursesettings'] = 'Invalid course - incorrect format or weeks';
$string['yourstudycalendars'] = 'Combined study calendar';
