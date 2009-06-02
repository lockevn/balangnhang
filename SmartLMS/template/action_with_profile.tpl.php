<h3><?= sprintf(Text::GetRandomWelcomeMessage(), $this->AUpu) ?></h3>
<?php if($this->authkey): ?>
<? if($this->AUpu == $this->pu): ?>
    <div class="box">
    <a href="/my/setting/account">Thay đổi thông tin cá nhân</a>
    <a href="/my/setting/picture">Đổi avatar</a>
    </div>
<? else: ?>
    <div class="<?= (($this->following) ? 'show': 'hide')?>" id="unfollow">Chán <?= $this->pu ?> rồi</div>
    <div class="<?= (($this->following) ? 'hide': 'show')?>" id="follow">Quan tâm <?= $this->pu ?></div>

    <div class="<?= (($this->followee) ? 'show': '')?>" id="unfollowee">Không cho <?= $this->pu ?> quan tâm nữa</div>    
    
    <div class="<?= (($this->blocking) ? 'show': 'hide')?>" id="unblock">Thôi cấm <?= $this->pu ?></div>
    <div class="<?= (($this->blocking) ? 'hide': 'show')?>" id="block">Cấm cửa <?= $this->pu ?></div>
<? endif; ?>

<script language="javascript">
$(document).ready(function(){        

        $('#unfollow').click(function(){
            var url = '/user/friend/delete.php?format=xml&rname=' + PAGE.pu;
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#unfollow').toggle();
                $('#follow').toggle();
                show_notice_msg('Bạn không còn quan tâm đến người này nữa', 'Thông báo');
            }
            else
            {
                show_notice_msg('Chức năng tạm thời không hoạt động');
            }
        });
        
        $('#follow').click(function(){
            var url = '/user/friend/add.php?format=xml&rname=' + PAGE.pu;
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#follow').toggle();
                $('#unfollow').toggle();
                show_notice_msg('Bạn đã quan tâm đến người này, từ nay những thông tin của họ sẽ được gửi đến bạn ngay tức thì', 'Thông báo');
            }
            else
            {
                show_notice_msg('Không kết bạn được do bạn đã bị người này cấm');
            }
        });

                
        $('#unfollowee').click(function(){
            var url = '/user/followee/delete.php?format=xml&rname=' + PAGE.pu;
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#unfollowee').toggle();                
                show_notice_msg('Người này không còn xem được tin của bạn nữa', 'Thông báo');
            }
            else
            {
                show_notice_msg('Chức năng tạm thời không hoạt động');
            }
        });
        
        
        $('#block').click(function(){
            if(!confirm('Người này bị bạn cấm và sẽ không thể biết tin tức của bạn nữa, bạn chắc không?')) return false;

            var url = '/user/ignore/add.php?format=xml&pname=' + PAGE.pu;            
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#block').toggle();
                $('#unblock').toggle();
            }
            else
            {
                alert('Tác vụ cấm người này không thành công. Xin hãy thử lại lần sau.');
            }
        });
        
        $('#unblock').click(function(){
            var url = '/user/ignore/delete.php?format=xml&pname=' + PAGE.pu;            
            var ret = ExecuteURLSync_ReturnCommandStatus(url);
            if(ret.stat == 'ok')
            {
                $('#unblock').toggle();
                $('#block').toggle();
            }
            else
            {
                alert('Tác vụ bỏ cấm người này không thành công. Xin hãy thử lại lần sau.');
            }
        });
});
</script>
<?php endif; ?>