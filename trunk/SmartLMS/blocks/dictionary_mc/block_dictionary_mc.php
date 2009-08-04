<? 
// code by Manh Cuong 
// manhcuong069@yahoo.com
// Data: Ho Ngoc Duc


class block_dictionary_mc extends block_list {
    // The init() method does not need to change at all

    function init() {
	    $this->title = get_string('dic_mc_title', 'block_dictionary_mc');
        $this->version = 2006150300;
	}
	
	function preferred_width() {
    // The preferred value is in pixels
    return 200;
}

function get_content() {
    if ($this->content !== NULL) {
        return $this->content;}
    

	

	global $CFG;

$this->content->items[] = '<!-- block added by Manh Cuong -->
<script type="text/javascript" src="http://e4kid.net/portal/blocks/dictionary_mc/modalbox/lib/prototype.js"></script>
<script type="text/javascript" src="http://e4kid.net/portal/blocks/dictionary_mc/modalbox/lib/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="http://e4kid.net/portal/blocks/dictionary_mc/modalbox/modalbox.js"></script>
<link rel="stylesheet" href="http://e4kid.net/portal/blocks/dictionary_mc/modalbox/modalbox.css" type="text/css" media="screen" />

<script src=\'http://e4kid.net/portal/blocks/dictionary_mc/clicksee.js\'></script>
<script language="javascript">
function doSearch(obj){ 
	Modalbox.show("http://e4kid.net/portal/dict/search_frame.php?dict=" + obj.dict.value + "&word=" + obj.word.value, {title: \'Từ điển E4KID\', width: 600}); 
	return false;
}
</script>
<form onSubmit="return doSearch(this);" name="dictionary" method=post action="http://e4kid.net/portal/dict/search.php" target="_blank" style="margin:0px;">
	<div style="padding-bottom:6px"> '.get_string('dic_mc_db', 'block_dictionary_mc').' <br /> 
	<select name="dict" style="font-size: 11px; width: 130px;"> 
		<option value="ev" selected="true">'.get_string('dic_mc_db_ev', 'block_dictionary_mc').'</option> 
		<option value="ve">'.get_string('dic_mc_db_ve', 'block_dictionary_mc').'</option> 
	</select></div>
	<div> '.get_string('dic_mc_enter_word', 'block_dictionary_mc').'<br /> 
	<input type="text" style="font-size: 11px; width: 87px;" name="word" /> 
	<input type="submit" value="'.get_string('dic_mc_button_search', 'block_dictionary_mc').'" name="go" style="font-size: 11px;" /> 
	</div> </form>
';
return $this->content;
}
}
?>