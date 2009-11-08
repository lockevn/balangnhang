<?php if(empty($this->accountBalance)): ?>
    <div class="info">No smartcom account balance existed (of this user)</div> 
<?php else:  
$CFG = $this->CFG;
?>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tr><td height="5px"></td></tr>
    <tr>
        <td height="30px" colspan="3">
            <div class="title">TÀI KHOẢN <a href=""><?= strtoupper($this->accountBalance->username) ?></a> CÓ:</div>
        </td>    
    </tr>
    <tr>
        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
        <td valign="top" width="100%" style="font-style: italic;">
            <table cellpadding="10px" cellspacing="0" width="100%" style="background:url(<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px" border="0">
                <tr>
                    <td valign="top">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td height="30px" align="right">
                                    <b>Số dư:</b>
                                </td>
                                <td width="15px"/>
                                <td align="left">
                                    <?= $this->accountBalance->coinvalue ?>
                                </td>
                            </tr>
                            <tr>
                                <td height="30px" align="right">
                                    <b>Được sử dụng tới ngày:</b>
                                </td>
                                <td width="15px"/>
                                <td align="left">
                                    <?= $this->accountBalance->expiredate ?>
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

<?php endif;  ?>