<link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/css/print.css" type="text/css" media="print">
<!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

<div id="pnlOnlinelist" class="notice">
<?php if(!is_array($this->onlineUsers)): ?>
	<div class="info">No user online in current course</div> 
<?php else:  ?>
	<ul>
		<? foreach((array)$this->onlineUsers as $element): ?>
		<li class="onlineuser"><?= $element->username ?></li>
		<? endforeach; ?>
	</ul>
<?php endif;  ?>
</div>

<div id="pnlRealtimeactivity" class="box">Real time activity</div>

<div id="pnlChat" class="box">
<fieldset>
	<legend>Chat panel</legend>	 
	<label for="username">Username to notice: </label>
	<input type='text' id='username' value = '' maxlength="250"><br />
	<label for="messageToUser">Message: </label>
	<textarea rows="3" cols="50" id='messageToUser'>
	</textarea>
	<input type='button' id='send' value = 'Send'>
</fieldset>

</div>

<script type="text/javascript" >
$(document).ready(function(){
	$('#pnlChat').hide();
	$('#pnlRealtimeactivity').hide();
	
	$("li.onlineuser").live("click", function(){		
		
		$('#pnlChat').fadeIn();		
		$('#pnlRealtimeactivity').fadeIn();
				
		$('#username').val($(this).text());
		$('#messageToUser').val(
			'Hello ' + $(this).text() + ', please click here http://gurucore.com to chat with me. I will assist you. ADMIN of SmartCom'
		);
	});
	
	
	$("#send").click(function(){
		alert('hello admin, I will send your message, later ;) ');
	});
	
});

</script>
