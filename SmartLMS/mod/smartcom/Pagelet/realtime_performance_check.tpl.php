<fieldset>
	<legend>Online users in course</legend>
	<div id="pnlOnlinelist" ></div>
</fieldset>

<fieldset id="pnlChat">
	<legend>Chat panel</legend>	 
	<label for="username">Username to notice: </label>
	<input type='text' id='username' value = '' maxlength="250"><br />
	<label for="messageToUser">Message: </label>
	<textarea rows="3" cols="50" id='messageToUser'>
	</textarea>
	<input type='button' id='send' value = 'Send'>	<span id='sendResult'></span>
</fieldset>


<div id="pnlRealtimeactivity" class="box">
</div>


<script type="text/javascript" >
var message = '';

$(document).ready(function(){
	// $('#pnlChat').hide();
	$('#pnlRealtimeactivity').hide();
	$('#pnlOnlinelist').hide();
	
	
	
	function GetAndRenderOnlineUserList(){
		$('#pnlOnlinelist').load('/mod/smartcom/Pagelet/online_user_in_course.php?courseid=<?=$this->courseid?>').fadeIn();
	}	
	// load in init pagelet
	GetAndRenderOnlineUserList();	
	// set loop
	var oIntervalHandler = setInterval(GetAndRenderOnlineUserList, 10000);
			
	
	$("#send").click(function(){
		var currentUserid = <?=$this->userid?>;
		
		$("#sendResult").show().empty().load(
			'/mod/smartcom/api/messages_send.php', 
			{
				fromuserid : currentUserid,
				touserid : $('#username').attr('userid'),
				message: $('#messageToUser').val()
			},
			function(){				
				$(this).fadeOut(8888);
			}
		);
	});
	

	$("li.onlineuser").live("click", function(){        
		var urlToLoadCompletedQuizToday = '/mod/smartcom/Pagelet/lastest_today_completed_quiz.php?userid='+$(this).attr('userid')+'&courseid=<?=$this->courseid?>';
		$('#pnlRealtimeactivity').load(urlToLoadCompletedQuizToday).fadeIn();        
			
		$('#username').val($(this).text());
		$('#username').attr('userid', $(this).attr('userid'));
		
		message = 'Hello ' + $(this).text() + ', please click here http://gurucore.com to chat with me. I will assist you. ADMIN of SmartCom';
		$('#messageToUser').val(message);
	});    

	
	$("li.completedTodayQuiz").live("click", function(){        
		var urlToLoadQuizReview = '/mod/smartcom/Pagelet/quiz_review.php?attemptid='+$(this).attr('attemptid');
		$('#pnlRealtimeactivity').html('Please wait!').load(urlToLoadQuizReview).fadeIn();
	});    
});

</script>