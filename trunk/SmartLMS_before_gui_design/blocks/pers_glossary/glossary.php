<?php
// page to manage a users glossary
require("../../config.php");

require_login();

global $USER;

$langs = get_records('pgq_langs', 'userid', $USER->id);

print_header(get_string('blockname','block_pers_glossary'), get_string('blockname','block_pers_glossary'), '<a href="'.$CFG->wwwroot.'/my/">My Moodle</a> <span class="sep">&#x25B6;</span> '.get_string('blockname','block_pers_glossary'), "", "", true, "", "");
?>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/lib/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/lib/yui/event/event.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/lib/yui/connection/connection.js"></script>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/blocks/pers_glossary/json.js"></script>
<style type="text/css">
.menu { position: relative; width: 120px; left: 650px; top: 10px; border: 1px solid #ccc; background: #eee; padding: 5px;}
.spacer { font-size: 1px; clear: both; margin: 0; padding: 0; height:0; }
.clearfix { zoom: 1; }

.clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }

* html .clearfix { height: 1%; }
</style>
<div class="menu" style="margin-bottom: -45px">
<a href="quiz.php"><?php echo get_string('startquiz','block_pers_glossary'); ?></a><br />
<a href="newlang.php"><?php echo get_string('newlang','block_pers_glossary'); ?></a><br />
</div>
<h3>Personal Glossary</h3>
<form method="post" id="termform">
<input type="hidden" name="id" value="-1" />
<div style="float: left">
<table border="0">
  <tr>
    <td colspan="2">
      <strong>Source</strong>
    </td>
  </tr>
  <tr>
    <td>Language</td>
    <td>
      <select name="source_lang" onChange="populateSelector(this.value,1)">
<?php
foreach ($langs as $lang)
{
	print "      <option value=\"" . $lang->id . "\">". $lang->name ."</option>\n";
}
?>
      </select>
    </td>
  </tr>
  <tr>
    <td>Term</td>
    <td><input type="text" name="sl_value" /></td>
  </tr>
  <tr>
    <td>Notes</td>
  </tr>
  <tr>
    <td colspan="2">
      <textarea cols="40" rows="3" name="sl_notes"></textarea>
    </td>
  </tr>
</table>
</div>

<div style="float: left; margin-left: 30px">
<table border="0">
  <tr>
    <td colspan="2">
      <strong>Target</strong>
    </td>
  </tr>
  <tr>
    <td>Language</td>
    <td>
      <select name="target_lang" onChange="populateSelector(this.value,2)">
<?php
foreach ($langs as $lang)
{
	print "      <option value=\"" . $lang->id . "\">". $lang->name ."</option>\n";
}
?>
      </select>
    </td>
  </tr>
  <tr>
    <td>Term</td>
    <td><input type="text" name="tl_value" /></td>
  </tr>
  <tr>
    <td>Notes</td>
  </tr>
  <tr>
    <td colspan="2">
      <textarea cols="40" rows="3" name="tl_notes"></textarea>
    </td>
  </tr>
  <tr>
   <! -- default button is just save! -->
   <td colspan="2" align="right" id="buttons">
     <div style="float: left; width: 100px" id="status"></div>
     <input type="button" onclick="saveform()" value="Save" />
   </td>
  </tr>
</table>
</div>
<div class="spacer clearfix">&nbsp;</div>
<div id="curlang"></div>
<div id="termSelector">
</div>
<!-- fetch and display a big A B C D thingamageek for the terms.. -->
<script type="text/javascript">
// set default source lang
var dh = '<?php echo $CFG->wwwroot ?>/blocks/pers_glossary/data.php';
var seldiv = document.getElementById('termSelector');
var statdiv = document.getElementById('status');
var letters = new Object();
var letter_arr = new Array();
var lastsellang = document.getElementById('termform').source_lang.value;
var lastselwhich = 1;

var langlist = new Array();
<?php
foreach ($langs as $lang)
{
	print "langlist[" . $lang->id . "] = '" . $lang->name . "';\n";
}
?>

