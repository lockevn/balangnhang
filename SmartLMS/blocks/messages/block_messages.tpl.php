<? 
global $CFG; 
?>

<div id="message_content">
</div>

<script type="text/javascript" language="javascript" >
var oIntervalHandler;

$(document).ready(function(){
	$.ajaxSetup ({  
		cache: false  
	});
   
	var $datalist = $("#message_content");		
	GetAndRenderList();
		
	function GetAndRenderList(){
		var url = 'http://192.168.2.198/blocks/messages/message_list.php?userid=<?=$this->userid?>&courseid=<?=$this->courseid?>';		
		$datalist.load(url);		
	}
	
	$($datalist).mouseover(function(){
		if(oIntervalHandler)
		{
			clearInterval(oIntervalHandler);
			oIntervalHandler = null;
		}
	}).bind("mouseleave",function(){
		if(!oIntervalHandler)
		{            
			oIntervalHandler = setInterval(GetAndRenderList, 10000);            
		}
	});
	
	oIntervalHandler = setInterval(GetAndRenderList, 10000);
});
</script>