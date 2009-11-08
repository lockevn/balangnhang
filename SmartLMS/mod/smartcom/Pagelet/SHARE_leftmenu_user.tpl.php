<? global $USER, $SESSION, $CFG; ?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tbody><tr><td height="30px">
        <div class="title">ACCOUNT MENU</div>
        <div class="titleicon"><a href=""><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BT_ST.JPG"/></a></div>
    </td></tr>
    <tr><td height="1px" bgcolor="#cccccc"/></tr>
    <tr><td align="center">
        <div style="width: 200px;">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tbody><tr>
                    <td>

                    <div class="tabtree">
                    <ul class="0" style="padding: 0pt; list-style-type: none; list-style-image: none; list-style-position: outside; font-weight: bold;">
                        <li class="first onerow here selected" style="line-height: 20px; height: 20px ! important;">
                            <a class="nolink" href="<?php echo $CFG->wwwroot; ?>/mod/smartcom/index.php?submodule=user_account_balance&lmz=1"><span>Account balance</span></a><div class="tabrow1 empty"> </div>
                        </li>
                        <li style="line-height: 30px;">
                            <a title="Edit profile" href="<?php echo $CFG->wwwroot ?>/mod/smartcom/index.php?submodule=prepaidcard_enduser_deposit_history&lmz=1">
                                <span>Account history</span>
                            </a> 
                        </li>
                        <li style="line-height: 30px;">
                            <a title="Notes" href="<?php echo $CFG->wwwroot ?>/mod/smartcom/index.php?submodule=prepaidcard_enduser_deposit&lmz=1">
                                <span>Prepaid card</span>
                            </a> 
                        </li>
                    </ul>
                    </div><div class="clearer"> </div>

              </td>
                </tr>
            </tbody></table>
        </div>
    </td></tr>
</tbody>
</table>

<script type="text/javascript" >
$(document).ready(function(){
	
});
</script>