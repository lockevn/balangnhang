<? global $USER; ?>

<? print_heading(get_string('modulenameplural', 'smartcom')); ?>

<?php if($this->state === 'depositok'): ?>
	<div class="info">Deposit OK!</div> 
<?php elseif($this->state === 'depositfail'):  ?>
	<div class="info">Deposit Fail! Please use correct card code. Nhập sai quá 3 lần thì tài khoản của bạn sẽ bị khoá.</div> 
<?php else:  ?>    
	<div class="info">Scratch your prepaid card, provide the secret code here</div> 
<?php endif;  ?>

<fieldset >
	<legend>Deposit SmartCom prepaid card to account</legend>     
	<label for="username">Username: </label>	
	<strong><?=$USER->username ?></strong>
	<br /><br />
	
	<label for="code">Code: </label> <input type='text' id='code' value = '' maxlength="50"><br />
	
	<input type='button' id='send' value = 'Send'>
	<div id='sendResult'></div>
</fieldset>
<!--<pre>
<? print_r($USER); ?>
</pre>-->


<script type="text/javascript" >
var message = '';

$(document).ready(function(){			
	$.ajaxSetup ({  
		cache: false  
	});  

	$("#send").click(function(){
		
		$("#sendResult").show().empty();
		$.blockUI();
		$.get(
			'/mod/smartcom/api/prepaidcard_enduser_deposit.php', 
			{
				code : $('#code').val()
			},
			function(response){
				$.unblockUI();
				response = parseInt(response);
				if(response == 0)
				{
					$("#sendResult").html('Nap the thanh cong');
				}
				else if(response == -1)
				{
					$("#sendResult").html('System Database is temporary unavailable');   
				}
				else if(response > 0)
				{
					$("#sendResult").html('Wrong code, please try again. You have provided wrong code ' + response + ' times');
				}
				else
				{
					$("#sendResult").html('Account locked');
				}
				
				$("#sendResult").fadeOut(8888);
			},
			"text"
		);
	});

});

</script>