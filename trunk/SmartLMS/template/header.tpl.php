<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

?>
<div id="topbar">
<ul id="browse_tiutit">
	<li>Chào bạn</li>
</ul>
<div class="topMenu">
<ul>

 <?php if($this->authkey): ?>
 	<li><a href="/home">Trang cá nhân</a></li>
    <li><a href="/my/replies" rel="fixed">Tin @Nhắc tới <?= $this->AUpu ?></a></li>
    <li><a href="/my/pm/inbox" rel="fixed">Hộp thư</a></li>
    <li><a href="/my/group_home" rel="fixed">Tin từ các nhóm</a></li>
    <li><a href="/invite" title="Mời bạn bè cùng tham gia tiutit.com">Mời bạn bè</a></li>
    <li><a href="/my/setting" rel="fixed" title="Cài đặt thông tin cá nhân">Thiết lập</a></li>
    <li><a href="/logout" title="Thoát">Thoát</a></li>
     <?php else: ?>
     	<li><a href="help">hỗ trọ</a></li>
	<li><a href="login">đăng nhập</a></li>
	<li><a href="register">đăng ký tài khỏan</a></li>
  <?php endif; ?>
  <li>&nbsp;<form action="" id="topsearch">
    <input type="text" id="topsearchquery" name="topseachquery" />
    <input type="submit" id="btn_top_search" value="Tìm" />
    </form></li>
</ul>
</div>
</div>

<script language="javascript">
$(document).ready(function(){
    $("#btn_top_search").click(function(){        
         var urlsearch = "/index.php?mod=search&t=simple&e=User&q=" + $("#topsearchquery").val();         
         document.location = urlsearch;
         return false;  // prevent submit
    });
});
</script>