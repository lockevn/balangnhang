<?php if(!is_array($this->onlineUsers)): ?>
	<div class="info">No user online in current course</div> 
<?php else:  ?>
	<ul>
		<? foreach((array)$this->onlineUsers as $element): ?>
		<li class="onlineuser" >
		<?= $element->depositforusername ?> được nạp 
		<?= $element->coinvalue ?> vào tài khoản tiền, và 
		<?= $element->periodvalue ?> cộng thêm vào ngày sử dụng
		
		<br />
		ngày nạp <?= $element->useddatetime ?>
		số thẻ <?= $element->serialno?>
		mệnh giá <?= $element->facevalue ?>
		</li>
		<? endforeach; ?>
	</ul>
<?php endif;  ?>