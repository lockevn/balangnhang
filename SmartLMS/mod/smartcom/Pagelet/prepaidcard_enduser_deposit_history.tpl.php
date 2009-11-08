<?php if(!is_array($this->histories) || empty($this->histories)): ?>
	<div class="info">This user has not used any prepaidcard</div> 
<?php else:  
$CFG = $this->CFG;
?>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top">
        
            <!-------------------------------------------------------------------->
            <div class="newsarea">
                <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <tr><td height="30px" colspan="3">
                        <div class="title">LỊCH SỬ NẠP THẺ</div>
                    </td></tr>
                    <tr>
                        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
                        <td valign="top" width="100%">
                            <table cellpadding="10px" cellspacing="0" width="100%" style="background:url(<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px">
                                <tr>
                                    <td>
                                        <table cellpadding="10px" cellspacing="0" width="100%" style="background:url(<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px">
                                            <tr>
                                                <td valign="top">                                                            
                                                    <table cellpadding="10px" cellspacing="1" width="100%" bgcolor="#999999">
                                                        <tr valign="middle">
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Tài khoản nạp thẻ
                                                            </td>
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Số tiền được cộng
                                                            </td>
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Số ngày sử dụng được cộng
                                                            </td>
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Ngày nạp
                                                            </td>
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Seri thẻ
                                                            </td>
                                                            <td align="center" class="courseBB" background="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/TB1_HD.jpg">
                                                                Mệnh giá
                                                            </td>
                                                        </tr>
                                                        <? foreach((array)$this->histories as $element): ?>
                                                        <tr valign="middle">
                                                            <td height="44px" align="center" bgcolor="#FFFFFF" class="courseGB">
                                                                <a href=""><?= $element->depositforusername ?></a>
                                                            </td>
                                                            <td align="left" bgcolor="#FFFFFF">
                                                                <?= $element->coinvalue ?>
                                                            </td>
                                                            <td align="center" bgcolor="#FFFFFF" class="courseB">
                                                                <?= $element->periodvalue ?>
                                                            </td>
                                                            <td align="left" bgcolor="#FFFFFF">
                                                                <?= $element->useddatetime ?>
                                                            </td>
                                                            <td align="center" bgcolor="#FFFFFF" class="courseB">
                                                                <?= $element->serialno?>
                                                            </td>
                                                            <td align="left" bgcolor="#FFFFFF">
                                                                <?= $element->facevalue ?>
                                                            </td>
                                                        </tr>    
                                                        <? endforeach; ?>                                                            
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                            
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_R.jpg" /></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<?php endif;  ?>