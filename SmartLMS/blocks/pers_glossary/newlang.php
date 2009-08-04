<?php
// page to manage a users glossary
require("../../config.php");

require_login();

global $USER;


print_header(get_string('blockname','block_pers_glossary'), get_string('blockname','block_pers_glossary'), '<a href="'.$CFG->wwwroot.'/my/">My Moodle</a> <span class="sep">&#x25B6;</span> '.get_string('blockname','block_pers_glossary'), "", "", true, "", "");

$lang = optional_param('lang', '', PARAM_RAW);

if ($lang)
{
	//Add a new langu
	$l = new object;
	$l->name = addslashes($lang);
	$l->userid = $USER->id;
	insert_record('pgq_langs',$l);
}
$langs = get_records('pgq_langs', 'userid', $USER->id);
?>
<div class="menu" style="margin-bottom: -45px">
<a href="glossary.php"><?php echo get_string('openglossary','block_pers_glossary'); ?></a><br />
<a href="quiz.php"><?php echo get_string('startquiz','block_pers_glossary'); ?></a><br />
</div>
<h3>Current languages</h3>
<?php
foreach ($langs as $l)
{
	print $l->name . "<br />\n";
}

?>
<style type="text/css">
.menu { position: relative; width: 120px; left: 650px; top: 10px; border: 1px solid #ccc; background: #eee; padding: 5px;}
</style>
<h3>Add language</h3>
<form method="post">
Name <input type="text" name="lang" /> <input type="submit" value="Add!" />
</form>
