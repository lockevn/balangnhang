<? global $USER, $SESSION; ?>
<? print_heading(get_string('modulenameplural', 'smartcom') . ' buying ticket to learn'); ?>
	
	
<div>Buying ticket for <strong><?=$this->course->fullname ?></strong>, you will spend <?= (int)($this->course->cost) ?> coin(s) in your account:</div> 

<?php if(empty($this->accountinfo)): ?>
<div class="error">You do not have account balance (do not have any coin) in Smartcom system. Please buy our prepaidcard and go <a href="/mod/smartcom/index.php?courseid=1&submodule=prepaidcard_enduser_deposit">PrepaidCard Deposit page</a> to deposit</div>
<?php else:  ?>
<div>Current account name is: <strong><?=$this->accountinfo->username ?></strong></div>
<div>Current account money is: <?=$this->accountinfo->coinvalue ?> coin(s)</div>
<div>Current account expire date is: <?=$this->accountinfo->expiredate ?></div>
<div>Remain account money after buying ticket is: <?=$this->accountinfo->coinvalue - (int)($this->course->cost) ?> coin(s)</div>
<div>Acccount expire date is not changed</div>

<input type='button' id='buy' value='I understand and agree to buy ticket'><span id='buyResult'></span>
<a id="linkbacktowork" style="display:none;" href="<?= $SESSION->wantsurl ?>" >// TEST: CONTINUE to <?= $SESSION->wantsurl ?></a>

<script type="text/javascript" >
$(document).ready(function(){
	$("#buy").click(function(){
		
		$("#buyResult").show().html('Processing, please wait ...');		
		$.blockUI();
		$.get(
			'/mod/smartcom/api/ticket_buy.php', 
			{
				courseid : <?=$this->course->id ?>,
				username : '<?=$USER->username ?>'
			},			
			function(response){
				$.unblockUI();
				if(response === 'ok')
				{
					$("#buyResult").html('You can continue! Redirecting ...');
					window.status = "";
					window.location = $('#linkbacktowork').attr('href');
					return true;
				}
				else
				{
					$("#buyResult").html('Error, you can not buy ticket. Your account is locked, or lack of coin(s) or expired');
					return false;
				}
			},
			"text"
		);		
	});
});
</script>
<?php endif;  ?>