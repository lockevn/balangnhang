<? global $USER, $CFG;  ?>

<? //print_heading(get_string('modulenameplural', 'smartcom')); ?>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top">
        
            <!-------------------------------------------------------------------->
            <div class="newsarea">
                <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <tr><td height="30px" colspan="3">
                        <div class="title">NẠP THẺ</div>
                    </td></tr>
                    <tr>
                        <td valign="top" width="5px"><img src="<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_L.jpg" /></td>
                        <td valign="top" width="100%" style="font-style: italic" >
                            <table cellpadding="10px" cellspacing="0" width="100%" style="background:url(<?php echo $CFG->wwwroot ?>/theme/menu_horizontal/template/images/BG1_M.jpg) top repeat-x" height="100px" border="0">
                                <tr>
                                    <td height="30px">
                                        <?php if($this->state === 'depositok'): ?>
                                            <div class="info">Deposit OK!</div> 
                                        <?php elseif($this->state === 'depositfail'):  ?>
                                            <div class="info">Deposit Fail! Please use correct card code. Nhập sai quá 3 lần thì tài khoản của bạn sẽ bị khoá.</div> 
                                        <?php else:  ?>    
                                            <div class="info">Scratch your prepaid card, provide the secret code here</div> 
                                        <?php endif;  ?>
                                        
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            <tr><td height="10px"></td></tr>                                                
                                            <tr><td height="1px" bgcolor="#CCCCCC"></td></tr>
                                        </table>                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20px">
                                        <b>Deposit SmartCom prepaid card to account</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="30px" width="80px">User name:</td>
                                                <td><a href=""><?=$USER->username ?></a></td>
                                            </tr>
                                            <tr>
                                                <td height="30px">Code:</td>
                                                <td><input style="width: 200px; " type='text' id='code' value = '' maxlength="50"></td>
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
            
            <!-------------------------------------------------------------------->
            <div class="newsarea">
                <input class="cls_button" type='button' id='send' value = 'Send'>
            </div>
            <br />
            <div id='sendResult'></div>
        </td>
    </tr>
</table>

<script type="text/javascript" >
var message = '';

$(document).ready(function(){            
    $.ajaxSetup ({  
        cache: false  
    });  

    $("#send").click(function(){
        
        $("#sendResult").show().empty();
        $.blockUI();
        $.get(
            '/mod/smartcom/api/prepaidcard_enduser_deposit.php', 
            {
                code : $('#code').val()
            },
            function(response){
                $.unblockUI();
                response = parseInt(response);
                if(response == 0)
                {
                    $("#sendResult").html('Nap the thanh cong');
                }
                else if(response == -1)
                {
                    $("#sendResult").html('System Database is temporary unavailable');   
                }
                else if(response > 0)
                {
                    $("#sendResult").html('Wrong code, please try again. You have provided wrong code ' + response + ' times');
                }
                else
                {
                    $("#sendResult").html('Account locked');
                }
                
                $("#sendResult").fadeOut(8888);
            },
            "text"
        );
    });

});

</script>