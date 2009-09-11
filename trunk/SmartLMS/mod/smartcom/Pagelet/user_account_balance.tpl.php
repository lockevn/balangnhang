<?php if(!is_array($this->onlineUsers)): ?>
	<div class="info">No user online in current course</div> 
<?php else:  ?>
	<ul>
		<? foreach((array)$this->onlineUsers as $element): ?>
		<li class="onlineuser" >
		tài khoản <?= $element->username ?> có số dư
		<?= $element->coinvalue ?>, được sử dụng tới ngày
		<?= $element->expiredate ?>
		</li>
		<? endforeach; ?>
	</ul>
<?php endif;  ?>