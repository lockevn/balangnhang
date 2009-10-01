<?php if(empty($this->accountBalance)): ?>
	<div class="info">No smartcom account balance existed (of this user)</div> 
<?php else:  ?>
<fieldset>
<legend>Tài khoản <b><?= $this->accountBalance->username ?></b> có:</legend>
<ul>
<li>số dư: <?= $this->accountBalance->coinvalue ?></li>
<li>được sử dụng tới ngày: <?= $this->accountBalance->expiredate ?></li>
</ul>
</fieldset>
<?php endif;  ?>