<? global $USER, $SESSION; ?>
<? print_heading(get_string('buy_ticket', 'ticket_buy')); ?>
	
	
<div><?= get_string("course_name", "ticket_buy") ?><strong><?=$this->course->fullname ?></strong></div>
<div><?= get_string("course_cost", "ticket_buy") ?><strong><?= (int)($this->course->cost) ?> (VND) </strong></div> 
<br>

<?php if(empty($this->accountinfo)): ?>
<div class="error"><?= get_string("empty_account", "ticket_buy") ?></div>
<?php else:  ?>
<div><?= get_string("account_name", "ticket_buy") ?><strong><?=$this->accountinfo->username ?></strong></div>
<div><?= get_string("account_balance", "ticket_buy") ?><strong><?=$this->accountinfo->coinvalue ?> (VND)</strong></div>
<div><?= get_string("account_expiredate", "ticket_buy") ?><strong><?=$this->accountinfo->expiredate ?></strong></div>
<div><?= get_string("account_remain", "ticket_buy") ?><strong><?=$this->accountinfo->coinvalue - (int)($this->course->cost) ?> (VND)</strong></div>
<!-- <div>Acccount expire date is not changed</div>-->

<input type='button' id='buy' value='<?= get_string("accept_buy_ticket", "ticket_buy")?>'><span id='buyResult'></span>

<script type="text/javascript" >
$(document).ready(function(){
	$("#buy").click(function(){
		
		$("#buyResult").show().html('<?= get_string("processing", "ticket_buy") ?>');		
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
					$("#buyResult").html('<?= get_string("successful", "ticket_buy") ?>');
					window.status = "";
					window.location = '<?= $SESSION->wantsurl ?>';
					return true;
				}
				else
				{
					$("#buyResult").html('<?= get_string("error", "ticket_buy") ?>');
					return false;
				}
			},
			"text"
		);		
	});
	
	$("#linkbacktowork").click(function(){
	window.location = '<?= $SESSION->wantsurl ?>';
	});
	
	
});
</script>
<?php endif;  ?>
<input type='button' id='linkbacktowork' value='<?= get_string("cancel_buy_ticket", "ticket_buy")?>'>