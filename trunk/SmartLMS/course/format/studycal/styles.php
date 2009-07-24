/* Overall layout */
.studycal-format {
  min-width: 763px;
  margin-top:0.5em;
}
.studycal-format #middle-column {
  margin: 0 12.5em 0 12.5em;
}
.studycal-format #left-column,
.studycal-format #right-column {
  width: 11.5em;
}
.studycal-format #left-column {
  float: left;
}
.studycal-format #right-column {
  float: right;
}

.studycal-format .accesshide,
#course-format-studycal-calendars .accesshide {
    position:absolute;
    left:-10000px;
}

.studycal-format h3.weeknum {
    font-size:1.0em;
    margin:0;
    font-weight:normal;
}

/* Progress */
#course-view .studycal-format li.activity,
#course-view .studycal-format ul.studycalentries li {
    position:relative;
    padding-right:2em;
}
#course-view .studycal-format.editing li.activity,
#course-view .studycal-format.editing ul.studycalentries li {
    padding-right:3em;
}
.studycal-format .studycalcheckbox {
    position:absolute;
    right:0;    
}
.studycal-format .studycalcheckbox input {
    margin-left:0;
}
.studycal-format .studycalcheckbox form {
    display:inline;
}

.studycal-format .viewprogress {
    text-align:right;
    font-size:0.8em;
    margin:0.2em 0 0.5em; 
}
.studycal-format .viewprogress h3 {
    display:inline;
    font-size:1.0em;
    font-weight:normal;
    margin:0;
}
.studycal-format .viewprogress li {
    display:inline;
    margin:0 0 0 0.5em;
}   
.studycal-format .viewprogress ul {
    display:inline;
}


/* Special-case overrides */
.studycal-format .block_adminblock select,
.studycal-format .block_calendar_month .minicalendar {
  width: 100%;
  padding: 0;
}
.studycal-format .block_calendar_month .minicalendar th,
.studycal-format .block_calendar_month .minicalendar td {
  padding: 0.1em 0 0.1em 1px;
}

/* Week entries and top entry */
.studycal-format li.section,
.studycal-format #section-0 {
}

/* IE 6 and 7 fixes */
* html #course-view .studycal-format li.activity,
* html .studycal-format ul.studycalentries li.studycalevent,
* html #course-view .studycal-format li.studycalsection,
* html .studycal-format div.studycalcontent {
    height:1px;
}
#course-view .studycal-format li.activity,
.studycal-format ul.studycalentries li.studycalevent,
.studycal-format li.studycalsection,
.studycal-format div.studycalcontent {
    min-height:1px;
}


/* Week entries */
.studycal-format ul,
.studycal-format .studycalweeks li.studycalsection {
    display:block;
    margin:0;
    padding:0;   
    list-style-type:none;
}

.studycal-format li.studycalsection {
    border-top:1px solid #ddd;
}

.studycal-format div.studycalleft {
    float:left;
    width:2em;
    padding:4px;
}
.studycal-format div.studycalcontent {
    margin-left:2.5em;
    padding: 4px;
    padding-bottom:4px;
    min-height:1px;
    margin-bottom:0;
}
.studycal-format.editing div.studycalleft {
    width:72px;
}
.studycal-format.editing div.studycalcontent {
    margin-left:76px;
}
.studycal-format.editing div.studycalcontentdeco2 {
    min-height:3em;
}
* html .studycal-format.editing div.studycalcontentdeco2 {
    height:3em;
} 
.studycal-format.editing li.grouped div.studycalcontentdeco2 {
    min-height:5.5em;
}
* html .studycal-format.editing li.grouped div.studycalcontentdeco2 {
    height:5.5em;
} 
.studycal-format div.studycalcontentdeco2 {
    min-height:1.5em;
}
* html .studycal-format div.studycalcontentdeco2 {
    height:1.5em;
}
.studycal-format li.grouped div.studycalcontentdeco2 {
    min-height:4em;
}
* html .studycal-format li.grouped div.studycalcontentdeco2 {
    height:4em;
}

.studycal-format .studycalleft .controlicons {
    margin-top: 4px;
}
.studycal-format .studycalleft form {
    display:inline;
}

.studycal-format .current {
    background: #ffd991;    
}
.studycal-format .current .studycalcontent {
    background:white;
}

.studycal-format.editing h2.studycaltop span.studycaltopleft {
    float:left;
    width:76px;
}
.studycal-format.editing h2.studycaltop span.studycaltopleft form {
    display:inline;
}

.studycal-format span.studycalentry {
    font-weight:bold;
}
.studycal-format .studycalweekdivider {
    margin-left:1px;
}


