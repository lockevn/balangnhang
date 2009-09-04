<?php if(!is_array($this->onlineUsers)): ?>
	<div class="info">No user online in current course</div> 
<?php else:  ?>
	<ul>
		<? foreach((array)$this->onlineUsers as $element): ?>
		<li class="onlineuser" userid="<?= $element->id?>" ><?= $element->username ?></li>
		<? endforeach; ?>
	</ul>
<?php endif;  ?>