<? global $USER; ?>

<? print_heading(get_string('modulenameplural', 'smartcom') . ' prepaid card generator'); ?>

<fieldset >
	<legend>Input parameter for batch generator process</legend>
	<label for="facevalue">facevalue: </label>
	<input type='text' id='facevalue' value = '' maxlength="10"><br />
	<label for="coinvalue">coinvalue: </label>
	<input type='text' id='coinvalue' value = '0' maxlength="10"><br />
	<label for="periodvalue">periodvalue: </label>
	<input type='text' id='periodvalue' value = '0' maxlength="50"><br />	
	<label for="batchcode">batchcode: </label>
	<input type='text' id='batchcode' value = '' maxlength="50"><br /><br />
	<label for="howmuch">how much: </label>
	<input type='text' id='howmuch' value = '' maxlength="6"><br /><br />
	
	
	<span>Click generate button, then wait. You will be redirect to download the excel file</span><br />
	<input type='button' id='generate' value = 'Generate'><span id='generateResult'></span>
</fieldset>

<script type="text/javascript" >
$(document).ready(function(){
	$.ajaxSetup ({  
		cache: false  
	}); 
		
	$("#generate").click(function(){
		var $facevalue = jQuery.trim($('#facevalue').val());
		var $coinvalue = jQuery.trim($('#coinvalue').val());
		var $periodvalue = jQuery.trim($('#periodvalue').val());
		var $batchcode = jQuery.trim($('#batchcode').val());
		var $howmuch = jQuery.trim($('#howmuch').val());
		
		if($facevalue=='' || $coinvalue=='' || $periodvalue=='' || $batchcode=='' || $howmuch=='')
		{
			alert('Need to provide all fields: facevalue, coinvalue, periodvalue, batchcode, howmuch.');
			return false;
		}
		
		$("#generateResult").show().html('Generating, please wait ...');
		$("#sendResult").show().empty();
		$.blockUI();
		$.post(
			'/mod/smartcom/api/prepaidcard_generator.php', 
			{
				facevalue : $facevalue,
				coinvalue : $coinvalue,
				periodvalue : $periodvalue,
				batchcode : $batchcode,
				howmuch : $howmuch
			},
			
			function(response){
				$.unblockUI();
				if(response.substr(0,4) !== 'http')
				{
					$("#generateResult").html('Generator engine is down. Reason = ' + response);
					return false;
				}				
				$("#generateResult").html('Please download the excel file');				
				$("#generateResult").fadeOut(18888);
				window.status = "";
				window.location = response;
			},
			"text"
		);
				
	});
	
});

</script>