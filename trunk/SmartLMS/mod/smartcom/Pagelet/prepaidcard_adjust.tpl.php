<? global $USER; ?>

<? print_heading(get_string('modulenameplural', 'smartcom') . ' prepaid card adjust'); ?>

<fieldset >
	<legend>Prepaid card search criteria</legend>     
	<label for="facevalue">facevalue: </label>
	<input type='text' id='facevalue' value = '' maxlength="10"><br />
	<label for="batchcode">batchcode: </label>
	<input type='text' id='batchcode' value = '' maxlength="50"><br />
	
	<label for="fromdate">fromdate: </label>
	<input type='text' id='fromdate' value = '' maxlength="50">
	<label for="todate">todate: </label>
	<input type='text' id='todate' value = '' maxlength="50"><br />
	
	<input type='button' id='search' value = 'Search'><span id='searchResult'></span>
</fieldset>

<h3 class="main">Result</h3>


<fieldset >
	<legend>Apply to result</legend>
	<label for="coinvalue">coinvalue: </label>
	<input type='text' id='coinvalue' value = '' maxlength="10"><br />
	<label for="periodvalue">periodvalue: </label>
	<input type='text' id='periodvalue' value = '' maxlength="50"><br />
	<input type='button' id='apply' value = 'Apply to set of selected card(s)'><span id='applyResult'></span>
</fieldset>