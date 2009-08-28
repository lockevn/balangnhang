$(document).ready(function(){
	String.prototype.leftPad = function (l, c) { return new Array(l - this.length + 1).join(c || '0') + this; }

	$.ajaxSetup ({  
		cache: false  
	});

	 $.getJSON(
		 '/mod/smartcom/api/student_quiz_grade_percent.php',
		 {
			courseid: CURRENT_COURSEID,
			userid: CURRENT_USERID
		 },  
		 function(json){
			$(".GURUCORE_quiz_grade").each(function(index, element){
				var quizid = $(element).attr('quizid');				
				var grade = json[quizid]['grade'];
				$(element).text(" (" + grade.leftPad(3, ' ') + "%) ");
			});
		 }
	 );
	 
	 
	 $.getJSON(
		 '/mod/smartcom/api/student_lesson_grade_percent.php',
		 {
			courseid: CURRENT_COURSEID,
			userid: CURRENT_USERID
		 },  
		 function(json){
			$(".GURUCORE_lesson_grade").each(function(index, element){
				var quizid = $(element).attr('sectionid');
				var grade = json[quizid]['grade'];
				$(element).text(" (" + grade.leftPad(3, ' ') + "%) ");
			});
		 }
	 );
	 
});