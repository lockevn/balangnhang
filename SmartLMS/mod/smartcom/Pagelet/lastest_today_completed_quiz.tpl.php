<?php if(!is_array($this->quizOfUser)): ?>
	<div class="info">No exercise in current course was completed today</div> 
<?php else:  ?>
	<ul>
		<? foreach((array)$this->quizOfUser as $element): ?>
		<li class="completedTodayQuiz" attemptid="<?= $element->attemptid?>" >
		<?= $element->name ?>
		</li>
		<? endforeach; ?>
	</ul>
<?php endif;  ?>