#course-format-studycal-upload-upload #explanation p,
#course-format-studycal-upload-upload #explanation li {
  width:40em;
}

#course-format-studycal-upload-upload form ul,
#course-format-studycal-upload-upload form li {
  display:block;
  list-style-type:none;
  margin-top:2em; 
  padding:0;
  margin-left:1em;
}

#course-format-studycal-upload-upload form table {
    margin-top:0.5em;
}
#course-format-studycal-upload-upload form table th {
    text-align:left;
    font-weight:normal;
    font-style:italic;
}
#course-format-studycal-upload-upload form table th.left {
    padding-right:0.5em;
}
#course-format-studycal-upload-upload form table td {
    padding-top:0.5em;
}
#course-format-studycal-upload-upload form h3 {
    font-size:1em;
    margin:0 0 0.5em -2em;
    padding:1.5em 0 0 2em;
    border-top:1px solid #ddd;  
}

#course-format-studycal-upload-upload form ul {
    border-bottom:1px solid #ddd;
    margin-bottom:2em;
    padding-bottom:2em;
}

#course-format-studycal-upload-upload #savechanges {
    margin: 0 0 1.5em 1em;
}

#course-format-studycal-viewprogress th {
  text-align:left;
}
#course-format-studycal-viewprogress td {
  vertical-align:top;
}
#course-format-studycal-viewprogress .noticks {
  color:#aaa;
}
#course-format-studycal-viewprogress .yes {
  color:#393;
}
#course-format-studycal-viewprogress .no.hasticks {
  color:#a44;
}
#course-format-studycal-viewprogress .odd {
  background:#f4f4f4;
}
#course-format-studycal-viewprogress td,
#course-format-studycal-viewprogress th {
  padding:4px 8px;
}
#course-format-studycal-viewprogress #content {
  margin:0 8px;
}
#course-format-studycal-viewprogress h1 {
  font-size:1em;
  margin:0;
}

/* New course/format/studycal styles */
.studycal-format .studycaltopright {
    font-weight:normal;
    float:right;
}
.studycal-format .studycalheadertext {
    vertical-align:top;
}

.studycal-format .studycalimg {
    margin-left:3px;
    margin-right:3px;
}

#course-format-studycal-calendars table.sctable {
    border:0;
    border-collapse:separate;
    border-spacing:4px;
    position:relative;
    left:-4px;
    background:white;
}

#course-format-studycal-calendars th.week {
    vertical-align:top;
    padding:0.5em;
    border:1px solid #e7e7d6;
    background:#e7e7d6;
    text-align:left;
    font-weight:normal;
}

#course-format-studycal-calendars .current th.week {
    border:1px solid #a6caf0;
    background:#a6caf0;
}

#course-format-studycal-calendars th.content {
    vertical-align:top;
    padding:0.1em 0.5em;
    border:1px solid #e7e7d6;
    background:#e7e7d6;
    text-align:left;
    font-weight:normal;
}

#course-format-studycal-calendars td.filler {
    border:1px solid #e7e7d6;
    background:#e7e7d6;
}

#course-format-studycal-calendars td.content {
    vertical-align:top;
    padding:0.5em;
    border:1px solid #f1f1f1;
    background:#f1f1f1;
}

#course-format-studycal-calendars .current td.content {
    border:1px solid #a6caf0;
}

#course-format-studycal-calendars ul {
    display:block;
    margin:0;
    padding:0;   
    list-style-type:none;
}

#course-format-studycal-calendars .posright,
#course-format-studycal-calendars .studycalcheckbox {
    position:absolute;
    right:0;
}

#course-format-studycal-calendars .posrel,
#course-format-studycal-calendars li.activity,
.studycal-format ul.studycalentries li.studycalevent,
#course-format-studycal-calendars ul.studycalentries li {
    position:relative;
    padding-right:2em;
    padding-top:0.2em;
    padding-bottom:0.2em;
}

#course-format-studycal-calendars span.studycalentry {
    font-weight:bold;
}

#course-format-studycal-calendars .studycalentries {
    margin-top:4px;
    border-top:1px solid #e7e7d6;
    padding-top:4px;
}

#course-format-studycal-calendars .current .studycalentries {
    border-top-color: #a6caf0;
}

#course-format-studycal-calendars li.activity h3 {
    font-size:1em;
    color:#666666;
    margin:0;
}

#course-format-studycal-calendars .coursecheckbox {
    float:left;
    min-width:16%;
    width:16%;
}

#course-format-studycal-calendars .showbutton {
    float:left;
    margin-bottom:1em;
}
