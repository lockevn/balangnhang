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
	<input type='text' id='username' value = '<?=$USER->username ?>' disabled="disabled"  readonly="true"><br /><br />
	
	<label for="code">Code: </label> <input type='text' id='code' value = '' maxlength="50"><br />
	
	<input type='button' id='send' value = 'Send'><span id='sendResult'></span>
</fieldset>
<!--<pre>
<? print_r($USER); ?>
</pre>-->