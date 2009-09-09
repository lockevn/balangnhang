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
<div id='grid'>
</div>

<fieldset >
	<legend>Apply to result</legend>
	<label for="coinvalue">coinvalue: </label>
	<input type='text' id='coinvalue' value = '' maxlength="10"><br />
	<label for="periodvalue">periodvalue: </label>
	<input type='text' id='periodvalue' value = '' maxlength="50"><br />
	<input type='button' id='apply' value = 'Apply to set of selected card(s)'><span id='applyResult'></span>
</fieldset>





<script type="text/javascript" >
$(document).ready(function(){
	
	$("#search").click(function(){
		$("#searchResult").show().html('loading grid ...').load(
			'/mod/smartcom/api/messages_send.php', 
			{
				facevalue : $('#facevalue').val(),
				batchcode : $('#batchcode').val(),				
				fromdate : $('#fromdate').val(),
				todate : $('#todate').val()
			},
			function(){                
				$(this).fadeOut(8888);
				$('#grid').html('this is list of card(s)').fadeOut().fadeIn();
			}
		);
	});
	
	$("#apply").click(function(){
		$("#applyResult").show().html('Applying ... Add coin = ' + $('#coinvalue').val() + ', and extend period = ' + $('#periodvalue').val() + ' to your selected card(s)');
	});
	
});

</script>