<? global $USER; ?>

<? print_heading(get_string('modulenameplural', 'smartcom') . ': Configure course for completion auto-suggestion'); ?>

<fieldset >
	<legend>Final exam</legend>
	<label for="finalexamquizpercent">finalexamquizpercent: </label>
	<input type='text' id='finalexamquizpercent' value = '60' maxlength="2">%<br />    
		
	<label for="finalexamid">finalexamid: </label>
	<?php if(is_array($this->arrQuiz)): ?>
	<select id="finalexamid" name="finalexamid">
		<? foreach((array)$this->arrQuiz as $element): ?>		
		<option value="<?= $element->id?>" <?= $element->selected ? 'selected="selected"' : '' ?> >
		<?= $element->name ?>
		</option>
		<? endforeach; ?>
	</select>	
	<?php else:  ?>
	<div class="info">There is no quiz in this course</div> 
	<?php endif;  ?>
	
</fieldset>
<br />
<fieldset >
	<legend>Overall quiz</legend>    
	<label for="averageoverallquizzespercent">averageoverallquizzespercent: </label>
	<input type='text' id='averageoverallquizzespercent' value = '60' maxlength="2">%<br />	
</fieldset>
<br />

<label for="nextcourseidset">choose next courseid set: </label><br />
	<?php if(is_array($this->arrCourseInSystem)): ?>
	<select multiple="multiple" name="nextcourseidset" id="nextcourseidset">
		<? foreach((array)$this->arrCourseInSystem as $element): ?>        
		<option value="<?= $element->id?>" <?= $element->selected ? 'selected="selected"' : '' ?> >        
		<?= $element->name ?>
		</option>
		<? endforeach; ?>
	</select>
	<?php else:  ?>
	<div class="info">There is no more course in our system</div> 
	<?php endif;  ?>
	

<br /><br /><br /><br />

<label for="enabletoapply">Enable this checkbox to apply this course: </label>
<input type="checkbox" value="enabletoapply" name="enabletoapply" id="enabletoapply" /> 
<input type='button' id='save' value = 'Save'><span id='saveResult'></span>

<script type="text/javascript" >
$(document).ready(function(){
	$("#save").click(function(){
		var $finalexamquizpercent = jQuery.trim($('#finalexamquizpercent').val());
		var $averageoverallquizzespercent = jQuery.trim($('#averageoverallquizzespercent').val());
		var $finalexamid = $('#finalexamid').val();		
		var $enabletoapply = $('#enabletoapply').attr('checked');		
		var $nextcourseidset = '';		
		$('#nextcourseidset').each(function () {
				$nextcourseidset += $(this).val() + ",";
			  });
		var $courseid = <?=$this->courseid?>;
				
		$("#saveResult").show().
		html('Applying ...').load(
			'/mod/smartcom/api/course_completion_suggest_configure.php', 
			{
				finalexamquizpercent : $finalexamquizpercent,
				averageoverallquizzespercent : $averageoverallquizzespercent,
				finalexamid : $finalexamid,
				enabletoapply : $enabletoapply,
				nextcourseidset : $nextcourseidset,
				courseid : $courseid
			},
			function(){                
				$(this).fadeOut(8888);
			}
		);
		
	});
	
});
</script>