function renderStatus(o)
{
	if (o.responseText !== undefined)
	{
		var ret = from_json(o.responseText);
		if (ret.ok)
		{
			statdiv.innerHTML = "Success";
			statdiv.style.backgroundColor = '#33ff33';
			populateSelector(lastsellang,lastselwhich);
		}
		else
		{
			statdiv.innerHTML = "Failed";
			statdiv.style.backgroundColor = '#ff3333';
		}
	}
}

function renderSelector(o)
{
	if (o.responseText !== undefined)
	{
		var ldhtml = '<div id="letterdiv" style="border-bottom: 1px solid #000"></div>';
		varlisthtml = '<div id="termlist"></div>';
		seldiv.innerHTML = ''; //o.responseText;
		seldiv.innerHTML += ldhtml;
		seldiv.innerHTML += varlisthtml;
		var letterdiv = document.getElementById('letterdiv');
		letters = new Object();
		letter_arr = new Array();

		var ret = from_json(o.responseText);
		if (ret.ok == 0)
			return;
		terms = ret.terms;
		for (id in terms)
		{
			var t = terms[id];
			var startc = t.sl_value.charAt(0).toUpperCase();
			if (!letters[startc])
			{
				//console.log("hum..");
				letters[startc] = new Array();
				letter_arr.push(startc);
			}
			letters[startc].push(t);
			//console.log(id, startc, t);
			//console.log(letters);
		}

		letter_arr = letter_arr.sort();
		for (l in letter_arr)
		{
			letter = letter_arr[l];
			letterdiv.innerHTML += '<a href="javascript:void(0)" onclick="showletter(\''+letter+'\')">'+letter+'</a> ';
		}
		// show the first letter stuff
		showletter(letter_arr[0]);
	}
}

function showletter(startc)
{
	var tl = document.getElementById('termlist');
	tl.innerHTML = '';
	for (var i = 0; i < letters[startc].length; i++)
	{
		var term = letters[startc][i];
		tl.innerHTML += '<a href="javascript:void(0)" onclick="editterm(\''+ startc +'\','+i+')">'+term.sl_value+'</a><br />';
	}
}

function editterm(startc,i)
{
	var term = letters[startc][i];
	var f = document.getElementById('termform');
	// add hidden id field for editing
	f.id.value = term.id;
	// populate form
	f.sl_value.value = term.sl_value;
	f.tl_value.value = term.tl_value;
	f.sl_notes.value = term.sl_notes;
	f.tl_notes.value = term.tl_notes;
}

function handleFailure(o)
{
	alert("xmlhttprequest failed!\nTransaction id: "+o.tId+"\nHTTP status: "+o.status+"\nStatus text: "+o.statusText); 
}

function populateSelector(lang,which)
{
	var qs;
	var ws;
	if (which == 1)
	{
		qs = '?data='+encodeURIComponent(to_json({act: 'get', source_lang: lang}));
		ws = 'Source language';
	}
	else
	{
		qs = '?data='+encodeURIComponent(to_json({act: 'get', target_lang: lang}));
		ws = 'Target language';
	}

	document.getElementById('curlang').innerHTML = 'Terms from '  + langlist[lang] + ' (' + ws + ')';
	var callback = {
		success: renderSelector,
		failure: handleFailure,
	};
	var request = YAHOO.util.Connect.asyncRequest('GET', dh+qs, callback); 
}

function saveform()
{
	// get the form
	var f = document.getElementById('termform');
	var act = 'insert';
	if (f.id.value != -1)
		act = 'edit';

	var qs = '?data='+encodeURIComponent(to_json({	act: act,
							source_lang: f.source_lang.value,
							target_lang: f.target_lang.value,
							sl_value: f.sl_value.value,
							tl_value: f.tl_value.value,
							sl_notes: f.sl_notes.value,
							tl_notes: f.tl_notes.value,
							id: f.id.value}));
	var callback = {
		success: renderStatus,
		failure: handleFailure,
	};
	var request = YAHOO.util.Connect.asyncRequest('GET', dh+qs, callback); 
}

populateSelector(lastsellang, lastselwhich);
</script>

