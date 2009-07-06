truy xuất Sòng
Song value = Application.Get("songbac") as Song;


Danh sách các authkey đang valid trên hệ thống
Application["authkeystore"]



user login vào hệ thống
kiểm tra xem user thuộc loại gì, có đóng tiền monthly fee chưa
nếu đóng rồi, cộp cho nó cái dấu (trong memory, trong phiên) để nó có thể sử dụng các dịch vụ cần monthly subscription

liệt kê các tour, các sới đang có
Với tour Free, liệt kê các sới free
nếu vào sới free, 
kiểm tra monthly sub, nếu monthly sub = không tồn tại, coi endtime = yesterday
kiểm tra endtime với current time
nếu ok, cho xài
nếu fail, không cho xài

Hiện tại, do không có chế độ monthly fee, bỏ qua đoạn kiểm tra monthly sub khi join sới free

User có thể tự tạo sới free để chơi
Khi chơi ở các sới free
tăng điểm cho user tại bản ghi tương ứng trong user_tour_gold với tourid = 1



Với các tour Pro, còn hạn chơi (enddate vẫn còn hiệu lực so với thời gian server hiện tại), kiểm tra user_tour_sub
nếu không có, báo là không có quyền (client cho nó cái thông báo). Yêu cầu (và hướng dẫn) user nạp tiền, nhắn tin ... để tham gia tour
Sau khi nạp tiền nhắn tin xong, hệ thống sẽ cập nhật ngay, nếu user refresh, có thể vào được tour pro

nếu có, cho nó nút chọn, thu xếp chỗ chơi
hệ thống tự tạo sới (nếu cần), nhét user vào một sới
mask username để không ai biết ai đang chơi, ai vừa vào (lấy 5 chữ số ở vị trí lẻ đầu tiên của authkey)
sớiid cũng được mask khi tạo sới (sử dụng mã hoá 2 chiều, với key bí mật + authkey của user, chỉ server giữ)
masked_soiid = encrypt(authkey + username + private server key, soiid)
client có authkey, username, masked_soiid
server có tất cả thông số trên
soiid = decrypt(authkey + username + private server key, masked_soiid)


Khi chơi ở các sới Pro, tăng điểm cho user tại bản ghi tương ứng trong user_tour_gold với tourid là tourid của sới hiện tại đang chơi
Khi tạo ván mới trong sới pro, chú ý đến enddate của tour hiện tại
Nếu quá enddate, không cho tạo ván mới nữa

Ván đang diễn ra (dù enddate của tour đã điểm) vẫn tiếp tục bình thường



(Chức năng này không nằm trong backend, cần có giao diện web riêng để làm)
Tại một thời điểm nào đó (trong tương lai)
Admin vào danh sách các tour đang có trên hệ thống
List sẽ báo tour nào đã hết hạn
Admin ấn nút trả thưởng, kiểm tra tiền nong, điểm số --> ra report trả thưởng

Thực hiện trả thưởng, báo cho user, offline

