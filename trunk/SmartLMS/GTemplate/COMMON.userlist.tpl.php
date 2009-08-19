<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); ?>

<? foreach ((array)($this->arrayData) as $item): ?>
<div class="itemrow">
    <a href="<?= Config::AVATAR_URL ?><?= strtolower($item['u']) ?>.gif" rel="prettyPhoto">
        <img src="<?= Config::AVATAR_URL ?><?= strtolower($item['u']) ?>.gif" width="<?= Config::AVATARMEDIUMSIZE ?>" class="" />
    </a>
    <a class="" href = '/profile/<?= $item['u'] ?>' title="<?= $item['u'] ?>"><?= $item['u'] ?></a>
    
    <?php if($this->authkey): ?>
        <? if ($this->AUpu != $item['u']): ?>
            <br />        
            <? if ( $item['following']): ?>                
                <span class="btn_unfollow" user="<?= $item['u'] ?>">Bạn đang quan tâm tới <?= $item['u'] ?>, thôi quan tâm</span>
                <span class="hide">Bạn vừa thôi quan tâm đến người này</span>
            <? else: ?>                
                <span class="btn_follow" user="<?= $item['u'] ?>">Quan tâm</span>
                <span class="hide">Bạn vừa quan tâm đến người này</span>
            <? endif; ?>                
            
            <? if ( $item['followee']): ?>
                <br />
                <span class="btn_remove_followee" user="<?= $item['u'] ?>"><?= $item['u'] ?> đang quan tâm đến bạn. Không cho phép nữa</span>
                <span class="hide">Người này vốn trước kia quan tâm đến bạn, nhưng bạn vừa không cho phép nữa</span>
            <? endif; ?>
            
            <? if ( $strState == 'member'): ?>
            <span class="btn_remove_from_group" user="<?= $item['u'] ?>">Đuổi khỏi nhóm</span>
            <span class="hide">Người này vừa bị đuổi khỏi nhóm</span>
            <? endif; ?>        
        <? endif; ?>
    <? endif; ?>
</div>
<hr />
<? endforeach; ?>

<div class="box">
    <?php echo $this->paging['prev'] . ' '. $this->paging['next']; ?>
</div>
<script language="javascript">
$(document).ready(function(){
    $('.btn_unfollow').click(function(){
        $(this).hide().next("span").fadeIn().parent(".itemrow").addClass("notice");
        show_notice_msg('Bạn đã thôi quan tâm ' + $(this).attr('user') + '','Thông báo');
        
        var url = '/user/friend/delete.php?format=xml&rname=' + $(this).attr('user');
        var ret = ExecuteURLSync_ReturnCommandStatus(url);
    });
    
    $('.btn_follow').click(function(){        
        $(this).hide().next("span").fadeIn().parent(".itemrow").addClass("notice");
        show_notice_msg('Bạn đã quan tâm tới ' + $(this).attr('user') + '','Thông báo');
        
        var url = '/user/friend/add.php?format=xml&rname=' + $(this).attr('user');
        var ret = ExecuteURLSync_ReturnCommandStatus(url);
    });
    
    $('.btn_remove_followee').click(function(){
        $(this).hide().next("span").fadeIn().parent(".itemrow").addClass("notice");
        show_notice_msg($(this).attr('user') + ' sẽ không nhận được tin tức từ bạn nữa','Thông báo');
        
        var url = '/user/followee/delete.php?format=xml&rname=' + $(this).attr('user');
        var ret = ExecuteURLSync_ReturnCommandStatus(url);
    });
    
    $('.btn_remove_from_group').click(function(){
        show_notice_msg($(this).attr('user') + 'đã được gỡ bỏ khỏi nhóm','Thông báo');
    });
});
</script>