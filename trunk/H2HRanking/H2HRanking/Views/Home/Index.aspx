<%@ Page Language="C#" MasterPageFile="~/Views/Shared/Site.Master" Inherits="System.Web.Mvc.ViewPage" %>

<asp:Content ID="indexTitle" ContentPlaceHolderID="TitleContent" runat="server">
    Home Page
</asp:Content>
<asp:Content ID="indexContent" ContentPlaceHolderID="MainContent" runat="server">
    <h2>
        <%= Html.Encode(ViewData["Message"]) %></h2>
    <h3>
    Tiêu chí xếp hạng
    </h3>
    <p>
         - Dựa trên phong độ hiện thời của các tay vợt - Dựa trên kết quả các trận đấu đơn đã từng diễn ra - Dựa trên đánh giá khách quan của hội đồng II/ 
    </p>
    <h3>Bảng xếp hạng</h3>
    <p>
     1. Hữu Định (tuy xuất hiện vài lần nhưng anh em nhất trí bình bầu là minh chủ võ lâm) 
    </p>
    <p>
                2. Khuất Nam (sân Quan Hoa) – Từ vị trí “Thóc” đã được lên “Đại bàng” do thắng liên tiếp 2 “Gà” trong các trận đấu chính thức. 
    </p>
    <p>
                3. Tiến (sân Khâm thiên) - Kỹ thuật cao & hiệu quả ( vị trí cũ 7->4) 
    </p>
    <p>
        4. Luyện (sân An Dương) – lối đánh Grandmother ( vị trí cũ 3) 
    </p>
    <p>
        5. Thắng (sân KS Thắng Lợi, Khâm Thiên) (mình hơi khiêm tốn :D, mục tiêu là top 3)</p>
    <p>
     &nbsp;6. Linh chim (sân KS Thắng Lợi) – Kỹ thuật tốt nhưng tâm lý yếu :D 
    </p>
    <p>
        7. Thạch (sân Khâm Thiên) – Kỹ thuật cao nhưng chưa hiệu quả (vị trí cũ 4 )  
    </p>
    <p>
        8. Tuấn (sân An Dương) - Kỹ thuật tốt 
    </p>
    <p>
        9. Phương (sân Võ thị sáu) – lối đánh Super Old Lady ( vị trí cũ 15 )
    </p>
    <p>
                10. Long (sân KS Thắng Lợi) – Kỹ thuật tốt
    </p>
    <p>
                11. LinhLK (sân KS Thắng Lợi) – Kỹ thuật bình thường nhưng tâm lý cực tốt, giao lưu nhiều có thể vào Top5 
    </p>
    <p>
        12. NamPX (sân KS Thắng Lợi) – lối đánh Grandfather 
    </p>
    <p>
        13. HưngPM (sân Khâm Thiên) – Đại bàng tương lai
    </p>
    <p>
        14. HàOK (sân Khâm Thiên) – Tay vợt Nghị lực; Ứng cử viên cho giải "Trang phục đẹp" và "Chiếc còi vàng" 
    </p>
    <p>
        15. Tùng bò (sân Khâm Thiên) – Tay vợt trẻ triển vọng ( vị trí cũ 9)   <p>
                16. Đức Lác (sân Võ thị sáu) - Tay vợt trẻ triển vọng
    </p>
    <p>
                17. Nguyễn Dũng (sân Võ thị sáu) – Tay vợt trẻ triển vọng 
    </p>
    <p>
        18. Thành Trung Tin1K45 (sân Láng Hạ) 
    </p>
    <p>
        19. Bùi Minh Tiến VEGA (sân nào không rõ)
    </p>
    <p>
        20. DânHut (sân Phó Đức Chính) 
    </p>
    <p>
        21. Minh đần (sân Trần Duy Hưng)  VEGA (ứng cử viên Vô địch Bắn chim toàn quốc) 23. Trinh Hiếu (không sân nào nhận :D) 24. Roger Federer 10000 pts
    </p>
    <p>
                25. Rafael Nadal 9000 pts 26. Novak Djokovic 7000 pts 27. …. 
    </p>
    <p>
                III/Lưu ý: 
    </p>
    <p>
                1/ Mọi thắc mắc đều có thể giải quyết đơn giản bằng thi đấu trực tiếp:D</p>
    <p>
     &nbsp;2/ Tay vợt nào muốn thay đổi vị trí xin mời thách đấu với các tay vợt khác. 
    </p>
    <p>
        3/ Tay vợt nào muốn vào bảng xếp hạng xin mời đến giao lưu tại một trong các sân trên.
    </p>
    <p>
        Link gốc để bàn luận tại đây <a href="http://www.facebook.com/home.php#/note.php?note_id=313833165503&comments" title="Facebook thread">Facebook note</a>.
    </p>
</asp:Content>
