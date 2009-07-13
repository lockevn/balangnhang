using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Xml.Linq;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using System.Text;

namespace TalaAPI.Lib
{
    public class TalaAutorun
    {
        HttpContext _context;

        string _CurrentAuthkey;
        TalaUser _CurrentAU;
        
        /// <summary>
        /// Khởi tạo, tìm các dữ liệu liên quan của người dùng đang đăng nhập hiện tại (current AU). Nếu không có authkey, sẽ tự động kết thúc request và trả về error notlogin
        /// </summary>
        /// <param name="context"></param>
        public TalaAutorun(HttpContext context)
        {
            _context = context;
            _CurrentAuthkey = context.Request["authkey"].ToStringSafetyNormalize();
            _CurrentAU = Song.Instance.GetUserByUsername(Song.Instance.GetUsernameByAuthkey(_CurrentAuthkey));

            if (_CurrentAU == null)
            {
                XMLHttpHandler httphandler = new XMLHttpHandler();
                httphandler.Cmd.Add(APICommandStatus.Get_WRONG_AUTHKEY_CommandStatus());
                httphandler.ProcessRequest(context);
                context.Response.End();
            }
        }


        public string AutorunInVan()
        {
            // TODO: mỗi hành động tự làm, đều append vào sRet
            StringBuilder sRet = new StringBuilder();
                        
            // tìm seat đang đến lượt hiện tại
            Seat currentInTurnSeat = _CurrentAU.CurrentSoi.GetSeatOfCurrentInTurn();

            /// kiểm tra với Volatine Repository (ở đây dùng luôn ASP.NET Cache)
            object isOK = _context.Cache[""];
            _context.Cache.Insert(
            /// if inturn player, on time fire
            //khi đến lượt, 
            //xem bài có mấy cây
            //9 cây, chưa bốc, tự bốc
            //10 cây, bốc nhưng chưa đánh, tự đánh

            //bài đã đánh có 3 cây
            //hạ hộ
            //gửi hộ

            //, đánh tránh những cây trong phỏm đã ăn, nếu có thể tránh
            //tránh cạ, nếu có thể tránh
            //đánh cây to nhất ok, nếu ko tránh đc, vẫn đánh cây to nhất



            // TODO: neeus 
            // TODO: 
            // TODO: 
            // TODO: 
            // TODO: 
            // TODO: 
            // TODO: 
            // TODO: 
            // TODO: 

            /// 
            /// trả lại chuỗi kết quả ra ngoài
            /// có thể để còn log

            return sRet.ToString();
        }

        
    }
}
