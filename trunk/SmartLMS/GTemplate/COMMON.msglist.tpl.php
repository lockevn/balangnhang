<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

?>

<div class="" id="mainlist">
<? if(!is_array($this->arrayData)): ?>
    Không có tin nào
<? else:  ?>
<? foreach($this->arrayData as $item): ?>
    <div class="span-15 last" id="<?=$item['guid']; ?>">
        <div class="span-15 last">
            <div class="span-1">
                <a href="/profile/<?=$item['fromu']; ?>"><img src="<?=Config::AVATAR_URL.strtolower($item['fromu']); ?>.gif" style="width:<?=Config::AVATARMEDIUMSIZE?>px;" /></a>
            </div>
            <div class="span-10">
                <a href="/profile/<?php echo $item['fromu']; ?>" class=""><?php echo $item['fromu']; ?></a>&nbsp;<?php echo $item['c']; ?>
            </div>
            <div class="span-4 last">            
                <? if($item['img']): ?>                
                <a href="<?=Config::IMG_URL . $item['img']; ?>" rel="prettyPhoto"><img src="<?=Config::IMG_URL .'thumb-'. $item['img'] ?>" width="<?php echo Config::POST_IMG_WIDTH ?>" /></a>
                <? endif; ?>
            </div>
        </div>

        <div class="span-15 last">
            <div class="span-11 ">
                <span>                
                    <?php if($item['numofrelate'] > 0): ?>
                    <a href="/index.php?mod=view_msg_tree&pid=<?=$item['fromid']; ?>&mguid=<?=$item['guid']; ?>"><img src="/image/icon.talk.png" alt="Câu chuyện" /></a>
                    <?php endif; ?>
                </span>
                <span><?php echo $item['dt']; ?></span>
                <span>gửi từ <a href="/Action/nav.php?d=<?=$item['devicename']; ?>" target="_blank"><?=$item['devicename']; ?></a></span>
            </div>

            <div class="span-4  last">
            <? if( $this->authkey): ?>
                <? if($this->AUpu === strtolower($item['fromu'])): ?>
                <span onclick="javascript:deleteMsg('<?=$item['guid']; ?>');">Xoá</span>
                <? else: ?>
                <span onclick="javascript:replyMsg('<?=$item['fromid']; ?>', '<?=$item['fromu']; ?>', '<?=$item['guid']; ?>');" >trả lời</span>
                <? endif; ?>
            <? endif; ?>
            </div>
        </div>
    </div>
    <hr />    
<? endforeach; ?>
<? endif;  ?>
</div>

<div class="">
    <?php echo $this->paging['prev'] . ' '. $this->paging['next']; ?>
</div>

<div id="replybox" class="box hide" style="">
    <!--this div is place holder for reply post box, we move post box to here-->
    <div id="replybox_oldmsg"></div>
    <div id="replybox_content"></div>
</div>

<script type="text/javascript" src="/js/lib/shortcut.js"></script>
<script language="javascript">
function deleteMsg(guid, gid)
{
    if(confirm('Bạn muốn xoá tin này?'))
    {
        $(document).ready(function(){            
            var url = '/message/blog/delete.php?mguid=' + guid + '&gid=' + gid;
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#' + guid).hide();
                $.growlUI('Thông báo', 'Tin đã được xoá!');
            }
            else
            {
                $.growlUI('Thông báo', 'Tác vụ xoá không thành công, xin hãy thử lại vào lúc khác nhé.');                
            }
        });
    }
    else
    {
        return false;
    }
}

function replyMsg(fromid, fromu, guid)
{   
    $(document).ready(function(){        
        var postform = $('#frmPostNewMsg');        
        $("#replybox_content").append(postform);
                
        $.blockUI({ 
            message: $('#replybox'),
             css: {
                width:          '60%', 
                top:            '40%', 
                left:           '20%', 
                // textAlign:      'center', 
                // color:          '#000', 
                // border:         '3px solid #aaa', 
                // backgroundColor:'#fff', 
                cursor:         'normal' 
            }, 
            timeout: 90000
        }); 
        
        
        
        function OnHidePostForm(){
            $.unblockUI({ 
                onUnblock: function(){  $("#frmPostNewMsgWrapper").append(postform);   } 
            });
            shortcut.remove("Escape");
        }
        // hide when click outsite or press Escape
        $('.blockOverlay').click(OnHidePostForm);        
        shortcut.add("Escape",
            OnHidePostForm,
            {                
                'propagate':true
            }
        );
            
        
        $("#replybox").data('fromid', fromid);
        $("#replybox").data('fromu', fromu);
        $("#replybox").data('guid', guid);         
        $('#c').focus();        
        $('#c').val('@'  + $("#replybox").data('fromu') + ' ');
    });
}

</script>