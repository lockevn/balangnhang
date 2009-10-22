<?php
// page to manage a users glossary
require("../../config.php");

require_login();

global $USER;

$nlang = array();
$langs = get_records('pgq_langs', 'userid', $USER->id);
$step = optional_param('step', 0, PARAM_RAW);

foreach ($langs as $lang)
	$nlang[$lang->id] = $lang->name;

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
.heading td { font-weight: bold }
</style>
<div class="menu" style="margin-bottom: -45px">
<a href="glossary.php"><?php echo get_string('openglossary','block_pers_glossary'); ?></a><br />
<a href="newlang.php"><?php echo get_string('newlang','block_pers_glossary'); ?></a><br />
</div>
<h3>Quiz</h3>
<?php
// Display setup form if step is 0
if ($step == 0) {
?>
<form method="post" action="quiz.php">
<input type="hidden" name="step" value="1" />
<table border="0">
<tr>
 <td colspan="2"><h4>Choose</h4></td>
</tr>
<tr>
 <td>Source Language</td>
 <td>
  <select name="source_lang">
<?php
foreach ($langs as $lang)
{
        print "   <option value=\"" . $lang->id . "\">". $lang->name ."</option>\n";
}
?>
  </select>
 </td>
</tr>
<tr>
 <td>Target Language</td>
 <td>
  <select name="target_lang">
<?php
foreach ($langs as $lang)
{
        print "   <option value=\"" . $lang->id . "\">". $lang->name ."</option>\n";
}
?>
  </select>
 </td>
</tr>
<tr>
 <td>Number of terms</td>
 <td><input type="text" size="8" name="termno" /></td>
</tr>
<tr>
 <td colspan="2">
  <input type="submit" value="Take quiz!" />
 </td>
</table>
<?php
}
elseif ($step == 1) // display quiz form itself if step is 1
{
$slang = required_param('source_lang', PARAM_RAW);
$tlang = required_param('target_lang', PARAM_RAW);
$termno = required_param('termno', PARAM_RAW);
$terms = get_records_select('pgq_terms', 'source_lang = \'' .addslashes($slang) . '\' AND target_lang = \'' . addslashes($tlang) . '\'', '', '*', '0',$termno);
?>
<form method="post" action="quiz.php">
<input type="hidden" name="step" value="2" />
<input type="hidden" name="source_lang" value="<?php echo $slang; ?>" />
<input type="hidden" name="target_lang" value="<?php echo $tlang; ?>" />
<input type="hidden" name="termno" value="<?php echo $termno; ?>" />
<table border="0">
<?php
$i = 0;
print "<tr><td><b>" . $nlang[$slang] . "</b></td><td> &nbsp;<b>" . $nlang[$tlang] . "</b></td></tr>\n";
foreach ($terms as $term)
{
	print "<tr><td>" . $term->sl_value . "</td>";
	print '<td>=<input type="hidden" name="sl_value[' . $i .']" value="' . $term->sl_value . '" /><input type="text" name="tl_value[' . $i . ']" /></td></tr>';
	print "\n";
	$i++;
}
print "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Submit\" /></td></tr></table>\n";
}
elseif ($step == 2) // display quiz results
{
$slang = required_param('source_lang', PARAM_RAW);
$tlang = required_param('target_lang', PARAM_RAW);
$termno = required_param('termno', PARAM_RAW);
$terms = get_records_select('pgq_terms', 'source_lang = \'' .addslashes($slang) . '\' AND target_lang = \'' . addslashes($tlang) . '\'', '', '*', '0',$termno);

$termlookup = array();
foreach ($terms as $t)
	$termlookup[$t->sl_value] = $t->tl_value;

$sl_values = required_param('sl_value', PARAM_RAW);
$tl_values = required_param('tl_value', PARAM_RAW);
$correct = 0;
?>
<h4>Results:</h4>
<table border="0" cellspacing="4">
<tr class="heading">
 <td>Term</td>
 <td style="padding-right: 8px">Your answer</td>
 <td>Correct answer</td>
</tr>
<?php
for ($i = 0; $i < count($sl_values); $i++)
{
	$val = $sl_values[$i];
	print "<tr><td>" . $val . "</td>";
	$bgcolor = (strtolower($termlookup[$val]) == strtolower($tl_values[$i]) ? '#44ff44' : '#ff4444');
	print "<td style=\"padding-left: 8px\"><font color=\"" . $bgcolor . "\">" . $tl_values[$i] . "</td><td>" . $termlookup[$val] . "</td></tr>\n";
	if (strtolower($termlookup[$val]) == strtolower($tl_values[$i]))
		$correct++;
}
$grade = round($correct/count($sl_values)*100, 2);
print "<tr><td style=\"border-top: 1px solid black\" colspan=\"3\">Grade: " . $grade . "% (" . $correct . "/" . count($sl_values) . ")</td></tr></table>\n";
} // end-elseif
?>

