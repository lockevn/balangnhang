<?//=$this->chartTongQuanKhoaHocTheoKyNang 
?>
<br /><br />
<?php 
$course = $this->course;
$user = $this->user;
$USER = $this->USER;
$CFG = $this->CFG;
?>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="20px"></td>
        <td width="220px" valign="top">
            <div class="leftpanel">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr><td height="30px">
                        <div class="title">PERSONAL MENU</div>
                        <div class="titleicon"><a href=""><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BT_ST.JPG" /></a></div>
                    </td></tr>
                    <tr><td height="1px" bgcolor="#CCCCCC"></td></tr>
                    <tr><td align="left">
                        <div style="width:200px;">
                        <?php 
                        $showroles = 1;
                        $currenttab = 'Learning progress';
                        require($CFG->dirroot.'/user/profile/menu_block.php');
                        ?>                            
                        </div>
                    </td></tr>
                </table>
            </div>        
            
            <!----------------------------------------------------->                    
            <div class="leftpanel">    
            </div>
            
        </td>
        <td width="20px"></td>
        <td valign="top">
        
            <!-------------------------------------------------------------------->
            <div class="newsarea">
                <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    
                    <tr>
                        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
                        <td valign="top" width="100%">
                            <table cellpadding="10px" cellspacing="0" width="100%" style="background:url(<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px">
                                <tr>
                                    <td valign="top">
                                        <?= $this->chartTongQuanKhoaHoc ?>
                                        <div id='div_chart_3'>
                                        <?= $this->chartChiTietBaiHoc ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>                                                                
                        </td>
                        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_R.jpg" /></td>
                    </tr>
                    
                </table>
            </div>
        
        </td>
        <td width="20px"></td>
    </tr>
</table>


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

function showDetailChartOfLesson(sectionid, lessonname)
{
	$(document).ready(function(){
		$('#div_chart_3').fadeIn(1000, function(){
			var url = '<?=$this->chitietbaihocurl?>' + '&sectionid=' + sectionid + '&lessonname=' + lessonname;
			tmp = findSWF("chart_3");
			tmp.reload(url);
		});		
	});    
}
</script>