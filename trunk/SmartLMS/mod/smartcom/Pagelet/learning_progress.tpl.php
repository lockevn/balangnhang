<?= $this->chartTongQuanKhoaHoc ?>
<?= $this->chartTongQuanKhoaHocTheoKyNang ?>
<br /><br />
<div id='div_chart_3'>
<?= $this->chartChiTietBaiHoc ?>
</div>

<script type="text/javascript" >
$(document).ready(function(){
	$('#div_chart_3').hide();    
});
	
function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
	return window["ie_" + movieName];
  } else {
	return document[movieName];
  }
}

function showDetailChartOfLesson(sectionid)
{
	$(document).ready(function(){
		$('#div_chart_3').fadeIn(1000, function(){
			var url = '<?=$this->chitietbaihocurl?>' + '&sectionid=' + sectionid;    
			tmp = findSWF("chart_3");
			tmp.reload(url);
		});		
	});    
}
</script>