<? global $USER; ?>

<? print_heading(get_string('modulenameplural', 'smartcom') . ' prepaid card adjust'); ?>

<fieldset >
	<legend>Prepaid card search criteria</legend>     
	<label for="serialno">serialno: </label>
	<input type='text' id='serialno' value = '' maxlength="50"><br />
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

<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table> 
<div id="pager" class="scroll" style="text-align:center;"></div> 
<br /> 

<fieldset >
	<legend>Apply to result</legend>
	<label for="addcoinvalue">addcoinvalue: </label>
	<input type='text' id='addcoinvalue' value = '0' maxlength="10"><br />
	<label for="addperiodvalue">addperiodvalue: </label>
	<input type='text' id='addperiodvalue' value = '0' maxlength="50"><br />
	<label for="addbatchcode">addbatchcode: </label>
	<input type='text' id='addbatchcode' value = '' maxlength="100"><br />
	<input type='button' id='apply' value = 'Apply to set of selected card(s)'><span id='applyResult'></span>
</fieldset>


<script type="text/javascript" >
$(document).ready(function(){
	jQuery("#grid").jqGrid({ 
		url:'/mod/smartcom/api/prepaidcard_unused_list.php?q=2',
		datatype: "json", 
		colNames:['ID','SerialNo', 'FaceValue', 'CoinValue','PeriodValue','BatchCode','PublishDateTime'], 
		colModel:[ 
			{name:'id',index:'id', width:55}, 		
			{name:'serialno',index:'serialno', width:120}, 
			{name:'facevalue',index:'facevalue', width:80, align:"right"}, 
			{name:'coinvalue',index:'coinvalue', width:80, align:"right"}, 
			{name:'periodvalue',index:'periodvalue', width:80, align:"right"}, 
			{name:'batchcode',index:'batchcode', width:288},
			{name:'publishdatetime',index:'publishdatetime', width:140, jsonmap:"publishdatetime"}
		], 
		rowNum:100, 
		rowList:[100, 500, 1000], 
		// imgpath: gridimgpath, 
		pager: jQuery('#pager'), 
//		sortname: 'id', 
//      sortorder: "desc", 
		viewrecords: true, 
		multiselect: true, 		
		caption: "Search result" });		
		
	
	function LoadGridBaseOnSearchCondition(){
		var $serialno = $('#serialno').val();
		var $facevalue = $('#facevalue').val();
		var $batchcode = $('#batchcode').val();                
		var $fromdate = $('#fromdate').val();
		var $todate = $('#todate').val();

		$("#searchResult").show().html('loading grid ...');
		$('#grid').fadeOut();
	   
		gridurl = '/mod/smartcom/api/prepaidcard_unused_list.php?q=2&serialno=' + $serialno + 
			'&facevalue=' + $facevalue + 
			'&batchcode='+ $batchcode + 
			'&fromdate='+ $fromdate +
			'&todate='+ $todate;
			
		jQuery("#grid").setGridParam(
		{
			url: gridurl,
			page : 1
		}
		).trigger("reloadGrid"); 
		

		$('#grid').fadeIn();
		$("#searchResult").fadeOut(500);
	}
	
	
	
	$("#search").click(LoadGridBaseOnSearchCondition);
	
	$("#apply").click(function(){		
		var s = jQuery("#grid").getGridParam('selarrrow');
		var $addcoinvalue = jQuery.trim($('#addcoinvalue').val());
		var $addperiodvalue = jQuery.trim($('#addperiodvalue').val());
		var $addbatchcode = jQuery.trim($('#addbatchcode').val());
		
		if(s=='' || $addcoinvalue=='' || $addperiodvalue=='' || $addbatchcode=='')
		{
			alert('Need to provide all fields: coinvalue, periodvalue, batchcode, selected items.');
			return false;
		}		
		
		$("#applyResult").show().
		html('Applying ... Add ' + $addcoinvalue + ' coin(s), and extend ' + $addperiodvalue + ' day(s) to your selected card(s): ' + s)
		.load(
			'/mod/smartcom/api/prepaidcard_adjust.php', 
			{
				coinvalue : $addcoinvalue,
				periodvalue : $addperiodvalue,
				batchcode : $addbatchcode,
				idlist : s
			},
			function(){                
				$(this).fadeOut(10000);
			}
		);		
				
		LoadGridBaseOnSearchCondition();
	});
	
});

</script>