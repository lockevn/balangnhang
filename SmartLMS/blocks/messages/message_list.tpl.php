<? 
global $CFG; 
?>

<?php if(!is_array($this->arrayData)): ?>
	<div class="info"> <?= get_string('nomessages', 'message') ?></div> 
<?php else:  ?>
	<?php foreach ($this->arrayData as $user): ?>    
	<ul class="list">
	<li class="listentry" id="list_public_msg_user_lasttest">
		<div class="user">
		<a href="<?=$CFG->wwwroot?>/user/view.php?id=<?=$user->id?>&course=<?=$this->courseid?>" title="<?= format_time(time() - $user->lastaccess)?>">
		<?=fullname($user)?>
		</a>
		</div>
		<div class="message">
		<a href="<?=$CFG->wwwroot ?>/message/discussion.php?id=<?=$user->id?>" 
			onclick="this.target='message_<?=$user->id?>'; return openpopup('/message/discussion.php?id=<?=$user->id?>', 'message_<?=$user->id?>', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0);">
		<img class="iconsmall" src="<?= $CFG->pixpath?>/t/message.gif" alt="" />
		&nbsp;<?=$user->count ?></a>
	</div>
	</li>
	</ul>
	<?php endforeach; ?>
<?php endif;  ?>