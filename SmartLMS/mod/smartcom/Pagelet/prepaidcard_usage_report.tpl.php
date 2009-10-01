<? print_heading(get_string('modulenameplural', 'smartcom') . ' prepaidcard usage report'); ?>
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


<script type="text/javascript" >
$(document).ready(function(){
	jQuery("#grid").jqGrid({ 
		url:'/mod/smartcom/api/prepaidcard_used_list.php?q=2',
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
//        sortname: 'id', 
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
	   
		gridurl = '/mod/smartcom/api/prepaidcard_used_list.php?q=2&serialno=' + $serialno + 
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
		
});

</script>