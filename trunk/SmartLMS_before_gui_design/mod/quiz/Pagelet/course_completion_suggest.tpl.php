<? global $USER; ?>

<fieldset >
	<legend>You've completed the final exam</legend>
	<div>You may choose one of these course(s) to advance your skill:</div>
	<ul>
	<? foreach((array)$this->arrCourseToContinue as $element): ?>		
	<li><a href="/course/view.php?id=<?= $element->id ?>"><?= $element->name ?></a></li>
	<? endforeach; ?>
	</ul>
</fieldset>

<script type="text/javascript" >
$(document).ready(function(){	
});
</script>