<div class="span-15 last">	
	<div class="span-15 last">    
		<div class="wwhbutton span-5" id="what" title="Tiutit là gì?">Tiutit là gì?</div>
		<div class="wwhbutton span-5" id="why" title="Vì sao sử dụng Tiutit?">Vì sao?</div>
		<div class="wwhbutton span-5 last" id="how" title="Dùng thế nào?">Dùng thế nào?</div>
        <div class="span-15 last" id="wwhcontent"></div>
	</div>
</div>

<div class="hide" id="wwhcontentholder">
    <div class="wwhcontent" id="content_what">
	    <b>Tiutit là kênh giữ liên lạc</b> cho <b>tất cả mọi người</b>.<br>
	    Qua Tiutit, bạn có thể <b>trao đổi thường xuyên</b> những tin Update <b>ngắn gọn</b> để <b>nắm bẳt tình hình</b> của mọi người bạn quan tâm <b>tốt hơn</b>.<br>	
	    Bạn chưa phải là Thành viên? <a href="/register">Đăng ký Thành viên</a>
    </div>
    <div class="wwhcontent" id="content_why">
        Vì Tiutit là kênh thông tin <b>hiệu quả nhất</b> cho phép bạn luôn <b>nắm bắt tình hình</b> của những người bạn <b>quan tâm</b>. Vì bạn chỉ cần có địện thoại hoặc máy tính để cập nhập <b>24/24 khắp mọi nơi</b>.
    </div>
    <div class="wwhcontent" id="content_how">	
        Rất <b>đơn giản</b>, chỉ cần đăng kí mất 15 giây và bạn có thể cập nhật Tin Update bất cứ nơi đâu qua <b>di động</b> bàng <b>SMS</b> hoặc bằng <b>máy tính</b> qua Website này.		
    </div>
</div>
<hr />
<script language="javascript">
$(document).ready(function(){
    $(".wwhbutton").click(function(){
        // remove all old content to holder
        $(".wwhcontent").appendTo($("#wwhcontentholder"));
        
        // fetch the content
        var content = $("#content_" + $(this).attr("id"));
        content.appendTo($("#wwhcontent"));
        
        // highlight current button
        $('.wwhbutton').removeClass('blue');
        $(this).addClass('blue');
    });
    
    $("#what").trigger("click");
});
</script>