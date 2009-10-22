<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); ?>

<div class="box" id="mainlist">
<? if(!is_array($this->arrayData) || sizeof($this->arrayData) <= 0 ): ?>
    Không có tin nào
<? else:  ?>
<? foreach($this->arrayData as $item): ?>
    <div class="span-15 box" id="pm<?=$item['id']; ?>">

        <div class="span-1">
            <a href="/profile/<?=$item['fromu']; ?>"><img src="<?=Config::AVATAR_URL.strtolower($item['fromu']); ?>.gif" style="width:<?=Config::AVATARMEDIUMSIZE?>px;" /></a>
        </div>
        <div class="span-11">
            <a href="/profile/<?php echo $item['fromu']; ?>" class=""><?php echo $item['fromu']; ?></a>&nbsp;<?php echo $item['c']; ?>
        </div>
        <div class="span-3 last">
            <a href="<?=Config::IMG_URL . $item['img']; ?>" rel="prettyPhoto"><img src="<?=Config::IMG_URL .'thumb-'. $item['img']; ?>" width="<?php echo Config::POST_IMG_WIDTH ?>" /></a>
        </div>


        <div class="span-11 blue">            
            <span><?php echo $item['dt']; ?></span>
            <span>gửi từ <a href="/Action/nav.php?d=<?=$item['devicename']; ?>" target="_blank"><?=$item['devicename']; ?></a></span>
        </div>

        <div class="span-3 last">                    
            <span class="delete_pm" mid="<?=$item['id']; ?>">Xoá</span>
            <? if($this->folder == 'inbox'): ?>
                <span class="reply_pm">Trả lời</span>
            <? endif; ?>            
        </div>

    </div>
<? endforeach; ?>
<? endif;  ?>
</div>

<div class="box">
    <?php echo $this->paging['prev'] . ' '. $this->paging['next']; ?>
</div>

<script language="javascript">
$(document).ready(function(){
    $('.reply_pm').click(function(){
        $('#tou').hide();
        $('#reply_to_u').show();
        $('#reply_to_u').val($(this).attr('pu'));
        $('#pm_msg_content').focus();
    });

    $('.delete_pm').click(function(){
        var mid = $(this).attr('mid');
        
        var url = '/message/direct/delete.php?method=get&folder=<?= $this->folder?>&mid='+mid+'&rid='+PAGE.AUpid;
        url = MakeProxiedURLToQAPI(url);            
        $.get(url,function(data){
            var cs = ParseXML_ReturnCommandStatus(data);
            if(cs.stat == 'ok')
            {
                $('#tou').val('');
                $('#reply_to_u').val('');                    
                $('#pm_msg_content').val('');
                
                $('div#pm' + mid).hide("slow");
                $.growlUI('Thông báo', 'Xoá xong');                    
            }
            else
            {
                $.growlUI('Thông báo', 'Hiện tại chưa xoá được, xin hãy thử lại sau');
            }                
        });
    });
});
</script>