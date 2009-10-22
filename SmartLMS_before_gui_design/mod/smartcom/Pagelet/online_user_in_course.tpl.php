<?php if(!is_array($this->onlineUsers) || empty($this->onlineUsers)) : ?>
<div class="info">No user (student) online in current course</div> 
<?php else:  ?>
<ul>
	<? foreach((array)$this->onlineUsers as $element): ?>
	<li class="onlineuser" userid="<?= $element->id?>" ><?= $element->username ?></li>
	<? endforeach; ?>
</ul>
<?php endif; ?>