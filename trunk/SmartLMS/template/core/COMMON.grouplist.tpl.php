<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); 

?>

<? if($this->authkey && $this->pu == $this->AUpu ): ?>
<div class="box"><a href="/group/create"><div class="button">Tự tạo nhóm riêng của mình</div></a></div>
<? endif; ?>

<? foreach ((array)($this->arrayData) as $item): 
    $gid = empty($item['gid']) ? $item['id'] : $item['gid'];
    $gcode = empty($item['gcode']) ? $item['code'] : $item['gcode'];
?>
<div class="box itemrow" gcode="<?= $gcode ?>">
    <? if($item['private'] == 1): ?>
    <a href="/view/report/<?= $gcode ?>">
    <img src="/image/icon.premiumgroup.png" alt="Advance" title="Đây là kênh tin cao cấp">
    </a>
    <? else: ?>
    <a href="/group/<?= $gcode ?>/<?= $item['id'] ?>">
    <img src="/image/icon.publicgroup.jpg" alt="Public" title="Đây là nhóm công cộng">
    </a>
    <? endif; ?>
    
    <? if($item['owner'] == 1): ?>
    <img src="/image/icon.owner.png" alt="ThisIsYourGroup" title="Đây là nhóm do bạn tạo">
    <? endif; ?>
    
    <a href="/group/<?= $gcode ?>/<?= $item['id'] ?>" class=""><?= $gcode ?></a>
    <? if($item['member'] == 1): ?>
    <span class="btn_leave_group" gid="<?= $gid ?>" >Rời nhóm</span><span class="hide">Bạn vừa rời khỏi nhóm này</span>
    <? else: ?>
    <span class="btn_join_group" gid="<?= $gid ?>" >Vào nhóm</span><span class="hide">Bạn vừa gia nhập nhóm này</span>
    <? endif; ?>
</div>
<? endforeach; ?>

<div class="box">
    <?php echo $this->paging['prev'] . ' '. $this->paging['next']; ?>
</div>

<script language="javascript">
$(document).ready(function(){
    $('.btn_join_group').click(function(){
        var gcode = $(this).parent(".itemrow").attr('gcode');
        $(this).hide().next("span").fadeIn().parent(".itemrow").addClass("notice");
        show_notice_msg('Bạn đã gia nhập nhóm ' + gcode + '','Thông báo');
        
        var url = '/public/group/adduser.php?format=xml&gid=' +$(this).attr('gid')+ '&pid=' + PAGE.AUpid;
        var ret = ExecuteURLSync_ReturnCommandStatus(url);
    });
    
    $('.btn_leave_group').click(function(){
        var gcode = $(this).parent(".itemrow").attr('gcode');
        $(this).hide().next("span").fadeIn().parent(".itemrow").addClass("notice");
        show_notice_msg('Bạn đã rời khỏi nhóm ' + gcode + '','Thông báo');
        
        var url = '/public/group/deleteuser.php?format=xml&gid=' +$(this).attr('gid')+ '&pid=' + PAGE.AUpid;
        var ret = ExecuteURLSync_ReturnCommandStatus(url);
    }); 
});
</script>