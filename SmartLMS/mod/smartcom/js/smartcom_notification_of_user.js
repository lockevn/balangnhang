$(document).ready(function(){
	var intervalTimeout = 5000;
	var lastJson = null;	
	var oPoolingIntervalHandler = null;
	
	$.jGrowl.defaults.close = function(e,m,o) {
		// delete the notice which is closed by user
		$.get(
		 '/mod/smartcom/api/notification_delete.php',
		 {
			id : o.noticeid
		 });
	};
				
	function GetAndRenderNotificationList(){
		$.getJSON(
		 '/mod/smartcom/api/notification_of_user.php'
		 ,
		 {courseid : CURRENT_COURSEID}
		 ,  
		 function(json){
			 if(json)
			 {		   
				if(json.stat == 'NOT_ALLOW' || json.stat == 'NOT_LOGIN')
				{
					// alert('Stop pooling');
					// not allow, do not has capability to pooling
					clearInterval(oPoolingIntervalHandler);
					return false;
				}
				
				// ignore do anything if response is no change				
				if(lastJson != null && 
				lastJson.length >= json.length &&
				lastJson[lastJson.length - 1].id >= json[json.length - 1].id
				)
				{
					// recent json does not have any newer entry than last rendered json					
					return true;
				}
				
				$.each(json, function(i, item){
					var bExistedItem = false;
					
					if(lastJson)
					{
						$.each(lastJson, function(j, olditem){
							if(olditem.id == item.id)
								bExistedItem = true;
						});
					}
					
					if(bExistedItem == false)					
					{
						var content = '<a href="'+ item.link +'" target="_blank" class="notificationlink">' + item.message + '</a>';
						$('#jGrowlAnchor').jGrowl(content, 
							{ 
								header: 'Notification from ' + item.senderusername,
								sticky : true,
								closer: false,
								noticeid : item.id
							});
					}
					else
					{
						// console.log('dont render duplicate entry ' + item.id);
					}
				});
				
				// save the last request to prevent duplicate display
				lastJson = json;
			 }
		 });    
	}
	
	
	
	// load in init pagelet
	GetAndRenderNotificationList();    
	// set loop
	oPoolingIntervalHandler = setInterval(GetAndRenderNotificationList, intervalTimeout);
				
});